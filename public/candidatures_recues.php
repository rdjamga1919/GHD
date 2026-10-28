<?php
session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/CandidatureRepository.php';
require_once __DIR__ . '/../src/repository/OffreRepository.php';

requireRole('partenaire');

$idOffre = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$idOffre || $idOffre <= 0) {
    http_response_code(404);
    exit("Offre introuvable.");
}

$idUtilisateur = (int) $_SESSION['utilisateur_id'];

$offreRepo = new OffreRepository();

$offre = $offreRepo->getOffreDuProprietaire(
        $idOffre,
        $idUtilisateur
);

if ($offre === null) {
    http_response_code(403);
    exit("Vous n'avez pas accès aux candidatures de cette offre.");
}

$candidatureRepo = new CandidatureRepository();

$candidatures = $candidatureRepo->getCandidaturesParOffre(
        $idOffre,
        $idUtilisateur
);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatures reçues - GHD-HSP</title>

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
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background-color: var(--surface-soft);
            color: var(--text);
        }

        h1 {
            color: var(--navy);
            font-size: 24px;
            margin-bottom: 4px;
        }

        h2 {
            color: var(--primary);
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        p {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.5;
        }

        /* Composants GHD-HSP */

        .card {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 24px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 3px 12px rgba(16, 47, 64, 0.025);
            margin-bottom: 20px;
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .card:hover {
            border-color: var(--border-strong);
            box-shadow: var(--shadow);
        }

        .card-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: var(--space-md);
            flex-wrap: wrap;
        }

        .candidate-name {
            color: var(--navy);
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin: 0;
        }

        .candidate-meta {
            display: flex;
            gap: var(--space-md);
            font-size: 13px;
            color: var(--text-muted);
            flex-wrap: wrap;
        }

        .motivation-box {
            background: var(--surface-soft);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 16px;
            margin-top: 8px;
        }

        .motivation-box h4 {
            color: var(--navy);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 8px;
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

        .tag--warning {
            background: var(--warning-bg);
            color: var(--warning);
        }

        .tag--info {
            background: var(--info-bg);
            color: var(--info);
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

        .alert {
            padding: 15px 17px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            color: var(--text-muted);
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<h1>Candidatures reçues</h1>
<h2><?= htmlspecialchars($offre['titre']) ?></h2>

<p style="margin-bottom: 20px;">
    <strong>Nombre de candidatures :</strong> <?= count($candidatures) ?>
</p>

<?php if (empty($candidatures)): ?>

    <div class="alert">
        Aucune candidature reçue pour cette offre.
    </div>

<?php else: ?>

    <?php foreach ($candidatures as $candidature): ?>
        <div class="card">
            <div class="card-header-flex">
                <div>
                    <h3 class="candidate-name">
                        <?= htmlspecialchars($candidature['prenom']) ?>
                        <?= htmlspecialchars($candidature['nom']) ?>
                    </h3>
                    <div class="candidate-meta" style="margin-top: 4px;">
                        <span>📧 <?= htmlspecialchars($candidature['email']) ?></span>
                        <span>📅 Postulé le : <?= htmlspecialchars($candidature['date_candidature']) ?></span>
                    </div>
                </div>

                <?php
                $statut = strtolower($candidature['statut']);
                $tagClass = 'tag--info';
                if ($statut === 'acceptée' || $statut === 'valide') $tagClass = 'tag--success';
                if ($statut === 'en attente') $tagClass = 'tag--warning';
                ?>
                <span class="tag <?= $tagClass ?>">
                        <?= htmlspecialchars(ucfirst($candidature['statut'])) ?>
                    </span>
            </div>

            <?php if (!empty($candidature['motivation'])): ?>
                <div class="motivation-box">
                    <h4>Message de motivation</h4>
                    <p><?= nl2br(htmlspecialchars($candidature['motivation'])) ?></p>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

<p style="margin-top: 30px;">
    <a href="offre_detail.php?id=<?= (int) $idOffre ?>" class="btn btn-secondary">
        ← Retour à l'offre
    </a>
</p>

</body>
</html>