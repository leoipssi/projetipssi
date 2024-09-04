<?php
session_start();
ini_set('session.gc_maxlifetime', 3600); // Durée de vie de la session à 1 heure
ini_set('session.cookie_lifetime', 3600); // Durée de vie du cookie de session
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);

// Fonction de logging personnalisée
function custom_log($message) {
    error_log(date('Y-m-d H:i:s') . ': ' . $message);
}

// Logging des informations de session
custom_log('Session ID: ' . session_id());
custom_log('Session Data: ' . json_encode($_SESSION));

// Test de persistance de session
if (!isset($_SESSION['init_time'])) {
    $_SESSION['init_time'] = time();
    custom_log('Nouvelle session initialisée');
} else {
    custom_log('Session existante depuis: ' . date('Y-m-d H:i:s', $_SESSION['init_time']));
}

// Vérification du jeton CSRF
if (isset($_SESSION['csrf_token'])) {
    custom_log('CSRF Token in session: ' . $_SESSION['csrf_token']);
} else {
    custom_log('CSRF Token not set in session');
}

require_once 'config.php';
require_once 'helpers.php';
require_once 'vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (file_exists('database.php')) {
    require_once 'database.php';
} else {
    die("Le fichier de configuration de la base de données est manquant.");
}

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

$logger = new \Monolog\Logger('app');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('logs/app.log', \Monolog\Logger::DEBUG));

$route = $_GET['route'] ?? 'home';

try {
    switch ($route) {
        case 'home':
            $controller = new HomeController();
            $controller->index();
            break;
        case 'login':
        case 'register':
        case 'logout':
            $controller = new AuthController($logger);
            $controller->$route();
            break;
        case 'vehicules':
            $controller = new VehiculeController();
            $action = $_GET['action'] ?? 'index';
            $controller->$action($_GET['id'] ?? null);
            break;
        case 'rentals':
            if (!isLoggedIn()) {
                header('Location: index.php?route=login');
                exit;
            }
            $controller = new RentalController();
            $action = $_GET['action'] ?? 'index';
            $controller->$action($_POST ?? null);
            break;
        case 'admin':
            if (!isAdmin()) {
                header('Location: index.php?route=login');
                exit;
            }
            $controller = new AdminController();
            $action = $_GET['action'] ?? 'dashboard';
            $controller->$action();
            break;
        default:
            throw new Exception("Page non trouvée", 404);
    }
} catch (Exception $e) {
    $logger->error($e->getMessage());
    if ($e->getCode() === 404) {
        header("HTTP/1.0 404 Not Found");
        include 'views/404.php';
    } else {
        include 'views/error.php';
    }
}
