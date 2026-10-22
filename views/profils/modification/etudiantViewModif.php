
<?php if ($flash !== null): ?>
    <p style="color: <?= $flash[0] === 'succes' ? 'green' : 'red' ?>;">
        <?= htmlspecialchars($flash[1]) ?>
    </p>
<?php endif; ?>

<h2>Modifier mon profil étudiant</h2>

<form method="post">

    <input type="hidden"
           name="csrf"
           value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

    <p>
        <label for="formation">Ma formation :</label>

        <input
                type="text"
                id="formation"
                name="formation"
                value="<?= htmlspecialchars($etudiant->getFormation()) ?>"
                required
        >
    </p>

    <p>
        <label for="etablissement">Mon établissement :</label>

        <select name="etablissement" id="etablissement" required>

            <option value="">-- Choisir un établissement --</option>

            <?php foreach ($etablissements as $etablissement): ?>

                <option
                        value="<?= (int) $etablissement->getIdEtablissement() ?>"
                        <?= $etudiant->getRefEtablissement() == $etablissement->getIdEtablissement()
                                ? 'selected'
                                : '' ?>
                >
                    <?= htmlspecialchars($etablissement->getNom()) ?>
                </option>

            <?php endforeach; ?>

        </select>
    </p>

    <button type="submit">Enregistrer</button>

</form>

<p>
    <a href="profil.php">Retour à mon profil</a>
</p>
