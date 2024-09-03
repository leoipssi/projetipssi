<?php
// Assurez-vous que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?route=login');
    exit;
}
?>

<h1>Mes locations</h1>

<?php if (empty($rental)): ?>
    <p>Vous n'avez pas encore de location.</p>
    <a href="index.php?route=vehicules" class="btn btn-primary">Voir les véhicules disponibles</a>
<?php else: ?>
    <div class="rental-container">
        <?php foreach ($rental as $rental): ?>
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
                    <a href="index.php?route=rental&action=return&id=<?= $rental->getId() ?>" class="btn btn-warning">Retourner le véhicule</a>
                <?php elseif ($rental->getStatus() === 'Terminée'): ?>
                    <a href="index.php?route=rental&action=invoice&id=<?= $rental->getId() ?>" class="btn btn-info">Voir la facture</a>
                <?php endif; ?>
                
                <a href="index.php?route=rental&action=details&id=<?= $rental->getId() ?>" class="btn btn-secondary">Détails</a>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="index.php?route=rental&page=<?= $page - 1 ?>" class="btn btn-primary">&laquo; Précédent</a>
        <?php endif; ?>
        
        <?php if ($page < $totalPages): ?>
            <a href="index.php?route=rental&page=<?= $page + 1 ?>" class="btn btn-primary">Suivant &raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<style>
    .rental-container {
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
    .status-annulée { color: #ff0000; }
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
</style>
