<?php
session_start();
require_once __DIR__ . '/../src/traitement/traitement_modif_evenement.php';
require_once __DIR__ . '/../views/evenements/evenement_modification_view.php';

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/inclus/header.php';
require_once __DIR__ . '/../src/repository/EvenementRepository.php';

requireRole('partenaire', 'medecin');

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Récupération des messages flash et anciennes données de formulaire
$flash = $_SESSION['flash'] ?? null;
$old = $_SESSION['old'] ?? [];
unset($_SESSION['flash'], $_SESSION['old']);

// Paramètres de la vue du formulaire
$action = 'modifier';
$titrePage = "Modifier l'évènement - GHD-HSP";


require_once __DIR__ . '/../views/evenements/evenement_modififcation_view.php';
require_once __DIR__ . '/../src/traitement/traitement_modif_evenement.php';
