<h1>Mes locations</h1>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if (empty($rentals)): ?>
    <p>Vous n'avez pas encore de location.</p>
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
            <a href="<?= $this->url('rentals', ['page' => $page - 1]) ?>" class="btn btn-primary">&laquo; Précédent</a>
        <?php endif; ?>
        
        <?php if ($page < $totalPages): ?>
            <a href="<?= $this->url('rentals', ['page' => $page + 1]) ?>" class="btn btn-primary">Suivant &raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
