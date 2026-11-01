<?php
/**
 * Formulaire de création / modification d'un événement.
 * Variables attendues : $titrePage, $actionUrl, $valeurs, $erreurs,
 *                       $modeModification, $nbInscrits
 * (Remplace l'en-tête/pied de page ci-dessous par ceux de ton site.)
 */

/** @var string $titrePage */
/** @var string $actionUrl */
/** @var array $valeurs */
/** @var array $erreurs */
/** @var bool $modeModification */
/** @var int $nbInscrits */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= echapper($titrePage) ?></title>
</head>
<body>
<main>
    <h1><?= echapper($titrePage) ?></h1>

    <?= afficherFlash() ?>
    <?php if (!empty($erreurs['general'])): ?>
        <p class="message message-erreur" role="alert"><?= echapper($erreurs['general']) ?></p>
    <?php endif; ?>

    <form method="post" action="<?= echapper($actionUrl) ?>">
        <?= champCsrf() ?>

        <p>
            <label for="titre">Titre *</label><br>
            <input type="text" id="titre" name="titre" maxlength="150" required
                   value="<?= echapper($valeurs['titre']) ?>">
            <?php if (isset($erreurs['titre'])): ?>
                <br><span class="erreur-champ"><?= echapper($erreurs['titre']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="description">Description *</label><br>
            <textarea id="description" name="description" rows="5" required><?= echapper($valeurs['description']) ?></textarea>
            <?php if (isset($erreurs['description'])): ?>
                <br><span class="erreur-champ"><?= echapper($erreurs['description']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="type_evenement">Type d'événement *</label><br>
            <input type="text" id="type_evenement" name="type_evenement" maxlength="100" required
                   value="<?= echapper($valeurs['type_evenement']) ?>">
            <?php if (isset($erreurs['type_evenement'])): ?>
                <br><span class="erreur-champ"><?= echapper($erreurs['type_evenement']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="adresse_lieu">Adresse du lieu *</label><br>
            <input type="text" id="adresse_lieu" name="adresse_lieu" maxlength="255" required
                   value="<?= echapper($valeurs['adresse_lieu']) ?>">
            <?php if (isset($erreurs['adresse_lieu'])): ?>
                <br><span class="erreur-champ"><?= echapper($erreurs['adresse_lieu']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="element_requis">Élément requis (facultatif)</label><br>
            <input type="text" id="element_requis" name="element_requis" maxlength="255"
                   value="<?= echapper($valeurs['element_requis']) ?>">
            <?php if (isset($erreurs['element_requis'])): ?>
                <br><span class="erreur-champ"><?= echapper($erreurs['element_requis']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="nombre_place">Nombre de places *</label><br>
            <input type="number" id="nombre_place" name="nombre_place" step="1" required
                   min="<?= echapper(max(1, (int) $nbInscrits)) ?>"
                   value="<?= echapper($valeurs['nombre_place']) ?>">
            <?php if ($modeModification && $nbInscrits > 0): ?>
                <br><small><?= echapper($nbInscrits) ?> personne(s) déjà inscrite(s) : la capacité ne peut pas descendre en dessous.</small>
            <?php endif; ?>
            <?php if (isset($erreurs['nombre_place'])): ?>
                <br><span class="erreur-champ"><?= echapper($erreurs['nombre_place']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="date_heure_evenement">Date et heure *</label><br>
            <input type="datetime-local" id="date_heure_evenement" name="date_heure_evenement" required
                   value="<?= echapper($valeurs['date_heure_evenement']) ?>">
            <?php if (isset($erreurs['date_heure_evenement'])): ?>
                <br><span class="erreur-champ"><?= echapper($erreurs['date_heure_evenement']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <button type="submit"><?= $modeModification ? 'Enregistrer les modifications' : "Créer l'événement" ?></button>
            <a href="<?= echapper(URL_LISTE_EVENEMENTS) ?>">Annuler</a>
        </p>
    </form>
</main>
</body>
</html>