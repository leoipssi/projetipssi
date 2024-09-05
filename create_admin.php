<?php
// Vérification de l'environnement d'exécution
$isCLI = (php_sapi_name() === 'cli');

if (!$isCLI) {
    if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
        die("Ce script ne peut être exécuté que localement.");
    }
}

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclure les fichiers nécessaires
require_once __DIR__ . 'database.php';  // Assurez-vous que ce chemin est correct
require_once __DIR__ . '/models/User.php';      // Assurez-vous que ce chemin est correct

echo "Fichiers inclus avec succès.\n";

// Le reste du code reste inchangé...

// Le code de création d'administrateur
$adminData = [
    'nom' => 'Nom_Admin',
    'prenom' => 'Prenom_Admin',
    'username' => 'admin_username',
    'password' => 'mot_de_passe_très_sécurisé',
    'email' => 'admin@example.com',
    'role' => 'Administrateur',
    'adresse' => 'Adresse de l\'administrateur',
    'code_postal' => '12345',
    'ville' => 'Ville_Admin',
    'telephone' => '0123456789'
];

echo "Données administrateur prêtes.\n";

try {
    echo "Recherche d'un utilisateur existant...\n";
    $existingUser = User::findByUsername($adminData['username']);
    if ($existingUser) {
        echo "Un utilisateur avec ce nom d'utilisateur existe déjà.\n";
    } else {
        echo "Création du nouveau compte administrateur...\n";
        $admin = User::create($adminData);
        if ($admin) {
            echo "Compte administrateur créé avec succès. ID: " . $admin->getId() . "\n";
        } else {
            echo "Erreur lors de la création du compte administrateur.\n";
        }
    }
} catch (Exception $e) {
    echo "Une erreur est survenue : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}

echo "Fin du script.\n";
