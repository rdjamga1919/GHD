<?php
// Les dépendances communes peuvent émettre des espaces avant les en-têtes.
ob_start();
require_once __DIR__ . '/../src/traitement/forum/traitement_section.php';

exigerAdminForum();

$repo = new CanalForumRepository();
$id = lireIdSection($_POST['id_canal'] ?? ($_GET['id'] ?? null));

if ($id === null) {
    http_response_code(404);
    exit('Section introuvable.');
}

try {
    $section = $repo->getCanalForum($id);
    } catch (PDOException $e) {
    error_log('[GHD][forum] Recherche section : ' . $e->getMessage());
    http_response_code(500);
    exit('Section momentanément indisponible.');
    }
if ($section === null) {
    http_response_code(404);
    exit('Section introuvable.');
}
$erreurSuppression = null;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['action'] ?? '') === 'supprimer') {
    $erreurSuppression = traiterSuppressionSection($repo, $section);
    $resultat = ['valeurs' => [
        'nom' => $section->getNomCanal(),
        'description' => (string) $section->getDescription(),
        'acces' => $section->getAcces(),
    ], 'erreurs' => []];
}
else {
    $resultat = traiterModificationSection($repo, $section);
}
$valeurs = $resultat['valeurs'];
$erreurs = $resultat['erreurs'];
$pageTitle = 'Modifier une section';
$activePage = 'forum';
$bodyClass = 'forum-layout';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/forum/sections/modifierSectionView.php';
require __DIR__ . '/../views/layouts/footer.php';
