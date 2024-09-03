<?php
class RentalOfferController extends BaseController {
    public function __construct() {
        parent::__construct();
        $this->requireAdmin();
    }

    public function index() {
        $offers = RentalOffer::findAll();
        $this->render('rental_offers/index', ['offers' => $offers]);
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->getPostData();
            $offer = RentalOffer::create($data);
            if ($offer) {
                $this->redirect('rental_offers', ['success' => 'Offre créée avec succès']);
            } else {
                $this->render('rental_offers/create', ['error' => 'Erreur lors de la création de l\'offre']);
            }
        } else {
            $vehicleTypes = VehicleType::findAll();
            $this->render('rental_offers/create', ['vehicleTypes' => $vehicleTypes]);
        }
    }

    public function edit($id) {
        $offer = RentalOffer::findById($id);
        if (!$offer) {
            $this->renderError(404);
            return;
        }

        if ($this->isPost()) {
            $data = $this->getPostData();
            if ($offer->update($data)) {
                $this->redirect('rental_offers', ['success' => 'Offre mise à jour avec succès']);
            } else {
                $this->render('rental_offers/edit', ['offer' => $offer, 'error' => 'Erreur lors de la mise à jour de l\'offre']);
            }
        } else {
            $vehicleTypes = VehicleType::findAll();
            $this->render('rental_offers/edit', ['offer' => $offer, 'vehicleTypes' => $vehicleTypes]);
        }
    }

    public function delete($id) {
        $offer = RentalOffer::findById($id);
        if ($offer && !$offer->hasActiveRentals()) {
            if ($offer->delete()) {
                $this->redirect('rental_offers', ['success' => 'Offre supprimée avec succès']);
            } else {
                $this->redirect('rental_offers', ['error' => 'Erreur lors de la suppression de l\'offre']);
            }
        } else {
            $this->redirect('rental_offers', ['error' => 'Impossible de supprimer cette offre']);
        }
    }

    public function toggleActive($id) {
        $offer = RentalOffer::findById($id);
        if ($offer) {
            $offer->toggleActive();
            $this->redirect('rental_offers', ['success' => 'Statut de l\'offre mis à jour']);
        } else {
            $this->renderError(404);
        }
    }
}
