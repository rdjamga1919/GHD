<?php

require_once __DIR__ . '/../src/repository/EvenementRepository.php';

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
$entete = is_file($layouts . 'header.php') ? $layouts . 'header.php' : __DIR__ . '/../views/evenements/_entete_provisoire.php';
$pied = is_file($layouts . 'footer.php') ? $layouts . 'footer.php' : __DIR__ . '/../views/evenements/_pied_provisoire.php';

require $entete;
require __DIR__ . '/../views/evenements/listeView.php';
require $pied;
