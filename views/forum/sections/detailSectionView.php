<?php
/**
 * Consultation d'une section.
 * Variables attendues : $section (?Section), $erreur (?string), $flash (?array), $estAdmin (bool)
 * (Remplace l'en-tête/pied de page ci-dessous par ceux de ton site.)
 */

/** @var Section|null $section */
/** @var string|null $erreur */
/** @var array|null $flash */
/** @var bool $estAdmin */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $section !== null ? echapper($section->getNom()) : 'Section introuvable' ?></title>
</head>
<body>
<main>
    <?php if ($flash !== null): ?>
        <p class="message message-<?= $flash['type'] === 'succes' ? 'succes' : 'erreur' ?>" role="status">
            <?= echapper($flash['message']) ?>
        </p>
    <?php endif; ?>

    <?php if ($erreur !== null || $section === null): ?>
        <h1>Section introuvable</h1>
        <p class="message message-erreur" role="alert"><?= echapper($erreur ?? 'Section introuvable.') ?></p>
    <?php else: ?>
        <h1><?= echapper($section->getNom()) ?></h1>
        <p><?= echapper($section->getDescription() ?? '') ?></p>
        <p>Accès : <?= echapper($section->getLibelleAcces()) ?></p>

        <?php if ($estAdmin): ?>
            <p>
                <a href="<?= echapper(URL_MODIFIER_SECTION . '?id=' . $section->getIdSection()) ?>">Modifier</a>
                <a href="<?= echapper(URL_SUPPRIMER_SECTION . '?id=' . $section->getIdSection()) ?>">Supprimer</a>
            </p>
        <?php endif; ?>

        <section id="publications">
            <h2>Publications</h2>
            <?php /* EMPLACEMENT RÉSERVÉ : la liste des publications (Post_forum) sera raccordée lors de l'intégration (Jordan). */ ?>
        </section>
    <?php endif; ?>

    <p><a href="<?= echapper(URL_LISTE_SECTIONS) ?>">Retour à la liste des sections</a></p>
</main>
</body>
</html>