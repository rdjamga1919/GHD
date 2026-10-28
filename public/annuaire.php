<?php

session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/UtilisateurRepository.php';

requireLogin();

// Récupérer tous les utilisateurs
$repo = new UtilisateurRepository();
$utilisateurs = $repo->findAll();

// Récupérer les critères de recherche
$recherche = trim($_GET['recherche'] ?? '');
$role = $_GET['role'] ?? '';

// Filtrer les utilisateurs
$resultats = [];

foreach ($utilisateurs as $utilisateur) {

    // Ne pas afficher les comptes non validés
    if (!$utilisateur->estValide()) {
        continue;
    }

    // Afficher uniquement les trois rôles de l'annuaire
    if (!in_array($utilisateur->getRole(), ['etudiant', 'medecin', 'partenaire'], true)) {
        continue;
    }

    // Filtrer par rôle
    if ($role !== '' && $utilisateur->getRole() !== $role) {
        continue;
    }

    // Rechercher dans le nom et le prénom
    $nomComplet = $utilisateur->getNom() . ' ' . $utilisateur->getPrenom();

    if ($recherche !== '' && stripos($nomComplet, $recherche) === false) {
        continue;
    }

    $resultats[] = $utilisateur;
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annuaire - GHD-HSP</title>

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
            --info: #1d4ed8;
            --info-bg: #eff6ff;
            --success: #15803d;
            --success-bg: #f0fdf4;
            --warning: #b45309;
            --warning-bg: #fffbeb;
            --danger: #b91c1c;
            --danger-bg: #fef2f2;
            --role-etudiant-bg: #e0f2fe;
            --role-etudiant-text: #0369a1;
            --role-medecin-bg: #dcfce7;
            --role-medecin-text: #15803d;
            --role-partenaire-bg: #f3e8ff;
            --role-partenaire-text: #6b21a8;
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
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
            background-color: var(--surface-soft);
            color: var(--text);
        }

        h1 {
            color: var(--navy);
            font-size: 24px;
            margin-bottom: 8px;
        }

        p {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.5;
        }

        /* GHD-HSP — Composants réutilisables intégrés */

        .card {
            display: flex;
            flex-direction: column;
            gap: 18px;
            padding: 26px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 3px 12px rgba(16, 47, 64, 0.025);
            margin: 25px 0;
        }

        form {
            display: flex;
            gap: var(--space-md);
            flex-wrap: wrap;
            align-items: center;
        }

        .form-control {
            display: block;
            min-height: 46px;
            padding: 11px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--surface);
            color: var(--text);
            font: inherit;
            font-size: 14px;
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

        input[type="text"].form-control, select.form-control {
            flex: 1;
            min-width: 220px;
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

        .btn-secondary {
            background: var(--surface);
            border-color: var(--border);
            box-shadow: none;
            color: var(--primary);
        }

        .btn-secondary:hover {
            background: var(--surface-teal);
            border-color: var(--border-accent);
            color: var(--primary);
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            margin-top: 15px;
        }

        .table {
            width: 100%;
            min-width: 520px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th,
        .table td {
            padding: 14px 16px;
            text-align: left;
            vertical-align: middle;
            border-bottom: 1px solid #edf1f3;
        }

        .table th {
            background: var(--surface-soft);
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .table td {
            color: var(--text-muted);
            font-size: 13px;
        }

        .table tbody tr:hover {
            background: #f5fafb;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 700;
        }

        .tag--etudiant {
            background: var(--role-etudiant-bg);
            color: var(--role-etudiant-text);
        }

        .tag--medecin {
            background: var(--role-medecin-bg);
            color: var(--role-medecin-text);
        }

        .tag--partenaire {
            background: var(--role-partenaire-bg);
            color: var(--role-partenaire-text);
        }

        .results-count {
            margin: 15px 0 5px 0;
            font-weight: 600;
            color: var(--navy);
        }
    </style>
</head>

<body>

<h1>Annuaire GHD</h1>
<p>Retrouvez les membres du réseau hospitalo-universitaire.</p>

<div class="card">
    <form method="GET" action="annuaire.php">
        <input
                type="text"
                name="recherche"
                class="form-control"
                placeholder="Rechercher un nom ou prénom"
                value="<?= htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8') ?>"
        >

        <select name="role" class="form-control">
            <option value="">Tous les profils</option>
            <option value="etudiant" <?= $role === 'etudiant' ? 'selected' : '' ?>>Étudiants</option>
            <option value="medecin" <?= $role === 'medecin' ? 'selected' : '' ?>>Médecins</option>
            <option value="partenaire" <?= $role === 'partenaire' ? 'selected' : '' ?>>Partenaires</option>
        </select>

        <button type="submit" class="btn">Rechercher</button>
        <a href="annuaire.php" class="btn btn-secondary">Réinitialiser</a>
    </form>
</div>

<p class="results-count"><?= count($resultats) ?> résultat(s)</p>

<div class="table-wrap">
    <table class="table">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Rôle</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($resultats as $utilisateur): ?>
            <?php
            $roleSlug = strtolower($utilisateur->getRole());
            ?>
            <tr>
                <td><?= htmlspecialchars($utilisateur->getNom(), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($utilisateur->getPrenom(), ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                            <span class="tag tag--<?= $roleSlug ?>">
                                <?= htmlspecialchars(ucfirst($utilisateur->getRole()), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                </td>
            </tr>
        <?php endforeach; ?>

        <?php if (empty($resultats)): ?>
            <tr>
                <td colspan="3" style="text-align: center; padding: 30px;">Aucun utilisateur trouvé.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<p style="margin-top: 25px;">
    <a href="profil.php" class="btn btn-secondary">Retour à mon profil</a>
</p>

</body>
</html>