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
    <title>Candidatures reçues - GHD</title>
</head>
<body>

<h1>Candidatures reçues</h1>

<h2><?= htmlspecialchars($offre['titre']) ?></h2>

<p>
    Nombre de candidatures : <?= count($candidatures) ?>
</p>

<?php if (empty($candidatures)): ?>

    <p>Aucune candidature reçue pour cette offre.</p>

<?php else: ?>

    <?php foreach ($candidatures as $candidature): ?>

        <hr>

        <h3>
            <?= htmlspecialchars($candidature['prenom']) ?>
            <?= htmlspecialchars($candidature['nom']) ?>
        </h3>

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
            <?= htmlspecialchars($candidature['statut']) ?>
        </p>

        <h4>Message de motivation</h4>

        <p>
            <?= nl2br(htmlspecialchars($candidature['motivation'])) ?>
        </p>

    <?php endforeach; ?>

<?php endif; ?>

<p>
    <a href="offre_detail.php?id=<?= (int) $idOffre ?>">
        Retour à l'offre
    </a>
</p>

</body>
</html>