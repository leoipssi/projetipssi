<?php
// Définir le chemin racine de l'application
define('ROOT_PATH', realpath($_SERVER['DOCUMENT_ROOT'] . '/e-motion'));

// Inclure les fichiers nécessaires
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/Vehicule.php';
?>

<h1>Tableau de bord administrateur</h1>

<div class="dashboard-stats">
    <div class="stat-card">
        <h2>Total des véhicules</h2>
        <p class="stat-number"><?= $totalVehicules ?? 0 ?></p>
        <a href="<?= $this->url('admin', ['action' => 'vehicules']) ?>" class="stat-link">Voir tous</a>
    </div>
    <div class="stat-card">
        <h2>Total des utilisateurs</h2>
        <p class="stat-number"><?= $totalUsers ?? 0 ?></p>
        <a href="<?= $this->url('admin', ['action' => 'users']) ?>" class="stat-link">Voir tous</a>
    </div>
    <div class="stat-card">
        <h2>Total des locations</h2>
        <p class="stat-number"><?= $totalRentals ?? 0 ?></p>
        <a href="<?= $this->url('admin', ['action' => 'rentals']) ?>" class="stat-link">Voir toutes</a>
    </div>
    <div class="stat-card">
        <h2>Revenu total</h2>
        <p class="stat-number"><?= number_format($totalRevenue ?? 0, 2) ?> €</p>
    </div>
</div>

<h2>Actions rapides</h2>
<div class="quick-actions">
    <a href="<?= $this->url('admin', ['action' => 'addVehicule']) ?>" class="button">Ajouter un véhicule</a>
    <a href="<?= $this->url('admin', ['action' => 'createOffer']) ?>" class="button">Créer une offre</a>
    <a href="<?= $this->url('admin', ['action' => 'manageUsers']) ?>" class="button">Gérer les utilisateurs</a>
</div>

<div class="dashboard-sections">
    <div class="dashboard-section">
        <h2>Dernières locations</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Véhicule</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentRentals as $rental): ?>
                    <tr>
                        <td><?= $rental->getId() ?></td>
                        <td>
                            <?php
                            $user = User::findById($rental->getClientId());
                            echo $user ? htmlspecialchars($user->getUsername()) : 'Utilisateur inconnu';
                            ?>
                        </td>
                        <td>
                            <?php
                            $vehicule = Vehicule::findById($rental->getVehiculeId());
                            if ($vehicule) {
                                echo htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele());
                            } else {
                                echo "Véhicule non trouvé";
                            }
                            ?>
                        </td>
                        <td><?= $rental->getDateDebut() ?></td>
                        <td><?= $rental->getDateFin() ?></td>
                        <td><span class="status-<?= strtolower($rental->getStatus()) ?>"><?= $rental->getStatus() ?></span></td>
                        <td>
                            <a href="<?= $this->url('admin', ['action' => 'viewRental', 'id' => $rental->getId()]) ?>" class="btn-action">Voir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="<?= $this->url('admin', ['action' => 'rentals']) ?>" class="view-all">Voir toutes les locations</a>
    </div>

    <div class="dashboard-section">
        <h2>Véhicules les plus loués</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Véhicule</th>
                    <th>Nombre de locations</th>
                    <th>Revenus générés</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topVehicules as $vehicule): ?>
                    <tr>
                        <td><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></td>
                        <td><?= $vehicule['rental_count'] ?? 0 ?></td>
                        <td><?= number_format($vehicule['revenue'] ?? 0, 2) ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .dashboard-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }
    .stat-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        width: 23%;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .stat-number {
        font-size: 2em;
        font-weight: bold;
        margin: 10px 0;
    }
    .quick-actions {
        margin-bottom: 30px;
    }
    .button {
        display: inline-block;
        padding: 10px 15px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        margin-right: 10px;
    }
    .admin-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .admin-table th, .admin-table td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }
    .admin-table th {
        background-color: #f8f9fa;
    }
    .status-encours { color: #ffa500; }
    .status-terminée { color: #008000; }
    .btn-action {
        display: inline-block;
        padding: 5px 10px;
        background-color: #17a2b8;
        color: white;
        text-decoration: none;
        border-radius: 3px;
    }
    .view-all {
        display: block;
        text-align: right;
        margin-top: 10px;
    }
    .dashboard-sections {
        display: flex;
        justify-content: space-between;
    }
    .dashboard-section {
        width: 48%;
    }
</style>
