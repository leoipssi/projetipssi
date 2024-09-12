<?php
// Définir DEBUG_MODE si ce n'est pas déjà fait
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', true); // Mettre à true pour le débogage
}

// Fonction de logging personnalisée
function db_log($message, $level = 'INFO') {
    if (DEBUG_MODE) {
        error_log("[DB] [$level] $message");
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

db_log("Tentative de connexion à la base de données...", 'DEBUG');
try {
    $db = new PDO($dsn, $user, $pass, $options);
    db_log("Connexion à la base de données réussie", 'INFO');
} catch (\PDOException $e) {
    db_log("Erreur de connexion PDO : " . $e->getMessage(), 'ERROR');
    db_log("Code d'erreur PDO : " . $e->getCode(), 'ERROR');
    $db = null;
}

function is_db_connected() {
    global $db;
    try {
        if ($db instanceof PDO) {
            $db->query("SELECT 1");
            return true;
        }
    } catch (PDOException $e) {
        db_log("Erreur lors de la vérification de la connexion : " . $e->getMessage(), 'ERROR');
    }
    return false;
}

// Vérification de l'existence de la classe Vehicule
if (!class_exists('Vehicule')) {
    db_log("La classe Vehicule n'existe pas. Assurez-vous qu'elle est bien chargée.", 'ERROR');
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
    }
} else {
    db_log("Impossible d'initialiser Vehicule::setDB(). Vérifiez la connexion à la base de données et l'existence de la classe Vehicule.", 'ERROR');
}

db_log("Fin de l'initialisation de la base de données", 'DEBUG');
