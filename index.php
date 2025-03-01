<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/models/Vehicule.php';
Vehicule::setLogLevel('DEBUG');

// Définir DEBUG_MODE (à mettre à false en production)
define('DEBUG_MODE', true);

// Forcer l'affichage des erreurs pour le débogage (à désactiver en production)
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Configuration des logs d'erreur PHP
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php-errors.log');

// Fonction de logging personnalisée
function custom_log($message) {
    error_log(date('Y-m-d H:i:s') . ': ' . $message . "\n", 3, __DIR__ . '/logs/debug.log');
    if (DEBUG_MODE) {
        echo $message . "<br>";
    }
}

custom_log("Script started");

// Vérification des fichiers requis
$required_files = [
    __DIR__ . '/database.php', 
    __DIR__ . '/config.php', 
    __DIR__ . '/helpers.php', 
    __DIR__ . '/vendor/autoload.php', 
    __DIR__ . '/models/Vehicule.php'
];
foreach ($required_files as $file) {
    if (!file_exists($file)) {
        custom_log("Fichier requis manquant: $file");
        die("Fichier requis manquant: $file");
    }
    require_once $file;
    custom_log("Fichier chargé : $file");
}

// Vérification de la connexion à la base de données
try {
    $db = Database::getInstance()->getConnection();
    custom_log("La connexion à la base de données est établie.");
} catch (PDOException $e) {
    custom_log("Erreur : La connexion à la base de données n'est pas établie. " . $e->getMessage());
    die("Erreur : La connexion à la base de données n'est pas établie.");
}

// Vérification de la connexion pour Vehicule
if (class_exists('Vehicule') && Vehicule::isDbConnected()) {
    custom_log("La connexion à la base de données est établie pour Vehicule");
} else {
    custom_log("Impossible de vérifier la connexion à la base de données pour Vehicule. Classe existe: " . (class_exists('Vehicule') ? 'Oui' : 'Non'));
}

// Appliquer les configurations de session
configure_session();

// Démarrage de la session
session_start();

custom_log('Session ID: ' . session_id());
custom_log('Session Data: ' . json_encode($_SESSION));

// Vérification de l'installation de Monolog
if (!class_exists('\Monolog\Logger')) {
    custom_log("Monolog n'est pas installé.");
    die("Monolog n'est pas installé. Veuillez exécuter 'composer install'.");
}

// Configuration de Monolog
$logger = new \Monolog\Logger('app');
$logger->pushHandler(new \Monolog\Handler\StreamHandler(__DIR__ . '/logs/app.log', \Monolog\Logger::DEBUG));

// Autoloader personnalisé
spl_autoload_register(function($class) use ($logger) {
    $directories = ['models', 'controller'];
    foreach ($directories as $directory) {
        $file = __DIR__ . "/$directory/$class.php";
        if (file_exists($file)) {
            require_once $file;
            custom_log("Classe $class chargée depuis $file");
            return;
        }
    }
    $logger->error("La classe {$class} n'a pas été trouvée.");
    throw new Exception("La classe {$class} n'a pas été trouvée.");
});

// Détermine la route actuelle
$route = isset($_GET['route']) ? sanitize_input($_GET['route']) : 'home';
custom_log("Route actuelle : $route");

// Créer une instance de AuthController
$authController = new AuthController($logger);

// Définir les routes valides
$validRoutes = ['home', 'login', 'register', 'logout', 'admin', 'vehicules', 'rentals', 'mes-locations'];

// Gestion des erreurs
try {
    if (!in_array($route, $validRoutes)) {
        throw new Exception("Page non trouvée", 404);
    }

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
            $action = isset($_GET['action']) ? sanitize_input($_GET['action']) : 'index';
            custom_log("Action admin demandée : $action");
            if (method_exists($controller, $action)) {
                try {
                    $controller->$action();
                } catch (Exception $e) {
                    $logger->error("Erreur lors de l'exécution de l'action admin '$action': " . $e->getMessage());
                    custom_log("Erreur dans l'action admin '$action': " . $e->getMessage());
                    throw $e;
                }
            } else {
                $logger->warning("Action admin non trouvée : {$action}");
                custom_log("Action admin non trouvée : {$action}");
                throw new Exception("Action admin non trouvée : {$action}", 404);
            }
            break;
        case 'vehicules':
            custom_log("Vérification de la connexion à la base de données avant d'instancier VehiculeController: " . (Vehicule::isDbConnected() ? "Connecté" : "Non connecté"));
            $controller = new VehiculeController($logger);
            $action = isset($_GET['action']) ? sanitize_input($_GET['action']) : 'index';
            if (method_exists($controller, $action)) {
                if ($action === 'show' && isset($_GET['id'])) {
                    $controller->$action($_GET['id']);
                } else {
                    $controller->$action($_POST ?? null);
                }
            } else {
                $logger->warning("Action de véhicule non trouvée : {$action}");
                throw new Exception("Action de véhicule non trouvée", 404);
            }
            break;
        case 'rentals':
        case 'mes-locations':
            if (!$authController->isLoggedIn()) {
                $logger->warning("Tentative d'accès à la page de locations sans connexion.");
                header('Location: index.php?route=login');
                exit;
            }
            $logger->info("Accès à la page de locations autorisé.");
            $controller = new RentalController($logger);
            $action = $route === 'mes-locations' ? 'index' : (isset($_GET['action']) ? sanitize_input($_GET['action']) : 'index');
            if (method_exists($controller, $action)) {
                $controller->$action($_POST ?? null);
            } else {
                $logger->warning("Action de location non trouvée : {$action}");
                throw new Exception("Action de location non trouvée", 404);
            }
            break;
    }
} catch (Exception $e) {
    $logger->error($e->getMessage() . ' dans ' . $e->getFile() . ' à la ligne ' . $e->getLine());
    if ($e->getCode() === 404) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Page non trouvée</h1>";
        echo "<p>Désolé, la page que vous recherchez n'existe pas.</p>";
    } else {
        echo "<h1>Une erreur est survenue</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        if (DEBUG_MODE) {
            echo "<p>Fichier : " . $e->getFile() . " à la ligne " . $e->getLine() . "</p>";
        }
    }
}

custom_log("Script ended");
?>
