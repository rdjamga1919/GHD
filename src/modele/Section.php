<?php
require_once __DIR__ . '/../src/traitement/forum/outils_forum.php';
require_once __DIR__ . '/../src/repository/SectionRepository.php';

exigerConnexionForum();

$section = null;
$erreur = null;
$id = lireIdSection($_GET['id'] ?? null);

if ($id === null) {
    http_response_code(404);
    $erreur = 'Section introuvable.';
} else {
    try {
        $section = (new SectionRepository())->findById($id);
    } catch (PDOException $ex) {
        error_log('[GHD][forum] Echec lecture section : ' . $ex->getMessage());
        http_response_code(500);
        $erreur = 'La section est momentanément indisponible. Veuillez réessayer.';
    }
    if ($section === null && $erreur === null) {
        http_response_code(404);
        $erreur = 'Section introuvable.';
    }
}

$flash = lireMessageFlashForum();
$estAdmin = estAdminForum();

require __DIR__ . '/../views/forum/sections/detailSectionView.php';