<h1>Créer une nouvelle offre de location</h1>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form action="<?= $this->url('rental_offers', ['action' => 'create']) ?>" method="post">
    <div class="form-group">
        <label for="vehicule_type_id">Type de véhicule</label>
        <select name="vehicule_type_id" id="vehicule_type_id" class="form-control" required>
            <?php foreach ($vehicleTypes as $type): ?>
                <option value="<?= $type->getId() ?>"><?= $type->getNom() ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="duree">Durée (jours)</label>
        <input type="number" name="duree" id="duree" class="form-control" required min="1">
    </div>

    <div class="form-group">
        <label for="kilometres">Kilométrage inclus</label>
        <input type="number" name="kilometres" id="kilometres" class="form-control" required min="0">
    </div>

    <div class="form-group">
        <label for="prix">Prix</label>
        <input type="number" name="prix" id="prix" class="form-control" required min="0" step="0.01">
    </div>

    <div class="form-group">
        <label for="is_active">
            <input type="checkbox" name="is_active" id="is_active" value="1" checked>
            Activer l'offre
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Créer l'offre</button>
</form>
