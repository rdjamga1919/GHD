<?php
ob_start();
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
<?php require __DIR__ . '/../views/layouts/header.php'; ?>

<h1 class="page-title">Candidatures reçues</h1>
<h2><?= htmlspecialchars($offre['titre']) ?></h2>

<p class="space-after-lg">
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
                    <div class="candidate-meta space-before-xs">
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

<p class="space-before-lg">
    <a href="offre_detail.php?id=<?= (int) $idOffre ?>" class="btn btn-secondary">
        ← Retour à l'offre
    </a>
</p>

<?php require __DIR__ . '/../views/layouts/footer.php'; ?>
