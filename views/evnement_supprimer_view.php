<?php
/**
 * Page de confirmation de suppression.
 * Variables attendues : $evenement, $actionUrl, $nbInscrits, $estAdmin, $erreur
 * (Remplace l'en-tête/pied de page ci-dessous par ceux de ton site.)
 */

/** @var Evenement $evenement */
/** @var string $actionUrl */
/** @var int $nbInscrits */
/** @var bool $estAdmin */
/** @var string|null $erreur */

$suppressionPossible = ($nbInscrits === 0) || $estAdmin;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supprimer l'événement</title>
</head>
<body>
<main>
    <h1>Supprimer l'événement</h1>

    <?php if ($erreur !== null): ?>
        <p class="message message-erreur" role="alert"><?= echapper($erreur) ?></p>
    <?php endif; ?>

    <p>
        Vous êtes sur le point de supprimer
        <strong><?= echapper($evenement->getTitre()) ?></strong>
        (<?= echapper($evenement->getDateHeureEvenement()) ?>).
        Cette action est définitive.
    </p>

    <?php if ($nbInscrits > 0): ?>
        <p class="message message-erreur">
            Cet événement compte <strong><?= echapper($nbInscrits) ?></strong> inscription(s).
            <?php if (!$estAdmin): ?>
                Seul un gestionnaire peut le supprimer.
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <?php if ($suppressionPossible): ?>
        <form method="post" action="<?= echapper($actionUrl) ?>">
            <?= champCsrf() ?>

            <?php if ($nbInscrits > 0): ?>
                <p>
                    <label>
                        <input type="checkbox" name="confirmer_inscriptions" value="1">
                        Je confirme la suppression des <?= echapper($nbInscrits) ?> inscription(s) associée(s).
                    </label>
                </p>
            <?php endif; ?>

            <p>
                <button type="submit">Confirmer la suppression</button>
                <a href="<?= echapper(URL_LISTE_EVENEMENTS) ?>">Annuler</a>
            </p>
        </form>
    <?php else: ?>
        <p><a href="<?= echapper(URL_LISTE_EVENEMENTS) ?>">Retour à la liste</a></p>
    <?php endif; ?>
</main>
</body>
</html>