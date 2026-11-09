<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/traitement/traitement_modif_evenement.php';

require_once __DIR__ . '/../views/evenements/evenement_modification_view.php';
