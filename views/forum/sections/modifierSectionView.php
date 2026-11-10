<?php
$optionsAcces = [
        'generale' => 'Général',
        'etudiants' => 'Étudiants',
        'medecins' => 'Médecins'
];

$erreurSuppression = $erreurSuppression ?? null;
?>

<section class="container forum-page forum-page--form">

    <h1 class="page-title">
        Modifier la section :
        <?= echapper($section->getNomCanal()) ?>
    </h1>

    <?php if (isset($erreurs['general'])): ?>
        <p class="alert alert-error" role="alert">
            <?= echapper($erreurs['general']) ?>
        </p>
    <?php endif; ?>

    <form class="card forum-form" method="post" action="<?= echapper(URL_MODIFIER_SECTION) ?>">
        <?= champCsrf() ?>

        <input
                    type="hidden"
                name="id_canal"
                value="<?= (int) $section->getIdCanal() ?>"
        >

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
                            <?= ($valeurs['acces'] ?? '') === $valeur ? 'selected' : '' ?>
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

        <button class="btn" type="submit">Enregistrer</button>
        <a class="btn btn-secondary" href="<?= echapper(URL_LISTE_SECTIONS) ?>">Annuler</a>
    </form>

    <section class="card forum-danger forum-stack" aria-labelledby="suppression-section">
    <h2 id="suppression-section">Supprimer cette section</h2>

    <?php if ($erreurSuppression !== null): ?>
        <p class="alert alert-error" role="alert">
            <?= echapper($erreurSuppression) ?>
        </p>
    <?php endif; ?>

    <p>
        La suppression est refusée si des publications
        sont liées à cette section.
    </p>

    <form class="forum-form"
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

        <button class="btn forum-btn-danger" type="submit">
            Supprimer la section
        </button>
    </form>
    </section>
</section>
