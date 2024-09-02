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
        $content = $this->render('admin/dashboard', [
            'totalVehicles' => $totalVehicles,
            'totalUsers' => $totalUsers,
            'totalRentals' => $totalRentals
        ]);
        $this->renderLayout($content);
    }

    public function manageVehicles() {
        $vehicles = Vehicle::findAll();
        $content = $this->render('admin/vehicles', ['vehicles' => $vehicles]);
        $this->renderLayout($content);
    }

    public function manageClients() {
        $clients = User::findAllClients();
        $content = $this->render('admin/clients', ['clients' => $clients]);
        $this->renderLayout($content);
    }

    public function manageOffers() {
        $offers = RentalOffer::findAll();
        $content = $this->render('admin/offers', ['offers' => $offers]);
        $this->renderLayout($content);
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
