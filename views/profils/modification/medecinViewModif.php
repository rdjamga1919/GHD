<?php
// Vue de modification du profil médecin - GHD-HSP
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mon profil médecin – GHD-HSP</title>

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
            --surface-disabled: #e2e8f0;
            --text: #1e293b;
            --text-muted: #64748b;
            --text-disabled: #94a3b8;
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
            --space-xs: 4px;
            --space-sm: 8px;
            --space-ms: 12px;
            --space-md: 16px;
            --space-lg: 24px;
            --space-xl: 32px;
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
            margin-bottom: 20px;
            letter-spacing: -0.02em;
        }

        h3 {
            color: var(--navy);
            font-size: 16px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 12px;
            letter-spacing: -0.01em;
        }

        h4 {
            color: var(--text);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        p {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.5;
            margin: 0 0 12px 0;
        }

        /* GHD-HSP Composants */
        .card {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 24px;
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

        .form-row {
            display: flex;
            gap: var(--space-md);
            flex-wrap: wrap;
            align-items: flex-end;
            margin-top: 8px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
            min-width: 220px;
        }

        .form-control, input[type="text"], select {
            display: block;
            width: 100%;
            min-height: 44px;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--surface);
            color: var(--text);
            font: inherit;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .form-control:hover, input:hover, select:hover {
            border-color: var(--border-strong);
        }

        .form-control:focus, input:focus, select:focus {
            outline: none;
            border-color: var(--focus);
            box-shadow: 0 0 0 4px rgba(37, 140, 186, 0.11);
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
            cursor: pointer;
            text-decoration: none;
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

        .item-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: var(--surface-soft);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 13px;
            color: var(--text);
        }

        .form-retrait {
            margin: 0;
            display: inline;
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
    </style>
</head>
<body>

<?php if ($flash !== null): ?>
    <div class="message message-<?= $flash[0] === 'succes' ? 'succes' : 'erreur' ?>">
        <?= htmlspecialchars($flash[1]) ?>
    </div>
<?php endif; ?>

<?php if ($medecin !== null): ?>

    <h2>Modifier mon profil</h2>

    <!-- Spécialités -->
    <div class="card">
        <h3>Mes spécialités</h3>
        <?php if (empty($specialites)): ?>
            <p>Aucune spécialité renseignée.</p>
        <?php else: ?>
            <ul class="item-list">
                <?php foreach ($specialites as $s): ?>
                    <li class="item-row">
                        <span><?= htmlspecialchars($s['libelle_specialite']) ?></span>
                        <form method="post" class="form-retrait">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                            <input type="hidden" name="action" value="retirer_specialite">
                            <input type="hidden" name="id" value="<?= (int) $s['id_specialite'] ?>">
                            <button type="submit" class="btn btn-danger" style="min-height: 32px; padding: 5px 12px; font-size: 12px;">Retirer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($specialitesDispo)): ?>
            <form method="post" class="form-row" style="margin-top: 10px;">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                <input type="hidden" name="action" value="ajouter_specialite">
                <div class="form-group">
                    <select name="id" class="form-control" required>
                        <option value="">-- Choisir une spécialité --</option>
                        <?php foreach ($specialitesDispo as $s): ?>
                            <option value="<?= (int) $s['id_specialite'] ?>">
                                <?= htmlspecialchars($s['libelle_specialite']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Ajouter</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Hôpitaux d'exercice -->
    <div class="card">
        <h3>Mes hôpitaux d'exercice</h3>
        <?php if (empty($hopitaux)): ?>
            <p>Aucun hôpital renseigné.</p>
        <?php else: ?>
            <ul class="item-list">
                <?php foreach ($hopitaux as $h): ?>
                    <li class="item-row">
                        <span><strong><?= htmlspecialchars($h['nom_hopital']) ?></strong> (<?= htmlspecialchars($h['commune']) ?>)</span>
                        <form method="post" class="form-retrait">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                            <input type="hidden" name="action" value="retirer_hopital">
                            <input type="hidden" name="id" value="<?= (int) $h['id_hopital'] ?>">
                            <button type="submit" class="btn btn-danger" style="min-height: 32px; padding: 5px 12px; font-size: 12px;">Retirer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($hopitauxDispo)): ?>
            <form method="post" class="form-row" style="margin-top: 10px;">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                <input type="hidden" name="action" value="ajouter_hopital">
                <div class="form-group">
                    <select name="id" class="form-control" required>
                        <option value="">-- Choisir un hôpital --</option>
                        <?php foreach ($hopitauxDispo as $h): ?>
                            <option value="<?= (int) $h['id_hopital'] ?>">
                                <?= htmlspecialchars($h['nom_hopital']) ?> (<?= htmlspecialchars($h['commune']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Ajouter</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Établissements d'enseignement -->
    <div class="card">
        <h3>Établissements d'enseignement</h3>
        <?php if (empty($etablissements)): ?>
            <p>Vous n'êtes rattaché à aucun établissement d'enseignement.</p>
        <?php else: ?>
            <ul class="item-list">
                <?php foreach ($etablissements as $e): ?>
                    <li class="item-row">
                        <span>
                            <strong><?= htmlspecialchars($e['nom_etablissement']) ?></strong>
                            <?php if (!empty($e['adresse'])): ?>
                                <span style="color: var(--text-muted); font-size: 12px;">(<?= htmlspecialchars($e['adresse']) ?>)</span>
                            <?php endif; ?>
                        </span>
                        <form method="post" action="../src/traitement/traitement_enseigner.php" class="form-retrait">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="id_etablissement" value="<?= (int) $e['id_etablissement'] ?>">
                            <button type="submit" class="btn btn-danger" style="min-height: 32px; padding: 5px 12px; font-size: 12px;">Retirer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div style="margin-top: 10px;">
            <h4>Ajouter un établissement</h4>
            <form method="get" action="modifier_profil.php" class="form-row">
                <div class="form-group">
                    <input type="text" name="q" class="form-control" value="<?= htmlspecialchars($recherche ?? '') ?>" placeholder="Nom de l'établissement">
                </div>
                <button type="submit" class="btn">Rechercher</button>
            </form>
        </div>

        <?php if (($recherche ?? '') !== ''): ?>
            <div style="margin-top: 10px;">
                <?php if (empty($resultats)): ?>
                    <?php if ($dejaAssocie ?? false): ?>
                        <p>Les établissements correspondant à cette recherche sont déjà associés à votre profil.</p>
                    <?php else: ?>
                        <p>Aucun établissement trouvé pour « <?= htmlspecialchars($recherche) ?> ».</p>
                    <?php endif; ?>
                    <p style="font-size: 13px; color: var(--text-muted);">
                        Votre établissement n'apparaît pas dans la liste ? Contactez un gestionnaire pour demander son ajout.
                    </p>
                <?php else: ?>
                    <ul class="item-list">
                        <?php foreach ($resultats as $e): ?>
                            <li class="item-row">
                                <span><?= htmlspecialchars($e->getNom()) ?></span>
                                <form method="post" action="../src/traitement/traitement_enseigner.php" class="form-retrait">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                    <input type="hidden" name="action" value="ajouter">
                                    <input type="hidden" name="id_etablissement" value="<?= (int) $e->getIdEtablissement() ?>">
                                    <button type="submit" class="btn" style="min-height: 32px; padding: 5px 12px; font-size: 12px;">Ajouter</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <p style="margin-top: 20px;">
        <a href="profil.php" class="btn btn-ghost">← Retour à mon profil</a>
    </p>

<?php endif; ?>

</body>
</html>