<?php
require_once __DIR__ . '/../src/traitement/forum/traitement_publication.php';

exigerConnexionForum();

$estAdmin = estAdminForum();
$section = null;
$publications = [];
$erreur = null;
$id = lireIdSection($_GET['id'] ?? null);
if ($id === null) {
    http_response_code(404);
    $erreur = 'Section introuvable.';
} else {
    try {
        $section = (new CanalForumRepository())->getCanalForum($id);
        if ($section === null) {
            http_response_code(404);
            $erreur = 'Section introuvable.';
        } elseif (!peutConsulterCanalForum($section)) {
            http_response_code(403);
            exit('Accès refusé.');
        } else {
            $publications = (new PostForumRepository())->getPublicationsVisibles(
                (string) ($_SESSION['role'] ?? ''),
                (int) $section->getIdCanal()
            );
        }
    } catch (PDOException $e) {
        error_log('[GHD][forum] Recherche section : ' . $e->getMessage());
        http_response_code(500);
        $erreur = 'Section momentanément indisponible.';
    }
}

$pageTitle = 'Détail de section';
$activePage = 'forum';
$bodyClass = 'forum-layout';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/forum/sections/detailSectionView.php';
require __DIR__ . '/../views/layouts/footer.php';
