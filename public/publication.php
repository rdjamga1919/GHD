<?php
require_once __DIR__ . '/../src/traitement/forum/traitement_publication.php';
exigerConnexionForum();

$erreurSuppression = null;
try {
    $repo = new PostForumRepository();
    $publication = chargerPublicationForum($repo, new CanalForumRepository(), lireIdSection($_GET['id'] ?? null));
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        if (($_POST['action'] ?? '') !== 'supprimer') {
            http_response_code(400);
            $erreurSuppression = 'Action invalide.';
        } else {
            $erreurSuppression = traiterSuppressionPublication($repo, $publication);
        }
    }
} catch (PDOException $e) {
    erreurTechniquePublication($e);
}
$estAuteur = estAuteurPublication($publication);
$flash = lireMessageFlashForum();
$pageTitle = $publication['titre'];
$activePage = 'forum';
$bodyClass = 'forum-layout';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/forum/publications/detailPublicationView.php';
require __DIR__ . '/../views/layouts/footer.php';
