<?php
// Vue du profil médecin - GHD-HSP
?>

<h1 class="page-title">Mon profil médecin</h1>

<div class="card">
    <div class="profile-field">
        <span class="profile-label">Nom</span>
        <span class="profile-value"><?= htmlspecialchars($medecin->getNom()) ?></span>
    </div>
    <div class="profile-field">
        <span class="profile-label">Prénom</span>
        <span class="profile-value"><?= htmlspecialchars($medecin->getPrenom()) ?></span>
    </div>
    <div class="profile-field">
        <span class="profile-label">Email</span>
        <span class="profile-value"><?= htmlspecialchars($medecin->getEmail()) ?></span>
    </div>
    <div class="profile-field">
        <span class="profile-label">N° RPPS</span>
        <span class="profile-value"><?= htmlspecialchars($medecin->getNumeroRpps()) ?></span>
    </div>
</div>

<div class="card">
    <h3>Spécialités</h3>
    <?php if (empty($specialites)): ?>
        <p>Aucune spécialité renseignée.</p>
    <?php else: ?>
        <div class="cluster">
            <?php foreach ($specialites as $s): ?>
                <span class="tag--medecin"><?= htmlspecialchars($s['libelle_specialite']) ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Hôpitaux d'exercice</h3>
    <?php if (empty($hopitaux)): ?>
        <p>Aucun hôpital renseigné.</p>
    <?php else: ?>
        <ul class="item-list">
            <?php foreach ($hopitaux as $h): ?>
                <li class="item-row">
                    <span><strong><?= htmlspecialchars($h['nom_hopital']) ?></strong></span>
                    <span class="caption"><?= htmlspecialchars($h['commune']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Établissements d'enseignement</h3>
    <?php if (empty($etablissements)): ?>
        <p>Vous n'êtes rattaché à aucun établissement d'enseignement.</p>
    <?php else: ?>
        <ul class="item-list">
            <?php foreach ($etablissements as $e): ?>
                <li class="item-row">
                    <span><strong><?= htmlspecialchars($e['nom_etablissement']) ?></strong></span>
                    <?php if (!empty($e['adresse'])): ?>
                        <span class="caption"><?= htmlspecialchars($e['adresse']) ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<div class="space-before-lg">
    <a href="modifier_profil.php" class="btn">Modifier mon profil</a>
</div>
