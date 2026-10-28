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
    <title>Postuler - GHD</title>
</head>
<body>

<h1>Postuler à une offre</h1>

<h2><?= htmlspecialchars($offre['titre']) ?></h2>

<p>
    Entreprise :
    <?= htmlspecialchars($offre['nom_entreprise'] ?? 'Non renseignée') ?>
</p>

<?php if ($aDejaPostule): ?>

    <p>Vous avez déjà postulé à cette offre.</p>

    <p>
        <a href="mes_candidatures.php">
            Voir mes candidatures
        </a>
    </p>

<?php else: ?>

    <?php foreach ($erreurs as $erreur): ?>
        <p style="color: red;">
            <?= htmlspecialchars($erreur) ?>
        </p>
    <?php endforeach; ?>

    <form method="POST"
          action="../src/traitement/traitement_candidature.php">

        <input type="hidden"
               name="id_offre"
               value="<?= (int) $idOffre ?>">

        <input type="hidden"
               name="csrf"
               value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

        <label for="motivation">Message de motivation</label>

        <br><br>

        <textarea
            id="motivation"
            name="motivation"
            rows="8"
            cols="60"
            minlength="20"
            maxlength="2000"
            required
        ><?= htmlspecialchars($motivation) ?></textarea>

        <br><br>

        <button type="submit">
            Envoyer ma candidature
        </button>

    </form>

<?php endif; ?>

<p>
    <a href="offre_detail.php?id=<?= (int) $idOffre ?>">
        Retour à l'offre
    </a>
</p>

</body>
</html>