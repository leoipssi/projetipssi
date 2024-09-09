<?php
if (!$this->isAdmin()) {
    $this->redirect('home');
}
?>
<h1>Créer une nouvelle offre de location</h1>
<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>
<form action="<?= $this->url('admin', ['action' => 'createOffer']) ?>" method="post">
    <div class="form-group">
        <label for="vehicule">Véhicule:</label>
        <select id="vehicule" name="vehicule_id" required class="form-control">
            <option value="">Sélectionnez un véhicule</option>
            <?php foreach ($vehicules as $vehicule): ?>
                <option value="<?= $vehicule->getId() ?>">
                    <?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele() . ' (' . $vehicule->getImmatriculation() . ')') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="duree">Durée:</label>
        <input type="text" id="duree" name="duree" value="7 jours" readonly class="form-control">
    </div>
    <div class="form-group">
        <label for="kilometres">Kilométrage inclus:</label>
        <input type="number" id="kilometres" name="kilometres" required class="form-control">
    </div>
    <div class="form-group">
        <label for="prix">Prix:</label>
        <input type="number" id="prix" name="prix" step="0.01" required class="form-control">
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea id="description" name="description" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Créer l'offre</button>
</form>
<a href="<?= $this->url('admin', ['action' => 'dashboard']) ?>" class="btn btn-secondary">Retour au tableau de bord</a>
<style>
    .form-group {
        margin-bottom: 15px;
    }
    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .btn {
        display: inline-block;
        padding: 10px 15px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        cursor: pointer;
    }
    .btn-secondary {
        background-color: #6c757d;
    }
</style>
