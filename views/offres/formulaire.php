<?php
// Vue du formulaire d'offre (création / modification) - GHD-HSP
$val = fn(string $k) => htmlspecialchars((string) ($old[$k] ?? $offre[$k] ?? ''));
$typeActuel = (string) ($old['type_d_offre'] ?? $offre['type_d_offre'] ?? '');
?>

<h1 class="page-title"><?= htmlspecialchars($titrePage ?? 'Gestion d\'une offre') ?></h1>

<?php if (!empty($flash)): ?>
    <div class="message message-<?= $flash[0] === 'succes' ? 'succes' : 'erreur' ?>">
        <?= htmlspecialchars($flash[1]) ?>
    </div>
<?php endif; ?>

<div class="card">
    <form method="post" action="../src/traitement/traitement_offre.php" class="content-stack">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
        <input type="hidden" name="action" value="<?= htmlspecialchars($action) ?>">
        <?php if ($offre !== null): ?>
            <input type="hidden" name="id_offre" value="<?= (int) $offre['id_offre'] ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" class="form-control" maxlength="150" required value="<?= $val('titre') ?>" placeholder="Ex: Stage Développeur Web Fullstack">
        </div>

        <div class="form-group">
            <label for="type_d_offre">Type d'offre *</label>
            <select id="type_d_offre" name="type_d_offre" class="form-control" required>
                <option value="">-- Choisir --</option>
                <?php foreach (['stage' => 'Stage', 'alternance' => 'Alternance', 'cdd' => 'CDD', 'cdi' => 'CDI'] as $v => $libelle): ?>
                    <option value="<?= $v ?>"<?= $typeActuel === $v ? ' selected' : '' ?>><?= $libelle ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description *</label>
            <textarea id="description" name="description" class="form-control" rows="5" required placeholder="Décrivez le contexte de l'offre..."><?= $val('description') ?></textarea>
        </div>

        <div class="form-group">
            <label for="missions">Missions *</label>
            <textarea id="missions" name="missions" class="form-control" rows="5" required placeholder="Détaillez les missions confiées..."><?= $val('missions') ?></textarea>
        </div>

        <div class="form-group">
            <label for="salaire">Rémunération (€, facultatif)</label>
            <input type="text" id="salaire" name="salaire" class="form-control" value="<?= $val('salaire') ?>" placeholder="Ex: 1200">
        </div>

        <button type="submit" class="btn space-before-sm">
            <?= $action === 'creer' ? "Publier l'offre" : 'Enregistrer' ?>
        </button>
    </form>
</div>

<p>
    <a href="offres.php" class="btn btn-ghost">← Retour aux offres</a>
</p>
