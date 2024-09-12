<?php
// Définir DEBUG_MODE si ce n'est pas déjà fait
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', true);
}

// Fonction de logging personnalisée
function db_log($message, $level = 'INFO') {
    $date = date('Y-m-d H:i:s');
    if (DEBUG_MODE) {
        error_log("[$date] [DB] [$level] $message");
    }
}

db_log("Début de l'initialisation de la base de données", 'DEBUG');

global $db;

$host = 'localhost';
$db_name = 'e_motion';
$user = 'emotion_user';
$pass = 'IPSSI2024';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

db_log("Paramètres de connexion : DSN = $dsn, User = $user", 'DEBUG');

db_log("Tentative de connexion à la base de données...", 'DEBUG');
try {
    $db = new PDO($dsn, $user, $pass, $options);
    db_log("Connexion à la base de données réussie", 'INFO');
    
    // Vérification supplémentaire de la connexion
    $db->query("SELECT 1");
    db_log("Connexion vérifiée avec succès via une requête test", 'INFO');
} catch (\PDOException $e) {
    db_log("Erreur de connexion PDO : " . $e->getMessage(), 'ERROR');
    db_log("Code d'erreur PDO : " . $e->getCode(), 'ERROR');
    db_log("Trace de l'erreur : " . $e->getTraceAsString(), 'DEBUG');
    $db = null;
}

function is_db_connected() {
    global $db;
    try {
        if ($db instanceof PDO) {
            $db->query("SELECT 1");
            db_log("Vérification de connexion réussie", 'DEBUG');
            return true;
        }
    } catch (PDOException $e) {
        db_log("Erreur lors de la vérification de la connexion : " . $e->getMessage(), 'ERROR');
    }
    db_log("La connexion à la base de données n'est pas valide", 'ERROR');
    return false;
}

// Vérification de l'existence de la classe Vehicule
if (!class_exists('Vehicule')) {
    db_log("La classe Vehicule n'existe pas. Assurez-vous qu'elle est bien chargée.", 'ERROR');
    db_log("Classes actuellement chargées : " . implode(', ', get_declared_classes()), 'DEBUG');
} else {
    db_log("La classe Vehicule existe", 'DEBUG');
}

// Initialisation de Vehicule::setDB() si nécessaire
if (class_exists('Vehicule') && is_db_connected()) {
    db_log("Tentative d'initialisation de Vehicule::setDB()", 'DEBUG');
    try {
        Vehicule::setDB($db);
        db_log("Vehicule::setDB() initialisé avec succès", 'INFO');
    } catch (Exception $e) {
        db_log("Erreur lors de l'initialisation de Vehicule::setDB() : " . $e->getMessage(), 'ERROR');
        db_log("Trace de l'erreur : " . $e->getTraceAsString(), 'DEBUG');
    }
} else {
    db_log("Impossible d'initialiser Vehicule::setDB(). Vérifiez la connexion à la base de données et l'existence de la classe Vehicule.", 'ERROR');
}

// Vérification finale de la connexion
if (is_db_connected()) {
    db_log("La connexion à la base de données est établie et fonctionnelle", 'INFO');
} else {
    db_log("La connexion à la base de données n'est pas établie ou n'est pas fonctionnelle", 'ERROR');
}

db_log("Fin de l'initialisation de la base de données", 'DEBUG');

// Fonction pour obtenir la connexion à la base de données
function getDB() {
    global $db;
    if (!$db instanceof PDO) {
        db_log("Tentative d'accès à la base de données alors que la connexion n'est pas établie", 'ERROR');
        throw new Exception("La connexion à la base de données n'est pas établie.");
    }
    return $db;
}
