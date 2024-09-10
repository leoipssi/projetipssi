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
    <style>
        .offre-active {
            background-color: #f8f9fa;
            border: 2px solid #28a745;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="vehicle-image-container">
                    <img src="<?= BASE_URL ?>/public/images/vehicules/<?= $vehicule->getId() ?>.png" class="vehicle-image" alt="<?= safeHtmlSpecialChars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?>" onclick="zoomImage(this)">
                </div>
            </div>
            <div class="col-md-6">
                <h1><?= safeHtmlSpecialChars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h1>
                <p class="lead">Type: <?= safeHtmlSpecialChars($vehicule->getType()) ?></p>
                <p>Couleur: <?= safeHtmlSpecialChars($vehicule->getCouleur()) ?></p>
                <p>Immatriculation: <?= safeHtmlSpecialChars($vehicule->getImmatriculation()) ?></p>
                <p>Kilométrage: <?= safeHtmlSpecialChars($vehicule->getKilometres()) ?> km</p>
                <p>Date d'achat: <?= safeHtmlSpecialChars($vehicule->getDateAchat()) ?></p>
                
                <?php if (!empty($offresActives)): ?>
                    <h2>Offres spéciales</h2>
                    <?php foreach ($offresActives as $offre): ?>
                        <div class="offre-active">
                            <h3>Offre <?= safeHtmlSpecialChars($offre->getDuree()) ?> jours</h3>
                            <p>Prix total: <?= safeHtmlSpecialChars($offre->getPrix()) ?> €</p>
                            <p>Kilométrage inclus: <?= safeHtmlSpecialChars($offre->getKilometres()) ?> km</p>
                            <p>Tarif journalier: <?= number_format($offre->getPrix() / $offre->getDuree(), 2) ?> €</p>
                            <a href="<?= $this->url('rentals', ['action' => 'create', 'offer_id' => $offre->getId()]) ?>" class="btn btn-success btn-lg mt-3">Réserver cette offre</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune offre spéciale n'est actuellement disponible pour ce véhicule.</p>
                    <h3 class="mt-4">Tarif journalier standard: <?= safeHtmlSpecialChars($vehicule->getTarifJournalier()) ?> €</h3>
                    <a href="<?= $this->url('rentals', ['action' => 'create', 'vehicule_id' => $vehicule->getId()]) ?>" class="btn btn-success btn-lg mt-3">Réserver ce véhicule</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="<?= BASE_URL ?>/public/js/script.js"></script>
    <script>
        function zoomImage(img) {
            const zoomed = document.createElement('div');
            zoomed.className = 'zoomed';
            zoomed.innerHTML = `<img src="${img.src}" alt="${img.alt}">`;
            zoomed.onclick = () => zoomed.remove();
            document.body.appendChild(zoomed);
        }
    </script>
</body>
</html>
