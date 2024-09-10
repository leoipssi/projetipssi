<?php
// Forcer l'affichage des erreurs pour le débogage (à désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fonction de logging personnalisée
function custom_log($message) {
    error_log(date('Y-m-d H:i:s') . ': ' . $message . "\n", 3, 'debug.log');
}

// Fonction de nettoyage des entrées
function sanitize_input($input) {
    return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
}

// Logging du démarrage du script
custom_log("Script started");

// Configurations de session avant le démarrage de la session
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.cookie_lifetime', 3600);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Uniquement si HTTPS est utilisé
ini_set('session.cookie_samesite', 'Lax');

// Démarrage de la session
session_start();

// Logging des informations de session
custom_log('Session ID: ' . session_id());
custom_log('Session Data: ' . json_encode($_SESSION));

// Vérification des fichiers requis
$required_files = ['config.php', 'helpers.php', 'vendor/autoload.php', 'database.php'];
foreach ($required_files as $file) {
    if (!file_exists($file)) {
        custom_log("Fichier requis manquant: $file");
        die("Fichier requis manquant: $file");
    }
    require_once $file;
}

// Vérification de l'installation de Monolog
if (!class_exists('\Monolog\Logger')) {
    custom_log("Monolog n'est pas installé.");
    die("Monolog n'est pas installé. Veuillez exécuter 'composer install'.");
}

// Configuration de Monolog
$logger = new \Monolog\Logger('app');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('logs/app.log', \Monolog\Logger::DEBUG));

// Vérification de la connexion à la base de données
if (!function_exists('is_db_connected')) {
    $logger->error("La fonction is_db_connected n'est pas définie. Vérifiez le fichier database.php.");
    die("Erreur : La fonction de vérification de la base de données n'est pas définie.");
}

if (!is_db_connected()) {
    $logger->error("La connexion à la base de données n'est pas établie.");
    die("Erreur : La connexion à la base de données n'est pas établie. Vérifiez le fichier database.php et les logs pour plus de détails.");
}

$logger->info("Connexion à la base de données vérifiée avec succès.");

// Autoloader personnalisé
spl_autoload_register(function($class) use ($logger) {
    $directories = ['models', 'controlleur'];
    foreach ($directories as $directory) {
        $file = $directory . '/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    $logger->error("La classe {$class} n'a pas été trouvée.");
    throw new Exception("La classe {$class} n'a pas été trouvée.");
});

// Détermine la route actuelle
$route = isset($_GET['route']) ? sanitize_input($_GET['route']) : 'home';

// Créer une instance de AuthController
$authController = new AuthController($logger);

// Gestion des erreurs
try {
    switch ($route) {
        case 'home':
            $controller = new HomeController($logger);
            $controller->index();
            break;
        case 'login':
        case 'register':
        case 'logout':
            $authController->$route();
            break;
        case 'admin':
            if (!$authController->isAdmin()) {
                $logger->warning("Accès refusé à la page d'administration pour un utilisateur non administrateur.");
                header('Location: index.php?route=home');
                exit;
            }
            $controller = new AdminController($logger);
            $controller->index();
            break;
        case 'vehicles':
            $controller = new VehicleController($logger);
            $action = isset($_GET['action']) ? sanitize_input($_GET['action']) : 'index';
            if (method_exists($controller, $action)) {
                $controller->$action($_POST ?? null);
            } else {
                $logger->warning("Action de véhicule non trouvée : {$action}");
                throw new Exception("Action de véhicule non trouvée", 404);
            }
            break;
        case 'rentals':
            if (!$authController->isLoggedIn()) {
                $logger->warning("Tentative d'accès à la page de locations sans connexion.");
                header('Location: index.php?route=login');
                exit;
            }
            $logger->info("Accès à la page de locations autorisé.");
            $controller = new RentalController($logger);
            $action = isset($_GET['action']) ? sanitize_input($_GET['action']) : 'index';
            if (method_exists($controller, $action)) {
                $controller->$action($_POST ?? null);
            } else {
                $logger->warning("Action de location non trouvée : {$action}");
                throw new Exception("Action de location non trouvée", 404);
            }
            break;
        default:
            $logger->warning("Route non trouvée : {$route}");
            throw new Exception("Page non trouvée", 404);
    }
} catch (Exception $e) {
    $logger->error($e->getMessage());
    if ($e->getCode() === 404) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Page non trouvée</h1>";
    } else {
        echo "<h1>Une erreur est survenue</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

custom_log("Script ended");
