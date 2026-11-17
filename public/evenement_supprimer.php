<?php
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/traitement/traitement_suppression_evenement.php';

require __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/evenements/evenement_supprimer_view.php';
        require __DIR__ . '/../views/layouts/footer.php';
