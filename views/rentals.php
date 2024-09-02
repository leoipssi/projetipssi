<h1>Mes locations</h1>

<?php if (empty($rentals)): ?>
    <p>Vous n'avez pas encore de location.</p>
<?php else: ?>
    <?php foreach ($rentals as $rental): ?>
        <div class="rental-card">
            <h2>Location #<?= $rental->getId() ?></h2>
            <p>Véhicule: <?= htmlspecialchars(Vehicle::findById($rental->getVehicleId())->getMarque() . ' ' . Vehicle::findById($rental->getVehicleId())->getModele()) ?></p>
            <p>Date de début: <?= htmlspecialchars($rental->getDateDebut()) ?></p>
            <p>Date de fin: <?= htmlspecialchars($rental->getDateFin()) ?></p>
            <p>Statut: <?= htmlspecialchars($rental->getStatus()) ?></p>
            <?php if ($rental->getStatus() === 'Terminée'): ?>
                <a href="index.php?route=rentals&action=invoice&id=<?= $rental->getId() ?>">Voir la facture</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
