<?php
// Vue de modification du profil étudiant - GHD-HSP
?>

<?php if ($flash !== null): ?>
    <div class="alert <?= $flash[0] === 'succes' ? 'alert-success' : 'alert-error' ?>">
        <?= htmlspecialchars($flash[1]) ?>
    </div>
<?php endif; ?>

<h1 class="page-title">Modifier mon profil étudiant</h1>

<div class="card">
    <form method="post">

        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

        <div class="form-group">
            <label for="formation">Ma formation :</label>
            <input
                    type="text"
                    id="formation"
                    name="formation"
                    class="form-control"
                    value="<?= htmlspecialchars($etudiant->getFormation()) ?>"
                    required
            >
        </div>

        <div class="form-group space-after-lg">
            <label for="etablissement">Mon établissement :</label>
            <select name="etablissement" id="etablissement" class="form-control" required>
                <option value="">-- Choisir un établissement --</option>
                <?php foreach ($etablissements as $etablissement): ?>
                    <option
                            value="<?= (int) $etablissement->getIdEtablissement() ?>"
                            <?= $etudiant->getRefEtablissement() == $etablissement->getIdEtablissement() ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($etablissement->getNom()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn">Enregistrer</button>

    </form>
</div>

<p>
    <a href="profil.php" class="btn btn-ghost">← Retour à mon profil</a>
</p>
