<h1>Louer un véhicule</h1>

<h2><?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form action="<?= $this->url('rentals', ['action' => 'create', 'vehicule_id' => $vehicule->getId()]) ?>" method="post">
    <div class="form-group">
        <label for="date_debut">Date de début:</label>
        <input type="date" id="date_debut" name="date_debut" required class="form-control">
    </div>
    
    <div class="form-group">
        <label for="date_fin">Date de fin:</label>
        <input type="date" id="date_fin" name="date_fin" required class="form-control">
    </div>
    
    <div class="form-group">
        <label for="offer_id">Offre de location:</label>
        <select id="offer_id" name="offer_id" required class="form-control">
            <?php foreach (RentalOffer::findActiveByVehicleType($vehicule->getTypeId()) as $offer): ?>
                <option value="<?= $offer->getId() ?>">
                    <?= htmlspecialchars($offer->getDuree() . ' jours / ' . $offer->getKilometres() . ' km - ' . $offer->getPrix() . ' €') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <button type="submit" class="btn btn-primary">Confirmer la location</button>
</form>

<a href="<?= $this->url('vehicules') ?>" class="btn btn-secondary">Retour aux véhicules</a>
