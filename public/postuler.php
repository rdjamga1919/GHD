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
    <title>Postuler – GHD-HSP</title>
    <link rel="stylesheet" href="variables.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
</head>
<body>

<main class="container" style="max-width: 800px;">

    <div class="page-header">
        <h1 class="page-title">Postuler à une offre</h1>
        <h2 style="color: var(--primary); font-size: 18px; font-weight: 600; margin-bottom: 0;"><?= htmlspecialchars($offre['titre']) ?></h2>
    </div>

    <p class="lead" style="margin-bottom: var(--space-lg);">
        <strong>Entreprise :</strong>
        <?= htmlspecialchars($offre['nom_entreprise'] ?? 'Non renseignée') ?>
    </p>

    <div class="card">
        <?php if ($aDejaPostule): ?>

            <div class="alert alert-info">
                <div class="alert-body">
                    <span class="alert-text">Vous avez déjà postulé à cette offre.</span>
                </div>
            </div>

            <div>
                <a href="mes_candidatures.php" class="btn">
                    Voir mes candidatures
                </a>
            </div>

        <?php else: ?>

            <?php foreach ($erreurs as $erreur): ?>
                <div class="alert alert-error">
                    <div class="alert-body">
                        <span class="alert-text"><?= htmlspecialchars($erreur) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>

            <form method="POST" action="../src/traitement/traitement_candidature.php" style="display: flex; flex-direction: column; gap: var(--space-md);">

                <input type="hidden" name="id_offre" value="<?= (int) $idOffre ?>">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

                <div class="form-group">
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

                <div>
                    <button type="submit" class="btn">
                        Envoyer ma candidature
                    </button>
                </div>

            </form>

        <?php endif; ?>
    </div>

    <div>
        <a href="offre_detail.php?id=<?= (int) $idOffre ?>" class="btn btn-secondary">
            ← Retour à l'offre
        </a>
    </div>

</main>

</body>
</html>