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

echo "Début du script.\n";

// Vérification et inclusion du fichier database.php
$databaseFile = __DIR__ . '/database.php';
if (file_exists($databaseFile)) {
    echo "Inclusion du fichier database.php...\n";
    require_once $databaseFile;
    echo "Fichier database.php inclus avec succès.\n";
} else {
    die("Erreur : Le fichier database.php n'existe pas dans le répertoire " . __DIR__ . "\n");
}

// Vérification et inclusion du fichier User.php
$userFile = __DIR__ . '/models/User.php';
if (file_exists($userFile)) {
    echo "Inclusion du fichier User.php...\n";
    require_once $userFile;
    echo "Fichier User.php inclus avec succès.\n";
} else {
    die("Erreur : Le fichier User.php n'existe pas dans le répertoire " . __DIR__ . "/models/\n");
}

// Vérification de la connexion à la base de données
if (!isset($conn) || !($conn instanceof PDO)) {
    die("Erreur : La connexion à la base de données n'est pas établie correctement.\n");
}

// Vérification de l'existence de la classe User
if (!class_exists('User')) {
    die("Erreur : La classe User n'a pas été chargée correctement.\n");
}

echo "Toutes les vérifications préliminaires ont réussi.\n";

// Données pour le compte administrateur
$adminData = [
    'nom' => 'Nom_Admin',
    'prenom' => 'Prenom_Admin',
    'username' => 'admin_username',
    'password' => 'mot_de_passe_très_sécurisé',  // Assurez-vous d'utiliser un mot de passe fort
    'email' => 'admin@example.com',
    'role' => 'Administrateur',
    'adresse' => 'Adresse de l\'administrateur',
    'code_postal' => '12345',
    'ville' => 'Ville_Admin',
    'telephone' => '0123456789'
];

echo "Données administrateur préparées.\n";

try {
    echo "Recherche d'un utilisateur existant...\n";
    $existingUser = User::findByUsername($adminData['username']);
    if ($existingUser) {
        echo "Attention : Un utilisateur avec ce nom d'utilisateur existe déjà.\n";
    } else {
        echo "Création du nouveau compte administrateur...\n";
        $admin = User::create($adminData);
        if ($admin) {
            echo "Succès : Compte administrateur créé avec succès. ID: " . $admin->getId() . "\n";
        } else {
            echo "Erreur : Échec de la création du compte administrateur.\n";
        }
    }
} catch (Exception $e) {
    echo "Erreur critique : " . $e->getMessage() . "\n";
    echo "Trace de l'erreur : " . $e->getTraceAsString() . "\n";
}

echo "Fin du script.\n";

// Décommentez la ligne suivante pour supprimer le script après son exécution
// unlink(__FILE__);
