<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Motion - Véhicules Électriques</title>
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
            <a class="navbar-brand" href="#">e-Motion</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Accueil</a>
                    </li>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Véhicules</a>
                        <a class="nav-link" href="<?= BASE_URL ?>">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Mes locations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Administration</a>
                        <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=vehicules">Véhicules</a>
                    </li>
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=mes-locations">Mes locations</a>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']->isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=admin">Administration</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=logout">Déconnexion</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/index.php?route=login">Connexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
<div class="jumbotron text-center bg-light py-5 mb-5">
    <h1 class="display-4">Bienvenue sur e-Motion</h1>
    <p class="lead">Découvrez notre sélection de véhicules électriques pour une mobilité durable et économique.</p>
    <a href="<?= BASE_URL ?>/index.php?route=vehicules" class="btn btn-primary btn-lg">Voir nos véhicules</a>
</div>
<section class="mb-5">
    <h2 class="text-center mb-4">Nos derniers véhicules</h2>
    <div class="row">
        <?php foreach ($recentVehicules as $vehicule): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?= BASE_URL ?>/public/images/vehicules/<?= $vehicule->getId() ?>.jpg" class="card-img-top" alt="<?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h5>
                        <p class="card-text">Type: <?= htmlspecialchars($vehicule->getType()) ?></p>
                        <a href="index.php?route=vehicules&action=show&id=<?= $vehicule->getId() ?>" class="btn btn-primary">Voir détails</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="jumbotron text-center">
            <h1 class="display-4">Bienvenue sur e-Motion</h1>
            <p class="lead">Découvrez notre sélection de véhicules électriques pour une mobilité durable et économique.</p>
            <a href="<?= BASE_URL ?>/index.php?route=vehicules" class="btn btn-primary btn-lg">Voir nos véhicules</a>
        </div>
        <section class="mt-5">
            <h2 class="text-center mb-4">Nos derniers véhicules</h2>
            <div class="row">
                <?php foreach ($recentVehicules as $vehicule): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?= BASE_URL ?>/public/images/vehicules/<?= $vehicule->getId() ?>.jpg" class="card-img-top" alt="<?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h5>
                                <p class="card-text">Type: <?= htmlspecialchars($vehicule->getType()) ?></p>
                                <a href="index.php?route=vehicules&action=show&id=<?= $vehicule->getId() ?>" class="btn btn-primary">Voir détails</a>
                                <a href="<?= BASE_URL ?>/index.php?route=vehicules&action=show&id=<?= $vehicule->getId() ?>" class="btn btn-primary">Voir détails</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="mt-5">
            <h2 class="text-center mb-4">Offres spéciales</h2>
            <div class="row">
                <?php foreach ($activeOffers as $offer): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Offre sur <?= htmlspecialchars($offer->getVehicule()->getMarque() . ' ' . $offer->getVehicule()->getModele()) ?></h5>
                                <p class="card-text">Prix: <?= htmlspecialchars($offer->getPrix()) ?> €</p>
                                <p class="card-text">Durée: <?= htmlspecialchars($offer->getDuree()) ?> jours</p>
                                <a href="<?= BASE_URL ?>/index.php?route=rentals&action=create&offer_id=<?= $offer->getId() ?>" class="btn btn-success">Réserver</a>
                            </div>
                        </div>
        <?php endforeach; ?>
    </div>
</section>
<section>
    <h2 class="text-center mb-4">Offres spéciales</h2>
    <div class="row">
        <?php foreach ($activeOffers as $offer): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Offre sur <?= htmlspecialchars($offer->getVehicule()->getMarque() . ' ' . $offer->getVehicule()->getModele()) ?></h5>
                        <p class="card-text">Prix: <?= htmlspecialchars($offer->getPrix()) ?> €</p>
                        <p class="card-text">Durée: <?= htmlspecialchars($offer->getDuree()) ?> jours</p>
                        <a href="index.php?route=rentals&action=create&offer_id=<?= $offer->getId() ?>" class="btn btn-success">Réserver</a>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
</section>
