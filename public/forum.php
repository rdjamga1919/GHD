<?php
// Les dépendances communes peuvent émettre des espaces avant les en-têtes.
ob_start();
require_once __DIR__ . '/../src/traitement/forum/outils_forum.php';
require_once __DIR__ . '/../src/repository/CanalForumRepository.php';

exigerConnexionForum();

$sections = [];
$erreur = null;
$estAdmin = estAdminForum();

try {
    $tous = (new CanalForumRepository())->getAllCanalForum();
    $sections = array_values(array_filter($tous, fn(CanalForum $c) => peutConsulterCanalForum($c)));
    }
    catch (PDOException $e) {
    error_log('[GHD][forum] Lecture sections : ' . $e->getMessage());
    $erreur = 'Les sections sont momentanément indisponibles.';
    }
$flash = lireMessageFlashForum();
$pageTitle = 'Sections du forum';
$activePage = 'forum';
$bodyClass = 'forum-layout';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/forum/sections/listeSectionsView.php';
require __DIR__ . '/../views/layouts/footer.php';
