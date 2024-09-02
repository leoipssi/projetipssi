<!-- views/admin/dashboard.php -->

<h1>Tableau de bord administrateur</h1>

<div class="dashboard-stats">
    <div class="stat-card">
        <h2>Total des véhicules</h2>
        <p class="stat-number"><?= $totalVehicles ?></p>
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
    <a href="index.php?route=admin&action=addVehicle" class="button">Ajouter un véhicule</a>
    <a href="index.php?route=admin&action=createOffer" class="button">Créer une offre</a>
    <a href="index.php?route=admin&action=manageUsers" class="button">Gérer les utilisateurs</a>
</div>

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
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recentRentals as $rental): ?>
            <tr>
                <td><?= $rental->getId() ?></td>
                <td><?= htmlspecialchars(User::findById($rental->getClientId())->getUsername()) ?></td>
                <td><?= htmlspecialchars(Vehicle::findById($rental->getVehicleId())->getMarque() . ' ' . Vehicle::findById($rental->getVehicleId())->getModele()) ?></td>
                <td><?= $rental->getDateDebut() ?></td>
                <td><?= $rental->getDateFin() ?></td>
                <td><?= $rental->getStatus() ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Véhicules les plus loués</h2>
<table class="admin-table">
    <thead>
        <tr>
            <th>Véhicule</th>
            <th>Nombre de locations</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($topVehicles as $vehicle): ?>
            <tr>
                <td><?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?></td>
                <td><?= $vehicle['rental_count'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
