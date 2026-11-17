<?php
ob_start();
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
<?php require __DIR__ . '/../views/layouts/header.php'; ?>

<h1 class="page-title">Postuler à une offre</h1>
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

            <div class="form-group space-after-lg">
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

<?php require __DIR__ . '/../views/layouts/footer.php'; ?>
