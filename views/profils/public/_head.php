<?php
// Début commun des vues de profil public - GHD-HSP
// Variable attendue : $titrePage
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titrePage ?? 'Profil', ENT_QUOTES, 'UTF-8') ?> – GHD-HSP</title>

    <style>
        :root {
            --primary: #126779;
            --primary-hover: #0f5766;
            --navy: #102f40;
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
            --role-etudiant-bg: #e0f2fe;
            --role-etudiant-text: #0369a1;
            --role-medecin-bg: #dcfce7;
            --role-medecin-text: #15803d;
            --role-partenaire-bg: #f3e8ff;
            --role-partenaire-text: #6b21a8;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            max-width: 700px;
            margin: 40px auto;
            padding: 20px;
            background-color: var(--surface-soft);
            color: var(--text);
        }

        h1 { color: var(--navy); font-size: 24px; margin: 0 0 8px; letter-spacing: -0.02em; }
        h2 { color: var(--navy); font-size: 16px; font-weight: 700; margin: 0 0 12px; }
        p  { font-size: 14px; color: var(--text-muted); line-height: 1.5; margin: 0; }

        .page-header { margin-bottom: 24px; }

        .card {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 24px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 3px 12px rgba(16, 47, 64, 0.025);
            margin-bottom: 24px;
        }

        .profile-field {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 10px 0;
            border-bottom: 1px solid #edf1f3;
            font-size: 14px;
        }
        .profile-field:last-child { border-bottom: none; }
        .profile-label { color: var(--text-muted); font-weight: 600; }
        .profile-value { color: var(--text); font-weight: 500; text-align: right; }
        .profile-value a { color: var(--primary); }

        .tag {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 700;
        }
        .tag--etudiant   { background: var(--role-etudiant-bg);   color: var(--role-etudiant-text); }
        .tag--medecin    { background: var(--role-medecin-bg);    color: var(--role-medecin-text); }
        .tag--partenaire { background: var(--role-partenaire-bg); color: var(--role-partenaire-text); }

        .item-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
        .item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 14px;
            background: var(--surface-soft);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 13px;
        }
        .item-row small { color: var(--text-muted); font-size: 12px; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 11px 18px;
            border: 1px solid var(--primary);
            border-radius: var(--radius-md);
            background: var(--primary);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background var(--transition), transform var(--transition);
        }
        .btn:hover { background: var(--primary-hover); transform: translateY(-1px); }
        .btn-ghost { background: var(--surface); border-color: var(--border); color: var(--primary); }
        .btn-ghost:hover { background: var(--surface-teal); border-color: var(--border-accent); }
    </style>
</head>
<body>