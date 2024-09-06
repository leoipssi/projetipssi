<h1>Gestion des Locations</h1>

<form action="index.php" method="get">
    <input type="hidden" name="route" value="admin">
    <input type="hidden" name="action" value="rentals">
    <input type="text" name="search" placeholder="Rechercher..." value="<?php echo htmlspecialchars($search); ?>">
    <select name="status">
        <option value="">Tous les statuts</option>
        <option value="en_cours" <?php echo $status === 'en_cours' ? 'selected' : ''; ?>>En cours</option>
        <option value="terminee" <?php echo $status === 'terminee' ? 'selected' : ''; ?>>Terminée</option>
        <option value="annulee" <?php echo $status === 'annulee' ? 'selected' : ''; ?>>Annulée</option>
    </select>
    <button type="submit">Filtrer</button>
</form>

<table>
    <thead>
        <tr>
            <th><a href="?route=admin&action=rentals&sort=id&order=<?php echo $sortBy === 'id' && $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">ID</a></th>
            <th><a href="?route=admin&action=rentals&sort=user_id&order=<?php echo $sortBy === 'user_id' && $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">Utilisateur</a></th>
            <th><a href="?route=admin&action=rentals&sort=vehicule_id&order=<?php echo $sortBy === 'vehicule_id' && $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">Véhicule</a></th>
            <th><a href="?route=admin&action=rentals&sort=date_debut&order=<?php echo $sortBy === 'date_debut' && $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">Date de début</a></th>
            <th><a href="?route=admin&action=rentals&sort=date_fin&order=<?php echo $sortBy === 'date_fin' && $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">Date de fin</a></th>
            <th><a href="?route=admin&action=rentals&sort=statut&order=<?php echo $sortBy === 'statut' && $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">Statut</a></th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rentals as $rental): ?>
        <tr>
            <td><?php echo htmlspecialchars($rental->getId()); ?></td>
            <td><?php echo htmlspecialchars($rental->getUserName()); ?></td>
            <td><?php echo htmlspecialchars($rental->getVehiculeName()); ?></td>
            <td><?php echo htmlspecialchars($rental->getDateDebut()); ?></td>
            <td><?php echo htmlspecialchars($rental->getDateFin()); ?></td>
            <td><?php echo htmlspecialchars($rental->getStatut()); ?></td>
            <td>
                <a href="index.php?route=admin&action=editRental&id=<?php echo $rental->getId(); ?>">Modifier</a>
                <a href="index.php?route=admin&action=deleteRental&id=<?php echo $rental->getId(); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette location ?');">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
// Pagination
for ($i = 1; $i <= $totalPages; $i++) {
    echo "<a href='index.php?route=admin&action=rentals&page=$i'>$i</a> ";
}
?>

<?php require_once BASE_PATH . '/views/admin/footer.php'; ?>
