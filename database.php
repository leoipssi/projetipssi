<?php
// Définir DEBUG_MODE si ce n'est pas déjà fait
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', false); // Mettez à true pour le débogage
}

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

// Fonction de logging personnalisée
function db_log($message, $level = 'INFO') {
    if (DEBUG_MODE) {
        error_log("[DB] [$level] $message");
    }
}

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

// Initialisation de Vehicule::setDB() si nécessaire
if (class_exists('Vehicule') && is_db_connected()) {
    Vehicule::setDB($db);
}
