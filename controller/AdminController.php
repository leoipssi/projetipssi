<?php
// Définir le chemin de base du projet
define('BASE_PATH', '/var/www/html/e-motion');

// Inclure les dépendances nécessaires
require_once BASE_PATH . '/controllers/BaseController.php';
require_once BASE_PATH . '/controllers/AuthController.php';
require_once BASE_PATH . '/models/Vehicule.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Rental.php';
require_once BASE_PATH . '/models/VehiculeType.php';
require_once BASE_PATH . '/models/RentalOffer.php';

class AdminController extends BaseController {
    protected $logger;

    public function __construct($logger = null) {
        parent::__construct();
        $this->logger = $logger ?? new \Monolog\Logger('admin');
        $this->logger->pushHandler(new \Monolog\Handler\StreamHandler(BASE_PATH . '/logs/admin.log', \Monolog\Logger::DEBUG));
        
        // Vérifie si l'utilisateur est un administrateur
        if (!$this->isAdmin()) {
            $this->logger->warning("Tentative d'accès non autorisé à l'interface admin");
            $this->redirect('home');
        }
    }

    protected function isAdmin() {
        return AuthController::isAdmin();
    }

    public function dashboard() {
        $this->logger->info("Accès au tableau de bord administrateur");
        try {
            $this->testDatabaseConnection();

            $totalVehicules = Vehicule::count();
            $totalUsers = User::count();
            $totalRentals = Rental::count();
            $totalRevenue = Rental::totalRevenue();
            
            $totalRevenue = is_null($totalRevenue) ? 0 : $totalRevenue;
            
            $recentRentals = Rental::getRecent(5);
            $topVehicules = Vehicule::getTopRented(5);

            $this->logger->debug("Statistiques récupérées pour le tableau de bord", [
                'totalVehicules' => $totalVehicules,
                'totalUsers' => $totalUsers,
                'totalRentals' => $totalRentals,
                'totalRevenue' => $totalRevenue
            ]);

            $this->render('admin/dashboard', [
                'totalVehicules' => $totalVehicules,
                'totalUsers' => $totalUsers,
                'totalRentals' => $totalRentals,
                'totalRevenue' => $totalRevenue,
                'recentRentals' => $recentRentals,
                'topVehicules' => $topVehicules
            ]);
        } catch (Exception $e) {
            $this->handleError($e, 'Erreur dans dashboard');
        }
    }

    public function vehicules() {
        $this->logger->info("Accès à la liste des véhicules");
        try {
            $this->testDatabaseConnection();
            $vehicules = Vehicule::getAll();
            $this->logger->debug("Nombre de véhicules récupérés : " . count($vehicules));
            $this->render('vehicules/index', [
                'vehicules' => $vehicules
            ]);
        } catch (Exception $e) {
            $this->handleError($e, 'Erreur dans vehicules');
        }
    }
    
    public function addVehicule() {
        $this->logger->info("Accès au formulaire d'ajout de véhicule");
        try {
            $this->testDatabaseConnection();
            if (!class_exists('VehiculeType')) {
                throw new Exception('VehiculeType class not found. File location: ' . BASE_PATH . '/models/VehiculeType.php');
            }

            $vehiculeTypes = VehiculeType::getAll();
            $errors = [];

            if ($this->isPost()) {
                $this->logger->debug("Tentative d'ajout d'un nouveau véhicule");
                $vehiculeData = $this->sanitizeUserData($_POST);
                $errors = $this->validateVehiculeInput($vehiculeData);

                if (empty($errors)) {
                    $vehicule = Vehicule::create($vehiculeData);
                    if ($vehicule) {
                        $this->logger->info("Nouveau véhicule ajouté avec succès", ['vehiculeId' => $vehicule->getId()]);
                        $this->redirect('admin', ['action' => 'dashboard', 'success' => 'Véhicule ajouté avec succès']);
                    } else {
                        $this->logger->error("Échec de l'ajout du véhicule");
                        $errors[] = "Erreur lors de l'ajout du véhicule.";
                    }
                }
            }

            $this->render('admin/addVehicule', [
                'vehiculeTypes' => $vehiculeTypes,
                'errors' => $errors
            ]);
        } catch (Exception $e) {
            $this->handleError($e, 'Erreur dans addVehicule');
        }
    }

