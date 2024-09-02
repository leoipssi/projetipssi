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

<h1><?= htmlspecialchars($vehicle->getMarque() . ' ' . $vehicle->getModele()) ?></h1>

<div class="vehicle-details">
    <p>Numéro de série: <?= htmlspecialchars($vehicle->getNumeroSerie()) ?></p>
    <p>Couleur: <?= htmlspecialchars($vehicle->getCouleur()) ?></p>
    <p>Immatriculation: <?= htmlspecialchars($vehicle->getImmatriculation()) ?></p>
    <p>Kilométrage: <?= htmlspecialchars($vehicle->getKilometres()) ?> km</p>
    <p>Date d'achat: <?= htmlspecialchars($vehicle->getDateAchat()) ?></p>
    <p>Prix d'achat: <?= htmlspecialchars($vehicle->getPrixAchat()) ?> €</p>
</div>

<?php if (isLoggedIn()): ?>
    <a href="index.php?route=rentals&action=create&vehicle_id=<?= $vehicle->getId() ?>">Louer ce véhicule</a>
<?php else: ?>
    <p>Connectez-vous pour louer ce véhicule.</p>
<?php endif; ?>
