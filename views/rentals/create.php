<h1>Louer un véhicule</h1>
<h2><?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?> (<?= htmlspecialchars($vehicule->getCategorie()) ?>)</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form action="<?= $this->url('rentals', ['action' => 'create', 'vehicule_id' => $vehicule->getId()]) ?>" method="post" id="rental-form">
    <div class="form-group">
        <label for="date_debut">Date de début:</label>
        <input type="date" id="date_debut" name="date_debut" required class="form-control" min="<?= date('Y-m-d') ?>">
    </div>
    
    <div class="form-group">
        <label for="date_fin">Date de fin:</label>
        <input type="date" id="date_fin" name="date_fin" required class="form-control">
    </div>
    
    <div id="tarif-estimation" style="display: none;">
        <p>Durée estimée: <span id="duree-estimee"></span> jours</p>
        <p>Tarif estimé: <span id="tarif-estime"></span> €</p>
    </div>
    
    <button type="submit" class="btn btn-primary">Confirmer la location</button>
</form>

<a href="<?= $this->url('vehicules') ?>" class="btn btn-secondary">Retour aux véhicules</a>

<script>
document.getElementById('rental-form').addEventListener('submit', function(e) {
    var dateDebut = new Date(document.getElementById('date_debut').value);
    var dateFin = new Date(document.getElementById('date_fin').value);
    var today = new Date();
    today.setHours(0, 0, 0, 0);

    if (dateDebut < today) {
        alert('La date de début doit être aujourd\'hui ou ultérieure.');
        e.preventDefault();
    } else if (dateFin <= dateDebut) {
        alert('La date de fin doit être postérieure à la date de début.');
        e.preventDefault();
    }
});

document.getElementById('date_debut').addEventListener('change', function() {
    document.getElementById('date_fin').min = this.value;
    updateTarifEstimation();
});

document.getElementById('date_fin').addEventListener('change', updateTarifEstimation);

function updateTarifEstimation() {
    var dateDebut = new Date(document.getElementById('date_debut').value);
    var dateFin = new Date(document.getElementById('date_fin').value);
    
    if (dateDebut && dateFin && dateFin > dateDebut) {
        var duree = Math.ceil((dateFin - dateDebut) / (1000 * 60 * 60 * 24)) + 1;
        var tarifJournalier = <?= $vehicule->getTarifJournalier() ?>;
        var tarifTotal = duree * tarifJournalier;
        
        document.getElementById('duree-estimee').textContent = duree;
        document.getElementById('tarif-estime').textContent = tarifTotal.toFixed(2);
        document.getElementById('tarif-estimation').style.display = 'block';
    } else {
        document.getElementById('tarif-estimation').style.display = 'none';
    }
}
</script>
