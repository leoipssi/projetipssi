<?php
class AdminController {
    public function __construct() {
        if (!isAdmin()) {
            header('Location: index.php?route=login');
            exit;
        }
    }

    public function dashboard() {
        $totalVehicles = Vehicle::count();
        $totalUsers = User::count();
        $totalRentals = Rental::count();
        $recentRentals = Rental::getRecent(5);
        $topVehicles = Vehicle::getMostRented(5);

        $content = $this->render('admin/dashboard', [
            'totalVehicles' => $totalVehicles,
            'totalUsers' => $totalUsers,
            'totalRentals' => $totalRentals,
            'recentRentals' => $recentRentals,
            'topVehicles' => $topVehicles
        ]);
        $this->renderLayout($content);
    }

    public function manageVehicles() {
        $action = $_GET['action'] ?? '';

        if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->addVehicle($_POST);
        } elseif ($action === 'edit' && isset($_GET['id'])) {
            $this->editVehicle($_GET['id']);
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $this->deleteVehicle($_GET['id']);
        }

        $vehicles = Vehicle::findAll();
        $content = $this->render('admin/vehicles', ['vehicles' => $vehicles]);
        $this->renderLayout($content);
    }

    private function addVehicle($data) {
        // Logique pour ajouter un véhicule
        $result = Vehicle::create($data);
        if ($result) {
            $_SESSION['flash'] = "Véhicule ajouté avec succès.";
        } else {
            $_SESSION['flash'] = "Erreur lors de l'ajout du véhicule.";
        }
    }

    private function editVehicle($id) {
        // Logique pour éditer un véhicule
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = Vehicle::update($id, $_POST);
            if ($result) {
                $_SESSION['flash'] = "Véhicule mis à jour avec succès.";
            } else {
                $_SESSION['flash'] = "Erreur lors de la mise à jour du véhicule.";
            }
        }
        $vehicle = Vehicle::findById($id);
        $content = $this->render('admin/edit_vehicle', ['vehicle' => $vehicle]);
        $this->renderLayout($content);
    }

    private function deleteVehicle($id) {
        // Logique pour supprimer un véhicule
        $result = Vehicle::delete($id);
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
