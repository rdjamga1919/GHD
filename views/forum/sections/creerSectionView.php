<?php
/**
 * Formulaire de création d'une section.
 * Variables attendues : $valeurs (array nom/acces), $erreurs (array)
 * (Remplace l'en-tête/pied de page ci-dessous par ceux de ton site.)
 */

/** @var array{nom:string, acces:string} $valeurs */
/** @var array<string,string> $erreurs */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Créer une section</title>
</head>
<body>
<main>
    <h1>Créer une section</h1>

    <?php if (isset($erreurs['general'])): ?>
        <p class="message message-erreur" role="alert"><?= echapper($erreurs['general']) ?></p>
    <?php endif; ?>

    <form method="post" action="<?= echapper(URL_CREER_SECTION) ?>">
        <?= champCsrf() ?>

        <p>
            <label for="nom">Nom de la section *</label><br>
            <input type="text" id="nom" name="nom" maxlength="<?= (int) SECTION_NOM_MAX ?>" required
                   value="<?= echapper($valeurs['nom']) ?>">
            <?php if (isset($erreurs['nom'])): ?>
                <span class="message message-erreur" role="alert"><?= echapper($erreurs['nom']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="description">Description</label><br>
            <textarea id="description" name="description" rows="5" maxlength="<?= (int) SECTION_DESCRIPTION_MAX ?>"><?= echapper($valeurs['description']) ?></textarea>
            <?php if (isset($erreurs['description'])): ?>
                <span class="message message-erreur" role="alert"><?= echapper($erreurs['description']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="acces">Accès *</label><br>
            <select id="acces" name="acces" required>
                <?php foreach (Section::ACCES as $valeur => $libelle): ?>
                    <option value="<?= echapper($valeur) ?>" <?= $valeurs['acces'] === $valeur ? 'selected' : '' ?>>
                        <?= echapper($libelle) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($erreurs['acces'])): ?>
                <span class="message message-erreur" role="alert"><?= echapper($erreurs['acces']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <button type="submit">Créer la section</button>
            <a href="<?= echapper(URL_LISTE_SECTIONS) ?>">Annuler</a>
        </p>
    </form>
</main>
</body>
</html>