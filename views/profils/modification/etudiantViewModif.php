<?php
// Vue de modification du profil étudiant - GHD-HSP
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mon profil étudiant – GHD-HSP</title>

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
            max-width: 600px;
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

        /* GHD-HSP Composants */
        .card {
            display: flex;
            flex-direction: column;
            gap: 20px;
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

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
        }

        label {
            color: var(--text);
            font-size: 13px;
            line-height: 21px;
            font-weight: 600;
        }

        .form-control, input[type="text"], select {
            display: block;
            width: 100%;
            min-height: 46px;
            padding: 11px 14px;
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
            min-height: 44px;
            padding: 11px 18px;
            border: 1px solid var(--primary);
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #147786, var(--primary));
            box-shadow: 0 3px 8px rgba(18, 103, 121, 0.10);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
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
            width: auto;
        }

        .btn-ghost:hover {
            background: var(--surface-teal);
            border-color: var(--border-accent);
            color: var(--primary);
        }

        .alert {
            padding: 15px 17px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.5;
        }

        .alert-success {
            background: var(--success-bg);
            border: 1px solid #d7eddf;
            color: var(--success);
        }

        .alert-error {
            background: var(--danger-bg);
            border: 1px solid #f3d8dc;
            color: var(--danger);
        }
    </style>
</head>
<body>

<?php if ($flash !== null): ?>
    <div class="alert <?= $flash[0] === 'succes' ? 'alert-success' : 'alert-error' ?>">
        <?= htmlspecialchars($flash[1]) ?>
    </div>
<?php endif; ?>

<h2>Modifier mon profil étudiant</h2>

<div class="card">
    <form method="post">

        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

        <div class="form-group">
            <label for="formation">Ma formation :</label>
            <input
                    type="text"
                    id="formation"
                    name="formation"
                    class="form-control"
                    value="<?= htmlspecialchars($etudiant->getFormation()) ?>"
                    required
            >
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label for="etablissement">Mon établissement :</label>
            <select name="etablissement" id="etablissement" class="form-control" required>
                <option value="">-- Choisir un établissement --</option>
                <?php foreach ($etablissements as $etablissement): ?>
                    <option
                            value="<?= (int) $etablissement->getIdEtablissement() ?>"
                            <?= $etudiant->getRefEtablissement() == $etablissement->getIdEtablissement() ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($etablissement->getNom()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn">Enregistrer</button>

    </form>
</div>

<p>
    <a href="profil.php" class="btn btn-ghost">← Retour à mon profil</a>
</p>

</body>
</html>