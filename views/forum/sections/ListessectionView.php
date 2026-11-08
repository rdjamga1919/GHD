<?php
/**
 * Liste des sections du forum.
 * Variables attendues : $sections (Section[]), $erreur (?string), $flash (?array), $estAdmin (bool)
 * (Remplace l'en-tête/pied de page ci-dessous par ceux de ton site.)
 */

/** @var Section[] $sections */
/** @var string|null $erreur */
/** @var array|null $flash */
/** @var bool $estAdmin */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forum — Sections</title>
</head>
<body>
<main>
    <h1>Forum GHD-HSP</h1>

    <?php if ($flash !== null): ?>
        <p class="message message-<?= $flash['type'] === 'succes' ? 'succes' : 'erreur' ?>" role="status">
            <?= echapper($flash['message']) ?>
        </p>
    <?php endif; ?>

    <?php if ($erreur !== null): ?>
        <p class="message message-erreur" role="alert"><?= echapper($erreur) ?></p>
    <?php endif; ?>

    <?php if ($estAdmin): ?>
        <p><a href="<?= echapper(URL_CREER_SECTION) ?>">Créer une section</a></p>
    <?php endif; ?>

    <?php if ($erreur === null && $sections === []): ?>
        <p>Aucune section n'est disponible pour le moment.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($sections as $section): ?>
                <li>
                    <h2>
                        <a href="<?= echapper(URL_DETAIL_SECTION . '?id=' . $section->getIdSection()) ?>">
                            <?= echapper($section->getNom()) ?>
                        </a>
                    </h2>
                    <p><?= echapper($section->getDescription() ?? '') ?></p>
                    <p>Accès : <?= echapper($section->getLibelleAcces()) ?></p>

                    <?php if ($estAdmin): ?>
                        <p>
                            <a href="<?= echapper(URL_MODIFIER_SECTION . '?id=' . $section->getIdSection()) ?>">Modifier</a>
                            <a href="<?= echapper(URL_SUPPRIMER_SECTION . '?id=' . $section->getIdSection()) ?>">Supprimer</a>
                        </p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>
</body>
</html>