<?php
// Forcer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fonction de logging personnalisée
function custom_log($message) {
    error_log(date('Y-m-d H:i:s') . ': ' . $message . "\n", 3, 'debug.log');
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

// Démarrage de la session
session_start();

// Logging des informations de session
custom_log('Session ID: ' . session_id());
custom_log('Session Data: ' . json_encode($_SESSION));

// Vérification des fichiers requis
$required_files = ['config.php', 'helpers.php', 'vendor/autoload.php', 'database.php'];
foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die("Fichier requis manquant: $file");
    }
    require_once $file;
}

// Vérification de l'installation de Monolog
if (!class_exists('\Monolog\Logger')) {
    die("Monolog n'est pas installé. Veuillez exécuter 'composer install'.");
}

// Configuration de Monolog
$logger = new \Monolog\Logger('app');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('logs/app.log', \Monolog\Logger::DEBUG));

// Vérification de la connexion à la base de données
if (!isset($db)) {
    $logger->error("La variable de connexion à la base de données n'est pas définie.");
    die("Erreur de connexion à la base de données : La variable de connexion n'est pas définie.");
}

try {
    $db->query("SELECT 1");
    $logger->info("Connexion à la base de données vérifiée avec succès.");
} catch (PDOException $e) {
    $logger->error("Erreur lors de la vérification de la connexion à la base de données : " . $e->getMessage());
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Autoloader personnalisé
spl_autoload_register(function($class) use ($logger) {
    $directories = ['models', 'controllers'];
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
$route = $_GET['route'] ?? 'home';

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
            $action = $_GET['action'] ?? 'index';
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
            $action = $_GET['action'] ?? 'index';
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
