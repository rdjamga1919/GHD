<h1>Mon profil partenaire</h1>

<h2>Informations personnelles</h2>

<p>Nom : <?= htmlspecialchars($partenaire->getNom()) ?></p>
<p>Prénom : <?= htmlspecialchars($partenaire->getPrenom()) ?></p>
<p>Email : <?= htmlspecialchars($partenaire->getEmail()) ?></p>
<p>Poste : <?= htmlspecialchars($partenaire->getPoste()) ?></p>

<h2>Mon entreprise</h2>

<?php if ($entreprise !== null): ?>

    <p>Nom : <?= htmlspecialchars($entreprise->getNom()) ?></p>
    <p>Adresse : <?= htmlspecialchars($entreprise->getAdresse()) ?></p>
    <p>Site web : <?= htmlspecialchars($entreprise->getSiteWeb()) ?></p>

    <a href="entreprise.php?id=<?= (int) $entreprise->getIdEntreprise() ?>">
        Voir mon entreprise
    </a>

<?php else: ?>

    <p>Aucune entreprise trouvée.</p>

<?php endif; ?>

<p>
    <a href="modifier_profil.php">Modifier mon profil</a>
</p>