<?php
require_once __DIR__ . '/../src/traitement/forum/outils_forum.php';
require_once __DIR__ . '/../src/repository/CanalForumRepository.php';

exigerConnexionForum();

$estAdmin = estAdminForum();
$section = null;
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
        }
    } catch (PDOException $e) {
        error_log('[GHD][forum] Recherche section : ' . $e->getMessage());
        http_response_code(500);
        $erreur = 'Section momentanément indisponible.';
    }
}

require __DIR__ . '/../views/forum/sections/detailSectionView.php';
