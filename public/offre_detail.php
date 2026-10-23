<?php

session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/OffreRepository.php';

requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$offre = null;

if ($id !== false && $id !== null && $id > 0) {
    $offre = (new OffreRepository())->getOffreAvecEntreprise($id);
}

if ($offre === null) {
    http_response_code(404);
}

require_once __DIR__ . '/../views/offres/detail.php';