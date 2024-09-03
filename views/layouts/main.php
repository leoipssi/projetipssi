<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'e-Motion - Location de véhicules électriques' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="<?= BASE_URL ?>/">Accueil</a></li>
                <li><a href="<?= BASE_URL ?>/index.php?route=vehicules">Véhicules</a></li>
                <?php if ($this->isLoggedIn()): ?>
                    <li><a href="<?= BASE_URL ?>/index.php?route=rentals">Mes locations</a></li>
                    <?php if ($this->getCurrentUser()->isAdmin()): ?>
                        <li><a href="<?= BASE_URL ?>/index.php?route=admin">Administration</a></li>
                    <?php endif; ?>
                    <li><a href="<?= BASE_URL ?>/index.php?route=logout">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>/index.php?route=login">Connexion</a></li>
                    <li><a href="<?= BASE_URL ?>/index.php?route=register">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <?= $content ?>
    </main>
    <footer>
        <p>&copy; <?= date('Y') ?> e-Motion. Tous droits réservés.</p>
    </footer>
    <script src="<?= BASE_URL ?>/public/js/script.js"></script>
</body>
</html>
