<?php
class HomeController extends BaseController {
    public function index() {
        $recentVehicules = Vehicule::getRecentVehicules(5);
        $activeOffers = RentalOffer::getActiveOffers(3);
        
        $this->render('home', [
            'recentVehicules' => $recentVehicules,
            'activeOffers' => $activeOffers
        ]);
    }
}
