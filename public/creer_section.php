<?php
require_once __DIR__ . '/../src/traitement/forum/traitement_section.php';

exigerAdminForum();

$resultat = traiterCreationSection(new CanalForumRepository());
$valeurs = $resultat['valeurs'];
$erreurs = $resultat['erreurs'];

require __DIR__ . '/../views/forum/sections/creerSectionView.php';