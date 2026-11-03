<?php
session_start();
$erreur = $_SESSION['erreur_connexion'] ?? null;
unset($_SESSION['erreur_connexion']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion – GHD-HSP</title>
    <link rel="stylesheet" href="variables.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
</head>
<body style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px;">

<div style="width: 100%; max-width: 420px;">
    <div class="page-header" style="text-align: center; margin-bottom: 24px;">
        <h1 class="page-title" style="font-size: 24px;">Connexion GHD-HSP</h1>
    </div>

    <?php if ($erreur): ?>
        <div class="alert alert-error">
            <span>⚠️</span>
            <div class="alert-body">
                <span class="alert-text"><?= htmlspecialchars($erreur) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="../src/traitement/traitement_connexion.php" method="POST" style="display: flex; flex-direction: column; gap: var(--space-md);">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="votre@email.com" required>
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-block" style="margin-top: 8px;">Se connecter</button>
        </form>
    </div>
</div>

</body>
</html>