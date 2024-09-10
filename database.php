<?php
$host = 'localhost'; // ou l'adresse de votre serveur de base de données
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
    // Ajout d'un message de succès
    error_log("Connexion à la base de données réussie");
} catch (\PDOException $e) {
    // Loggez l'erreur
    error_log("Erreur de connexion PDO : " . $e->getMessage());
    // Affichez l'erreur (à retirer en production)
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    // Ne définissez pas $db en cas d'erreur
    $db = null;
}
