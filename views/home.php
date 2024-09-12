<?php
$this->logger->info("Début du fichier home.php");
$this->logger->info("Nombre de véhicules récents : " . count($recentVehicules));
$this->logger->info("Nombre d'offres actives : " . count($activeOffers));
?>

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
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
