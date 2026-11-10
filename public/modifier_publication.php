<?php
require_once __DIR__ . '/../src/traitement/forum/traitement_publication.php';
exigerConnexionForum();

try {
    $repo = new PostForumRepository();
    $publication = chargerPublicationForum($repo, new CanalForumRepository(), lireIdSection($_GET['id'] ?? null));
    $resultat = traiterModificationPublication($repo, $publication);
} catch (PDOException $e) {
    erreurTechniquePublication($e);
}
$valeurs = $resultat['valeurs'];
$erreurs = $resultat['erreurs'];
$pageTitle = 'Modifier une publication';
$activePage = 'forum';
$bodyClass = 'forum-layout';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/forum/publications/modifierPublicationView.php';
require __DIR__ . '/../views/layouts/footer.php';
