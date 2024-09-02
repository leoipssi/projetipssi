<?php
session_start();

// Charger la configuration de la base de données
require_once 'config/database.php';

// Fonction d'autoloading pour charger automatiquement les classes
spl_autoload_register(function($class) {
    $directories = ['models', 'controllers'];
    foreach ($directories as $directory) {
        $file = __DIR__ . "/{$directory}/{$class}.php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fonction pour vérifier si l'utilisateur est un administrateur
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Administrateur';
}

// Routage simple
$route = $_GET['route'] ?? 'home';

// Gestion des routes
switch ($route) {
    case 'home':
        include 'views/home.php';
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Logique de connexion
            $username = $_POST['username'];
            $password = $_POST['password'];
            // Vérifier les identifiants et définir la session si correct
        }
        include 'views/login.php';
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Logique d'inscription
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            // Créer un nouvel utilisateur
        }
        include 'views/register.php';
        break;

    case 'logout':
        // Logique de déconnexion
        session_destroy();
        header('Location: index.php');
        exit;

    case 'admin':
        if (!isAdmin()) {
            header('Location: index.php?route=login');
            exit;
        }
        $adminController = new AdminController();
        $subRoute = $_GET['sub_route'] ?? 'dashboard';
        
        switch ($subRoute) {
            case 'dashboard':
                $adminController->dashboard();
                break;
            case 'vehicles':
                $adminController->manageVehicles();
                break;
            case 'clients':
                $adminController->manageClients();
                break;
            case 'offers':
                $adminController->manageOffers();
                break;
            default:
                include 'views/admin/dashboard.php';
        }
        break;

    case 'client':
        if (!isLoggedIn()) {
            header('Location: index.php?route=login');
            exit;
        }
        $clientController = new ClientController();
        $subRoute = $_GET['sub_route'] ?? 'offers';
        
        switch ($subRoute) {
            case 'offers':
                $clientController->displayAvailableOffers();
                break;
            case 'rentals':
                $clientController->displayClientRentals();
                break;
            case 'subscribe':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $offerId = $_POST['offer_id'];
                    $clientController->subscribeToRental($_SESSION['user_id'], $offerId);
                }
                break;
            default:
                include 'views/client/offers.php';
        }
        break;

    default:
        include 'views/404.php';
        break;
}
