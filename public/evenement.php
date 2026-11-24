<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/inclues/evenement_droits.php';
require_once __DIR__ . '/../src/repository/EvenementRepository.php';

// Message flash éventuel (succès / erreur après une action)
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$peutGerer = false;

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
            // Boutons « Modifier » / « Supprimer » : organisateur médecin ou partenaire uniquement
            $peutGerer = peutGererEvenement($organisateurs);
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

$layouts = __DIR__ . '/../views/layouts/';
$entete = $layouts . 'header.php';
$pied = $layouts . 'footer.php';

require $entete;
require __DIR__ . '/../views/evenements/detailView.php';
require $pied;