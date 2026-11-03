<?php
// Vue du profil médecin - GHD-HSP
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil médecin – GHD-HSP</title>
    <link rel="stylesheet" href="variables.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
</head>
<body>

<div class="container">
    <div class="page-header">
        <h2 class="page-title">Mon profil médecin</h2>
    </div>

    <div class="card">
        <div class="split" style="padding: 10px 0; border-bottom: 1px solid var(--border);">
            <span class="caption" style="font-weight: 600;">Nom</span>
            <span class="split-text" style="font-weight: 500;"><?= htmlspecialchars($medecin->getNom()) ?></span>
        </div>
        <div class="split" style="padding: 10px 0; border-bottom: 1px solid var(--border);">
            <span class="caption" style="font-weight: 600;">Prénom</span>
            <span class="split-text" style="font-weight: 500;"><?= htmlspecialchars($medecin->getPrenom()) ?></span>
        </div>
        <div class="split" style="padding: 10px 0; border-bottom: 1px solid var(--border);">
            <span class="caption" style="font-weight: 600;">Email</span>
            <span class="split-text" style="font-weight: 500;"><?= htmlspecialchars($medecin->getEmail()) ?></span>
        </div>
        <div class="split" style="padding: 10px 0;">
            <span class="caption" style="font-weight: 600;">N° RPPS</span>
            <span class="split-text" style="font-weight: 500;"><?= htmlspecialchars($medecin->getNumeroRpps()) ?></span>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">Spécialités</h3>
        <?php if (empty($specialites)): ?>
            <p class="lead">Aucune spécialité renseignée.</p>
        <?php else: ?>
            <div class="cluster">
                <?php foreach ($specialites as $s): ?>
                    <span class="tag tag--medecin"><?= htmlspecialchars($s['libelle_specialite']) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3 class="card-title">Hôpitaux d'exercice</h3>
        <?php if (empty($hopitaux)): ?>
            <p class="lead">Aucun hôpital renseigné.</p>
        <?php else: ?>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                <?php foreach ($hopitaux as $h): ?>
                    <li class="split" style="padding: 10px 14px; background: var(--surface-soft); border: 1px solid var(--border); border-radius: var(--radius-md);">
                        <span class="split-text"><strong><?= htmlspecialchars($h['nom_hopital']) ?></strong></span>
                        <span class="caption"><?= htmlspecialchars($h['commune']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3 class="card-title">Établissements d'enseignement</h3>
        <?php if (empty($etablissements)): ?>
            <p class="lead">Vous n'êtes rattaché à aucun établissement d'enseignement.</p>
        <?php else: ?>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                <?php foreach ($etablissements as $e): ?>
                    <li class="split" style="padding: 10px 14px; background: var(--surface-soft); border: 1px solid var(--border); border-radius: var(--radius-md);">
                        <span class="split-text"><strong><?= htmlspecialchars($e['nom_etablissement']) ?></strong></span>
                        <?php if (!empty($e['adresse'])): ?>
                            <span class="caption"><?= htmlspecialchars($e['adresse']) ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div>
        <a href="modifier_profil.php" class="btn">Modifier mon profil</a>
    </div>
</div>

</body>
</html>