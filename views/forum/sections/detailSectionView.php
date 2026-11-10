<?php /** @var CanalForum|null $section */ ?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Détail de section</title>
</head>
<body>
<main>
    <?php if ($erreur !== null || $section === null): ?>
        <h1>Section indisponible</h1>
        <p role="alert"><?= echapper($erreur ?? 'Section introuvable.') ?></p>
    <?php else: ?>
        <h1>
            <?= echapper($section->getNomCanal()) ?>
        </h1>
        <p>
            <?= nl2br(echapper($section->getDescription() ?? '')) ?>
        </p>
        <p>
            Accès : <?= echapper($section->getAcces()) ?>
        </p>

        <?php if ($estAdmin): ?>
        <a href="<?= echapper(URL_MODIFIER_SECTION . '?id=' . $section->getIdCanal()) ?>">Modifier / supprimer</a>
        <?php endif; ?>

        <section>
            <h2>Publications</h2>
            <p>Les publications seront affichées ici après l'intégration du module correspondant.</p>
        </section>
    <?php endif; ?>

    <p>
        <a href="<?= echapper(URL_LISTE_SECTIONS) ?>">Retour au forum</a>
    </p>
</main>
</body>
</html>
