<?php
session_start();
require_once 'helpers.php';

// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Charger la configuration de la base de données
if (file_exists('database.php')) {
    require_once 'database.php';
} else {
    die("Le fichier de configuration de la base de données est manquant.");
}

// Inclusion de l'autoloader de Composer
// Assurez-vous que le fichier vendor/autoload.php existe et que Composer a été exécuté
require_once __DIR__ . '/vendor/autoload.php';

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
    die("La classe {$class} n'a pas été trouvée.");
});

// Initialiser le logger (ou toute autre dépendance nécessaire)
// Vous devrez peut-être ajuster cette partie en fonction de votre configuration
$logger = new \Monolog\Logger('app');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('path/to/your.log', \Monolog\Logger::DEBUG));

// Routage simple
$route = $_GET['route'] ?? 'home';

// Gestion des routes
try {
    switch ($route) {
        case 'home':
            if (class_exists('HomeController')) {
                $controller = new HomeController();
                $controller->index();
            } else {
                throw new Exception("Le contrôleur Home n'existe pas.");
            }
            break;

        case 'login':
            if (class_exists('AuthController')) {
                $controller = new AuthController($logger); // Passer le logger au constructeur
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->login();
                } else {
                    $controller->login(); // Afficher le formulaire de connexion
                }
            } else {
                throw new Exception("Le contrôleur Auth n'existe pas.");
            }
            break;

        case 'register':
            if (class_exists('AuthController')) {
                $controller = new AuthController($logger); // Passer le logger au constructeur
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->register();
                } else {
                    $controller->register(); // Afficher le formulaire d'inscription
                }
            } else {
                throw new Exception("Le contrôleur Auth n'existe pas.");
            }
            break;

        case 'logout':
            if (class_exists('AuthController')) {
                $controller = new AuthController($logger); // Passer le logger au constructeur
                $controller->logout();
            } else {
                throw new Exception("Le contrôleur Auth n'existe pas.");
            }
            break;

        case 'vehicules':
            if (class_exists('VehiculeController')) {
                $controller = new VehiculeController();
                $action = $_GET['action'] ?? 'index';
                if ($action === 'show' && isset($_GET['id'])) {
                    $controller->show($_GET['id']);
                } else {
                    $controller->index();
                }
            } else {
                throw new Exception("Le contrôleur Vehicule n'existe pas.");
            }
            break;

        case 'rentals':
            if (!isLoggedIn()) {
                header('Location: index.php?route=login');
                exit;
            }
            if (class_exists('RentalController')) {
                $controller = new RentalController();
                $action = $_GET['action'] ?? 'index';
                if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->create($_POST);
                } else {
                    $controller->index();
                }
            } else {
                throw new Exception("Le contrôleur Rental n'existe pas.");
            }
            break;

        case 'admin':
            if (!isAdmin()) {
                header('Location: index.php?route=login');
                exit;
            }
            if (class_exists('AdminController')) {
                $controller = new AdminController();
                $action = $_GET['action'] ?? 'dashboard';
                switch ($action) {
                    case 'dashboard':
                        $controller->dashboard();
                        break;
                    case 'vehicules':
                        $controller->manageVehicules();
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
            } else {
                throw new Exception("Le contrôleur Admin n'existe pas.");
            }
            break;

        default:
            throw new Exception("Page non trouvée", 404);
    }
} catch (Exception $e) {
    // Gestion des erreurs
    if ($e->getCode() === 404) {
        header("HTTP/1.0 404 Not Found");
        if (file_exists('views/404.php')) {
            include 'views/404.php';
        } else {
            echo "Page non trouvée";
        }
    } else {
        // Log de l'erreur
        error_log($e->getMessage());
        echo "Une erreur est survenue : " . $e->getMessage();
    }
}
