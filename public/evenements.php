<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/inclues/evenement_droits.php';
require_once __DIR__ . '/../src/repository/EvenementRepository.php';

// Message flash éventuel (ex. « Événement supprimé avec succès. »)
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$peutCreer = peutCreerEvenement();

$evenements = [];
$erreur = null;

try {
    $evenements = (new EvenementRepository())->getEvenementsAvecDetails();
} catch (PDOException $e) {
    error_log('evenements.php : ' . $e->getMessage());
    $erreur = "Les événements sont momentanément indisponibles.";
}

$pageTitle = 'Événements';

$layouts = __DIR__ . '/../views/layouts/';
$entete = $layouts . 'header.php';
$pied = $layouts . 'footer.php';

require $entete;
require __DIR__ . '/../views/evenements/listeView.php';
require $pied;