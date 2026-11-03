<?php
// Vue du profil partenaire - GHD-HSP
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil partenaire – GHD-HSP</title>
    <link rel="stylesheet" href="variables.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
</head>
<body>

<div class="container" style="max-width: 600px;">
    <div class="page-header">
        <h1 class="page-title">Mon profil partenaire</h1>
    </div>

    <div class="card">
        <h2 class="card-title">Informations personnelles</h2>
        <div class="split" style="padding: 10px 0; border-bottom: 1px solid var(--border);">
            <span class="caption" style="font-weight: 600;">Nom</span>
            <span class="split-text" style="font-weight: 500;"><?= htmlspecialchars($partenaire->getNom()) ?></span>
        </div>
        <div class="split" style="padding: 10px 0; border-bottom: 1px solid var(--border);">
            <span class="caption" style="font-weight: 600;">Prénom</span>
            <span class="split-text" style="font-weight: 500;"><?= htmlspecialchars($partenaire->getPrenom()) ?></span>
        </div>
        <div class="split" style="padding: 10px 0; border-bottom: 1px solid var(--border);">
            <span class="caption" style="font-weight: 600;">Email</span>
            <span class="split-text" style="font-weight: 500;"><?= htmlspecialchars($partenaire->getEmail()) ?></span>
        </div>
        <div class="split" style="padding: 10px 0;">
            <span class="caption" style="font-weight: 600;">Poste</span>
            <span class="split-text" style="font-weight: 500;"><?= htmlspecialchars($partenaire->getPoste()) ?></span>
        </div>
    </div>

    <div class="card">
        <h2 class="card-title">Mon entreprise</h2>

        <?php if ($entreprise !== null): ?>
            <div class="split" style="padding: 10px 0; border-bottom: 1px solid var(--border);">
                <span class="caption" style="font-weight: 600;">Nom</span>
                <span class="split-text"><strong><?= htmlspecialchars($entreprise->getNom()) ?></strong></span>
            </div>
            <div class="split" style="padding: 10px 0; border-bottom: 1px solid var(--border);">
                <span class="caption" style="font-weight: 600;">Adresse</span>
                <span class="split-text"><?= htmlspecialchars($entreprise->getAdresse()) ?></span>
            </div>
            <div class="split" style="padding: 10px 0;">
                <span class="caption" style="font-weight: 600;">Site web</span>
                <span class="split-text">
                    <a href="<?= htmlspecialchars($entreprise->getSiteWeb()) ?>" target="_blank" rel="noopener noreferrer">
                        <?= htmlspecialchars($entreprise->getSiteWeb()) ?>
                    </a>
                </span>
            </div>

            <div style="margin-top: 8px;">
                <a href="entreprise.php?id=<?= (int) $entreprise->getIdEntreprise() ?>" class="btn btn-secondary">
                    Voir mon entreprise
                </a>
            </div>
        <?php else: ?>
            <p class="lead">Aucune entreprise trouvée.</p>
        <?php endif; ?>
    </div>

    <div>
        <a href="modifier_profil.php" class="btn">Modifier mon profil</a>
    </div>
</div>

</body>
</html>