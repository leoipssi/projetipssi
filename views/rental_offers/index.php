<h1>Gestion des offres de location</h1>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<a href="<?= $this->url('rental_offers', ['action' => 'create']) ?>" class="btn btn-primary">Créer une nouvelle offre</a>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Type de véhicule</th>
            <th>Durée (jours)</th>
            <th>Kilométrage</th>
            <th>Prix</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($offers as $offer): ?>
            <tr>
                <td><?= $offer->getId() ?></td>
                <td><?= $offer->getVehiculeType()->getNom() ?></td>
                <td><?= $offer->getDuree() ?></td>
                <td><?= $offer->getKilometres() ?></td>
                <td><?= $offer->getPrix() ?> €</td>
                <td><?= $offer->isActive() ? 'Actif' : 'Inactif' ?></td>
                <td>
                    <a href="<?= $this->url('rental_offers', ['action' => 'edit', 'id' => $offer->getId()]) ?>" class="btn btn-sm btn-secondary">Modifier</a>
                    <a href="<?= $this->url('rental_offers', ['action' => 'toggleActive', 'id' => $offer->getId()]) ?>" class="btn btn-sm btn-<?= $offer->isActive() ? 'warning' : 'success' ?>">
                        <?= $offer->isActive() ? 'Désactiver' : 'Activer' ?>
                    </a>
                    <a href="<?= $this->url('rental_offers', ['action' => 'delete', 'id' => $offer->getId()]) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ?');">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
