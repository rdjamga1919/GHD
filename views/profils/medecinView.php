<?php

?>

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
            <li>
                <?= htmlspecialchars($h['nom_hopital']) ?>
                (<?= htmlspecialchars($h['commune']) ?>)
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h3>Je suis professeur dans…</h3>

<?php if (empty($etablissements)): ?>
    <p>Vous n'êtes rattaché à aucun établissement d'enseignement.</p>
<?php else: ?>
    <ul>
        <?php foreach ($etablissements as $e): ?>
            <li>
                <?= htmlspecialchars($e['nom_etablissement']) ?>
                <?php if (!empty($e['adresse'])): ?>
                    (<?= htmlspecialchars($e['adresse']) ?>)
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<a href="modifier_profil.php">Modifier mon profil</a>

