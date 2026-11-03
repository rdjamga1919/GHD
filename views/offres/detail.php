<?php
// Vue de détail d'une offre - GHD-HSP
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $offre !== null ? htmlspecialchars($offre['titre']) : 'Offre introuvable' ?> – GHD-HSP</title>
    <link rel="stylesheet" href="variables.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
</head>
<body>

<div class="container">
    <?php if ($flash !== null): ?>
        <div class="alert alert-<?= $flash[0] === 'succes' ? 'success' : 'error' ?>">
            <div class="alert-body">
                <span class="alert-text"><?= htmlspecialchars($flash[1]) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <?php if ($offre === null): ?>
            <h2 class="card-title">Offre introuvable</h2>
            <div class="alert alert-error">
                <div class="alert-body">
                    <span class="alert-text">Cette offre n'existe pas ou n'est plus disponible.</span>
                </div>
            </div>
        <?php else: ?>
            <?php $ouverte = ($offre['etat'] === 'ouverte'); ?>

            <div class="split" style="align-items: flex-start;">
                <div>
                    <h2 class="page-title" style="margin-bottom: 6px;"><?= htmlspecialchars($offre['titre']) ?></h2>
                    <p class="lead" style="margin-bottom: 0;">
                        <strong>Entreprise :</strong> <?= htmlspecialchars($offre['nom_entreprise'] ?? 'Non renseignée') ?>
                    </p>
                </div>
                <div>
                    <span class="tag <?= $ouverte ? 'tag--success' : 'tag--warning' ?>">
                        <?= $ouverte ? 'Offre ouverte' : 'Offre fermée' ?>
                    </span>
                </div>
            </div>

            <div class="stats-grid" style="margin-top: 8px; padding-top: 16px; border-top: 1px solid var(--border);">
                <div class="stat-card" style="padding: 16px;">
                    <span class="stat-label">Type d'offre</span>
                    <strong style="color: var(--navy); font-size: 15px;"><?= htmlspecialchars(ucfirst($offre['type_d_offre'])) ?></strong>
                </div>
                <div class="stat-card" style="padding: 16px;">
                    <span class="stat-label">Date de publication</span>
                    <strong style="color: var(--navy); font-size: 15px;"><?= htmlspecialchars(date('d/m/Y', strtotime($offre['date_publication']))) ?></strong>
                </div>
                <?php if ($offre['salaire'] !== null): ?>
                    <div class="stat-card" style="padding: 16px;">
                        <span class="stat-label">Rémunération</span>
                        <strong style="color: var(--navy); font-size: 15px;"><?= htmlspecialchars(number_format((float) $offre['salaire'], 2, ',', ' ')) ?> €</strong>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($offre['adresse']) || !empty($offre['site_web'])): ?>
                <div>
                    <?php if (!empty($offre['adresse'])): ?>
                        <p class="lead" style="margin-bottom: 4px;"><strong>Adresse :</strong> <?= htmlspecialchars($offre['adresse']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($offre['site_web'])): ?>
                        <p class="lead" style="margin-bottom: 4px;">
                            <strong>Site web :</strong>
                            <a href="<?= htmlspecialchars($offre['site_web']) ?>" target="_blank" rel="noopener noreferrer">
                                <?= htmlspecialchars($offre['site_web']) ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div>
                <h3 class="card-title" style="margin-top: 12px;">Description</h3>
                <p class="lead"><?= nl2br(htmlspecialchars($offre['description'])) ?></p>

                <h3 class="card-title" style="margin-top: 16px;">Missions</h3>
                <p class="lead"><?= nl2br(htmlspecialchars($offre['missions'])) ?></p>
            </div>

            <?php if ($estProprietaire): ?>
                <div class="cluster" style="margin-top: 12px;">
                    <?php if ($ouverte): ?>
                        <a href="modifier_offre.php?id=<?= (int) $offre['id_offre'] ?>" class="btn btn-secondary">Modifier l'offre</a>
                        <form method="post" action="../src/traitement/traitement_offre.php" style="margin: 0;">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                            <input type="hidden" name="action" value="fermer">
                            <input type="hidden" name="id_offre" value="<?= (int) $offre['id_offre'] ?>">
                            <button type="submit" class="btn" style="background: var(--danger); border-color: var(--danger);">Fermer l'offre</button>
                        </form>
                    <?php endif; ?>
                    <a href="candidatures_recues.php?id=<?= (int) $offre['id_offre'] ?>" class="btn">
                        Voir les candidatures reçues
                    </a>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <div>
        <a href="offres.php" class="btn btn-secondary">← Retour aux offres</a>
    </div>
</div>

</body>
</html>