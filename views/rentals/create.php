<h1>Louer un véhicule</h1>
<h2><?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?> (<?= htmlspecialchars($vehicule->getType()) ?>)</h2>

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
    
    <div class="form-group">
        <label for="offer_id">Offre de location:</label>
        <select id="offer_id" name="offer_id" required class="form-control">
            <?php foreach ($offers as $offer): ?>
                <option value="<?= $offer->getId() ?>" data-prix="<?= $offer->getPrix() ?>" data-duree="<?= $offer->getDuree() ?>">
                    <?= htmlspecialchars($offer->getDuree() . ' jours / ' . $offer->getKilometres() . ' km - ' . $offer->getPrix() . ' €') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="price-estimation" style="display: none;">
        <p>Prix estimé: <span id="estimated-price"></span> €</p>
    </div>
    
    <button type="submit" class="btn btn-primary">Confirmer la location</button>
</form>

<a href="<?= $this->url('vehicules') ?>" class="btn btn-secondary">Retour aux véhicules</a>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('rental-form');
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const offerSelect = document.getElementById('offer_id');
    const priceEstimation = document.getElementById('price-estimation');
    const estimatedPrice = document.getElementById('estimated-price');

    form.addEventListener('submit', function(e) {
        const start = new Date(dateDebut.value);
        const end = new Date(dateFin.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (start < today) {
            alert('La date de début doit être aujourd\'hui ou ultérieure.');
            e.preventDefault();
        } else if (end <= start) {
            alert('La date de fin doit être postérieure à la date de début.');
            e.preventDefault();
        }
    });

    dateDebut.addEventListener('change', updatePriceEstimation);
    dateFin.addEventListener('change', updatePriceEstimation);
    offerSelect.addEventListener('change', updatePriceEstimation);

    function updatePriceEstimation() {
        const start = new Date(dateDebut.value);
        const end = new Date(dateFin.value);
        
        if (start && end && end > start) {
            const selectedOffer = offerSelect.options[offerSelect.selectedIndex];
            const offerPrice = parseFloat(selectedOffer.dataset.prix);
            const offerDuration = parseInt(selectedOffer.dataset.duree);

            const durationInDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
            const totalPrice = offerPrice * Math.ceil(durationInDays / offerDuration);

            estimatedPrice.textContent = totalPrice.toFixed(2);
            priceEstimation.style.display = 'block';
        } else {
            priceEstimation.style.display = 'none';
        }
    }

    dateDebut.addEventListener('change', function() {
        dateFin.min = this.value;
    });
});
</script>
