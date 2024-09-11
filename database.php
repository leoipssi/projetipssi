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
    die("Erreur : La connexion à la base de données n'a pas pu être établie. Détails : " . $e->getMessage());
}

function is_db_connected() {
    global $db;
    return ($db instanceof PDO);
}
