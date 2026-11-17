<section class="container forum-page forum-page--form">
    <h1 class="page-title">Modifier ma publication</h1>
    <p>Canal : <?= echapper($publication['nom_canal']) ?>. L’auteur, le canal et la date de création sont conservés.</p>
    <?php if (isset($erreurs['general'])): ?><p class="alert alert-error" role="alert"><?= echapper($erreurs['general']) ?></p><?php endif; ?>
    <form class="card forum-form" method="post" action="<?= echapper(URL_MODIFIER_PUBLICATION . '?id=' . (int) $publication['id_post']) ?>">
        <?= champCsrf() ?>
        <div class="form-group"><label for="titre">Titre *</label>
            <input class="form-control" id="titre" name="titre" maxlength="150" required value="<?= echapper($valeurs['titre']) ?>"></div>
        <?php if (isset($erreurs['titre'])): ?><p class="alert alert-error" role="alert"><?= echapper($erreurs['titre']) ?></p><?php endif; ?>
        <div class="form-group"><label for="contenu">Contenu *</label>
            <textarea class="form-control" id="contenu" name="contenu" rows="7" required><?= echapper($valeurs['contenu']) ?></textarea></div>
        <?php if (isset($erreurs['contenu'])): ?><p class="alert alert-error" role="alert"><?= echapper($erreurs['contenu']) ?></p><?php endif; ?>
        <button class="btn" type="submit">Enregistrer</button>
        <a class="btn btn-secondary" href="<?= echapper(URL_DETAIL_PUBLICATION . '?id=' . (int) $publication['id_post']) ?>">Annuler</a>
    </form>
</section>
