<div class="row">
    <div class="col-md-6">
        <img src="<?= BASE_URL ?>/public/images/vehicules/<?= $vehicule->getId() ?>.jpg" class="img-fluid rounded" alt="<?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?>">
    </div>
    <div class="col-md-6">
        <h1><?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h1>
        <p class="lead">Type: <?= htmlspecialchars($vehicule->getType()) ?></p>
        <p>Catégorie: <?= htmlspecialchars($vehicule->getCategorie()) ?></p>
        <p>Couleur: <?= htmlspecialchars($vehicule->getCouleur()) ?></p>
        <p>Immatriculation: <?= htmlspecialchars($vehicule->getImmatriculation()) ?></p>
        <p>Kilométrage: <?= htmlspecialchars($vehicule->getKilometres()) ?> km</p>
        <p>Date d'achat: <?= htmlspecialchars($vehicule->getDateAchat()) ?></p>
        <h3 class="mt-4">Tarif journalier: <?= htmlspecialchars($vehicule->getTarifJournalier()) ?> €</h3>
        <a href="index.php?route=rentals&action=create&vehicule_id=<?= $vehicule->getId() ?>" class="btn btn-success btn-lg mt-3">Réserver ce véhicule</a>
    </div>
</div>

<div class="mt-5">
    <h2>Offres disponibles pour ce véhicule</h2>
    <div class="row">
        <?php foreach ($offres as $offre): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Offre <?= htmlspecialchars($offre->getDuree()) ?> jours</h5>
                        <p class="card-text">Prix: <?= htmlspecialchars($offre->getPrix()) ?> €</p>
                        <p class="card-text">Kilométrage inclus: <?= htmlspecialchars($offre->getKilometres()) ?> km</p>
                        <a href="index.php?route=rentals&action=create&offer_id=<?= $offre->getId() ?>" class="btn btn-primary">Choisir cette offre</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
