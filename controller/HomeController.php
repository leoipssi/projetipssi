<?php
class HomeController {
    public function index() {
        $recentVehicles = Vehicle::getRecentVehicles(5);
        $activeOffers = RentalOffer::getActiveOffers(3);
        
        $content = $this->render('home', ['recentVehicles' => $recentVehicles, 'activeOffers' => $activeOffers]);
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
