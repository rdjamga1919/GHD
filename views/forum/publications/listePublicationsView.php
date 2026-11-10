<section class="container forum-page">
    <h1 class="page-title">Publications du forum</h1>
    <?php if ($flash !== null): ?>
        <p class="alert <?= ($flash['type'] ?? '') === 'succes' ? 'alert-success' : 'alert-error' ?>" role="status"><?= echapper($flash['message'] ?? '') ?></p>
    <?php endif; ?>
    <p class="cluster"><a class="btn" href="<?= echapper(URL_CREER_PUBLICATION . ($idCanal !== null ? '?id_canal=' . $idCanal : '')) ?>">Créer une publication</a>
        <a href="<?= echapper(URL_LISTE_SECTIONS) ?>">Retour aux canaux</a></p>
    <form class="card forum-filter" method="get" action="<?= echapper(URL_LISTE_PUBLICATIONS) ?>">
        <div class="form-group"><label for="id_canal">Filtrer par canal</label>
        <select class="form-control" id="id_canal" name="id_canal" required>
            <option value="" disabled <?= $idCanal === null ? 'selected' : '' ?>>Choisir un canal</option>
            <?php foreach ($canaux as $option): ?>
                <option value="<?= (int) $option->getIdCanal() ?>" <?= $idCanal === (int) $option->getIdCanal() ? 'selected' : '' ?>><?= echapper($option->getNomCanal()) ?></option>
            <?php endforeach; ?>
        </select></div>
        <button class="btn" type="submit">Filtrer</button>
        <a href="<?= echapper(URL_LISTE_PUBLICATIONS) ?>">Tous les canaux accessibles</a>
    </form>
    <?php if ($publications === []): ?>
        <div class="card forum-empty"><h2 class="card-title">Pas encore de publication</h2><p>Aucune publication dans les canaux sélectionnés.</p></div>
    <?php endif; ?>
    <?php foreach ($publications as $post): ?>
        <article class="card">
            <h2 class="card-title"><a href="<?= echapper(URL_DETAIL_PUBLICATION . '?id=' . (int) $post['id_post']) ?>"><?= echapper($post['titre']) ?></a></h2>
            <p class="caption">Par <?= echapper(trim($post['auteur_prenom'] . ' ' . $post['auteur_nom'])) ?>
                — <time><?= echapper($post['date_heure_creation']) ?></time>
                — <?= echapper($post['nom_canal']) ?></p>
        </article>
    <?php endforeach; ?>
</section>
