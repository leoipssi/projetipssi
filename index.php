<?php
echo "Début du script<br>";

// Forcer l'affichage des erreurs pour le débogage (à désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration des logs d'erreur PHP
ini_set('log_errors', 1);
ini_set('error_log', '/chemin/vers/php-errors.log'); // Assurez-vous que ce chemin est accessible en écriture

// Fonction de logging personnalisée
function custom_log($message) {
    error_log(date('Y-m-d H:i:s') . ': ' . $message . "\n", 3, 'debug.log');
    echo $message . "<br>"; // Affiche également le message dans le navigateur
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

echo "Vérification des fichiers requis<br>";
// Vérification des fichiers requis
$required_files = ['database.php', 'config.php', 'helpers.php', 'vendor/autoload.php'];
foreach ($required_files as $file) {
    if (!file_exists($file)) {
        custom_log("Fichier requis manquant: $file");
        die("Fichier requis manquant: $file");
    }
    require_once $file;
    echo "Fichier chargé : $file<br>";
}

echo "Vérification de l'installation de Monolog<br>";
// Vérification de l'installation de Monolog
if (!class_exists('\Monolog\Logger')) {
    custom_log("Monolog n'est pas installé.");
    die("Monolog n'est pas installé. Veuillez exécuter 'composer install'.");
}

echo "Configuration de Monolog<br>";
// Configuration de Monolog
$logger = new \Monolog\Logger('app');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('logs/app.log', \Monolog\Logger::DEBUG));

echo "Vérification de la connexion à la base de données<br>";
// Vérification de la connexion à la base de données
if (!function_exists('is_db_connected')) {
    custom_log("La fonction is_db_connected() n'est pas définie.");
    die("Erreur : La fonction is_db_connected() n'est pas définie.");
}

if (!is_db_connected()) {
    $logger->error("La connexion à la base de données n'est pas établie.");
    die("Erreur : La connexion à la base de données n'est pas établie.");
}

echo "Configuration de l'autoloader<br>";
// Autoloader personnalisé
spl_autoload_register(function($class) use ($logger) {
    echo "Tentative de chargement de la classe : $class<br>";
    $directories = ['models', 'controller'];
    foreach ($directories as $directory) {
        $file = $directory . '/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            echo "Classe $class chargée depuis $file<br>";
            return;
        }
    }
    $logger->error("La classe {$class} n'a pas été trouvée.");
    throw new Exception("La classe {$class} n'a pas été trouvée.");
});

// Détermine la route actuelle
$route = isset($_GET['route']) ? sanitize_input($_GET['route']) : 'home';
echo "Route actuelle : $route<br>";

// Créer une instance de AuthController
echo "Création de l'instance AuthController<br>";
$authController = new AuthController($logger);

// Gestion des erreurs
try {
    switch ($route) {
        case 'home':
            echo "Exécution de la route 'home'<br>";
            $controller = new HomeController($logger);
            $controller->index();
            break;
        case 'login':
        case 'register':
        case 'logout':
            echo "Exécution de la route '$route'<br>";
            $authController->$route();
            break;
        case 'admin':
            echo "Vérification des droits d'admin<br>";
            if (!$authController->isAdmin()) {
                $logger->warning("Accès refusé à la page d'administration pour un utilisateur non administrateur.");
                header('Location: index.php?route=home');
                exit;
            }
            $controller = new AdminController($logger);
            $controller->index();
            break;
        case 'vehicules':
    echo "Exécution de la route 'vehicules'<br>";
    $controller = new VehiculeController($logger);
    $action = isset($_GET['action']) ? sanitize_input($_GET['action']) : 'index';
    echo "Action de véhicule : $action<br>";
    if (method_exists($controller, $action)) {
        $controller->$action($_POST ?? null);
    } else {
        $logger->warning("Action de véhicule non trouvée : {$action}");
        throw new Exception("Action de véhicule non trouvée", 404);
    }
    break;
        case 'rentals':
            echo "Exécution de la route 'rentals'<br>";
            if (!$authController->isLoggedIn()) {
                $logger->warning("Tentative d'accès à la page de locations sans connexion.");
                header('Location: index.php?route=login');
                exit;
            }
            $logger->info("Accès à la page de locations autorisé.");
            $controller = new RentalController($logger);
            $action = isset($_GET['action']) ? sanitize_input($_GET['action']) : 'index';
            echo "Action de location : $action<br>";
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
    $logger->error($e->getMessage() . ' dans ' . $e->getFile() . ' à la ligne ' . $e->getLine());
    if ($e->getCode() === 404) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Page non trouvée</h1>";
    } else {
        echo "<h1>Une erreur est survenue</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>Fichier : " . $e->getFile() . " à la ligne " . $e->getLine() . "</p>";
    }
}

custom_log("Script ended");
echo "Fin du script<br>";
?>
