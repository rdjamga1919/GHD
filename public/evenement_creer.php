<?php
// Point d'entrée : session, authentification, permission, puis traitement et vue.
session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/modele/Evenement.php';
require_once __DIR__ . '/../src/repository/EvenementRepository.php';

// À CONFIRMER : rôles autorisés à créer un événement (valeurs de la colonne Utilisateur.role).
const ROLES_CREATION_EVENEMENT = ['gestionnaire', 'medecin', 'partenaire'];

// Vérifié à chaque requête, GET comme POST : cette page est l'entrée des deux.
requireLogin();

$roleAutorise = false;
foreach (ROLES_CREATION_EVENEMENT as $role) {
    if (hasRole($role)) {
        $roleAutorise = true;
        break;
    }
}
if (!$roleAutorise) {
    http_response_code(403);
    exit('Accès refusé.');
}

require __DIR__ . '/../src/traitement/traitement_creation_evenement.php'; // prépare $valeurs, $erreurs, $flash, $csrfToken
require __DIR__ . '/../views/evenements/evenement_creation_view.php';