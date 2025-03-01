<?php
// Ensure $totalPages and $currentPage are set
$totalPages = $totalPages ?? 1;
$currentPage = $currentPage ?? 1;
// Helper function to safely handle potentially null values
function safeHtmlSpecialChars($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Véhicules Électriques - E-Motion</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Nos Véhicules Électriques</h1>
        <div class="row">
            <?php if (isset($vehicules) && is_array($vehicules) && !empty($vehicules)): ?>
                <?php foreach ($vehicules as $vehicule): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?= BASE_URL ?>/public/images/vehicules/<?= $vehicule->getId() ?>.png" class="card-img-top" alt="<?= safeHtmlSpecialChars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= safeHtmlSpecialChars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h5>
                                <p class="card-text">Type: <?= safeHtmlSpecialChars($vehicule->getType()) ?></p>
                                <a href="index.php?route=vehicules&action=show&id=<?= $vehicule->getId() ?>" class="btn btn-primary">Voir détails</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">Aucun véhicule disponible pour le moment.</p>
                </div>
            <?php endif; ?>
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
    </div>
    <script src="<?= BASE_URL ?>/public/js/script.js"></script>
</body>
</html>
