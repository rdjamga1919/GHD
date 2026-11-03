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
    <title>Candidatures reçues – GHD-HSP</title>
    <link rel="stylesheet" href="variables.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
</head>
<body>

<div class="container" style="max-width: 900px;">
    <div class="page-header">
        <h1 class="page-title">Candidatures reçues</h1>
        <h2 style="color: var(--primary); font-size: 18px; margin-bottom: 0; font-weight: 600;"><?= htmlspecialchars($offre['titre']) ?></h2>
    </div>

    <p class="lead" style="margin-bottom: var(--space-lg);">
        <strong>Nombre de candidatures :</strong> <?= count($candidatures) ?>
    </p>

    <?php if (empty($candidatures)): ?>

        <div class="card" style="text-align: center; padding: 40px;">
            <p class="lead" style="margin: 0;">Aucune candidature reçue pour cette offre.</p>
        </div>

    <?php else: ?>

        <div style="display: flex; flex-direction: column; gap: var(--space-md);">
            <?php foreach ($candidatures as $candidature): ?>
                <div class="card">
                    <div class="split" style="align-items: flex-start;">
                        <div>
                            <h3 class="card-title" style="margin: 0;">
                                <?= htmlspecialchars($candidature['prenom']) ?>
                                <?= htmlspecialchars($candidature['nom']) ?>
                            </h3>
                            <div class="cluster" style="margin-top: 6px; font-size: 13px; color: var(--text-muted);">
                                <span>📧 <?= htmlspecialchars($candidature['email']) ?></span>
                                <span>📅 Postulé le : <?= htmlspecialchars($candidature['date_candidature']) ?></span>
                            </div>
                        </div>

                        <?php
                        $statut = strtolower($candidature['statut']);
                        $tagClass = 'tag';
                        if ($statut === 'acceptée' || $statut === 'valide') {
                            $tagClass = 'tag tag--success';
                        } elseif ($statut === 'en attente') {
                            $tagClass = 'tag tag--warning';
                        }
                        ?>
                        <span class="<?= $tagClass ?>">
                            <?= htmlspecialchars(ucfirst($candidature['statut'])) ?>
                        </span>
                    </div>

                    <?php if (!empty($candidature['motivation'])): ?>
                        <div style="background: var(--surface-soft); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 16px; margin-top: 8px;">
                            <h4 style="color: var(--navy); font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px;">Message de motivation</h4>
                            <p class="lead" style="margin: 0;"><?= nl2br(htmlspecialchars($candidature['motivation'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

    <div style="margin-top: var(--space-lg);">
        <a href="offre_detail.php?id=<?= (int) $idOffre ?>" class="btn btn-secondary">
            ← Retour à l'offre
        </a>
    </div>
</div>

</body>
</html>