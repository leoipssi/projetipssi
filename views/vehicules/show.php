<h1><?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h1>

<div class="vehicule-details">
    <p>Numéro de série: <?= htmlspecialchars($vehicule->getNumeroSerie()) ?></p>
    <p>Couleur: <?= htmlspecialchars($vehicule->getCouleur()) ?></p>
    <p>Immatriculation: <?= htmlspecialchars($vehicule->getImmatriculation()) ?></p>
    <p>Kilométrage: <?= htmlspecialchars($vehicule->getKilometres()) ?> km</p>
    <p>Date d'achat: <?= htmlspecialchars($vehicule->getDateAchat()) ?></p>
    <p>Prix d'achat: <?= htmlspecialchars($vehicule->getPrixAchat()) ?> €</p>
</div>

<?php if ($this->isLoggedIn()): ?>
    <a href="<?= $this->url('rentals', ['action' => 'create', 'vehicule_id' => $vehicule->getId()]) ?>" class="btn btn-primary">Louer ce véhicule</a>
<?php else: ?>
    <p>Connectez-vous pour louer ce véhicule.</p>
<?php endif; ?>

<a href="<?= $this->url('vehicules') ?>" class="btn btn-secondary">Retour à la liste des véhicules</a>
