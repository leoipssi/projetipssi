<?php
$servername = "localhost";
$username = "emotion_user";
$password = "IPSSI2024";
$database = "e_motion";

try {
    $db = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie à la base de données.";
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
