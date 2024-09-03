<h1>Modifier l'offre de location</h1>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form action="<?= $this->url('rental_offers', ['action' => 'edit', 'id' => $offer->getId()]) ?>" method="post">
    <div class="form-group">
        <label for="vehicule_type_id">Type de véhicule</label>
        <select name="vehicule_type_id" id="vehicule_type_id" class="form-control" required>
            <?php foreach ($vehicleTypes as $type): ?>
                <option value="<?= $type->getId() ?>" <?= $type->getId() == $offer->getVehiculeTypeId() ? 'selected' : '' ?>><?= $type->getNom() ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="duree">Durée (jours)</label>
        <input type="number" name="duree" id="duree" class="form-control" required min="1" value="<?= $offer->getDuree() ?>">
    </div>

    <div class="form-group">
        <label for="kilometres">Kilométrage inclus</label>
        <input type="number" name="kilometres" id="kilometres" class="form-control" required min="0" value="<?= $offer->getKilometres() ?>">
    </div>

    <div class="form-group">
        <label for="prix">Prix</label>
        <input type="number" name="prix" id="prix" class="form-control" required min="0" step="0.01" value="<?= $offer->getPrix() ?>">
    </div>

    <div class="form-group">
        <label for="is_active">
            <input type="checkbox" name="is_active" id="is_active" value="1" <?= $offer->isActive() ? 'checked' : '' ?>>
            Activer l'offre
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Mettre à jour l'offre</button>
</form>
