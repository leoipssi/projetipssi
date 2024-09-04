<?php
// Assurez-vous que l'utilisateur est connecté
if (!$this->isLoggedIn()) {
    $this->redirect('login');
}
?>

<h1 class="text-center mb-4">Mes locations</h1>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <form action="<?= $this->url('rentals') ?>" method="get" class="form-inline">
            <div class="form-group mr-2">
                <label for="status" class="mr-2">Filtrer par statut:</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Tous</option>
                    <option value="En cours" <?= $status === 'En cours' ? 'selected' : '' ?>>En cours</option>
                    <option value="Terminée" <?= $status === 'Terminée' ? 'selected' : '' ?>>Terminée</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>
    </div>
</div>

<?php if (empty($rentals)): ?>
    <div class="alert alert-info">
        <p>Vous n'avez pas encore de location<?= $status ? " avec le statut '$status'" : '' ?>.</p>
    </div>
    <a href="<?= $this->url('vehicules') ?>" class="btn btn-primary">Voir les véhicules disponibles</a>
<?php else: ?>
    <div class="row">
        <?php foreach ($rentals as $rental): ?>
            <?php
            $vehicule = Vehicule::findById($rental->getVehiculeId());
            ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Location #<?= $rental->getId() ?></h5>
                        <p class="card-text"><strong>Véhicule:</strong> <?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></p>
                        <p class="card-text"><strong>Catégorie:</strong> <?= htmlspecialchars($vehicule->getCategorie()) ?></p>
                        <p class="card-text"><strong>Date de début:</strong> <?= htmlspecialchars($rental->getDateDebut()) ?></p>
                        <p class="card-text"><strong>Date de fin:</strong> <?= htmlspecialchars($rental->getDateFin()) ?></p>
                        <p class="card-text"><strong>Durée:</strong> <?= htmlspecialchars($rental->getDuree()) ?> jours</p>
                        <p class="card-text"><strong>Tarif total:</strong> <?= htmlspecialchars($rental->getTarif()) ?> €</p>
                        <p class="card-text">
                            <strong>Statut:</strong> 
                            <span class="badge bg-<?= $rental->getStatus() === 'En cours' ? 'warning' : 'success' ?>">
                                <?= htmlspecialchars($rental->getStatus()) ?>
                            </span>
                        </p>
                    </div>
                    <div class="card-footer">
                        <?php if ($rental->getStatus() === 'En cours'): ?>
                            <a href="<?= $this->url('rentals', ['action' => 'return', 'id' => $rental->getId()]) ?>" class="btn btn-warning">Retourner le véhicule</a>
                        <?php elseif ($rental->getStatus() === 'Terminée'): ?>
                            <a href="<?= $this->url('rentals', ['action' => 'invoice', 'id' => $rental->getId()]) ?>" class="btn btn-info">Voir la facture</a>
                        <?php endif; ?>
                        <a href="<?= $this->url('rentals', ['action' => 'show', 'id' => $rental->getId()]) ?>" class="btn btn-secondary">Détails</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $this->url('rentals', ['page' => $page - 1, 'status' => $status]) ?>">&laquo; Précédent</a>
                </li>
            <?php endif; ?>
            
            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $this->url('rentals', ['page' => $page + 1, 'status' => $status]) ?>">Suivant &raquo;</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
