<?php
// Inclure les dépendances nécessaires
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/models/Vehicule.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Rental.php';
require_once __DIR__ . '/models/VehiculeType.php';
require_once __DIR__ . '/models/RentalOffer.php';

class AdminController extends BaseController {
    public function __construct() {
        parent::__construct();
        // Vérifie si l'utilisateur est un administrateur
        if (!AuthController::isAdmin()) {
            $this->redirect('home');
        }
    }

    public function dashboard() {
        // Récupère les statistiques pour le tableau de bord
        $totalVehicules = Vehicule::count();
        $totalUsers = User::count();
        $totalRentals = Rental::count();
        $totalRevenue = Rental::totalRevenue();
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

    public function addVehicule() {
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

    public function manageUsers() {
        // Récupère les paramètres de la requête
        $page = $this->getQueryParam('page', 1);
        $search = $this->getQueryParam('search', '');
        $role = $this->getQueryParam('role', '');
        $sortBy = $this->getQueryParam('sort', 'id');
        $sortOrder = $this->getQueryParam('order', 'ASC');

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
    }

    private function validateVehiculeInput($data) {
        $errors = [];
        // Ajoutez ici la logique de validation pour les véhicules
        return $errors;
    }

    private function validateOfferInput($data) {
        $errors = [];
        // Ajoutez ici la logique de validation pour les offres
        return $errors;
    }
}