    public function createOffer() {
        $this->logger->info("Accès au formulaire de création d'offre");
        try {
            $this->testDatabaseConnection();
            if (!class_exists('VehiculeType')) {
                throw new Exception('VehiculeType class not found. File location: ' . BASE_PATH . '/models/VehiculeType.php');
            }

            $vehiculeTypes = VehiculeType::getAll();
            $errors = [];

            if ($this->isPost()) {
                $this->logger->debug("Tentative de création d'une nouvelle offre");
                $offerData = $this->sanitizeUserData($_POST);
                $errors = $this->validateOfferInput($offerData);

                if (empty($errors)) {
                    $offer = RentalOffer::create($offerData);
                    if ($offer) {
                        $this->logger->info("Nouvelle offre créée avec succès", ['offerId' => $offer->getId()]);
                        $this->redirect('admin', ['action' => 'dashboard', 'success' => 'Offre créée avec succès']);
                    } else {
                        $this->logger->error("Échec de la création de l'offre");
                        $errors[] = "Erreur lors de la création de l'offre.";
                    }
                }
            }

            $this->render('admin/createOffer', [
                'vehiculeTypes' => $vehiculeTypes,
                'errors' => $errors
            ]);
        } catch (Exception $e) {
            $this->handleError($e, 'Erreur dans createOffer');
        }
    }

    public function users() {
        $this->redirect('admin', ['action' => 'manageUsers']);
    }

    public function manageUsers() {
        $this->logger->info("Début de la méthode manageUsers");
        try {
            $this->testDatabaseConnection();
            $page = $this->getQueryParam('page', 1);
            $search = $this->getQueryParam('search', '');
            $role = $this->getQueryParam('role', '');
            $sortBy = $this->getQueryParam('sort', 'id');
            $sortOrder = $this->getQueryParam('order', 'ASC');

            $this->logger->debug("Paramètres de filtrage", [
                'page' => $page,
                'search' => $search,
                'role' => $role,
                'sortBy' => $sortBy,
                'sortOrder' => $sortOrder
            ]);

            try {
                $users = User::getFiltered($page, $search, $role, $sortBy, $sortOrder);
                $this->logger->debug("Utilisateurs récupérés avec succès", ['count' => count($users)]);
            } catch (Exception $e) {
                $this->logger->error("Erreur dans User::getFiltered()", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            $totalPages = User::getTotalPages($search, $role);

            $this->render('admin/manageUsers', [
                'users' => $users,
                'page' => $page,
                'totalPages' => $totalPages,
                'search' => $search,
                'role' => $role,
                'sortBy' => $sortBy,
                'sortOrder' => $sortOrder,
                'availableRoles' => User::getAvailableRoles()
            ]);

            $this->logger->info("Fin de la méthode manageUsers");
        } catch (Exception $e) {
            $this->logger->error("Erreur détaillée dans manageUsers", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->handleError($e, 'Erreur dans manageUsers: ' . $e->getMessage());
        }
    }

    private function validateVehiculeInput($data) {
        $errors = [];
        if (empty($data['nom'])) {
            $errors[] = "Le nom du véhicule est requis.";
        }
        if (empty($data['type'])) {
            $errors[] = "Le type de véhicule est requis.";
        }
        return $errors;
    }

    private function validateOfferInput($data) {
        $errors = [];
        if (empty($data['titre'])) {
            $errors[] = "Le titre de l'offre est requis.";
        }
        if (empty($data['prix'])) {
            $errors[] = "Le prix de l'offre est requis.";
        }
        return $errors;
    }

    private function handleError(Exception $e, $context) {
        $this->logger->error($context . ': ' . $e->getMessage(), [
            'exception' => $e,
            'trace' => $e->getTraceAsString()
        ]);
        $this->render('error', [
            'message' => 'Une erreur est survenue.',
            'details' => $e->getMessage()
        ]);
    }

    private function testDatabaseConnection() {
        try {
            global $conn;
            if (!$conn) {
                throw new Exception("La connexion à la base de données n'est pas établie.");
            }
            $result = $conn->query("SELECT 1");
            if ($result === false) {
                throw new Exception("Impossible d'exécuter une requête de test sur la base de données.");
            }
        } catch (Exception $e) {
            $this->logger->error("Erreur de connexion à la base de données", ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
