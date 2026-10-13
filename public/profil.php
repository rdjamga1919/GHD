<?php
// public/profil.php
require_once __DIR__ . '/../src/traitement/traitement_verif_session.php';
// $partenaire contient les informations de l'utilisateur connecté
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - GHD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Mon Profil Partenaire</h1>

    <div class="profile-info">
        <p><strong>Nom :</strong> <?= htmlspecialchars($partenaire['nom'] ?? '') ?></p>
        <p><strong>Prénom :</strong> <?= htmlspecialchars($partenaire['prenom'] ?? '') ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($partenaire['email'] ?? '') ?></p>
        <p><strong>Poste :</strong> <?= htmlspecialchars($partenaire['poste'] ?? '') ?></p>
        <p>
            <strong>Entreprise :</strong>
            <a href="entreprise.php?id=<?= $partenaire['entreprise_id'] ?? '' ?>">
                <?= htmlspecialchars($partenaire['nom_entreprise'] ?? 'Voir l\'entreprise') ?>
            </a>
        </p>
    </div>

    <a href="modifier_profil.php" class="btn">Modifier mon poste</a>
</div>
</body>
</html>