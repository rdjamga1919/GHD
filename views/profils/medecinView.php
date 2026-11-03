<?php
// Vue du profil médecin - GHD-HSP
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil médecin – GHD-HSP</title>

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
            margin-bottom: 24px;
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

        p {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.5;
            margin: 0;
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

        .profile-field {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #edf1f3;
            font-size: 14px;
        }

        .profile-field:last-child {
            border-bottom: none;
        }

        .profile-label {
            color: var(--text-muted);
            font-weight: 600;
        }

        .profile-value {
            color: var(--text);
            font-weight: 500;
            text-align: right;
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

        .tag--medecin {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 7px;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 11px;
            font-weight: 700;
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
    </style>
</head>
<body>

<h2>Mon profil médecin</h2>

<div class="card">
    <div class="profile-field">
        <span class="profile-label">Nom</span>
        <span class="profile-value"><?= htmlspecialchars($medecin->getNom()) ?></span>
    </div>
    <div class="profile-field">
        <span class="profile-label">Prénom</span>
        <span class="profile-value"><?= htmlspecialchars($medecin->getPrenom()) ?></span>
    </div>
    <div class="profile-field">
        <span class="profile-label">Email</span>
        <span class="profile-value"><?= htmlspecialchars($medecin->getEmail()) ?></span>
    </div>
    <div class="profile-field">
        <span class="profile-label">N° RPPS</span>
        <span class="profile-value"><?= htmlspecialchars($medecin->getNumeroRpps()) ?></span>
    </div>
</div>

<div class="card">
    <h3>Spécialités</h3>
    <?php if (empty($specialites)): ?>
        <p>Aucune spécialité renseignée.</p>
    <?php else: ?>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <?php foreach ($specialites as $s): ?>
                <span class="tag--medecin"><?= htmlspecialchars($s['libelle_specialite']) ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Hôpitaux d'exercice</h3>
    <?php if (empty($hopitaux)): ?>
        <p>Aucun hôpital renseigné.</p>
    <?php else: ?>
        <ul class="item-list">
            <?php foreach ($hopitaux as $h): ?>
                <li class="item-row">
                    <span><strong><?= htmlspecialchars($h['nom_hopital']) ?></strong></span>
                    <span style="color: var(--text-muted); font-size: 12px;"><?= htmlspecialchars($h['commune']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Établissements d'enseignement</h3>
    <?php if (empty($etablissements)): ?>
        <p>Vous n'êtes rattaché à aucun établissement d'enseignement.</p>
    <?php else: ?>
        <ul class="item-list">
            <?php foreach ($etablissements as $e): ?>
                <li class="item-row">
                    <span><strong><?= htmlspecialchars($e['nom_etablissement']) ?></strong></span>
                    <?php if (!empty($e['adresse'])): ?>
                        <span style="color: var(--text-muted); font-size: 12px;"><?= htmlspecialchars($e['adresse']) ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<div style="margin-top: 20px;">
    <a href="modifier_profil.php" class="btn">Modifier mon profil</a>
</div>

</body>
</html>