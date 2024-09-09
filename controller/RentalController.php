<?php
require_once 'models/Rental.php';
require_once 'models/Vehicule.php';
require_once 'models/RentalOffer.php';
require_once 'models/Invoice.php';

class RentalController extends BaseController {
    public function index() {
        $this->requireLogin();
        $userId = $this->getCurrentUser()->getId();
        $page = $this->getQueryParam('page', 1);
        $perPage = 10;
        $status = $this->getQueryParam('status', null);
        
        $rentals = Rental::findByUserId($userId, $page, $perPage, $status);
        $totalRentals = Rental::countByUserId($userId, $status);
        $totalPages = ceil($totalRentals / $perPage);
        
        $this->render('rentals/index', [
            'rentals' => $rentals,
            'page' => $page,
            'totalPages' => $totalPages,
            'status' => $status,
            'title' => 'Mes locations'
        ], 'main');
    }

    public function create($offerId) {
        $this->requireLogin();
        $offer = RentalOffer::findById($offerId);
        if (!$offer || !$offer->isAvailable()) {
            $this->redirect('rental_offers', ['error' => 'Offre non disponible']);
        }

        $vehicule = Vehicule::findById($offer->getVehiculeId());

        if ($this->isPost()) {
            $data = $this->getPostData();
            $data['client_id'] = $this->getCurrentUser()->getId();
            $data['vehicule_id'] = $vehicule->getId();
            $data['offer_id'] = $offerId;
            $data['status'] = 'En cours';
            
            if (!$this->validateDates($data['date_debut'], $data['date_fin'])) {
                $this->render('rentals/create', [
                    'offer' => $offer,
                    'vehicule' => $vehicule,
                    'error' => 'Dates invalides',
                    'title' => 'Créer une location'
                ], 'main');
                return;
            }

            $data['prix_total'] = $this->calculateTotalPrice($data['date_debut'], $data['date_fin'], $offer);

            $rental = Rental::create($data);
            if ($rental) {
                $vehicule->setAvailable(false);
                $vehicule->update();
                $offer->setAvailable(false);
                $offer->update();
                $this->redirect('rentals', ['success' => 'Location créée avec succès']);
            } else {
                $this->render('rentals/create', [
                    'offer' => $offer,
                    'vehicule' => $vehicule,
                    'error' => 'Erreur lors de la création de la location',
                    'title' => 'Créer une location'
                ], 'main');
            }
        } else {
            $this->render('rentals/create', [
                'offer' => $offer,
                'vehicule' => $vehicule,
                'title' => 'Créer une location'
            ], 'main');
        }
    }

    public function show($id) {
        $this->requireLogin();
        $rental = Rental::findById($id);
        if ($rental && $rental->getClientId() == $this->getCurrentUser()->getId()) {
            $vehicule = Vehicule::findById($rental->getVehiculeId());
            $offer = RentalOffer::findById($rental->getOfferId());
            $this->render('rentals/show', [
                'rental' => $rental, 
                'vehicule' => $vehicule, 
                'offer' => $offer,
                'title' => 'Détails de la location'
            ], 'main');
        } else {
            $this->renderError(404);
        }
    }

    public function returnVehicule($id) {
        $this->requireLogin();
        $rental = Rental::findById($id);
        if ($rental && $rental->getClientId() == $this->getCurrentUser()->getId() && $rental->getStatus() == 'En cours') {
            $rental->update(['status' => 'Terminée']);
            $vehicule = Vehicule::findById($rental->getVehiculeId());
            $vehicule->setAvailable(true);
            $vehicule->update();
            $offer = RentalOffer::findById($rental->getOfferId());
            $offer->setAvailable(true);
            $offer->update();
            
            $invoice = $this->generateInvoice($rental);
            $this->redirect('rentals', ['success' => 'Véhicule retourné avec succès. Facture n°' . $invoice->getId() . ' générée.']);
        } else {
            $this->renderError(404);
        }
    }

    public function invoice($id) {
        $this->requireLogin();
        $rental = Rental::findById($id);
        if ($rental && $rental->getClientId() == $this->getCurrentUser()->getId() && $rental->getStatus() == 'Terminée') {
            $invoice = Invoice::findByRentalId($rental->getId());
            if ($invoice) {
                $this->render('invoices/show', [
                    'invoice' => $invoice, 
                    'rental' => $rental,
                    'title' => 'Facture de location'
                ], 'main');
            } else {
                $this->renderError(404);
            }
        } else {
            $this->renderError(404);
        }
    }

    private function generateInvoice($rental) {
        $offer = RentalOffer::findById($rental->getOfferId());
        $amount = $this->calculateTotalPrice($rental->getDateDebut(), $rental->getDateFin(), $offer);
        
        return Invoice::create([
            'rental_id' => $rental->getId(),
            'montant' => $amount,
            'date_emission' => date('Y-m-d')
        ]);
    }

    private function calculateTotalPrice($dateDebut, $dateFin, $offer) {
        $duree = $this->calculerDuree($dateDebut, $dateFin);
        return $offer->getPrix() * ceil($duree / $offer->getDuree());
    }

    private function calculerDuree($dateDebut, $dateFin) {
        $debut = new DateTime($dateDebut);
        $fin = new DateTime($dateFin);
        $interval = $debut->diff($fin);
        return $interval->days + 1; // +1 car on compte le jour de début
    }

    private function validateDates($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $now = new DateTime();

        return $start >= $now && $end > $start;
    }
}
