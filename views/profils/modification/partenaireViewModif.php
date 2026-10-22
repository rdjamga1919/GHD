<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier mon profil partenaire</title>
</head>

<body>

<h1>Modifier mon profil partenaire</h1>

<?php if ($flash !== null): ?>
    <p><?= htmlspecialchars($flash[1]) ?></p>
<?php endif; ?>

<form action="modifier_profil.php" method="POST">

    <input type="hidden"
           name="csrf"
           value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

    <div>
        <label for="poste">Poste occupé :</label>

        <input type="text"
               id="poste"
               name="poste"
               value="<?= htmlspecialchars($partenaire->getPoste()) ?>"
               required>
    </div>

    <br>

    <div>
        <label for="entreprise">Entreprise :</label>

        <select name="ref_entreprise" id="entreprise" required>

            <?php foreach ($entreprises as $entreprise): ?>

                <option value="<?= (int) $entreprise->getIdEntreprise() ?>"
                    <?= $entreprise->getIdEntreprise() == $partenaire->getRefEntreprise()
                        ? 'selected'
                        : '' ?>>

                    <?= htmlspecialchars($entreprise->getNom()) ?>

                </option>

            <?php endforeach; ?>

        </select>
    </div>

    <br>

    <button type="submit">Enregistrer les modifications</button>

</form>

<p>
    <a href="profil.php">Retour à mon profil</a>
</p>

</body>
</html>