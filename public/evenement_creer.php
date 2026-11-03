<?php
// public/evenement_creer.php
// Point d'entrée : aucune requête SQL ici, aucun HTML de formulaire.

session_start();

// Marqueur : seules les pages de public/ le définissent. Les vues le vérifient
// pour refuser tout accès direct par URL.
define('APP_ENTRY', true);

require_once __DIR__ . '/../src/inclues/authentification.php';

requireLogin();

// Vérifier les permissions de création (rôle « gestionnaire »).
if (!isAdmin()) {
    http_response_code(403);
    exit('Accès refusé.');
}

// Charger le traitement.
// GET : il s'arrête aussitôt (return). POST : validation + insertion,
// puis header('Location: ...'); exit; en cas de succès.
$erreurs = [];
$valeurs = [];
require __DIR__ . '/../src/traitement/evenement_creer_traitement.php';

// Charger la vue (atteinte seulement si aucune redirection n'a eu lieu).
require __DIR__ . '/../views/evenements/evenementCreerView.php';