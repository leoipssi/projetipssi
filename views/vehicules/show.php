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

// Rechercher l'offre rattachée à ce véhicule
$offreRattachee = null;
if (isset($offres) && is_array($offres)) {
    foreach ($offres as $offre) {
        if ($offre->getVehiculeId() == $vehicule->getId()) {
            $offreRattachee = $offre;
            break;
        }
    }
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
        .vehicle-image-container {
            position: relative;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        .vehicle-image {
            width: 100%;
            height: auto;
            cursor: zoom-in;
        }
        .zoomed {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .zoomed img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            cursor: zoom-out;
        }
        .offre-rattachee {
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
                
                <?php if ($offreRattachee): ?>
                    <div class="offre-rattachee">
                        <h2>Offre Spéciale</h2>
                        <p>Durée: <?= safeHtmlSpecialChars($offreRattachee->getDuree()) ?></p>
                        <p>Prix total: <?= safeHtmlSpecialChars($offreRattachee->getPrix()) ?> €</p>
                        <p>Kilométrage inclus: <?= safeHtmlSpecialChars($offreRattachee->getKilometres()) ?> km</p>
                        <p>Tarif journalier: <?= number_format($offreRattachee->getPrix() / 7, 2) ?> €</p>
                        <?php if ($offreRattachee->getDescription()): ?>
                            <p>Description: <?= safeHtmlSpecialChars($offreRattachee->getDescription()) ?></p>
                        <?php endif; ?>
                        <a href="<?= $this->url('rentals', ['action' => 'create', 'offer_id' => $offreRattachee->getId()]) ?>" class="btn btn-success btn-lg mt-3">Réserver cette offre</a>
                    </div>
                <?php else: ?>
                    <h3 class="mt-4">Tarif journalier: <?= safeHtmlSpecialChars($vehicule->getTarifJournalier()) ?> €</h3>
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
