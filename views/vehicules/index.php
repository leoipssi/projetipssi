<h1>Nos véhicules</h1>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if ($this->isAdmin()): ?>
    <a href="<?= $this->url('vehicules', ['action' => 'create']) ?>" class="btn btn-primary">Ajouter un véhicule</a>
<?php endif; ?>

<?php if (!empty($vehicules)): ?>
    <div class="vehicules-grid">
        <?php foreach ($vehicules as $vehicule): ?>
            <div class="vehicule-card">
                <h2><?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h2>
                <p>Couleur: <?= htmlspecialchars($vehicule->getCouleur()) ?></p>
                <p>Immatriculation: <?= htmlspecialchars($vehicule->getImmatriculation()) ?></p>
                <p>Kilométrage: <?= htmlspecialchars($vehicule->getKilometres()) ?> km</p>
                <a href="<?= $this->url('vehicules', ['action' => 'show', 'id' => $vehicule->getId()]) ?>" class="btn btn-info">Voir détails</a>
                <?php if ($this->isAdmin()): ?>
                    <a href="<?= $this->url('vehicules', ['action' => 'edit', 'id' => $vehicule->getId()]) ?>" class="btn btn-warning">Modifier</a>
                    <a href="<?= $this->url('vehicules', ['action' => 'delete', 'id' => $vehicule->getId()]) ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?');">Supprimer</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Aucun véhicule disponible pour le moment.</p>
<?php endif; ?>
