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
    <title>Candidatures reçues - GHD</title>
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>
<body>

<main class="container">

    <h1 class="page-title">Candidatures reçues</h1>

    <div class="card">
        <h2><?= htmlspecialchars($offre['titre']) ?></h2>
        <p>
            Nombre de candidatures : <?= count($candidatures) ?>
        </p>
    </div>

    <?php if (empty($candidatures)): ?>

        <div class="card">
            <p>Aucune candidature reçue pour cette offre.</p>
        </div>

    <?php else: ?>

        <?php foreach ($candidatures as $candidature): ?>

            <div class="card">

                <h2>
                    <?= htmlspecialchars($candidature['prenom']) ?>
                    <?= htmlspecialchars($candidature['nom']) ?>
                </h2>

                <p>
                    Email :
                    <?= htmlspecialchars($candidature['email']) ?>
                </p>

                <p>
                    Date :
                    <?= htmlspecialchars($candidature['date_candidature']) ?>
                </p>

                <p>
                    Statut :
                    <span class="tag"><?= htmlspecialchars($candidature['statut']) ?></span>
                </p>

                <h3>Message de motivation</h3>

                <p>
                    <?= nl2br(htmlspecialchars($candidature['motivation'])) ?>
                </p>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

    <p>
        <a class="btn btn-secondary" href="offre_detail.php?id=<?= (int) $idOffre ?>">
            Retour à l'offre
        </a>
    </p>

</main>

</body>
</html>