<?php
require_once __DIR__ . '/../src/traitement/forum/outils_forum.php';
require_once __DIR__ . '/../src/repository/SectionRepository.php';

exigerConnexionForum();

$sections = [];
$erreur = null;
try {
    $sections = (new SectionRepository())->findAll();
} catch (PDOException $ex) {
    error_log('[GHD][forum] Echec lecture sections : ' . $ex->getMessage());
    $erreur = 'Les sections sont momentanément indisponibles. Veuillez réessayer.';
}

$flash = lireMessageFlashForum();
$estAdmin = estAdminForum();

require __DIR__ . '/../views/forum/sections/listeSectionsView.php';