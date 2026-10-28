<?php
session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/OffreRepository.php';
require_once __DIR__ . '/../src/repository/CandidatureRepository.php';

requireRole('etudiant');

$idOffre = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$idOffre || $idOffre <= 0) {
    http_response_code(404);
    exit("Offre invalide.");
}

$offre = (new OffreRepository())->getOffreAvecEntreprise($idOffre);

if ($offre === null) {
    http_response_code(404);
    exit("Cette offre n'existe pas.");
}

if ($offre['etat'] !== 'ouverte') {
    exit("Cette offre est fermée.");
}

// Vérifier si l'étudiant a déjà postulé
$candidatureRepo = new CandidatureRepository();

$aDejaPostule = $candidatureRepo->candidatureExiste(
        (int) $_SESSION['utilisateur_id'],
        (int) $idOffre
);

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$erreurs = $_SESSION['erreurs'] ?? [];
$motivation = $_SESSION['valeurs']['motivation'] ?? '';

unset($_SESSION['erreurs'], $_SESSION['valeurs']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postuler - GHD-HSP</title>

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
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: var(--surface-soft);
            color: var(--text);
        }

        h1 {
            color: var(--navy);
            font-size: 24px;
            margin-bottom: 4px;
            letter-spacing: -0.02em;
        }

        h2 {
            color: var(--primary);
            font-size: 18px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        p {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 20px;
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
        }

        label {
            color: var(--text);
            font-size: 13px;
            line-height: 21px;
            font-weight: 600;
        }

        .form-control, textarea.form-control {
            display: block;
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--surface);
            color: var(--text);
            font: inherit;
            font-size: 14px;
            box-sizing: border-box;
            resize: vertical;
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .form-control:hover, textarea.form-control:hover {
            border-color: var(--border-strong);
        }

        .form-control:focus, textarea.form-control:focus {
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

        .alert-info {
            background: var(--info-bg);
            border: 1px solid #d9e9f8;
            color: var(--info);
        }
    </style>
</head>
<body>

<h1>Postuler à une offre</h1>
<h2><?= htmlspecialchars($offre['titre']) ?></h2>
<p>
    <strong>Entreprise :</strong>
    <?= htmlspecialchars($offre['nom_entreprise'] ?? 'Non renseignée') ?>
</p>

<div class="card">
    <?php if ($aDejaPostule): ?>

        <div class="alert alert-info">
            Vous avez déjà postulé à cette offre.
        </div>

        <div>
            <a href="mes_candidatures.php" class="btn">
                Voir mes candidatures
            </a>
        </div>

    <?php else: ?>

        <?php foreach ($erreurs as $erreur): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endforeach; ?>

        <form method="POST" action="../src/traitement/traitement_candidature.php">

            <input type="hidden" name="id_offre" value="<?= (int) $idOffre ?>">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="motivation">Message de motivation</label>
                <textarea
                        id="motivation"
                        name="motivation"
                        class="form-control"
                        rows="8"
                        minlength="20"
                        maxlength="2000"
                        required
                        placeholder="Exprimez vos motivations pour ce poste (20 caractères minimum)..."
                ><?= htmlspecialchars($motivation) ?></textarea>
            </div>

            <button type="submit" class="btn">
                Envoyer ma candidature
            </button>

        </form>

    <?php endif; ?>
</div>

<p>
    <a href="offre_detail.php?id=<?= (int) $idOffre ?>" class="btn btn-ghost">
        ← Retour à l'offre
    </a>
</p>

</body>
</html>