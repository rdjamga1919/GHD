<?php
// Vue de modification du profil partenaire - GHD-HSP
?>

<h1 class="page-title">Modifier mon profil partenaire</h1>

<?php if ($flash !== null): ?>
    <div class="message message-<?= ($flash[0] ?? '') === 'succes' ? 'succes' : 'erreur' ?>">
        <?= htmlspecialchars($flash[1]) ?>
    </div>
<?php endif; ?>

<div class="card">
    <form action="modifier_profil.php" method="POST">

        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

        <div class="form-group">
            <label for="poste">Poste occupé :</label>
            <input type="text"
                   id="poste"
                   name="poste"
                   class="form-control"
                   value="<?= htmlspecialchars($partenaire->getPoste()) ?>"
                   required>
        </div>

        <div class="form-group space-after-lg">
            <label for="entreprise">Entreprise :</label>
            <select name="ref_entreprise" id="entreprise" class="form-control" required>
                <?php foreach ($entreprises as $entreprise): ?>
                    <option value="<?= (int) $entreprise->getIdEntreprise() ?>"
                            <?= $entreprise->getIdEntreprise() == $partenaire->getRefEntreprise() ? 'selected' : '' ?>>
                        <?= htmlspecialchars($entreprise->getNom()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn">Enregistrer les modifications</button>

    </form>
</div>

<p>
    <a href="profil.php" class="btn btn-ghost">← Retour à mon profil</a>
</p>
