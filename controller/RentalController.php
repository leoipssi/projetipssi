<?php
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
            'status' => $status
        ]);
    }

    public function create($vehiculeId) {
        $this->requireLogin();
        $vehicule = Vehicule::findById($vehiculeId);
        if (!$vehicule || !$vehicule->isAvailable()) {
            $this->redirect('vehicules', ['error' => 'Véhicule non disponible']);
        }

        if ($this->isPost()) {
            $data = $this->getPostData();
            $data['client_id'] = $this->getCurrentUser()->getId();
            $data['vehicule_id'] = $vehiculeId;
            $data['status'] = 'En cours';
            
            // Vérification des dates
            if (!$this->validateDates($data['date_debut'], $data['date_fin'])) {
                $this->render('rentals/create', ['vehicule' => $vehicule, 'error' => 'Dates invalides']);
                return;
            }

            $rental = Rental::create($data);
            if ($rental) {
                $vehicule->setAvailable(false);
                $this->redirect('rentals', ['success' => 'Location créée avec succès']);
            } else {
                $this->render('rentals/create', ['vehicule' => $vehicule, 'error' => 'Erreur lors de la création de la location']);
            }
        } else {
            $offers = RentalOffer::findByVehiculeType($vehicule->getTypeId());
            $this->render('rentals/create', ['vehicule' => $vehicule, 'offers' => $offers]);
        }
    }

    public function show($id) {
        $this->requireLogin();
        $rental = Rental::findById($id);
        if ($rental && $rental->getClientId() == $this->getCurrentUser()->getId()) {
            $vehicule = Vehicule::findById($rental->getVehiculeId());
            $offer = RentalOffer::findById($rental->getOfferId());
            $this->render('rentals/show', ['rental' => $rental, 'vehicule' => $vehicule, 'offer' => $offer]);
        } else {
            $this->renderError(404);
        }
    }

    public function returnVehicle($id) {
        $this->requireLogin();
        $rental = Rental::findById($id);
        if ($rental && $rental->getClientId() == $this->getCurrentUser()->getId() && $rental->getStatus() == 'En cours') {
            $rental->update(['status' => 'Terminée']);
            $vehicule = Vehicule::findById($rental->getVehiculeId());
            $vehicule->setAvailable(true);
            
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
                $this->render('invoices/show', ['invoice' => $invoice, 'rental' => $rental]);
            } else {
                $this->renderError(404);
            }
        } else {
            $this->renderError(404);
        }
    }

    private function generateInvoice($rental) {
        $offer = RentalOffer::findById($rental->getOfferId());
        $amount = $this->calculateTotalAmount($rental, $offer);
        
        return Invoice::create([
            'rental_id' => $rental->getId(),
            'montant' => $amount,
            'date_emission' => date('Y-m-d')
        ]);
    }

    private function calculateTotalAmount($rental, $offer) {
        $days = (strtotime($rental->getDateFin()) - strtotime($rental->getDateDebut())) / (60 * 60 * 24);
        return $offer->getPrix() * $days;
    }

    private function validateDates($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $now = new DateTime();

        return $start >= $now && $end > $start;
    }
}
