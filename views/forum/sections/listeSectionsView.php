<?php /** @var CanalForum[] $sections */ ?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Forum GHD-HSP</title>
</head>
<body>
<main>
    <h1>Sections du forum</h1>
    <?php if ($flash !== null): ?><p role="status">
        <?= echapper($flash['message'] ?? '') ?>
        </p>
    <?php endif; ?>

    <?php if ($erreur !== null): ?>
        <p role="alert">
        <?= echapper($erreur) ?>
        </p>
    <?php endif; ?>

    <?php if ($estAdmin): ?>
        <p>
        <a href="<?= echapper(URL_CREER_SECTION) ?>">Créer une section</a>
        </p>
    <?php endif; ?>

    <?php if ($erreur === null && $sections === []): ?>
        <p>Aucune section accessible pour ton rôle.</p>
    <?php endif; ?>

    <?php foreach ($sections as $section): ?>
        <article>
            <h2>
                <a href="<?= echapper(URL_DETAIL_SECTION . '?id=' . $section->getIdCanal()) ?>">
                    <?= echapper($section->getNomCanal()) ?>
                </a>
            </h2>
            <p>
                <?= nl2br(echapper($section->getDescription() ?? '')) ?>
            </p>
            <p>
                Accès : <?= echapper($section->getAcces()) ?>
            </p>
            <?php if ($estAdmin): ?>
            <a href="<?= echapper(URL_MODIFIER_SECTION . '?id=' . $section->getIdCanal()) ?>">Modifier / supprimer</a>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</main>
</body>
</html>