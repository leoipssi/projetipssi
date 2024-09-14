<?php
require_once __DIR__ . '/../helpers.php';

// Initialiser les variables de session
$isLoggedIn = isLoggedIn();
$currentUser = getCurrentUser();
$isAdmin = $currentUser && $currentUser->isAdmin();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'e-Motion - Véhicules Électriques' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .jumbotron {
            background-color: #f8f9fa;
            padding: 4rem 2rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>">e-Motion</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=vehicules">Véhicules</a>
                    </li>
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=mes-locations">Mes locations</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=admin">Administration</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=logout">Déconnexion</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=login">Connexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <?= $content ?>
    </main>

    <footer class="mt-5 py-3 bg-light">
        <div class="container text-center">
            <p>&copy; <?= date('Y') ?> e-Motion. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if (defined('DEBUG_MODE') && DEBUG_MODE): ?>
    <div class="container mt-5">
        <h3>Informations de débogage</h3>
        <pre>
        <?php
        echo "isLoggedIn: " . var_export($isLoggedIn, true) . "\n";
        echo "isAdmin: " . var_export($isAdmin, true) . "\n";
        echo "Session: " . var_export($_SESSION, true) . "\n";
        ?>
        </pre>
    </div>
    <?php endif; ?>
</body>
</html>
