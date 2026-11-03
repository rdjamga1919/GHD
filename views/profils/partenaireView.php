<?php
// Vue du profil partenaire - GHD-HSP
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil partenaire – GHD-HSP</title>

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
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: var(--surface-soft);
            color: var(--text);
        }

        h1 {
            color: var(--navy);
            font-size: 24px;
            margin-bottom: 24px;
            letter-spacing: -0.02em;
        }

        h2 {
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

        .tag--partenaire {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 7px;
            background: #f3e8ff;
            color: #6b21a8;
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
    </style>
</head>
<body>

<h1>Mon profil partenaire</h1>

<div class="card">
    <h2>Informations personnelles</h2>
    <div class="profile-field">
        <span class="profile-label">Nom</span>
        <span class="profile-value"><?= htmlspecialchars($partenaire->getNom()) ?></span>
    </div>
    <div class="profile-field">
        <span class="profile-label">Prénom</span>
        <span class="profile-value"><?= htmlspecialchars($partenaire->getPrenom()) ?></span>
    </div>
    <div class="profile-field">
        <span class="profile-label">Email</span>
        <span class="profile-value"><?= htmlspecialchars($partenaire->getEmail()) ?></span>
    </div>
    <div class="profile-field">
        <span class="profile-label">Poste</span>
        <span class="profile-value"><?= htmlspecialchars($partenaire->getPoste()) ?></span>
    </div>
</div>

<div class="card">
    <h2>Mon entreprise</h2>

    <?php if ($entreprise !== null): ?>
        <div class="profile-field">
            <span class="profile-label">Nom</span>
            <span class="profile-value"><strong><?= htmlspecialchars($entreprise->getNom()) ?></strong></span>
        </div>
        <div class="profile-field">
            <span class="profile-label">Adresse</span>
            <span class="profile-value"><?= htmlspecialchars($entreprise->getAdresse()) ?></span>
        </div>
        <div class="profile-field">
            <span class="profile-label">Site web</span>
            <span class="profile-value">
                <a href="<?= htmlspecialchars($entreprise->getSiteWeb()) ?>" target="_blank" rel="noopener noreferrer" style="color: var(--primary);">
                    <?= htmlspecialchars($entreprise->getSiteWeb()) ?>
                </a>
            </span>
        </div>

        <div style="margin-top: 12px;">
            <a href="entreprise.php?id=<?= (int) $entreprise->getIdEntreprise() ?>" class="btn btn-ghost">
                Voir mon entreprise
            </a>
        </div>
    <?php else: ?>
        <p>Aucune entreprise trouvée.</p>
    <?php endif; ?>
</div>

<div>
    <a href="modifier_profil.php" class="btn">Modifier mon profil</a>
</div>

</body>
</html>