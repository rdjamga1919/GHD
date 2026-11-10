<?php
$optionsAcces = [
        'generale' => 'Général',
        'etudiants' => 'Étudiants',
        'medecins' => 'Médecins'
];
?>

<section class="container forum-page forum-page--form">

    <h1 class="page-title">Créer une section</h1>

    <?php if (isset($erreurs['general'])): ?>
        <p class="alert alert-error" role="alert">
            <?= echapper($erreurs['general']) ?>
        </p>
    <?php endif; ?>

    <form class="card forum-form" method="post" action="<?= echapper(URL_CREER_SECTION) ?>">
        <?= champCsrf() ?>

        <div class="form-group">
            <label for="nom">Nom *</label>
            <input
                    class="form-control"
                    id="nom"
                    name="nom"
                    type="text"
                    maxlength="<?= SECTION_NOM_MAX ?>"
                    required
                    value="<?= echapper($valeurs['nom'] ?? '') ?>"
            >
        </div>

        <?php if (isset($erreurs['nom'])): ?>
            <p class="alert alert-error" role="alert">
                <?= echapper($erreurs['nom']) ?>
            </p>
        <?php endif; ?>

        <div class="form-group">
            <label for="description">Description (facultative)</label>
            <textarea
                    class="form-control"
                    id="description"
                    name="description"
                    rows="5"
                    maxlength="<?= SECTION_DESCRIPTION_MAX ?>"
            ><?= echapper($valeurs['description'] ?? '') ?></textarea>
        </div>

        <?php if (isset($erreurs['description'])): ?>
            <p class="alert alert-error" role="alert">
                <?= echapper($erreurs['description']) ?>
            </p>
        <?php endif; ?>

        <div class="form-group">
            <label for="acces">Accès *</label>

            <select class="form-control" id="acces" name="acces" required>
                <?php foreach ($optionsAcces as $valeur => $libelle): ?>
                    <option
                            value="<?= echapper($valeur) ?>"
                            <?= ($valeurs['acces'] ?? 'generale') === $valeur ? 'selected' : '' ?>
                    >
                        <?= echapper($libelle) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if (isset($erreurs['acces'])): ?>
            <p class="alert alert-error" role="alert">
                <?= echapper($erreurs['acces']) ?>
            </p>
        <?php endif; ?>

        <button class="btn" type="submit">Créer la section</button>
        <a class="btn btn-secondary" href="<?= echapper(URL_LISTE_SECTIONS) ?>">Annuler</a>
    </form>
</section>
