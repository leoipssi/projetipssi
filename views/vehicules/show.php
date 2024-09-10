<div class="container">
    <h1><?= $this->e($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <img src="<?= $this->asset('images/vehicules/' . $vehicule->getId() . '.png') ?>" alt="<?= $this->e($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?>" class="img-fluid">
        </div>
        <div class="col-md-6">
            <p><strong>Type:</strong> <?= $this->e($vehicule->getType()) ?></p>
            <p><strong>Couleur:</strong> <?= $this->e($vehicule->getCouleur()) ?></p>
            <p><strong>Immatriculation:</strong> <?= $this->e($vehicule->getImmatriculation()) ?></p>
            <p><strong>Kilométrage:</strong> <?= number_format($vehicule->getKilometres(), 0, ',', ' ') ?> km</p>
            <p><strong>Date d'achat:</strong> <?= $this->e($vehicule->getDateAchat()) ?></p>
            
            <?php if (!empty($offresActives)): ?>
                <h2>Offres spéciales</h2>
                <?php foreach ($offresActives as $offre): ?>
                    <div class="offre-active">
                        <h3>Offre <?= $this->e($offre->getDuree()) ?> jours</h3>
                        <p>Prix total: <?= number_format($offre->getPrix(), 2, ',', ' ') ?> €</p>
                        <p>Kilométrage inclus: <?= number_format($offre->getKilometres(), 0, ',', ' ') ?> km</p>
                        <p>Tarif journalier: <?= number_format($offre->getPrix() / $offre->getDuree(), 2, ',', ' ') ?> €</p>
                        <a href="<?= $this->url('rentals', ['action' => 'create', 'offer_id' => $offre->getId()]) ?>" class="btn btn-success">Réserver cette offre</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune offre spéciale n'est actuellement disponible pour ce véhicule.</p>
            <?php endif; ?>

            <h3>Tarif journalier standard: <?= number_format($vehicule->getTarifJournalier(), 2, ',', ' ') ?> €</h3>
            <a href="<?= $this->url('rentals', ['action' => 'create', 'vehicule_id' => $vehicule->getId()]) ?>" class="btn btn-primary">Réserver ce véhicule</a>
        </div>
    </div>

    <?php if ($this->isAdmin()): ?>
    <div class="mt-4">
        <a href="<?= $this->url('vehicules', ['action' => 'edit', 'id' => $vehicule->getId()]) ?>" class="btn btn-warning">Modifier</a>
        <a href="<?= $this->url('vehicules', ['action' => 'delete', 'id' => $vehicule->getId()]) ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?');">Supprimer</a>
    </div>
    <?php endif; ?>
</div>
