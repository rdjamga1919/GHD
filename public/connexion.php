<?php
ob_start();
session_start();
$erreur = $_SESSION['erreur_connexion'] ?? null;
unset($_SESSION['erreur_connexion']);
?>
<?php require __DIR__ . '/../views/layouts/header.php'; ?>

<div class="login-container">
    <h1 class="page-title">Connexion GHD-HSP</h1>

    <?php if ($erreur): ?>
        <div class="alert">
            <span>⚠️</span>
            <div><?= htmlspecialchars($erreur) ?></div>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="../src/traitement/traitement_connexion.php" method="POST">
            <div class="form-group space-after-md">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="votre@email.com" required>
            </div>

            <div class="form-group space-after-lg">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn">Se connecter</button>
        </form>
    </div>

    <!-- Lien vers l'inscription -->
    <div class="content-note">
        <p class="lead">
            Vous n'avez pas encore de compte ?
            <a href="inscription.php" class="link-emphasis">Inscrivez-vous</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/../views/layouts/footer.php'; ?>
