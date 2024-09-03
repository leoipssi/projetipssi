<?php
class RentalController extends BaseController {
    public function index() {
        $this->requireLogin();
        $userId = $this->getCurrentUser()->getId();
        $page = $this->getQueryParam('page', 1);
        $perPage = 10; // Nombre de locations par page
        $rentals = Rental::findByUserId($userId, $page, $perPage);
        $totalRentals = Rental::countByUserId($userId);
        $totalPages = ceil($totalRentals / $perPage);
        $this->render('rentals/index', [
            'rentals' => $rentals,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function create($vehiculeId) {
        $this->requireLogin();
        $vehicule = Vehicule::findById($vehiculeId);
        if (!$vehicule) {
            $this->redirect('vehicules', ['error' => 'Véhicule non trouvé']);
        }

        if ($this->isPost()) {
            $data = $this->getPostData();
            $data['client_id'] = $this->getCurrentUser()->getId();
            $data['vehicule_id'] = $vehiculeId;
            $rental = Rental::create($data);
            if ($rental) {
                $this->redirect('rentals', ['success' => 'Location créée avec succès']);
            } else {
                $this->render('rentals/create', ['vehicule' => $vehicule, 'error' => 'Erreur lors de la création de la location']);
            }
        } else {
            $this->render('rentals/create', ['vehicule' => $vehicule]);
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
            $vehicule->update(['disponible' => true]);
            
            $this->generateInvoice($rental);
            $this->redirect('rentals', ['success' => 'Véhicule retourné avec succès']);
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
        $amount = $offer->getPrix();
        
        return Invoice::create([
            'rental_id' => $rental->getId(),
            'montant' => $amount,
            'date_emission' => date('Y-m-d')
        ]);
    }
}
