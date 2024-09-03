<?php
if (!$this->isAdmin()) {
    $this->redirect('home');
}
?>

<h1>Ajouter un nouveau véhicule</h1>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form action="<?= $this->url('admin', ['action' => 'addVehicule']) ?>" method="post">
    <div class="form-group">
        <label for="marque">Marque:</label>
        <input type="text" id="marque" name="marque" required class="form-control">
    </div>

    <div class="form-group">
        <label for="modele">Modèle:</label>
        <input type="text" id="modele" name="modele" required class="form-control">
    </div>

    <div class="form-group">
        <label for="type">Type:</label>
        <select id="type" name="type_id" required class="form-control">
            <?php foreach ($vehiculeTypes as $type): ?>
                <option value="<?= $type->getId() ?>"><?= htmlspecialchars($type->getNom()) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="numero_serie">Numéro de série:</label>
        <input type="text" id="numero_serie" name="numero_serie" required class="form-control">
    </div>

    <div class="form-group">
        <label for="couleur">Couleur:</label>
        <input type="text" id="couleur" name="couleur" required class="form-control">
    </div>

    <div class="form-group">
        <label for="immatriculation">Immatriculation:</label>
        <input type="text" id="immatriculation" name="immatriculation" required class="form-control">
    </div>

    <div class="form-group">
        <label for="kilometres">Kilométrage:</label>
        <input type="number" id="kilometres" name="kilometres" required class="form-control">
    </div>

    <div class="form-group">
        <label for="date_achat">Date d'achat:</label>
        <input type="date" id="date_achat" name="date_achat" required class="form-control">
    </div>

    <div class="form-group">
        <label for="prix_achat">Prix d'achat:</label>
        <input type="number" id="prix_achat" name="prix_achat" step="0.01" required class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Ajouter le véhicule</button>
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
