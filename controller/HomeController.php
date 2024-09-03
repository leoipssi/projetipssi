<?php
class HomeController extends BaseController {
    public function index() {
        $recentVehicules = Vehicule::getRecentVehicules(5);
        $activeOffers = RentalOffer::getActiveOffers(3);
        
        $content = $this->render('home', ['recentVehicules' => $recentVehicules, 'activeOffers' => $activeOffers]);
        $this->renderLayout($content);
    }

    // Changez la visibilité de cette méthode en 'protected' ou 'public'
    protected function render($view, $data = []) {
        extract($data);
        ob_start();
        include "views/{$view}.php";
        return ob_get_clean();
    }

    protected function renderLayout($content) {
        include 'views/layouts/main.php';
    }
}
