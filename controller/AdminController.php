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
    public function __construct() {
        parent::__construct();
        // Vérifie si l'utilisateur est un administrateur
        if (!$this->isAdmin()) {
            $this->redirect('home');
        }
    }

    protected function isAdmin() {
        return AuthController::isAdmin();
    }

    public function dashboard() {
        // Récupère les statistiques pour le tableau de bord
        $totalVehicules = Vehicule::count();
        $totalUsers = User::count();
        $totalRentals = Rental::count();
        $totalRevenue = Rental::totalRevenue();
        
        // Ensure $totalRevenue is a number
        $totalRevenue = is_null($totalRevenue) ? 0 : $totalRevenue;
        
        $recentRentals = Rental::getRecent(5);
        $topVehicules = Vehicule::getTopRented(5);

        // Affiche la vue du tableau de bord
        $this->render('admin/dashboard', [
            'totalVehicules' => $totalVehicules,
            'totalUsers' => $totalUsers,
            'totalRentals' => $totalRentals,
            'totalRevenue' => $totalRevenue,
            'recentRentals' => $recentRentals,
            'topVehicules' => $topVehicules
        ]);
    }

    public function vehicules() {
        try {
            $vehicules = Vehicule::getAll();
            $this->render('vehicules/index', [
                'vehicules' => $vehicules
            ]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->render('error', [
                'message' => 'Une erreur est survenue lors de la récupération des véhicules.'
            ]);
        }
    }
    
    public function addVehicule() {
        // Check if VehiculeType class exists
        if (!class_exists('VehiculeType')) {
            throw new Exception('VehiculeType class not found. Please ensure it is properly defined and included.');
        }

        // Récupère les types de véhicules disponibles
        $vehiculeTypes = VehiculeType::getAll();
        $errors = [];

        if ($this->isPost()) {
            $vehiculeData = $this->sanitizeUserData($_POST);
            $errors = $this->validateVehiculeInput($vehiculeData);

            if (empty($errors)) {
                try {
                    $vehicule = Vehicule::create($vehiculeData);
                    if ($vehicule) {
                        $this->redirect('admin', ['action' => 'dashboard', 'success' => 'Véhicule ajouté avec succès']);
                    } else {
                        $errors[] = "Erreur lors de l'ajout du véhicule.";
                    }
                } catch (Exception $e) {
                    $errors[] = "Une erreur est survenue lors de l'ajout du véhicule : " . $e->getMessage();
                }
            }
        }

        // Affiche la vue d'ajout de véhicule
        $this->render('admin/addVehicule', [
            'vehiculeTypes' => $vehiculeTypes,
            'errors' => $errors
        ]);
    }

    public function createOffer() {
        // Check if VehiculeType class exists
        if (!class_exists('VehiculeType')) {
            throw new Exception('VehiculeType class not found. Please ensure it is properly defined and included.');
        }

        // Récupère les types de véhicules disponibles
        $vehiculeTypes = VehiculeType::getAll();
        $errors = [];

        if ($this->isPost()) {
            $offerData = $this->sanitizeUserData($_POST);
            $errors = $this->validateOfferInput($offerData);

            if (empty($errors)) {
                try {
                    $offer = RentalOffer::create($offerData);
                    if ($offer) {
                        $this->redirect('admin', ['action' => 'dashboard', 'success' => 'Offre créée avec succès']);
                    } else {
                        $errors[] = "Erreur lors de la création de l'offre.";
                    }
                } catch (Exception $e) {
                    $errors[] = "Une erreur est survenue lors de la création de l'offre : " . $e->getMessage();
                }
            }
        }

        // Affiche la vue de création d'offre
        $this->render('admin/createOffer', [
            'vehiculeTypes' => $vehiculeTypes,
            'errors' => $errors
        ]);
    }

    public function users() {
        // Redirige vers manageUsers
        $this->redirect('admin', ['action' => 'manageUsers']);
    }

    public function manageUsers() {
        // Récupère les paramètres de la requête
        $page = $this->getQueryParam('page', 1);
        $search = $this->getQueryParam('search', '');
        $role = $this->getQueryParam('role', '');
        $sortBy = $this->getQueryParam('sort', 'id');
        $sortOrder = $this->getQueryParam('order', 'ASC');

        try {
            // Récupère la liste des utilisateurs filtrée
            $users = User::getFiltered($page, $search, $role, $sortBy, $sortOrder);
            $totalPages = User::getTotalPages($search, $role);

            // Affiche la vue de gestion des utilisateurs
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
        } catch (Exception $e) {
            // Log l'erreur
            error_log('Erreur dans manageUsers: ' . $e->getMessage());
            // Affiche un message d'erreur à l'utilisateur
            $this->render('error', [
                'message' => 'Une erreur est survenue lors de la récupération des utilisateurs.'
            ]);
        }
    }

    private function validateVehiculeInput($data) {
        $errors = [];
        // Ajoutez ici la logique de validation pour les véhicules
        // Par exemple :
        if (empty($data['nom'])) {
            $errors[] = "Le nom du véhicule est requis.";
        }
        if (empty($data['type'])) {
            $errors[] = "Le type de véhicule est requis.";
        }
        // Ajoutez d'autres validations selon vos besoins
        return $errors;
    }

    private function validateOfferInput($data) {
        $errors = [];
        // Ajoutez ici la logique de validation pour les offres
        // Par exemple :
        if (empty($data['titre'])) {
            $errors[] = "Le titre de l'offre est requis.";
        }
        if (empty($data['prix'])) {
            $errors[] = "Le prix de l'offre est requis.";
        }
        // Ajoutez d'autres validations selon vos besoins
        return $errors;
    }
}
