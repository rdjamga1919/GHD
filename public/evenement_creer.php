<?php
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/inclues/authentification.php';

requireLogin();

if (!hasRole('medecin') && !hasRole('partenaire')) {
    http_response_code(403);
    exit('Accès refusé.');
}
require_once __DIR__ . '/../src/traitement/traitement_creation_evenement.php';
require __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/evenements/evenement_creation_view.php';
        require __DIR__ . '/../views/layouts/footer.php';
