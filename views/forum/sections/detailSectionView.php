<?php /** @var CanalForum|null $section */ ?>
<section class="container forum-page">
    <p><a href="<?= echapper(URL_LISTE_SECTIONS) ?>">← Retour au forum</a></p>
    <?php if ($erreur !== null || $section === null): ?>
        <div class="card">
            <h1 class="page-title">Section indisponible</h1>
            <p class="alert alert-error" role="alert"><?= echapper($erreur ?? 'Section introuvable.') ?></p>
        </div>
    <?php else: ?>
        <div class="card page-header">
            <span class="eyebrow">Forum GHD-HSP</span>
            <h1 class="page-title"><?= echapper($section->getNomCanal()) ?></h1>
            <p class="lead forum-text"><?= nl2br(echapper($section->getDescription() ?? '')) ?></p>
            <span class="tag forum-tag">Accès : <?= echapper(['generale' => 'Tous les membres', 'etudiants' => 'Étudiants', 'medecins' => 'Médecins'][$section->getAcces()] ?? $section->getAcces()) ?></span>
            <?php if ($estAdmin): ?>
                <p><a class="btn btn-secondary" href="<?= echapper(URL_MODIFIER_SECTION . '?id=' . $section->getIdCanal()) ?>">Modifier / supprimer</a></p>
            <?php endif; ?>
        </div>
        <section class="forum-stack" aria-labelledby="publications-titre">
            <div class="forum-toolbar">
                <h2 id="publications-titre">Publications</h2>
                <a class="btn" href="<?= echapper(URL_CREER_PUBLICATION . '?id_canal=' . (int) $section->getIdCanal()) ?>">Créer une publication</a>
            </div>
            <?php if ($publications === []): ?>
                <div class="card forum-empty"><p>Aucune publication dans cette section pour le moment.</p><p class="caption">Partage une question ou une information avec les membres du canal.</p></div>
            <?php endif; ?>
            <?php foreach ($publications as $publication): ?>
                <article class="card">
                    <h3 class="card-title"><a href="<?= echapper(URL_DETAIL_PUBLICATION . '?id=' . (int) $publication['id_post']) ?>"><?= echapper($publication['titre']) ?></a></h3>
                    <p class="caption">Par <?= echapper(trim($publication['auteur_prenom'] . ' ' . $publication['auteur_nom'])) ?>
                        — <time><?= echapper($publication['date_heure_creation']) ?></time></p>
                    <p><a href="<?= echapper(URL_DETAIL_PUBLICATION . '?id=' . (int) $publication['id_post']) ?>">Lire la publication →</a></p>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</section>
