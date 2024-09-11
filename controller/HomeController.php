<?php
class HomeController extends BaseController {
    public function index() {
        global $db;

        // Vérification de la connexion à la base de données
        if (!$db instanceof PDO) {
            error_log("La connexion à la base de données n'est pas disponible dans HomeController");
            // Gérer l'erreur appropriée ici, par exemple :
            $this->render('error', ['message' => 'Erreur de connexion à la base de données']);
            return;
        }

        try {
            // Définir la connexion pour la classe Vehicule
            Vehicule::setDB($db);
            
            // Récupérer les véhicules récents
            $recentVehicules = Vehicule::getRecentVehicules(5);
            error_log("Véhicules récents récupérés avec succès : " . count($recentVehicules));

            // Définir la connexion pour la classe RentalOffer si nécessaire
            // RentalOffer::setDB($db);  // Décommentez si nécessaire
            
            // Récupérer les offres actives
            $activeOffers = RentalOffer::getActiveOffers(3);
            error_log("Offres actives récupérées avec succès : " . count($activeOffers));

            // Rendre la vue
            $this->render('home', [
                'recentVehicules' => $recentVehicules,
                'activeOffers' => $activeOffers
            ]);
        } catch (Exception $e) {
            error_log("Erreur dans HomeController::index : " . $e->getMessage());
            // Gérer l'erreur appropriée ici, par exemple :
            $this->render('error', ['message' => 'Une erreur est survenue lors du chargement de la page d\'accueil']);
        }
    }
}
