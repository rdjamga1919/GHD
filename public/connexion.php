<?php
session_start();
$erreur = $_SESSION['erreur_connexion'] ?? null;
unset($_SESSION['erreur_connexion']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - GHD-HSP</title>

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
            --text-on-navy: #e2e8f0;
            --text-on-navy-muted: #94a3b8;
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
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-color: var(--surface-soft);
            color: var(--text);
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        h1 {
            color: var(--navy);
            font-size: 24px;
            margin-bottom: 24px;
            text-align: center;
            letter-spacing: -0.02em;
        }

        /* Composants GHD-HSP */

        .card {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 32px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 3px 12px rgba(16, 47, 64, 0.025);
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
        }

        .form-group label {
            color: var(--text);
            font-size: 13px;
            line-height: 21px;
            font-weight: 600;
        }

        .form-control {
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

        .form-control:hover {
            border-color: var(--border-strong);
        }

        .form-control:focus {
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

        .alert {
            display: flex;
            align-items: flex-start;
            gap: var(--space-ms);
            padding: 15px 17px;
            border: 1px solid #f3d8dc;
            border-radius: 12px;
            background: var(--danger-bg);
            color: var(--danger);
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h1>Connexion GHD-HSP</h1>

    <?php if ($erreur): ?>
        <div class="alert">
            <span>⚠️</span>
            <div><?= htmlspecialchars($erreur) ?></div>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="../src/traitement/traitement_connexion.php" method="POST">
            <div class="form-group" style="margin-bottom: 16px;">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="votre@email.com" required>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn">Se connecter</button>
        </form>
    </div>
</div>

</body>
</html>