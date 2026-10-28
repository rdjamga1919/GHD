<?php
// Vue de détail d'une offre - GHD-HSP
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $offre !== null ? htmlspecialchars($offre['titre']) : 'Offre introuvable' ?> – GHD-HSP</title>

    <style>
        :root {
            --primary: #126779;
            --primary-hover: #0f5766;
            --primary-pressed: #0b434f;
            --navy: #102f40;
            --accent: #62c8ba;
            --focus: #258cba;
            --surface: #ffffff;
            --surface-soft: #f8fafb;
            --surface-teal: #f0f7f7;
            --text: #1e293b;
            --text-muted: #64748b;
            --success: #15803d;
            --success-bg: #f0fdf4;
            --danger: #b91c1c;
            --danger-bg: #fef2f2;
            --border: #e2e8f0;
            --border-strong: #cbd5e1;
            --border-accent: #62c8ba;
            --radius-md: 8px;
            --radius-lg: 16px;
            --transition: 0.2s ease-in-out;
            --shadow: 0 10px 25px -5px rgba(16, 47, 64, 0.1);
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: var(--surface-soft);
            color: var(--text);
        }

        h2 {
            color: var(--navy);
            font-size: 24px;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        h3 {
            color: var(--navy);
            font-size: 16px;
            font-weight: 700;
            margin-top: 24px;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }

        p {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.6;
            margin: 0 0 12px 0;
        }

        /* GHD-HSP Composants */
        .card {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 32px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 3px 12px rgba(16, 47, 64, 0.025);
            margin-bottom: 24px;
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .card:hover {
            border-color: var(--border-strong);
            box-shadow: var(--shadow);
        }

        .tag {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .tag--success {
            background: var(--success-bg);
            color: var(--success);
        }

        .tag--error {
            background: var(--danger-bg);
            color: var(--danger);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 44px;
            padding: 11px 18px;
            border: 1px solid var(--primary);
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #147786, var(--primary));
            box-shadow: 0 3px 8px rgba(18, 103, 121, 0.10);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background var(--transition), border-color var(--transition), box-shadow var(--transition), transform var(--transition);
        }

        .btn:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: var(--danger);
            border-color: var(--danger);
            box-shadow: none;
        }

        .btn-danger:hover {
            background: #991b1b;
            border-color: #991b1b;
        }

        .btn-ghost {
            background: var(--surface);
            border-color: var(--border);
            box-shadow: none;
            color: var(--primary);
        }

        .btn-ghost:hover {
            background: var(--surface-teal);
            border-color: var(--border-accent);
            color: var(--primary);
        }

        .message {
            padding: 15px 17px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.5;
        }

        .message-succes {
            background: var(--success-bg);
            border: 1px solid #d7eddf;
            color: var(--success);
        }

        .message-erreur {
            background: var(--danger-bg);
            border: 1px solid #f3d8dc;
            color: var(--danger);
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #edf1f3;
            margin-bottom: 16px;
        }

        .meta-item span {
            display: block;
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .meta-item strong {
            font-size: 14px;
            color: var(--text);
        }

        .actions-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 20px;
            align-items: center;
        }
    </style>
</head>
<body>

<?php if ($flash !== null): ?>
    <div class="message message-<?= $flash[0] === 'succes' ? 'succes' : 'erreur' ?>">
        <?= htmlspecialchars($flash[1]) ?>
    </div>
<?php endif; ?>

<div class="card">
    <?php if ($offre === null): ?>

        <h2>Offre introuvable</h2>
        <div class="message message-erreur" style="margin: 0;">
            Cette offre n'existe pas ou n'est plus disponible.
        </div>

    <?php else: ?>
        <?php $ouverte = ($offre['etat'] === 'ouverte'); ?>

        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
            <div>
                <h2><?= htmlspecialchars($offre['titre']) ?></h2>
                <p style="margin-bottom: 4px;">
                    <strong>Entreprise :</strong> <?= htmlspecialchars($offre['nom_entreprise'] ?? 'Non renseignée') ?>
                </p>
            </div>
            <div>
                <span class="tag <?= $ouverte ? 'tag--success' : 'tag--error' ?>">
                    <?= $ouverte ? 'Offre ouverte' : 'Offre fermée' ?>
                </span>
            </div>
        </div>

        <div class="meta-grid" style="margin-top: 16px;">
            <div class="meta-item">
                <span>Type d'offre</span>
                <strong><?= htmlspecialchars(ucfirst($offre['type_d_offre'])) ?></strong>
            </div>
            <div class="meta-item">
                <span>Date de publication</span>
                <strong><?= htmlspecialchars(date('d/m/Y', strtotime($offre['date_publication']))) ?></strong>
            </div>
            <?php if ($offre['salaire'] !== null): ?>
                <div class="meta-item">
                    <span>Rémunération</span>
                    <strong><?= htmlspecialchars(number_format((float) $offre['salaire'], 2, ',', ' ')) ?> €</strong>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($offre['adresse']) || !empty($offre['site_web'])): ?>
            <div style="margin-bottom: 16px;">
                <?php if (!empty($offre['adresse'])): ?>
                    <p style="margin-bottom: 4px;"><strong>Adresse :</strong> <?= htmlspecialchars($offre['adresse']) ?></p>
                <?php endif; ?>
                <?php if (!empty($offre['site_web'])): ?>
                    <p style="margin-bottom: 4px;">
                        <strong>Site web :</strong>
                        <a href="<?= htmlspecialchars($offre['site_web']) ?>" target="_blank" rel="noopener noreferrer" style="color: var(--primary);">
                            <?= htmlspecialchars($offre['site_web']) ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div>
            <h3>Description</h3>
            <p><?= nl2br(htmlspecialchars($offre['description'])) ?></p>

            <h3>Missions</h3>
            <p><?= nl2br(htmlspecialchars($offre['missions'])) ?></p>
        </div>

        <?php if ($estProprietaire): ?>
            <div class="actions-group">
                <?php if ($ouverte): ?>
                    <a href="modifier_offre.php?id=<?= (int) $offre['id_offre'] ?>" class="btn btn-ghost">Modifier l'offre</a>
                    <form method="post" action="../src/traitement/traitement_offre.php" style="margin: 0;">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                        <input type="hidden" name="action" value="fermer">
                        <input type="hidden" name="id_offre" value="<?= (int) $offre['id_offre'] ?>">
                        <button type="submit" class="btn btn-danger">Fermer l'offre</button>
                    </form>
                <?php endif; ?>
                <a href="candidatures_recues.php?id=<?= (int) $offre['id_offre'] ?>" class="btn">
                    Voir les candidatures reçues
                </a>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<p>
    <a href="offres.php" class="btn btn-ghost">← Retour aux offres</a>
</p>

</body>
</html>