<?php
$optionsAcces = [
        'generale' => 'Général',
        'etudiants' => 'Étudiants',
        'medecins' => 'Médecins'
];

$erreurSuppression = $erreurSuppression ?? null;
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier une section</title>
</head>
<body>
<main>
    <h1>
        Modifier la section :
        <?= echapper($section->getNomCanal()) ?>
    </h1>

    <?php if (isset($erreurs['general'])): ?>
        <p role="alert">
            <?= echapper($erreurs['general']) ?>
        </p>
    <?php endif; ?>

    <form method="post" action="<?= echapper(URL_MODIFIER_SECTION) ?>">
        <?= champCsrf() ?>

        <input
                type="hidden"
                name="id_canal"
                value="<?= (int) $section->getIdCanal() ?>"
        >

        <p>
            <label for="nom">Nom *</label>
            <input
                    id="nom"
                    name="nom"
                    type="text"
                    maxlength="<?= SECTION_NOM_MAX ?>"
                    required
                    value="<?= echapper($valeurs['nom'] ?? '') ?>"
            >
        </p>

        <?php if (isset($erreurs['nom'])): ?>
            <p role="alert">
                <?= echapper($erreurs['nom']) ?>
            </p>
        <?php endif; ?>

        <p>
            <label for="description">Description (facultative)</label>
            <textarea
                    id="description"
                    name="description"
                    rows="5"
                    maxlength="<?= SECTION_DESCRIPTION_MAX ?>"
            ><?= echapper($valeurs['description'] ?? '') ?></textarea>
        </p>

        <?php if (isset($erreurs['description'])): ?>
            <p role="alert">
                <?= echapper($erreurs['description']) ?>
            </p>
        <?php endif; ?>

        <p>
            <label for="acces">Accès *</label>

            <select id="acces" name="acces" required>
                <?php foreach ($optionsAcces as $valeur => $libelle): ?>
                    <option
                            value="<?= echapper($valeur) ?>"
                            <?= ($valeurs['acces'] ?? '') === $valeur ? 'selected' : '' ?>
                    >
                        <?= echapper($libelle) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <?php if (isset($erreurs['acces'])): ?>
            <p role="alert">
                <?= echapper($erreurs['acces']) ?>
            </p>
        <?php endif; ?>

        <button type="submit">Enregistrer</button>
        <a href="<?= echapper(URL_LISTE_SECTIONS) ?>">Annuler</a>
    </form>

    <hr>

    <h2>Supprimer cette section</h2>

    <?php if ($erreurSuppression !== null): ?>
        <p role="alert">
            <?= echapper($erreurSuppression) ?>
        </p>
    <?php endif; ?>

    <p>
        La suppression est refusée si des publications
        sont liées à cette section.
    </p>

    <form
            method="post"
            action="<?= echapper(URL_MODIFIER_SECTION) ?>"
            onsubmit="return confirm('Supprimer définitivement cette section ?');"
    >
        <?= champCsrf() ?>

        <input
                type="hidden"
                name="id_canal"
                value="<?= (int) $section->getIdCanal() ?>"
        >

        <input
                type="hidden"
                name="action"
                value="supprimer"
        >

        <button type="submit">
            Supprimer la section
        </button>
    </form>
</main>
</body>
</html>
