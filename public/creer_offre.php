<?php
session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';

// Vérification du rôle partenaire requis
requireRole('partenaire');

// Génération du jeton CSRF s'il n'existe pas
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Récupération et nettoyage des messages flash et anciennes données
$flash = $_SESSION['flash'] ?? null;
$old = $_SESSION['old'] ?? [];
unset($_SESSION['flash'], $_SESSION['old']);

// Initialisation des variables pour le formulaire
$offre = null;
$action = 'creer';
$titrePage = 'Créer une offre - GHD-HSP';

// Inclusion de la vue du formulaire associée
require_once __DIR__ . '/../views/offres/formulaire.php';