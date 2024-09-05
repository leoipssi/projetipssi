<?php
// Vérification de l'environnement d'exécution
$isCLI = (php_sapi_name() === 'cli');

if (!$isCLI) {
    // Si exécuté via le web, vérifiez l'adresse IP
    if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
        die("Ce script ne peut être exécuté que localement.");
    }
}
// Inclure les fichiers nécessaires
require_once 'database.php';  // Ajustez le chemin selon votre structure
require_once 'models/User.php';      // Assurez-vous que le chemin est correct

// Le code de création d'administrateur ici
$adminData = [
    'nom' => 'Nom_Admin',
    'prenom' => 'Prenom_Admin',
    'username' => 'administrateur',
    'password' => 'Ipssi2024',  // Utilisez un mot de passe fort
    'email' => 'admin@example.com',
    'role' => 'Administrateur',
    'adresse' => 'Adresse de l\'administrateur',
    'code_postal' => '12345',
    'ville' => 'Ville_Admin',
    'telephone' => '0123456789'
];

try {
    $existingUser = User::findByUsername($adminData['username']);
    if ($existingUser) {
        echo "Un utilisateur avec ce nom d'utilisateur existe déjà.";
    } else {
        $admin = User::create($adminData);
        if ($admin) {
            echo "Compte administrateur créé avec succès. ID: " . $admin->getId();
        } else {
            echo "Erreur lors de la création du compte administrateur.";
        }
    }
} catch (Exception $e) {
    echo "Une erreur est survenue : " . $e->getMessage();
}

// Décommentez la ligne suivante après avoir exécuté le script avec succès
// unlink(__FILE__);
?>
