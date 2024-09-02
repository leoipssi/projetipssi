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
        $controller = new HomeController();
        $controller->index();
        break;

    case 'login':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login($_POST['username'], $_POST['password']);
        } else {
            $controller->showLoginForm();
        }
        break;

    case 'register':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register($_POST);
        } else {
            $controller->showRegisterForm();
        }
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'vehicles':
        $controller = new VehicleController();
        $controller->index();
        break;

    case 'rentals':
        $controller = new RentalController();
        if (isLoggedIn()) {
            $controller->index();
        } else {
            header('Location: index.php?route=login');
            exit;
        }
        break;

    case 'admin':
        if (!isAdmin()) {
            header('Location: index.php?route=login');
            exit;
        }
        $controller = new AdminController();
        $subRoute = $_GET['sub_route'] ?? 'dashboard';
        
        switch ($subRoute) {
            case 'dashboard':
                $controller->dashboard();
                break;
            case 'vehicles':
                $controller->manageVehicles();
                break;
            case 'clients':
                $controller->manageClients();
                break;
            case 'offers':
                $controller->manageOffers();
                break;
            default:
                $controller->dashboard();
        }
        break;

    default:
        include 'views/404.php';
        break;
}
