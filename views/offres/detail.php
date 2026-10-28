<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $offre !== null ? htmlspecialchars($offre['titre']) : 'Offre introuvable' ?></title>
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>
<body>

<main class="container">

    <?php if ($flash): ?>
        <div class="alert <?= $flash[0] === 'succes' ? 'alert-success' : 'alert-error' ?>"><?= htmlspecialchars($flash[1]) ?></div>
    <?php endif; ?>

    <?php if ($offre === null): ?>

        <h1 class="page-title">Offre introuvable</h1>
        <div class="alert alert-error">Cette offre n'existe pas ou n'est plus disponible.</div>

    <?php else: ?>
        <?php $ouverte = ($offre['etat'] === 'ouverte'); ?>

        <h1 class="page-title"><?= htmlspecialchars($offre['titre']) ?></h1>

        <div class="card">
            <p><span class="tag"><?= $ouverte ? 'Offre ouverte' : 'Offre fermée' ?></span></p>

            <p>Entreprise : <?= htmlspecialchars($offre['nom_entreprise'] ?? 'Non renseignée') ?></p>
            <?php if (!empty($offre['adresse'])): ?>
                <p>Adresse : <?= htmlspecialchars($offre['adresse']) ?></p>
            <?php endif; ?>
            <?php if (!empty($offre['site_web'])): ?>
                <p>Site web : <?= htmlspecialchars($offre['site_web']) ?></p>
            <?php endif; ?>

            <p>Type : <?= htmlspecialchars(ucfirst($offre['type_d_offre'])) ?></p>
            <p>Publiée le <?= htmlspecialchars(date('d/m/Y', strtotime($offre['date_publication']))) ?></p>
            <?php if ($offre['salaire'] !== null): ?>
                <p>Rémunération : <?= htmlspecialchars(number_format((float) $offre['salaire'], 2, ',', ' ')) ?> €</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Description</h2>
            <p><?= nl2br(htmlspecialchars($offre['description'])) ?></p>

            <h2>Missions</h2>
            <p><?= nl2br(htmlspecialchars($offre['missions'])) ?></p>
        </div>

        <?php if ($estProprietaire && $ouverte): ?>
            <p><a class="btn" href="modifier_offre.php?id=<?= (int) $offre['id_offre'] ?>">Modifier l'offre</a></p>
            <form method="post" action="../src/traitement/traitement_offre.php">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                <input type="hidden" name="action" value="fermer">
                <input type="hidden" name="id_offre" value="<?= (int) $offre['id_offre'] ?>">
                <button type="submit" class="btn btn-secondary">Fermer l'offre</button>
            </form>
        <?php endif; ?>
        <?php if ($estProprietaire): ?>
            <p>
                <a class="btn btn-secondary" href="candidatures_recues.php?id=<?= (int) $offre['id_offre'] ?>">
                    Voir les candidatures reçues
                </a>
            </p>
        <?php endif; ?>

    <?php endif; ?>

    <p><a class="btn btn-secondary" href="offres.php">Retour aux offres</a></p>

</main>

</body>
</html>