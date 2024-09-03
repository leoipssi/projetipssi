<?php
if (!$this->isAdmin()) {
    $this->redirect('home');
}
?>

<h1>Gestion des utilisateurs</h1>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<div class="search-filter-bar">
    <form action="<?= $this->url('admin', ['action' => 'manageUsers']) ?>" method="get">
        <input type="text" name="search" placeholder="Rechercher un utilisateur" value="<?= htmlspecialchars($search) ?>">
        
        <select name="role">
            <option value="">Tous les rôles</option>
            <?php foreach ($availableRoles as $availableRole): ?>
                <option value="<?= $availableRole ?>" <?= $role === $availableRole ? 'selected' : '' ?>>
                    <?= htmlspecialchars($availableRole) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit" class="btn btn-primary">Rechercher</button>
    </form>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>
                <a href="<?= $this->url('admin', ['action' => 'manageUsers', 'sort' => 'id', 'order' => $sortBy === 'id' && $sortOrder === 'ASC' ? 'desc' : 'asc', 'search' => $search, 'role' => $role]) ?>">
                    ID <?= $sortBy === 'id' ? ($sortOrder === 'ASC' ? '▲' : '▼') : '' ?>
                </a>
            </th>
            <th>
                <a href="<?= $this->url('admin', ['action' => 'manageUsers', 'sort' => 'username', 'order' => $sortBy === 'username' && $sortOrder === 'ASC' ? 'desc' : 'asc', 'search' => $search, 'role' => $role]) ?>">
                    Nom d'utilisateur <?= $sortBy === 'username' ? ($sortOrder === 'ASC' ? '▲' : '▼') : '' ?>
                </a>
            </th>
            <th>
                <a href="<?= $this->url('admin', ['action' => 'manageUsers', 'sort' => 'email', 'order' => $sortBy === 'email' && $sortOrder === 'ASC' ? 'desc' : 'asc', 'search' => $search, 'role' => $role]) ?>">
                    Email <?= $sortBy === 'email' ? ($sortOrder === 'ASC' ? '▲' : '▼') : '' ?>
                </a>
            </th>
            <th>
                <a href="<?= $this->url('admin', ['action' => 'manageUsers', 'sort' => 'role', 'order' => $sortBy === 'role' && $sortOrder === 'ASC' ? 'desc' : 'asc', 'search' => $search, 'role' => $role]) ?>">
                    Rôle <?= $sortBy === 'role' ? ($sortOrder === 'ASC' ? '▲' : '▼') : '' ?>
                </a>
            </th>
            <th>
                <a href="<?= $this->url('admin', ['action' => 'manageUsers', 'sort' => 'created_at', 'order' => $sortBy === 'created_at' && $sortOrder === 'ASC' ? 'desc' : 'asc', 'search' => $search, 'role' => $role]) ?>">
                    Date d'inscription <?= $sortBy === 'created_at' ? ($sortOrder === 'ASC' ? '▲' : '▼') : '' ?>
                </a>
            </th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user->getId() ?></td>
                <td><?= htmlspecialchars($user->getUsername()) ?></td>
                <td><?= htmlspecialchars($user->getEmail()) ?></td>
                <td><?= htmlspecialchars($user->getRole()) ?></td>
                <td><?= $user->getCreatedAt() ?></td>
                <td>
                    <a href="<?= $this->url('admin', ['action' => 'editUser', 'id' => $user->getId()]) ?>" class="btn btn-sm btn-warning">Modifier</a>
                    <a href="<?= $this->url('admin', ['action' => 'deleteUser', 'id' => $user->getId()]) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="<?= $this->url('admin', ['action' => 'manageUsers', 'page' => $page - 1, 'search' => $search, 'role' => $role, 'sort' => $sortBy, 'order' => $sortOrder]) ?>" class="btn btn-secondary">&laquo; Précédent</a>
    <?php endif; ?>
    
    <?php if ($page < $totalPages): ?>
        <a href="<?= $this->url('admin', ['action' => 'manageUsers', 'page' => $page + 1, 'search' => $search, 'role' => $role, 'sort' => $sortBy, 'order' => $sortOrder]) ?>" class="btn btn-secondary">Suivant &raquo;</a>
    <?php endif; ?>
</div>

<a href="<?= $this->url('admin', ['action' => 'dashboard']) ?>" class="btn btn-secondary">Retour au tableau de bord</a>

<style>
    .search-filter-bar {
        margin-bottom: 20px;
    }
    .search-filter-bar input, .search-filter-bar select {
        padding: 8px;
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
    .admin-table th a {
        color: #333;
        text-decoration: none;
    }
    .btn {
        display: inline-block;
        padding: 10px 15px;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        cursor: pointer;
    }
    .btn-primary { background-color: #007bff; }
    .btn-secondary { background-color: #6c757d; }
    .btn-warning { background-color: #ffc107; }
    .btn-danger { background-color: #dc3545; }
    .btn-sm {
        padding: 5px 10px;
        font-size: 0.875rem;
    }
    .pagination {
        margin-top: 20px;
    }
</style>
