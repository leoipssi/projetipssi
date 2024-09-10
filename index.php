<?php
// Configurations de session avant le démarrage de la session
ini_set('session.gc_maxlifetime', 3600)
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.cookie_lifetime', 3600);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Uniquement si HTTPS est utilisé
// Démarrage de la session
session_start();

// Fonction de logging personnalisée
function custom_log($message) {
    error_log(date('Y-m-d H:i:s') . ': ' . $message);
}

// Logging des informations de session
custom_log('Session ID: ' . session_id());
custom_log('Session Data: ' . json_encode($_SESSION));

require_once 'config.php';
require_once 'helpers.php';
require_once 'vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if (file_exists('database.php')) {
    require_once 'database.php';
} else {
    die("Le fichier de configuration de la base de données est manquant.");
}

spl_autoload_register(function($class) {
    $directories = ['models', 'controllers'];
    foreach ($directories as $directory) {
@@ -37,10 +43,16 @@ function custom_log($message) {
    }
    throw new Exception("La classe {$class} n'a pas été trouvée.");
});

$logger = new \Monolog\Logger('app');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('logs/app.log', \Monolog\Logger::DEBUG));

// Détermine la route actuelle
$route = $_GET['route'] ?? 'home';

// Créer une instance de AuthController
$authController = new AuthController($logger);

// Gestion des erreurs
try {
    switch ($route) {
@@ -51,12 +63,11 @@ function custom_log($message) {
        case 'login':
        case 'register':
        case 'logout':
            $controller = new AuthController($logger);
            $controller->$route();
            $authController->$route();
            break;
        case 'admin':
            // Vérifie les autorisations d'accès
            if (!AuthController::isAdmin()) {
            if (!$authController->isAdmin()) {
                $logger->warning("Accès refusé à la page d'administration pour un utilisateur non administrateur.");
                header('Location: index.php?route=home');
                exit;
@@ -83,7 +94,7 @@ function custom_log($message) {
            break;
        case 'rentals':
            // Vérifie si l'utilisateur est connecté
            if (!AuthController::checkLoggedIn()) {
            if (!$authController->isLoggedIn()) {
                $logger->warning("Tentative d'accès à la page de locations sans connexion.");
                header('Location: index.php?route=login');
                exit;
            }
            $logger->info("Accès à la page de locations autorisé.");
            $controller = new RentalController();
            $action = $_GET['action'] ?? 'index';
            if (method_exists($controller, $action)) {
                $controller->$action($_POST ?? null);
            } else {
                $logger->warning("Action de location non trouvée : {$action}");
                throw new Exception("Action de location non trouvée", 404);
            }
            break;
        default:
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
