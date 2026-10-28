<?php
session_start();

require_once __DIR__ . '/../src/bdd/Bdd.php';
require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/CandidatureRepository.php';
require_once __DIR__ . '/../src/repository/EtudiantRepository.php';

// Visiteur : redirigé vers la connexion. Autre rôle qu'étudiant : 403.
requireRole('etudiant');

// L'étudiant est celui de la session, confirmé en BDD.
$idUtilisateur = (int) $_SESSION['utilisateur_id'];
$etudiant = (new EtudiantRepository())->findById($idUtilisateur);
if ($etudiant === null || !$etudiant->estValide()) {
    http_response_code(403);
    exit('Accès refusé.');
}

$erreurs = [];
$candidatures = [];
try {
    $candidatures = (new CandidatureRepository())->findByEtudiant($idUtilisateur);
} catch (PDOException $e) {
    error_log("Erreur mes_candidatures : " . $e->getMessage());
    $erreurs[] = "Impossible d'afficher vos candidatures pour le moment.";
}

// Statuts de la BDD (ENUM) -> libellé et classe CSS GHD-HSP
$statuts = [
        'en_attente' => ['En attente', 'tag tag--warning'],
        'accepte'    => ['Acceptée', 'tag tag--success'],
        'bloque'     => ['Refusée', 'tag tag--error'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes candidatures – GHD-HSP</title>

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
            --info: #1d4ed8;
            --info-bg: #eff6ff;
            --success: #15803d;
            --success-bg: #f0fdf4;
            --warning: #b45309;
            --warning-bg: #fffbeb;
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
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background-color: var(--surface-soft);
            color: var(--text);
        }

        .page-header h1 {
            color: var(--navy);
            font-size: 24px;
            margin-bottom: 20px;
            letter-spacing: -0.02em;
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
        }

        .card-header {
            color: var(--navy);
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
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
            min-height: 36px;
            padding: 8px 14px;
            font-size: 12px;
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

        .alert-error {
            background: var(--danger-bg);
            border: 1px solid #f3d8dc;
            color: var(--danger);
        }

        /* Badges / Tags GHD-HSP */
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

        .tag--warning {
            background: var(--warning-bg);
            color: var(--warning);
        }

        .tag--error {
            background: var(--danger-bg);
            color: var(--danger);
        }

        .tag--inconnu {
            background: var(--surface-disabled);
            color: var(--text-muted);
        }

        /* Tableau GHD-HSP */
        .table-wrap {
            width: 100%;
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--surface);
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            vertical-align: middle;
            border-bottom: 1px solid #edf1f3;
        }

        th {
            background: var(--surface-soft);
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        td {
            color: var(--text-muted);
            font-size: 13px;
        }

        tbody tr:hover {
            background: #f5fafb;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .empty-msg {
            text-align: center;
            padding: 30px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

<main class="content">

    <div class="page-header">
        <h1>Mes candidatures</h1>
    </div>

    <?php if (!empty($erreurs)): ?>
        <div class="alert alert-error">
            <?php foreach ($erreurs as $msg): ?>
                <p><?= htmlspecialchars($msg) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">Mes candidatures (<?= count($candidatures) ?>)</div>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Offre</th>
                    <th>Entreprise</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Détail</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($candidatures)): ?>
                    <?php foreach ($candidatures as $c): ?>
                        <?php
                        $statut = $statuts[$c['statut']] ?? [$c['statut'], 'tag tag--inconnu'];
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($c['titre_offre']) ?></strong></td>
                            <td><?= $c['nom_entreprise'] !== null ? htmlspecialchars($c['nom_entreprise']) : 'Non renseignée' ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($c['date_candidature']))) ?></td>
                            <td><span class="<?= htmlspecialchars($statut[1]) ?>"><?= htmlspecialchars($statut[0]) ?></span></td>
                            <td>
                                <a class="btn btn-ghost" href="offre_detail.php?id=<?= (int) $c['id_offre'] ?>">Voir l'offre</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty-msg">
                            Vous n'avez encore postulé à aucune offre.<br><br>
                            <a href="offres.php" class="btn">Consulter les offres</a>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

</body>
</html>