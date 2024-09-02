<h1>Tableau de bord administrateur</h1>

<div class="dashboard-stats">
    <div class="stat-card">
        <h2>Total des vehicules</h2>
        <p class="stat-number"><?= $totalVehicules ?></p>
    </div>
    <div class="stat-card">
        <h2>Total des utilisateurs</h2>
        <p class="stat-number"><?= $totalUsers ?></p>
    </div>
    <div class="stat-card">
        <h2>Total des locations</h2>
        <p class="stat-number"><?= $totalRentals ?></p>
    </div>
</div>

<h2>Actions rapides</h2>
<div class="quick-actions">
    <a href="index.php?route=admin&action=addVehicule" class="button">Ajouter un vehicule</a>
    <a href="index.php?route=admin&action=createOffer" class="button">Créer une offre</a>
    <a href="index.php?route=admin&action=manageUsers" class="button">Gérer les utilisateurs</a>
</div>

<h2>Dernières locations</h2>
<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Vehicule</th>
            <th>Date de début</th>
            <th>Date de fin</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recentRentals as $rental): ?>
            <tr>
                <td><?= $rental->getId() ?></td>
                <td><?= htmlspecialchars(User::findById($rental->getClientId())->getUsername()) ?></td>
                <td><?= htmlspecialchars(Vehicule::findById($rental->getVehiculeId())->getMarque() . ' ' . Vehicule::findById($rental->getVehiculeId())->getModele()) ?></td>
                <td><?= $rental->getDateDebut() ?></td>
                <td><?= $rental->getDateFin() ?></td>
                <td><?= $rental->getStatus() ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Vehicules les plus loués</h2>
<table class="admin-table">
    <thead>
        <tr>
            <th>Vehicule</th>
            <th>Nombre de locations</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($topVehicules as $vehicule): ?>
            <tr>
                <td><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></td>
                <td><?= $vehicule['rental_count'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
