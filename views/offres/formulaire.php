<?php
$val = fn(string $k) => htmlspecialchars((string) ($old[$k] ?? $offre[$k] ?? ''));
$typeActuel = (string) ($old['type_d_offre'] ?? $offre['type_d_offre'] ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titrePage) ?></title>
</head>
<body>

<h2><?= htmlspecialchars($titrePage) ?></h2>

<?php if ($flash): ?>
    <p class="message message-<?= $flash[0] === 'succes' ? 'succes' : 'erreur' ?>"><?= htmlspecialchars($flash[1]) ?></p>
<?php endif; ?>

<form method="post" action="../src/traitement/traitement_offre.php">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
    <input type="hidden" name="action" value="<?= htmlspecialchars($action) ?>">
    <?php if ($offre !== null): ?>
        <input type="hidden" name="id_offre" value="<?= (int) $offre['id_offre'] ?>">
    <?php endif; ?>

    <p>
        <label for="titre">Titre *</label><br>
        <input type="text" id="titre" name="titre" maxlength="150" required value="<?= $val('titre') ?>">
    </p>
    <p>
        <label for="type_d_offre">Type d'offre *</label><br>
        <select id="type_d_offre" name="type_d_offre" required>
            <option value="">-- Choisir --</option>
            <?php foreach (['stage' => 'Stage', 'alternance' => 'Alternance', 'cdd' => 'CDD', 'cdi' => 'CDI'] as $v => $libelle): ?>
                <option value="<?= $v ?>"<?= $typeActuel === $v ? ' selected' : '' ?>><?= $libelle ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="description">Description *</label><br>
        <textarea id="description" name="description" rows="5" required><?= $val('description') ?></textarea>
    </p>
    <p>
        <label for="missions">Missions *</label><br>
        <textarea id="missions" name="missions" rows="5" required><?= $val('missions') ?></textarea>
    </p>
    <p>
        <label for="salaire">Rémunération (€, facultatif)</label><br>
        <input type="text" id="salaire" name="salaire" value="<?= $val('salaire') ?>">
    </p>

    <button type="submit"><?= $action === 'creer' ? "Publier l'offre" : 'Enregistrer' ?></button>
</form>

<p><a href="offres.php">Retour aux offres</a></p>

</body>
</html>