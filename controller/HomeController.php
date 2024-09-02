<?php
class HomeController {
    public function index() {
        $recentVehicules = Vehicule::getRecentVehicules(5);
        $activeOffers = RentalOffer::getActiveOffers(3);
        
        $content = $this->render('home', ['recentVehicules' => $recentVehicules, 'activeOffers' => $activeOffers]);
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
