<?php
// Vue publique d'un profil étudiant - GHD-HSP
// Variables attendues : $utilisateur, $etudiant, $etablissement
// Données volontairement non affichées : email, CV, mot de passe.
?>
<div class="page-header">
    <h1><?= htmlspecialchars($etudiant->getPrenom() . ' ' . $etudiant->getNom(), ENT_QUOTES, 'UTF-8') ?></h1>
    <span class="tag tag--etudiant">Étudiant</span>
</div>

<div class="card">
    <h2>Parcours</h2>
    <div class="profile-field">
        <span class="profile-label">Formation</span>
        <span class="profile-value"><?= htmlspecialchars($etudiant->getFormation() ?: 'Non renseignée', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="profile-field">
        <span class="profile-label">Établissement</span>
        <span class="profile-value"><?= htmlspecialchars($etablissement !== null ? $etablissement->getNom() : 'Non renseigné', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
</div>
