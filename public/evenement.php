<?php

require_once __DIR__ . '/../src/repository/EvenementRepository.php';

$idEvenement = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$evenement = null;
$organisateurs = [];
$erreur = null;

if ($idEvenement !== false && $idEvenement !== null && $idEvenement > 0) {
    try {
        $repo = new EvenementRepository();
        $evenement = $repo->getEvenementDetail($idEvenement);
        if ($evenement !== null) {
            $organisateurs = $repo->getOrganisateurs($idEvenement);
        }
    } catch (PDOException $e) {
        error_log('evenements.php : ' . $e->getMessage());
        $erreur = "L'événement est momentanément indisponible.";
    }
}

if ($evenement === null && $erreur === null) {
    http_response_code(404);
}

$pageTitle = $evenement !== null ? $evenement['titre'] : 'Événement introuvable';

// Header/footer communs s'ils existent (accueil), sinon repli provisoire.
$layouts = __DIR__ . '/../views/layouts/';
$entete = is_file($layouts . 'header.php') ? $layouts . 'header.php' : __DIR__ . '/../views/evenements/_entete_provisoire.php';
$pied = is_file($layouts . 'footer.php') ? $layouts . 'footer.php' : __DIR__ . '/../views/evenements/_pied_provisoire.php';

require $entete;
require __DIR__ . '/../views/evenements/detailView.php';
require $pied;
