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

try {
    $db = new PDO($dsn, $user, $pass, $options);
    error_log("Connexion à la base de données réussie");
} catch (\PDOException $e) {
    error_log("Erreur de connexion PDO : " . $e->getMessage());
    $db = null;
}

function is_db_connected() {
    global $db;
    return ($db instanceof PDO);
}
