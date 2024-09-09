<?php
// Assurez-vous que $vehicule est défini et est un objet valide
if (!isset($vehicule) || !is_object($vehicule)) {
    echo "Erreur : Véhicule non trouvé.";
    exit;
}
// Fonction helper pour éviter les erreurs de htmlspecialchars avec des valeurs null
function safeHtmlSpecialChars($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= safeHtmlSpecialChars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?> - E-Motion</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <img src="<?= BASE_URL ?>/public/images/vehicules/<?= $vehicule->getId() ?>.png" class="img-fluid rounded" alt="<?= safeHtmlSpecialChars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?>">
            </div>
            <div class="col-md-6">
                <h1><?= safeHtmlSpecialChars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h1>
                <p class="lead">Type: <?= safeHtmlSpecialChars($vehicule->getType()) ?></p>
                <p>Catégorie: <?= safeHtmlSpecialChars($vehicule->getCategorie()) ?></p>
                <p>Couleur: <?= safeHtmlSpecialChars($vehicule->getCouleur()) ?></p>
                <p>Immatriculation: <?= safeHtmlSpecialChars($vehicule->getImmatriculation()) ?></p>
                <p>Kilométrage: <?= safeHtmlSpecialChars($vehicule->getKilometres()) ?> km</p>
                <p>Date d'achat: <?= safeHtmlSpecialChars($vehicule->getDateAchat()) ?></p>
                <h3 class="mt-4">Tarif journalier: <?= safeHtmlSpecialChars($vehicule->getTarifJournalier()) ?> €</h3>
                <a href="index.php?route=rentals&action=create&vehicule_id=<?= $vehicule->getId() ?>" class="btn btn-success btn-lg mt-3">Réserver ce véhicule</a>
            </div>
        </div>
        <div class="mt-5">
            <h2>Offres disponibles pour ce véhicule</h2>
            <div class="row">
                <?php
                if (isset($offres) && is_array($offres) && !empty($offres)):
                    foreach ($offres as $offre):
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Offre <?= safeHtmlSpecialChars($offre->getDuree()) ?> jours</h5>
                                <p class="card-text">Prix: <?= safeHtmlSpecialChars($offre->getPrix()) ?> €</p>
                                <p class="card-text">Kilométrage inclus: <?= safeHtmlSpecialChars($offre->getKilometres()) ?> km</p>
                                <a href="index.php?route=rentals&action=create&offer_id=<?= $offre->getId() ?>" class="btn btn-primary">Choisir cette offre</a>
                            </div>
                        </div>
                    </div>
                <?php
                    endforeach;
                else:
                ?>
                    <div class="col-12">
                        <p>Aucune offre disponible pour ce véhicule.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="<?= BASE_URL ?>/public/js/script.js"></script>
</body>
</html>
