<?php

class HomeController extends BaseController {
    private $db;

    public function __construct($logger = null) {
        parent::__construct($logger);
        global $db;
        $this->db = $db;
    }

    public function index() {
        try {
            $this->checkDatabaseConnection();

            Vehicule::setDB($this->db);
            RentalOffer::setDB($this->db);
            
            $recentVehicules = $this->getRecentVehicules();
            $activeOffers = $this->getActiveOffers();

            $this->render('home', [
                'recentVehicules' => $recentVehicules,
                'activeOffers' => $activeOffers
            ]);
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    private function checkDatabaseConnection() {
        if (!$this->db instanceof PDO) {
            throw new Exception("La connexion à la base de données n'est pas disponible");
        }
    }

    private function getRecentVehicules() {
        try {
            $recentVehicules = Vehicule::getRecentVehicules(5);
            $this->logger->info("Véhicules récents récupérés avec succès", ['count' => count($recentVehicules)]);
            return $recentVehicules;
        } catch (Exception $e) {
            $this->logger->error("Erreur lors de la récupération des véhicules récents", ['error' => $e->getMessage()]);
            throw new Exception("Impossible de récupérer les véhicules récents");
        }
    }

    private function getActiveOffers() {
        try {
            $activeOffers = RentalOffer::getActiveOffers(3);
            $this->logger->info("Offres actives récupérées avec succès", ['count' => count($activeOffers)]);
            return $activeOffers;
        } catch (Exception $e) {
            $this->logger->error("Erreur lors de la récupération des offres actives", ['error' => $e->getMessage()]);
            throw new Exception("Impossible de récupérer les offres actives");
        }
    }

    private function handleError(Exception $e) {
        $this->logger->error("Erreur dans HomeController::index", [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        $this->render('error', ['message' => 'Une erreur est survenue lors du chargement de la page d\'accueil']);
    }

    public function about() {
        $this->render('about', ['title' => 'À propos de nous']);
    }

    public function contact() {
        if ($this->isPost()) {
            $this->handleContactForm();
        } else {
            $this->render('contact', ['title' => 'Contactez-nous']);
        }
    }

    private function handleContactForm() {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $message = $_POST['message'] ?? '';

        // Validation des données du formulaire
        if (empty($name) || empty($email) || empty($message)) {
            $this->render('contact', [
                'title' => 'Contactez-nous',
                'error' => 'Veuillez remplir tous les champs.'
            ]);
            return;
        }

        $this->render('contact', [
            'title' => 'Contactez-nous',
            'success' => 'Votre message a été envoyé avec succès.'
        ]);
    }

    public function terms() {
        $this->render('terms', ['title' => 'Conditions d\'utilisation']);
    }

    public function privacy() {
        $this->render('privacy', ['title' => 'Politique de confidentialité']);
    }
}
