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
            $data['duration'] = 7; // Durée fixée à 7 jours
            $vehicule = Vehicule::findById($data['vehicule_id']);
            if (!$vehicule) {
                $this->render('rental_offers/create', ['error' => 'Véhicule non trouvé']);
                return;
            }
            $offer = RentalOffer::create($data);
            if ($offer) {
                $this->redirect('rental_offers', ['success' => 'Offre créée avec succès']);
            } else {
                $this->render('rental_offers/create', ['error' => 'Erreur lors de la création de l\'offre']);
            }
        } else {
            $vehicules = Vehicule::findAll();
            $this->render('rental_offers/create', ['vehicules' => $vehicules]);
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
            $data['duration'] = 7; // Assurez-vous que la durée reste 7 jours
            if ($offer->update($data)) {
                $this->redirect('rental_offers', ['success' => 'Offre mise à jour avec succès']);
            } else {
                $this->render('rental_offers/edit', ['offer' => $offer, 'error' => 'Erreur lors de la mise à jour de l\'offre']);
            }
        } else {
            $vehicules = Vehicule::findAll();
            $this->render('rental_offers/edit', ['offer' => $offer, 'vehicules' => $vehicules]);
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

    public function hide($id) {
        $offer = RentalOffer::findById($id);
        if ($offer) {
            $offer->hide();
            $this->redirect('rental_offers', ['success' => 'Offre masquée avec succès']);
        } else {
            $this->renderError(404);
        }
    }

    public function listAvailable() {
        $availableOffers = RentalOffer::findAvailable();
        $this->render('rental_offers/available', ['offers' => $availableOffers]);
    }

    public function checkAvailability($id) {
        $offer = RentalOffer::findById($id);
        if ($offer) {
            $isAvailable = $offer->isAvailable();
            $this->render('rental_offers/availability', ['offer' => $offer, 'isAvailable' => $isAvailable]);
        } else {
            $this->renderError(404);
        }
    }

    public function listRentedVehicules() {
        $rentedVehicules = Vehicule::findRented();
        $this->render('rental_offers/rented_vehicules', ['vehicules' => $rentedVehicules]);
    }

    public function subscribe($id) {
        $offer = RentalOffer::findById($id);
        if (!$offer) {
            $this->renderError(404);
            return;
        }
        if ($this->isPost()) {
            $data = $this->getPostData();
            $data['offer_id'] = $id;
            $data['duration'] = 7; // Durée fixée à 7 jours
            $rental = Rental::create($data);
            if ($rental) {
                $this->redirect('rentals', ['success' => 'Contrat de location souscrit avec succès']);
            } else {
                $this->render('rental_offers/subscribe', ['offer' => $offer, 'error' => 'Erreur lors de la souscription du contrat']);
            }
        } else {
            $this->render('rental_offers/subscribe', ['offer' => $offer]);
        }
    }
}
