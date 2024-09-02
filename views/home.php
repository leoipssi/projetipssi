<h1>Bienvenue sur e-Motion</h1>

<section>
    <h2>Nos derniers véhicules</h2>
    <?php foreach ($recentVehicules as $vehicule): ?>
        <div>
            <h3><?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h3>
            <p>Type: <?= htmlspecialchars($vehicule->getType()) ?></p>
            <a href="index.php?route=vehicules&action=show&id=<?= $vehicule->getId() ?>">Voir détails</a>
        </div>
    <?php endforeach; ?>
</section>

<section>
    <h2>Offres spéciales</h2>
    <?php foreach ($activeOffers as $offer): ?>
        <div>
            <h3>Offre sur <?= htmlspecialchars($offer->getVehicule()->getMarque() . ' ' . $offer->getVehicule()->getModele()) ?></h3>
            <p>Prix: <?= htmlspecialchars($offer->getPrix()) ?> €</p>
            <p>Durée: <?= htmlspecialchars($offer->getDuree()) ?> jours</p>
            <a href="index.php?route=rentals&action=create&offer_id=<?= $offer->getId() ?>">Réserver</a>
        </div>
    <?php endforeach; ?>
</section>
