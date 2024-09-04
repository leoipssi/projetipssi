<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'e-Motion - Location de véhicules électriques' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="<?= BASE_URL ?>/">e-Motion</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=vehicules">Véhicules</a>
                        </li>
                        <?php if (isset($this) && method_exists($this, 'isLoggedIn') && $this->isLoggedIn()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=rentals">Mes locations</a>
                            </li>
                            <?php if (isset($this) && method_exists($this, 'getCurrentUser') && $this->getCurrentUser()->isAdmin()): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=admin">Administration</a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    <ul class="navbar-nav">
                        <?php if (isset($this) && method_exists($this, 'isLoggedIn') && $this->isLoggedIn()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=logout">Déconnexion</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=login">Connexion</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=register">Inscription</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="flex-shrink-0">
        <div class="container my-4">
            <?= $content ?>
        </div>
    </main>
    <footer class="footer mt-auto py-3 bg-light">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> e-Motion. Tous droits réservés.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/js/script.js"></script>
</body>
</html>
