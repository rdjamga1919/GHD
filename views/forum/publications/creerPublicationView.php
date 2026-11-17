<section class="container forum-page forum-page--form">
    <h1 class="page-title">Créer une publication</h1>
    <?php if (isset($erreurs['general'])): ?><p class="alert alert-error" role="alert"><?= echapper($erreurs['general']) ?></p><?php endif; ?>
    <?php if ($canaux === []): ?><p>Aucun canal accessible. Impossible de publier pour le moment.</p><?php endif; ?>
    <form class="card forum-form" method="post" action="<?= echapper(URL_CREER_PUBLICATION) ?>">
        <?= champCsrf() ?>
        <div class="form-group"><label for="titre">Titre *</label>
            <input class="form-control" id="titre" name="titre" maxlength="150" required value="<?= echapper($valeurs['titre']) ?>"></div>
        <?php if (isset($erreurs['titre'])): ?><p class="alert alert-error" role="alert"><?= echapper($erreurs['titre']) ?></p><?php endif; ?>
        <div class="form-group"><label for="contenu">Contenu *</label>
            <textarea class="form-control" id="contenu" name="contenu" rows="7" required><?= echapper($valeurs['contenu']) ?></textarea></div>
        <?php if (isset($erreurs['contenu'])): ?><p class="alert alert-error" role="alert"><?= echapper($erreurs['contenu']) ?></p><?php endif; ?>
        <div class="form-group"><label for="id_canal">Canal *</label>
            <select class="form-control" id="id_canal" name="id_canal" required>
                <option value="">Choisir un canal</option>
                <?php foreach ($canaux as $canal): ?>
                    <option value="<?= (int) $canal->getIdCanal() ?>" <?= (string) $canal->getIdCanal() === $valeurs['id_canal'] ? 'selected' : '' ?>><?= echapper($canal->getNomCanal()) ?></option>
                <?php endforeach; ?>
            </select></div>
        <?php if (isset($erreurs['id_canal'])): ?><p class="alert alert-error" role="alert"><?= echapper($erreurs['id_canal']) ?></p><?php endif; ?>
        <button class="btn" type="submit" <?= $canaux === [] ? 'disabled' : '' ?>>Publier</button>
        <a class="btn btn-secondary" href="<?= echapper(URL_LISTE_PUBLICATIONS) ?>">Annuler</a>
    </form>
</section>
