<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'e_motion');
define('DB_USER', 'emotion_user');
define('DB_PASS', 'IPSSI2024');
define('BASE_URL', 'https://extranet.emotionipssi.com');

// Ajoutez cette ligne pour inclure le fichier Vehicule.php
require_once __DIR__ . '/models/Vehicule.php';

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Maintenant, vous pouvez appeler setDB
    Vehicule::setDB($db);
    
    error_log("Connexion à la base de données réussie");
} catch(PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    die("Erreur de connexion à la base de données. Veuillez contacter l'administrateur.");
}
