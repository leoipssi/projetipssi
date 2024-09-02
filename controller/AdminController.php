<?php
class AdminController {
    public function __construct() {
        if (!isAdmin()) {
            header('Location: index.php?route=login');
            exit;
        }
    }

    public function dashboard() {
        $totalVehicules = Vehicule::count();
        $totalUsers = User::count();
        $totalRentals = Rental::count();
        $recentRentals = Rental::getRecent(5);
        $topVehicules = Vehicule::getMostRented(5);

        $content = $this->render('admin/dashboard', [
            'totalVehicules' => $totalVehicules,
            'totalUsers' => $totalUsers,
            'totalRentals' => $totalRentals,
            'recentRentals' => $recentRentals,
            'topVehicules' => $topVehicules
        ]);
        $this->renderLayout($content);
    }

    public function manageVehicules() {
        $action = $_GET['action'] ?? '';

        if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->addVehicule($_POST);
        } elseif ($action === 'edit' && isset($_GET['id'])) {
            $this->editVehicule($_GET['id']);
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $this->deleteVehicule($_GET['id']);
        }

        $vehicules = Vehicule::findAll();
        $content = $this->render('admin/vehicules', ['vehicules' => $vehicules]);
        $this->renderLayout($content);
    }

    private function addVehicule($data) {
        // Logique pour ajouter un véhicule
        $result = Vehicule::create($data);
        if ($result) {
            $_SESSION['flash'] = "Véhicule ajouté avec succès.";
        } else {
            $_SESSION['flash'] = "Erreur lors de l'ajout du véhicule.";
        }
    }

    private function editVehicule($id) {
        // Logique pour éditer un véhicule
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = Vehicule::update($id, $_POST);
            if ($result) {
                $_SESSION['flash'] = "Véhicule mis à jour avec succès.";
            } else {
                $_SESSION['flash'] = "Erreur lors de la mise à jour du véhicule.";
            }
        }
        $vehicule = Vehicule::findById($id);
        $content = $this->render('admin/edit_vehicule', ['vehicule' => $vehicule]);
        $this->renderLayout($content);
    }

    private function deleteVehicule($id) {
        // Logique pour supprimer un véhicule
        $result = Vehicule::delete($id);
        if ($result) {
            $_SESSION['flash'] = "Véhicule supprimé avec succès.";
        } else {
            $_SESSION['flash'] = "Erreur lors de la suppression du véhicule.";
        }
    }

    public function manageClients() {
        $clients = User::findAllClients();
        $content = $this->render('admin/clients', ['clients' => $clients]);
        $this->renderLayout($content);
    }

    public function manageOffers() {
        $action = $_GET['action'] ?? '';

        if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->addOffer($_POST);
        } elseif ($action === 'edit' && isset($_GET['id'])) {
            $this->editOffer($_GET['id']);
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $this->deleteOffer($_GET['id']);
        }

        $offers = RentalOffer::findAll();
        $content = $this->render('admin/offers', ['offers' => $offers]);
        $this->renderLayout($content);
    }

    private function addOffer($data) {
        // Logique pour ajouter une offre
        $result = RentalOffer::create($data);
        if ($result) {
            $_SESSION['flash'] = "Offre ajoutée avec succès.";
        } else {
            $_SESSION['flash'] = "Erreur lors de l'ajout de l'offre.";
        }
    }

    private function editOffer($id) {
        // Logique pour éditer une offre
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = RentalOffer::update($id, $_POST);
            if ($result) {
                $_SESSION['flash'] = "Offre mise à jour avec succès.";
            } else {
                $_SESSION['flash'] = "Erreur lors de la mise à jour de l'offre.";
            }
        }
        $offer = RentalOffer::findById($id);
        $content = $this->render('admin/edit_offer', ['offer' => $offer]);
        $this->renderLayout($content);
    }

    private function deleteOffer($id) {
        // Logique pour supprimer une offre
        $result = RentalOffer::delete($id);
        if ($result) {
            $_SESSION['flash'] = "Offre supprimée avec succès.";
        } else {
            $_SESSION['flash'] = "Erreur lors de la suppression de l'offre.";
        }
    }

    private function render($view, $data = []) {
        extract($data);
        ob_start();
        include "views/{$view}.php";
        return ob_get_clean();
    }

    private function renderLayout($content) {
        include 'views/layouts/main.php';
    }
}
