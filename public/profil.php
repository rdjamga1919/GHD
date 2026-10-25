<?php
require_once __DIR__ . '/../src/traitement/traitement_verif_session.php';
exigerConnexion();

$medecin = null;
$specialites = [];
$hopitaux = [];

if ($_SESSION['role'] === 'medecin') {
    require_once __DIR__ . '/../src/repository/MedecinRepository.php';

    $repo = new MedecinRepository();
    $medecin = $repo->findById((int) $_SESSION['utilisateur_id']);

    if ($medecin !== null) {
        $specialites = $repo->getSpecialites($medecin->getIdMedecin());
        $hopitaux = $repo->getHopitaux($medecin->getIdMedecin());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon profil</title>
</head>
<body>

<?php if ($medecin !== null): ?>

    <h2>Mon profil</h2>
    <p>Nom : <?= htmlspecialchars($medecin->getNom()) ?></p>
    <p>Prénom : <?= htmlspecialchars($medecin->getPrenom()) ?></p>
    <p>Email : <?= htmlspecialchars($medecin->getEmail()) ?></p>
    <p>N° RPPS : <?= htmlspecialchars($medecin->getNumeroRpps()) ?></p>

    <h3>Spécialités</h3>
    <?php if (empty($specialites)): ?>
        <p>Aucune spécialité renseignée.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($specialites as $s): ?>
                <li><?= htmlspecialchars($s['libelle_specialite']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h3>Hôpitaux d'exercice</h3>
    <?php if (empty($hopitaux)): ?>
        <p>Aucun hôpital renseigné.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($hopitaux as $h): ?>
                <li><?= htmlspecialchars($h['nom_hopital']) ?> (<?= htmlspecialchars($h['commune']) ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <a href="modifier_profil.php">Modifier mon profil</a>

<?php endif; ?>

</body>
</html>