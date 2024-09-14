<?php
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Vehicule.php';
require_once __DIR__ . '/../models/RentalOffer.php';
require_once __DIR__ . '/../models/User.php';

class HomeController extends BaseController {
    private $db;

    public function __construct($logger = null) {
        parent::__construct($logger);
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        try {
            $this->logger->debug("Début de la méthode index()");
            $this->checkDatabaseConnection();
            $this->logger->debug("Connexion à la base de données vérifiée");
            
            $this->logger->debug("Tentative de récupération des véhicules récents");
            $recentVehicules = $this->getRecentVehicules();
            $this->logger->debug("Véhicules récents récupérés", ['count' => count($recentVehicules)]);

            // Vérification supplémentaire
            foreach ($recentVehicules as $index => $vehicule) {
                $this->logger->debug("Vérification du véhicule", [
                    'index' => $index,
                    'is_object' => is_object($vehicule),
                    'class' => is_object($vehicule) ? get_class($vehicule) : 'N/A',
                    'has_getId' => is_object($vehicule) && method_exists($vehicule, 'getId')
                ]);
            }
            
            $this->logger->debug("Tentative de récupération des offres actives");
            $activeOffers = $this->getActiveOffers();
            $this->logger->debug("Offres actives récupérées", ['count' => count($activeOffers)]);
            
            // Récupérer les informations de l'utilisateur connecté
            $user = $this->getCurrentUser();
            
            $this->logger->debug("Appel de la méthode render()");
            $this->render('home', [
                'recentVehicules' => $recentVehicules,
                'activeOffers' => $activeOffers,
                'user' => $user
            ]);
            $this->logger->debug("Fin de la méthode index()");
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
            $this->logger->debug("Tentative de récupération des véhicules récents");
            $recentVehicules = Vehicule::getRecentVehicules(5);
            $this->logger->info("Véhicules récents récupérés avec succès", ['count' => count($recentVehicules)]);
            
            $validVehicules = [];
            foreach ($recentVehicules as $index => $vehicule) {
                if (!is_object($vehicule)) {
                    $this->logger->error("L'élément n'est pas un objet", ['index' => $index, 'type' => gettype($vehicule)]);
                    continue;
                }
                if (!($vehicule instanceof Vehicule)) {
                    $this->logger->error("L'élément n'est pas une instance de Vehicule", ['index' => $index, 'class' => get_class($vehicule)]);
                    continue;
                }
                if (!method_exists($vehicule, 'getId')) {
                    $this->logger->error("La méthode getId() n'existe pas pour cet objet", ['index' => $index, 'methods' => get_class_methods($vehicule)]);
                    continue;
                }
                
                $this->logger->debug("Véhicule valide récupéré", [
                    'index' => $index, 
                    'id' => $vehicule->getId(), 
                    'marque' => $vehicule->getMarque(), 
                    'modele' => $vehicule->getModele()
                ]);
                $validVehicules[] = $vehicule;
            }
            
            $this->logger->info("Nombre de véhicules valides : " . count($validVehicules));
            return $validVehicules;
        } catch (Exception $e) {
            $this->logger->error("Erreur lors de la récupération des véhicules récents", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception("Impossible de récupérer les véhicules récents: " . $e->getMessage());
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
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        if (DEBUG_MODE) {
            echo "<h1>Erreur</h1>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p>File: " . htmlspecialchars($e->getFile()) . " at line " . $e->getLine() . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        } else {
            $this->render('error', ['message' => 'Une erreur est survenue lors du chargement de la page d\'accueil']);
        }
    }

    public function about() {
        $user = $this->getCurrentUser();
        $this->render('about', ['title' => 'À propos de nous', 'user' => $user]);
    }

    public function contact() {
        $user = $this->getCurrentUser();
        if ($this->isPost()) {
            $this->handleContactForm($user);
        } else {
            $this->render('contact', ['title' => 'Contactez-nous', 'user' => $user]);
        }
    }

    private function handleContactForm($user) {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $message = $_POST['message'] ?? '';
        // Validation des données du formulaire
        if (empty($name) || empty($email) || empty($message)) {
            $this->render('contact', [
                'title' => 'Contactez-nous',
                'error' => 'Veuillez remplir tous les champs.',
                'user' => $user
            ]);
            return;
        }
        $this->render('contact', [
            'title' => 'Contactez-nous',
            'success' => 'Votre message a été envoyé avec succès.',
            'user' => $user
        ]);
    }

    public function terms() {
        $user = $this->getCurrentUser();
        $this->render('terms', ['title' => 'Conditions d\'utilisation', 'user' => $user]);
    }

    public function privacy() {
        $user = $this->getCurrentUser();
        $this->render('privacy', ['title' => 'Politique de confidentialité', 'user' => $user]);
    }
}
?>
