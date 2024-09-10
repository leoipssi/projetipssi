<?php
$host = 'localhost';
$db   = 'e_motion';
$user = 'emotion_user';
$pass = 'IPSSI2024';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $db = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Ici, nous allons afficher l'erreur au lieu de la relancer
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}
