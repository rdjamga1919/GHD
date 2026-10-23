<?php

session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';

requireRole('partenaire');

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$flash = $_SESSION['flash'] ?? null;
$old = $_SESSION['old'] ?? [];
unset($_SESSION['flash'], $_SESSION['old']);

$offre = null;
$action = 'creer';
$titrePage = 'Créer une offre';

require_once __DIR__ . '/../views/offres/formulaire.php';