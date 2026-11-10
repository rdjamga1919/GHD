<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/traitement/traitement_suppression_evenement.php';

require_once __DIR__ . '/../views/evenements/evenement_supprimer_view.php';
