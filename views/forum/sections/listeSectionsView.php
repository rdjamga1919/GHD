<?php /** @var CanalForum[] $sections */ ?>
<section class="container forum-page">
    <div class="page-header">
        <span class="eyebrow">Échanger et partager</span>
        <h1 class="page-title">Sections du forum</h1>
        <p class="lead">Retrouve les espaces de discussion accessibles à ton profil.</p>
    </div>
    <?php if ($flash !== null): ?>
        <p class="alert <?= ($flash['type'] ?? '') === 'succes' ? 'alert-success' : 'alert-error' ?>" role="status"><?= echapper($flash['message'] ?? '') ?></p>
    <?php endif; ?>
    <?php if ($erreur !== null): ?>
        <p class="alert alert-error" role="alert"><?= echapper($erreur) ?></p>
    <?php endif; ?>
    <div class="cluster">
        <a class="btn btn-secondary" href="/GHD/public/publications.php">Voir les publications</a>
        <?php if ($estAdmin): ?>
            <a class="btn" href="<?= echapper(URL_CREER_SECTION) ?>">Créer une section</a>
        <?php endif; ?>
    </div>
    <?php if ($erreur === null && $sections === []): ?>
        <div class="card forum-empty"><h2 class="card-title">Aucune section accessible</h2><p class="lead">Aucune section accessible pour ton rôle.</p></div>
    <?php endif; ?>
    <div class="grid-2">
        <?php foreach ($sections as $section): ?>
            <article class="card">
                <span class="tag forum-tag"><?= echapper(['generale' => 'Tous les membres', 'etudiants' => 'Espace étudiants', 'medecins' => 'Espace médecins'][$section->getAcces()] ?? $section->getAcces()) ?></span>
                <h2 class="card-title"><a href="<?= echapper(URL_DETAIL_SECTION . '?id=' . $section->getIdCanal()) ?>"><?= echapper($section->getNomCanal()) ?></a></h2>
                <p class="lead forum-text"><?= nl2br(echapper($section->getDescription() ?? '')) ?></p>
                <div class="cluster">
                    <a class="btn btn-secondary" href="<?= echapper(URL_DETAIL_SECTION . '?id=' . $section->getIdCanal()) ?>">Ouvrir la section</a>
                    <?php if ($estAdmin): ?>
                        <a href="<?= echapper(URL_MODIFIER_SECTION . '?id=' . $section->getIdCanal()) ?>">Modifier / supprimer</a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
