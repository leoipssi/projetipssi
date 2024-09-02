<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Motion - Location de véhicules électriques</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="index.php?route=vehicles">Véhicules</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="index.php?route=rentals">Mes locations</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="index.php?route=admin">Administration</a></li>
                    <?php endif; ?>
                    <li><a href="index.php?route=logout">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="index.php?route=login">Connexion</a></li>
                    <li><a href="index.php?route=register">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <?php echo $content; ?>
    </main>

    <footer>
        <p>&copy; 2024 e-Motion. Tous droits réservés.</p>
    </footer>

    <script src="public/js/script.js"></script>
</body>
</html>
