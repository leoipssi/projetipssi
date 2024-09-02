<?php
// Vérifie si on affiche les détails d'un véhicule spécifique
if (isset($vehicule) && $vehicule instanceof Vehicule) {
    // Affichage des détails d'un véhicule
    ?>
    <h1><?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h1>
    <div class="vehicule-details">
        <p>Numéro de série: <?= htmlspecialchars($vehicule->getNumeroSerie()) ?></p>
        <p>Couleur: <?= htmlspecialchars($vehicule->getCouleur()) ?></p>
        <p>Immatriculation: <?= htmlspecialchars($vehicule->getImmatriculation()) ?></p>
        <p>Kilométrage: <?= htmlspecialchars($vehicule->getKilometres()) ?> km</p>
        <p>Date d'achat: <?= htmlspecialchars($vehicule->getDateAchat()) ?></p>
        <p>Prix d'achat: <?= htmlspecialchars($vehicule->getPrixAchat()) ?> €</p>
    </div>
    <?php if (isLoggedIn()): ?>
        <a href="index.php?route=rentals&action=create&vehicule_id=<?= $vehicule->getId() ?>">Louer ce véhicule</a>
    <?php else: ?>
        <p>Connectez-vous pour louer ce véhicule.</p>
    <?php endif; ?>
    <a href="index.php?route=vehicules">Retour à la liste des véhicules</a>
<?php
} else {
    // Affichage de la liste des véhicules
    ?>
    <h1>Nos véhicules</h1>
    <?php if (!empty($vehicules)): ?>
        <?php foreach ($vehicules as $vehicule): ?>
            <div class="vehicule-card">
                <h2><?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h2>
                <p>Couleur: <?= htmlspecialchars($vehicule->getCouleur()) ?></p>
                <p>Immatriculation: <?= htmlspecialchars($vehicule->getImmatriculation()) ?></p>
                <p>Kilométrage: <?= htmlspecialchars($vehicule->getKilometres()) ?> km</p>
                <a href="index.php?route=vehicules&action=show&id=<?= $vehicule->getId() ?>">Voir détails</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun véhicule disponible pour le moment.</p>
    <?php endif; ?>
<?php
}
?>
