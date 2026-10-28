<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offres</title>
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>
<body>

<main class="container">

    <h1 class="page-title">Offres</h1>

    <?php if ($flash): ?>
        <div class="alert <?= $flash[0] === 'succes' ? 'alert-success' : 'alert-error' ?>"><?= htmlspecialchars($flash[1]) ?></div>
    <?php endif; ?>

    <?php if ($estPartenaire): ?>
        <p><a class="btn" href="creer_offre.php">Créer une offre</a></p>
    <?php endif; ?>

    <?php if (empty($offres)): ?>
        <div class="card">
            <p>Aucune offre n'est disponible pour le moment.</p>
        </div>
    <?php else: ?>
        <div class="grid-cards">
            <?php foreach ($offres as $o): ?>
                <?php $ouverte = ($o['etat'] === 'ouverte'); ?>
                <article class="card">
                    <h2>
                        <a href="offre_detail.php?id=<?= (int) $o['id_offre'] ?>">
                            <?= htmlspecialchars($o['titre']) ?>
                        </a>
                    </h2>
                    <p>
                        <span class="tag"><?= $ouverte ? 'Ouverte' : 'Fermée' ?></span>
                    </p>
                    <p>
                        <?= htmlspecialchars($o['nom_entreprise'] ?? 'Entreprise non renseignée') ?>
                        · <?= htmlspecialchars(ucfirst($o['type_d_offre'])) ?>
                        · Publiée le <?= htmlspecialchars(date('d/m/Y', strtotime($o['date_publication']))) ?>
                    </p>
                    <p><?= htmlspecialchars(mb_strimwidth($o['description'], 0, 150, '…')) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

</body>
</html>