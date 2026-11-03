<?php
// Vue de la liste des offres - GHD-HSP
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des offres – GHD-HSP</title>
    <link rel="stylesheet" href="variables.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
</head>
<body>

<div class="container">
    <div class="split" style="margin-bottom: 24px;">
        <h2 class="page-title">Offres d'emploi et stages</h2>
        <?php if ($estPartenaire): ?>
            <a href="creer_offre.php" class="btn">+ Créer une offre</a>
        <?php endif; ?>
    </div>

    <?php if ($flash !== null): ?>
        <div class="alert alert-<?= $flash[0] === 'succes' ? 'success' : 'error' ?>">
            <div class="alert-body">
                <span class="alert-text"><?= htmlspecialchars($flash[1]) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($offres)): ?>
        <div class="card" style="text-align: center; padding: 40px;">
            <p class="lead">Aucune offre n'est disponible pour le moment.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: var(--space-md);">
            <?php foreach ($offres as $o): ?>
                <?php $ouverte = ($o['etat'] === 'ouverte'); ?>
                <div class="card">
                    <div class="split" style="align-items: flex-start;">
                        <h3 class="card-title" style="margin: 0;">
                            <a href="offre_detail.php?id=<?= (int) $o['id_offre'] ?>">
                                <?= htmlspecialchars($o['titre']) ?>
                            </a>
                        </h3>
                        <span class="tag <?= $ouverte ? 'tag--success' : 'tag--warning' ?>">
                            <?= $ouverte ? 'Ouverte' : 'Fermée' ?>
                        </span>
                    </div>
                    <p class="lead" style="color: var(--text); font-weight: 500; margin: 0;">
                        <?= htmlspecialchars($o['nom_entreprise'] ?? 'Entreprise non renseignée') ?>
                        · <span style="text-transform: capitalize;"><?= htmlspecialchars($o['type_d_offre']) ?></span>
                        · <span class="caption">Publiée le <?= htmlspecialchars(date('d/m/Y', strtotime($o['date_publication']))) ?></span>
                    </p>
                    <p class="lead" style="margin: 0;"><?= htmlspecialchars(mb_strimwidth($o['description'], 0, 150, '…')) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>