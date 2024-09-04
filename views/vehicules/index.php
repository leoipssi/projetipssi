<h1 class="text-center mb-4">Nos Véhicules Électriques</h1>

<div class="row">
    <?php foreach ($vehicules as $vehicule): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="<?= BASE_URL ?>/public/images/vehicules/<?= $vehicule->getId() ?>.jpg" class="card-img-top" alt="<?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h5>
                    <p class="card-text">Type: <?= htmlspecialchars($vehicule->getType()) ?></p>
                    <p class="card-text">Catégorie: <?= htmlspecialchars($vehicule->getCategorie()) ?></p>
                    <a href="index.php?route=vehicules&action=show&id=<?= $vehicule->getId() ?>" class="btn btn-primary">Voir détails</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $currentPage == $i ? 'active' : '' ?>">
                    <a class="page-link" href="index.php?route=vehicules&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>
