<?php
require_once __DIR__ . '/../src/traitement/forum/traitement_publication.php';
exigerConnexionForum();

try {
    $canauxRepo = new CanalForumRepository();
    $canaux = array_values(array_filter($canauxRepo->getAllCanalForum(), 'peutConsulterCanalForum'));
    $idCanal = null;
    if (isset($_GET['id_canal'])) {
        $canal = chargerCanalPublication($canauxRepo, lireIdSection($_GET['id_canal']));
        $idCanal = (int) $canal->getIdCanal();
    }
    $resultat = traiterCreationPublication(new PostForumRepository(), $canauxRepo, $idCanal);
} catch (PDOException $e) {
    erreurTechniquePublication($e);
}
$valeurs = $resultat['valeurs'];
$erreurs = $resultat['erreurs'];
$pageTitle = 'Créer une publication';
$activePage = 'forum';
$bodyClass = 'forum-layout';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/forum/publications/creerPublicationView.php';
require __DIR__ . '/../views/layouts/footer.php';
