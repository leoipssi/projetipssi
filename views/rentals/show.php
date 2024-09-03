<h1>Détails de la Location #<?= $rental->getId() ?></h1>

<div class="rental-details">
    <p><strong>Véhicule:</strong> <?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></p>
    <p><strong>Type:</strong> <?= htmlspecialchars($vehicule->getType()) ?></p>
    <p><strong>Date de début:</strong> <?= htmlspecialchars($rental->getDateDebut()) ?></p>
    <p><strong>Date de fin:</strong> <?= htmlspecialchars($rental->getDateFin()) ?></p>
    <p><strong>Durée:</strong> <?= htmlspecialchars($offer->getDuree()) ?> jours</p>
    <p><strong>Kilométrage inclus:</strong> <?= htmlspecialchars($offer->getKilometres()) ?> km</p>
    <p><strong>Prix par jour:</strong> <?= htmlspecialchars($offer->getPrix()) ?> €</p>
    <?php
    $days = (strtotime($rental->getDateFin()) - strtotime($rental->getDateDebut())) / (60 * 60 * 24);
    $totalPrice = $offer->getPrix() * $days;
    ?>
    <p><strong>Prix total:</strong> <?= htmlspecialchars(number_format($totalPrice, 2)) ?> €</p>
    <p><strong>Statut:</strong> <span class="status-<?= strtolower($rental->getStatus()) ?>"><?= htmlspecialchars($rental->getStatus()) ?></span></p>
</div>

<?php if ($rental->getStatus() === 'En cours'): ?>
    <a href="<?= $this->url('rentals', ['action' => 'return', 'id' => $rental->getId()]) ?>" class="btn btn-warning">Retourner le véhicule</a>
<?php elseif ($rental->getStatus() === 'Terminée'): ?>
    <a href="<?= $this->url('rentals', ['action' => 'invoice', 'id' => $rental->getId()]) ?>" class="btn btn-info">Voir la facture</a>
<?php endif; ?>

<a href="<?= $this->url('rentals') ?>" class="btn btn-secondary">Retour à mes locations</a>
