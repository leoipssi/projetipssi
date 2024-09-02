<h1>Bienvenue sur e-Motion</h1>

<section>
    <h2>Nos derniers véhicules</h2>
    <?php foreach ($recentVehicles as $vehicle): ?>
        <div>
            <h3><?= htmlspecialchars($vehicle->getMarque() . ' ' . $vehicle->getModele()) ?></h3>
            <p>Type: <?= htmlspecialchars($vehicle->getType()) ?></p>
            <a href="index.php?route=vehicles&action=show&id=<?= $vehicle->getId() ?>">Voir détails</a>
        </div>
    <?php endforeach; ?>
</section>

<section>
    <h2>Offres spéciales</h2>
    <?php foreach ($activeOffers as $offer): ?>
        <div>
            <h3>Offre sur <?= htmlspecialchars($offer->getVehicle()->getMarque() . ' ' . $offer->getVehicle()->getModele()) ?></h3>
            <p>Prix: <?= htmlspecialchars($offer->getPrix()) ?> €</p>
            <p>Durée: <?= htmlspecialchars($offer->getDuree()) ?> jours</p>
            <a href="index.php?route=rentals&action=create&offer_id=<?= $offer->getId() ?>">Réserver</a>
        </div>
    <?php endforeach; ?>
</section>
