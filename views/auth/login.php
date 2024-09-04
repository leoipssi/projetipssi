<div class="row justify-content-center">
    <div class="col-md-8">
        <h1 class="text-center mb-4">Connexion</h1>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form action="index.php?route=login" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($username ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>
        <p class="text-center mt-3">
            Pas encore inscrit ? <a href="index.php?route=register">Inscrivez-vous ici</a>
        </p>
    </div>
</div>
