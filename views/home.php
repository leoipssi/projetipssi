<?php 
$vehiculeCount = 0;
if (isset($recentVehicules) && is_array($recentVehicules)):
    $this->logger->debug("Type de recentVehicules : " . gettype($recentVehicules));
    $this->logger->debug("Nombre de véhicules récents : " . count($recentVehicules));
    foreach ($recentVehicules as $index => $vehicule): 
        $vehiculeCount++;
        $this->logger->debug("Traitement du véhicule $index : " . print_r($vehicule, true));
        if (!is_object($vehicule) || !($vehicule instanceof Vehicule)) {
            $this->logger->error("Élément non valide trouvé dans recentVehicules", ['index' => $index, 'element' => var_export($vehicule, true)]);
            continue;
        }
?>

<div class="container mt-4">
    <div class="jumbotron text-center">
        <h1 class="display-4">Bienvenue sur e-Motion</h1>
        <p class="lead">Découvrez notre sélection de véhicules électriques pour une mobilité durable et économique.</p>
        <a href="<?= BASE_URL ?>/index.php?route=vehicules" class="btn btn-primary btn-lg">Voir nos véhicules</a>
    </div>
    <section class="mt-5">
        <h2 class="text-center mb-4">Nos derniers véhicules</h2>
        <div class="row">
            <?php 
            $vehiculeCount = 0;
            if (isset($recentVehicules) && is_array($recentVehicules)):
                foreach ($recentVehicules as $vehicule): 
                    $vehiculeCount++;
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php
                        $imagePath = BASE_URL . "/public/images/vehicules/" . $vehicule->getId() . ".jpg";
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . parse_url($imagePath, PHP_URL_PATH))) {
                            $this->logger->debug("L'image existe : " . $imagePath);
                        } else {
                            $this->logger->warning("L'image n'existe pas : " . $imagePath);
                            $imagePath = BASE_URL . "/public/images/placeholder.jpg"; // Image par défaut
                        }
                        ?>
                        <img src="<?= $imagePath ?>" class="card-img-top" alt="<?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></h5>
                            <p class="card-text">Type: <?= htmlspecialchars($vehicule->getType()) ?></p>
                            <a href="<?= BASE_URL ?>/index.php?route=vehicules&action=show&id=<?= $vehicule->getId() ?>" class="btn btn-primary">Voir détails</a>
                        </div>
                    </div>
                </div>
            <?php 
                endforeach;
            endif;
            $this->logger->debug("Nombre de véhicules affichés : " . $vehiculeCount);
            ?>
        </div>
    </section>
    <section class="mt-5">
        <h2 class="text-center mb-4">Offres spéciales</h2>
        <div class="row">
            <?php 
            $offerCount = 0;
            if (isset($activeOffers) && is_array($activeOffers)):
                foreach ($activeOffers as $offer): 
                    $offerCount++;
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Offre sur <?= htmlspecialchars($offer->getVehicule()->getMarque() . ' ' . $offer->getVehicule()->getModele()) ?></h5>
                            <p class="card-text">Prix: <?= htmlspecialchars($offer->getPrix()) ?> €</p>
                            <p class="card-text">Durée: <?= htmlspecialchars($offer->getDuree()) ?> jours</p>
                            <a href="<?= BASE_URL ?>/index.php?route=rentals&action=create&offer_id=<?= $offer->getId() ?>" class="btn btn-success">Réserver</a>
                        </div>
                    </div>
                </div>
            <?php 
    endforeach;
else:
    $this->logger->error("recentVehicules n'est pas défini ou n'est pas un tableau");
endif;
$this->logger->debug("Nombre de véhicules affichés : " . $vehiculeCount);
?>
        </div>
    </section>
</div>
