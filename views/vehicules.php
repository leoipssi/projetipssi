<h1>Nos véhicules</h1>

<?php foreach ($vehicles as $vehicle): ?>
    <div class="vehicle-card">
        <h2><?= htmlspecialchars($vehicle->getMarque() . ' ' . $vehicle->getModele()) ?></h2>
        <p>Couleur: <?= htmlspecialchars($vehicle->getCouleur()) ?></p>
        <p>Immatriculation: <?= htmlspecialchars($vehicle->getImmatriculation()) ?></p>
        <p>Kilométrage: <?= htmlspecialchars($vehicle->getKilometres()) ?> km</p>
        <a href="index.php?route=vehicles&action=show&id=<?= $vehicle->getId() ?>">Voir détails</a>
    </div>
<?php endforeach; ?>
