<?php
// Assurez-vous que l'utilisateur est connecté
if (!$this->isLoggedIn()) {
    $this->redirect('login');
}
?>

<h1>Mes locations</h1>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<div class="filter-section">
    <form action="<?= $this->url('rentals') ?>" method="get">
        <label for="status">Filtrer par statut:</label>
        <select name="status" id="status">
            <option value="">Tous</option>
            <option value="En cours" <?= $status === 'En cours' ? 'selected' : '' ?>>En cours</option>
            <option value="Terminée" <?= $status === 'Terminée' ? 'selected' : '' ?>>Terminée</option>
        </select>
        <button type="submit" class="btn btn-primary">Filtrer</button>
    </form>
</div>

<?php if (empty($rentals)): ?>
    <p>Vous n'avez pas encore de location<?= $status ? " avec le statut '$status'" : '' ?>.</p>
    <a href="<?= $this->url('vehicules') ?>" class="btn btn-primary">Voir les véhicules disponibles</a>
<?php else: ?>
    <div class="rentals-container">
        <?php foreach ($rentals as $rental): ?>
            <?php
            $vehicule = Vehicule::findById($rental->getVehiculeId());
            $offer = RentalOffer::findById($rental->getOfferId());
            ?>
            <div class="rental-card">
                <h2>Location #<?= $rental->getId() ?></h2>
                <p><strong>Véhicule:</strong> <?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></p>
                <p><strong>Type:</strong> <?= htmlspecialchars($vehicule->getType()) ?></p>
                <p><strong>Date de début:</strong> <?= htmlspecialchars($rental->getDateDebut()) ?></p>
                <p><strong>Date de fin:</strong> <?= htmlspecialchars($rental->getDateFin()) ?></p>
                <p><strong>Durée:</strong> <?= htmlspecialchars($offer->getDuree()) ?> jours</p>
                <p><strong>Kilométrage inclus:</strong> <?= htmlspecialchars($offer->getKilometres()) ?> km</p>
                <p><strong>Prix:</strong> <?= htmlspecialchars($offer->getPrix()) ?> €</p>
                <p><strong>Statut:</strong> <span class="status-<?= strtolower($rental->getStatus()) ?>"><?= htmlspecialchars($rental->getStatus()) ?></span></p>
                
                <?php if ($rental->getStatus() === 'En cours'): ?>
                    <a href="<?= $this->url('rentals', ['action' => 'return', 'id' => $rental->getId()]) ?>" class="btn btn-warning">Retourner le véhicule</a>
                <?php elseif ($rental->getStatus() === 'Terminée'): ?>
                    <a href="<?= $this->url('rentals', ['action' => 'invoice', 'id' => $rental->getId()]) ?>" class="btn btn-info">Voir la facture</a>
                <?php endif; ?>
                
                <a href="<?= $this->url('rentals', ['action' => 'show', 'id' => $rental->getId()]) ?>" class="btn btn-secondary">Détails</a>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="<?= $this->url('rentals', ['page' => $page - 1, 'status' => $status]) ?>" class="btn btn-primary">&laquo; Précédent</a>
        <?php endif; ?>
        
        <?php if ($page < $totalPages): ?>
            <a href="<?= $this->url('rentals', ['page' => $page + 1, 'status' => $status]) ?>" class="btn btn-primary">Suivant &raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<style>
    .rentals-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    .rental-card {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        width: calc(33% - 20px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .status-encours { color: #ffa500; }
    .status-terminée { color: #008000; }
    .btn {
        display: inline-block;
        padding: 5px 10px;
        margin-top: 10px;
        text-decoration: none;
        color: #fff;
        border-radius: 3px;
    }
    .btn-primary { background-color: #007bff; }
    .btn-warning { background-color: #ffc107; }
    .btn-info { background-color: #17a2b8; }
    .btn-secondary { background-color: #6c757d; }
    .pagination {
        margin-top: 20px;
        text-align: center;
    }
    .filter-section {
        margin-bottom: 20px;
    }
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }
    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
</style>
