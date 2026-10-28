
<h2>Mon profil étudiant</h2>

<p>
    <strong>Nom :</strong>
    <?= htmlspecialchars($etudiant->getNom()) ?>
</p>

<p>
    <strong>Prénom :</strong>
    <?= htmlspecialchars($etudiant->getPrenom()) ?>
</p>

<p>
    <strong>Email :</strong>
    <?= htmlspecialchars($etudiant->getEmail()) ?>
</p>

<p>
    <strong>Formation :</strong>
    <?= htmlspecialchars($etudiant->getFormation() ?? 'Non renseignée') ?>
</p>
<p>
    <strong>Établissement :</strong>
    <?= htmlspecialchars($etablissement !== null ? $etablissement->getNom() : 'Non renseigné') ?>
</p>

<p>
    <a href="modifier_profil.php">Modifier mon profil</a>
</p>
<p>
    <a href="mes_candidatures.php">Voir mes candidatures</a>
</p>
