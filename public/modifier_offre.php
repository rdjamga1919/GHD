<?php
session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/OffreRepository.php';

// Vérification du rôle partenaire requis
requireRole('partenaire');

// Génération du jeton CSRF s'il n'existe pas
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Récupération et validation de l'ID de l'offre
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$offre = null;

if ($id !== false && $id !== null && $id > 0) {
    $offre = (new OffreRepository())->getOffreDuProprietaire($id, (int) $_SESSION['utilisateur_id']);
}

// Vérification si l'offre existe, appartient bien au partenaire et est ouverte
if ($offre === null || $offre['etat'] !== 'ouverte') {
    $_SESSION['flash'] = ['erreur', "Offre introuvable, fermée ou qui ne vous appartient pas."];
    header('Location: offres.php');
    exit;
}

// Récupération des messages flash et anciennes données de formulaire
$flash = $_SESSION['flash'] ?? null;
$old = $_SESSION['old'] ?? [];
unset($_SESSION['flash'], $_SESSION['old']);

// Paramètres de la vue du formulaire
$action = 'modifier';
$titrePage = "Modifier l'offre - GHD-HSP";

// Inclusion de la vue du formulaire
require_once __DIR__ . '/../views/offres/formulaire.php';