<?php
// Vue de la liste des offres - GHD-HSP
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des offres – GHD-HSP</title>

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

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        h2 {
            color: var(--navy);
            font-size: 24px;
            margin: 0;
            letter-spacing: -0.02em;
        }

        /* GHD-HSP Composants */
        .card {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 24px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 3px 12px rgba(16, 47, 64, 0.025);
            transition: border-color var(--transition), box-shadow var(--transition), transform var(--transition);
        }

        .card:hover {
            border-color: var(--border-strong);
            box-shadow: var(--shadow);
            transform: translateY(-1px);
        }

        .liste-offres {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .offre-titre-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
        }

        .offre-titre-wrapper h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }

        .offre-titre-wrapper h3 a {
            color: var(--navy);
            text-decoration: none;
            transition: color var(--transition);
        }

        .offre-titre-wrapper h3 a:hover {
            color: var(--primary);
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

        p {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.5;
            margin: 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 40px;
            padding: 9px 16px;
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

        .etat-vide {
            text-align: center;
            padding: 40px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            color: var(--text-muted);
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="header-flex">
    <h2>Offres d'emploi et stages</h2>
    <?php if ($estPartenaire): ?>
        <a href="creer_offre.php" class="btn">+ Créer une offre</a>
    <?php endif; ?>
</div>

<?php if ($flash !== null): ?>
    <div class="message message-<?= $flash[0] === 'succes' ? 'succes' : 'erreur' ?>">
        <?= htmlspecialchars($flash[1]) ?>
    </div>
<?php endif; ?>

<?php if (empty($offres)): ?>
    <div class="etat-vide">
        Aucune offre n'est disponible pour le moment.
    </div>
<?php else: ?>
    <ul class="liste-offres">
        <?php foreach ($offres as $o): ?>
            <?php $ouverte = ($o['etat'] === 'ouverte'); ?>
            <li class="card">
                <div class="offre-titre-wrapper">
                    <h3>
                        <a href="offre_detail.php?id=<?= (int) $o['id_offre'] ?>">
                            <?= htmlspecialchars($o['titre']) ?>
                        </a>
                    </h3>
                    <span class="tag <?= $ouverte ? 'tag--success' : 'tag--error' ?>">
                        <?= $ouverte ? 'Ouverte' : 'Fermée' ?>
                    </span>
                </div>
                <p style="color: var(--text); font-weight: 500;">
                    <?= htmlspecialchars($o['nom_entreprise'] ?? 'Entreprise non renseignée') ?>
                    · <span style="text-transform: capitalize;"><?= htmlspecialchars($o['type_d_offre']) ?></span>
                    · <span style="color: var(--text-muted); font-size: 13px;">Publiée le <?= htmlspecialchars(date('d/m/Y', strtotime($o['date_publication']))) ?></span>
                </p>
                <p><?= htmlspecialchars(mb_strimwidth($o['description'], 0, 150, '…')) ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

</body>
</html>