<?php
require_once __DIR__ . '/../src/traitement/forum/traitement_publication.php';
exigerConnexionForum();

$idCanal = null;
$canal = null;
$publications = [];
$erreur = null;
try {
    $canauxRepo = new CanalForumRepository();
    if (isset($_GET['id_canal'])) {
        $idCanal = lireIdSection($_GET['id_canal']);
        $canal = chargerCanalPublication($canauxRepo, $idCanal);
    }
    $canaux = array_values(array_filter($canauxRepo->getAllCanalForum(), 'peutConsulterCanalForum'));
    $publications = (new PostForumRepository())->getPublicationsVisibles((string) ($_SESSION['role'] ?? ''), $idCanal);
} catch (PDOException $e) {
    erreurTechniquePublication($e);
}
$flash = lireMessageFlashForum();
$pageTitle = 'Publications du forum';
$activePage = 'forum';
$bodyClass = 'forum-layout';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/forum/publications/listePublicationsView.php';
require __DIR__ . '/../views/layouts/footer.php';
