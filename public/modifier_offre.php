<?php

session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/OffreRepository.php';

requireRole('partenaire');

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$offre = null;

if ($id !== false && $id !== null && $id > 0) {
    $offre = (new OffreRepository())->getOffreDuProprietaire($id, (int) $_SESSION['utilisateur_id']);
}

if ($offre === null || $offre['etat'] !== 'ouverte') {
    $_SESSION['flash'] = ['erreur', "Offre introuvable, fermée ou qui ne vous appartient pas."];
    header('Location: offres.php');
    exit;
}

$flash = $_SESSION['flash'] ?? null;
$old = $_SESSION['old'] ?? [];
unset($_SESSION['flash'], $_SESSION['old']);

$action = 'modifier';
$titrePage = "Modifier l'offre";

require_once __DIR__ . '/../views/offres/formulaire.php';