<?php
// Vue du profil partenaire - GHD-HSP
?>

<h1 class="page-title">Mon profil partenaire</h1>

<div class="card">
    <h2>Informations personnelles</h2>
    <div class="profile-field">
        <span class="profile-label">Nom</span>
        <span class="profile-value"><?= htmlspecialchars($partenaire->getNom()) ?></span>
    </div>
    <div class="profile-field">
        <span class="profile-label">Prénom</span>
        <span class="profile-value"><?= htmlspecialchars($partenaire->getPrenom()) ?></span>
    </div>
    <div class="profile-field">
        <span class="profile-label">Email</span>
        <span class="profile-value"><?= htmlspecialchars($partenaire->getEmail()) ?></span>
    </div>
    <div class="profile-field">
        <span class="profile-label">Poste</span>
        <span class="profile-value"><?= htmlspecialchars($partenaire->getPoste()) ?></span>
    </div>
</div>

<div class="card">
    <h2>Mon entreprise</h2>

    <?php if ($entreprise !== null): ?>
        <div class="profile-field">
            <span class="profile-label">Nom</span>
            <span class="profile-value"><strong><?= htmlspecialchars($entreprise->getNom()) ?></strong></span>
        </div>
        <div class="profile-field">
            <span class="profile-label">Adresse</span>
            <span class="profile-value"><?= htmlspecialchars($entreprise->getAdresse()) ?></span>
        </div>
        <div class="profile-field">
            <span class="profile-label">Site web</span>
            <span class="profile-value">
                <a href="<?= htmlspecialchars($entreprise->getSiteWeb()) ?>" target="_blank" rel="noopener noreferrer" class="text-primary">
                    <?= htmlspecialchars($entreprise->getSiteWeb()) ?>
                </a>
            </span>
        </div>

        <div class="space-before-md">
            <a href="entreprise.php?id=<?= (int) $entreprise->getIdEntreprise() ?>" class="btn btn-ghost">
                Voir mon entreprise
            </a>
        </div>
    <?php else: ?>
        <p>Aucune entreprise trouvée.</p>
    <?php endif; ?>
</div>

<div>
    <a href="modifier_profil.php" class="btn">Modifier mon profil</a>
</div>
