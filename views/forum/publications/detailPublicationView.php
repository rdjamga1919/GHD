<article class="container forum-page">
    <?php if ($flash !== null): ?>
        <p class="alert <?= ($flash['type'] ?? '') === 'succes' ? 'alert-success' : 'alert-error' ?>" role="status"><?= echapper($flash['message'] ?? '') ?></p>
    <?php endif; ?>
    <h1 class="page-title"><?= echapper($publication['titre']) ?></h1>
    <p class="caption">Par <?= echapper(trim($publication['auteur_prenom'] . ' ' . $publication['auteur_nom'])) ?>
        — <time><?= echapper($publication['date_heure_creation']) ?></time>
        — <?= echapper($publication['nom_canal']) ?></p>
    <div class="card forum-text"><?= nl2br(echapper($publication['contenu'])) ?></div>
    <?php if ($erreurSuppression !== null): ?>
        <p class="alert alert-error" role="alert"><?= echapper($erreurSuppression) ?></p>
    <?php endif; ?>
    <?php if ($estAuteur): ?>
        <p class="cluster"><a class="btn" href="<?= echapper(URL_MODIFIER_PUBLICATION . '?id=' . (int) $publication['id_post']) ?>">Modifier</a></p>
        <form class="card forum-form forum-danger" method="post" action="<?= echapper(URL_DETAIL_PUBLICATION . '?id=' . (int) $publication['id_post']) ?>">
            <?= champCsrf() ?>
            <input type="hidden" name="action" value="supprimer">
            <p><label class="forum-confirm"><input type="checkbox" name="confirmer_suppression" value="1" required> Je confirme la suppression définitive de ma publication.</label></p>
            <p>Une publication contenant des réponses ne peut pas être supprimée.</p>
            <button class="btn forum-btn-danger" type="submit">Supprimer</button>
        </form>
    <?php endif; ?>
    <section class="card forum-stack" aria-labelledby="reponses-titre">
        <h2 id="reponses-titre">Réponses</h2>
        <p>L’affichage et la gestion des réponses seront intégrés dans le module dédié.</p>
    </section>
    <p><a href="<?= echapper(URL_LISTE_PUBLICATIONS . '?id_canal=' . (int) $publication['ref_canal']) ?>">Publications du canal</a>
        · <a href="<?= echapper(URL_LISTE_PUBLICATIONS) ?>">Toutes les publications accessibles</a></p>
</article>
