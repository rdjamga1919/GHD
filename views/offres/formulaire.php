<?php
$val = fn(string $k) => htmlspecialchars((string) ($old[$k] ?? $offre[$k] ?? ''));
$typeActuel = (string) ($old['type_d_offre'] ?? $offre['type_d_offre'] ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titrePage) ?></title>
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>
<body>

<main class="container">

    <h1 class="page-title"><?= htmlspecialchars($titrePage) ?></h1>

    <?php if ($flash): ?>
        <div class="alert <?= $flash[0] === 'succes' ? 'alert-success' : 'alert-error' ?>"><?= htmlspecialchars($flash[1]) ?></div>
    <?php endif; ?>

    <form class="card" method="post" action="../src/traitement/traitement_offre.php">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
        <input type="hidden" name="action" value="<?= htmlspecialchars($action) ?>">
        <?php if ($offre !== null): ?>
            <input type="hidden" name="id_offre" value="<?= (int) $offre['id_offre'] ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" class="form-control" maxlength="150" required value="<?= $val('titre') ?>">
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
            <textarea id="description" name="description" class="form-control" rows="5" required><?= $val('description') ?></textarea>
        </div>
        <div class="form-group">
            <label for="missions">Missions *</label>
            <textarea id="missions" name="missions" class="form-control" rows="5" required><?= $val('missions') ?></textarea>
        </div>
        <div class="form-group">
            <label for="salaire">Rémunération (€, facultatif)</label>
            <input type="text" id="salaire" name="salaire" class="form-control" value="<?= $val('salaire') ?>">
        </div>

        <button type="submit" class="btn"><?= $action === 'creer' ? "Publier l'offre" : 'Enregistrer' ?></button>
    </form>

    <p><a class="btn btn-secondary" href="offres.php">Retour aux offres</a></p>

</main>

</body>
</html>