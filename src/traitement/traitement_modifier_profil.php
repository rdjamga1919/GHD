<?php
// public/entreprise.php
require_once __DIR__ . '/../src/traitement/traitement_verif_session.php';
require_once __DIR__ . '/../src/bdd/Bdd.new.php';
require_once __DIR__ . '/../src/repository/EntrepriseRepository.php';
require_once __DIR__ . '/../src/repository/PartenaireRepository.php';

// Récupération de l'ID de l'entreprise depuis l'URL
$entrepriseId = $_GET['id'] ?? null;

if (!$entrepriseId) {
    header('Location: profil.php?erreur=Entreprise non spécifiée.');
    exit();
}

$entrepriseRepo = new EntrepriseRepository();
$partenaireRepo = new PartenaireRepository();

// Récupération des détails de l'entreprise et des partenaires associés
$entreprise = $entrepriseRepo->getEntrepriseById($entrepriseId);
$partenaires = $partenaireRepo->getPartenairesByEntrepriseId($entrepriseId);

if (!$entreprise) {
    header('Location: profil.php?erreur=Entreprise introuvable.');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Entreprise - <?= htmlspecialchars($entreprise['nom']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Fiche Entreprise</h1>

    <div class="entreprise-details">
        <h2><?= htmlspecialchars($entreprise['nom']) ?></h2>
        <p><strong>Adresse :</strong> <?= htmlspecialchars($entreprise['adresse'] ?? 'Non renseignée') ?></p>
        <p>
            <strong>Site web :</strong>
            <?php if (!empty($entreprise['site_web'])): ?>
                <a href="<?= htmlspecialchars($entreprise['site_web']) ?>" target="_blank">
                    <?= htmlspecialchars($entreprise['site_web']) ?>
                </a>
            <?php else: ?>
                Non renseigné
            <?php endif; ?>
        </p>
    </div>

    <div class="partenaires-section">
        <h3>Partenaires rattachés à cette entreprise</h3>
        <?php if (!empty($partenaires)): ?>
            <ul>
                <?php foreach ($partenaires as $p): ?>
                    <li>
                        <?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?>
                        (<em><?= htmlspecialchars($p['poste']) ?></em>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun partenaire rattaché pour le moment.</p>
        <?php endif; ?>
    </div>

    <a href="profil.php" class="btn">Retour au profil</a>
</div>
</body>
</html>