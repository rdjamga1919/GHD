<?php

session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/OffreRepository.php';

requireLogin();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$offre = null;

if ($id !== false && $id !== null && $id > 0) {
    $offre = (new OffreRepository())->getOffreAvecEntreprise($id);
}

if ($offre === null) {
    http_response_code(404);
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$estProprietaire = $offre !== null
    && ($_SESSION['role'] ?? '') === 'partenaire'
    && (int) $offre['ref_utilisateur'] === (int) $_SESSION['utilisateur_id'];

require_once __DIR__ . '/../views/offres/detail.php';