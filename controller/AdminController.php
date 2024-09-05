<?php
class AdminController extends BaseController {
    public function __construct() {
        parent::__construct();
        if (!AuthController::isAdmin()) {
            $this->redirect('home');
        }
    }

    public function dashboard() {
        $totalVehicules = Vehicule::count();
        $totalUsers = User::count();
        $totalRentals = Rental::count();
        $totalRevenue = Rental::totalRevenue();
        $recentRentals = Rental::getRecent(5);
        $topVehicules = Vehicule::getTopRented(5);

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

        $this->render('admin/addVehicule', [
            'vehiculeTypes' => $vehiculeTypes,
            'errors' => $errors
        ]);
    }

    public function createOffer() {
        $vehiculeTypes = VehiculeType::getAll();
        $errors = [];

        if ($this->isPost()) {
            $offerData = $this->sanitizeUserData($_POST);
            $errors = $this->validateOfferInput($offerData);

            if (empty($errors)) {
                try {
                    $offer = Offer::create($offerData);
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

        $this->render('admin/createOffer', [
            'vehiculeTypes' => $vehiculeTypes,
            'errors' => $errors
        ]);
    }

    public function manageUsers() {
        $page = $this->getQueryParam('page', 1);
        $search = $this->getQueryParam('search', '');
        $role = $this->getQueryParam('role', '');
        $sortBy = $this->getQueryParam('sort', 'id');
        $sortOrder = $this->getQueryParam('order', 'ASC');

        $users = User::getFiltered($page, $search, $role, $sortBy, $sortOrder);
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
