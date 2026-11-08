<?php
require_once __DIR__ . '/../src/traitement/forum/traitement_section.php';

exigerAdminForum();

$repo = new SectionRepository();

// L'identifiant vient de l'URL (GET) ou du champ caché (POST) : toujours validé.
$id = lireIdSection($_POST['id_section'] ?? ($_GET['id'] ?? null));
$section = null;
if ($id !== null) {
    try {
        $section = $repo->findById($id);
    } catch (PDOException $ex) {
        error_log('[GHD][forum] Echec lecture section : ' . $ex->getMessage());
        redirigerAvecMessageForum(URL_LISTE_SECTIONS, 'erreur', 'La section est momentanément indisponible.');
    }
}
if ($section === null) {
    redirigerAvecMessageForum(URL_LISTE_SECTIONS, 'erreur', 'Section introuvable.');
}

$resultat = traiterModificationSection($repo, $section);
$valeurs = $resultat['valeurs'];
$erreurs = $resultat['erreurs'];

require __DIR__ . '/../views/forum/sections/modifierSectionView.php';