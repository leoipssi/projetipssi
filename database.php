<?php
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

error_log("Tentative de connexion à la base de données...");
error_log("DSN: $dsn");
error_log("Utilisateur: $user");

try {
    $db = new PDO($dsn, $user, $pass, $options);
    error_log("Connexion à la base de données réussie");
} catch (\PDOException $e) {
    error_log("Erreur de connexion PDO : " . $e->getMessage());
    error_log("Code d'erreur PDO : " . $e->getCode());
    error_log("Trace : " . $e->getTraceAsString());
    // Ne pas utiliser die() ici, laissez le script continuer
    $db = null; // Assurez-vous que $db est null en cas d'échec
}

function is_db_connected() {
    global $db;
    try {
        if ($db instanceof PDO) {
            $db->query("SELECT 1");
            return true;
        }
    } catch (PDOException $e) {
        error_log("Erreur lors de la vérification de la connexion : " . $e->getMessage());
    }
    return false;
}
