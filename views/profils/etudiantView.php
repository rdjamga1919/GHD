<?php
// Vue du profil étudiant - GHD-HSP
?>

<h1 class="page-title">Mon profil étudiant</h1>

<div class="card">
    <div class="profile-field">
        <span class="profile-label">Nom</span>
        <span class="profile-value"><?= htmlspecialchars($etudiant->getNom()) ?></span>
    </div>

    <div class="profile-field">
        <span class="profile-label">Prénom</span>
        <span class="profile-value"><?= htmlspecialchars($etudiant->getPrenom()) ?></span>
    </div>

    <div class="profile-field">
        <span class="profile-label">Email</span>
        <span class="profile-value"><?= htmlspecialchars($etudiant->getEmail()) ?></span>
    </div>

    <div class="profile-field">
        <span class="profile-label">Formation</span>
        <span class="profile-value"><?= htmlspecialchars($etudiant->getFormation() ?? 'Non renseignée') ?></span>
    </div>

    <div class="profile-field">
        <span class="profile-label">Établissement</span>
        <span class="profile-value"><?= htmlspecialchars($etablissement !== null ? $etablissement->getNom() : 'Non renseigné') ?></span>
    </div>

    <div class="actions">
        <a href="modifier_profil.php" class="btn">Modifier mon profil</a>
        <a href="mes_candidatures.php" class="btn btn-ghost">Voir mes candidatures</a>
    </div>
</div>
