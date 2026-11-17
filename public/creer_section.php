<?php
// Les dépendances communes peuvent émettre des espaces avant les en-têtes.
ob_start();
require_once __DIR__ . '/../src/traitement/forum/traitement_section.php';

exigerAdminForum();

$resultat = traiterCreationSection(new CanalForumRepository());
$valeurs = $resultat['valeurs'];
$erreurs = $resultat['erreurs'];

$pageTitle = 'Créer une section';
$activePage = 'forum';
$bodyClass = 'forum-layout';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/forum/sections/creerSectionView.php';
require __DIR__ . '/../views/layouts/footer.php';
