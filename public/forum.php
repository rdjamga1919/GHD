<?php
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
require __DIR__ . '/../views/forum/sections/listeSectionsView.php';