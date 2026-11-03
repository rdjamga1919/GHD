<?php
session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/OffreRepository.php';

// Vérification que l'utilisateur est connecté
requireLogin();

// Génération du jeton CSRF s'il n'existe pas
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Récupération et validation de l'ID de l'offre
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$offre = null;

if ($id !== false && $id !== null && $id > 0) {
    $offre = (new OffreRepository())->getOffreAvecEntreprise($id);
}

// Si l'offre n'existe pas, renvoyer une erreur 404
if ($offre === null) {
    http_response_code(404);
}

// Récupération des messages flash
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Vérification si l'utilisateur connecté est le propriétaire de l'offre
$estProprietaire = $offre !== null
    && ($_SESSION['role'] ?? '') === 'partenaire'
    && (int) $offre['ref_utilisateur'] === (int) $_SESSION['utilisateur_id'];

// Inclusion de la vue associée pour l'affichage
require_once __DIR__ . '/../views/offres/detail.php';