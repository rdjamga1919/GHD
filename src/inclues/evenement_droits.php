<?php
// src/inclues/evenement_droits.php
// Droits liés à l'organisation d'événements (utilisés pour AFFICHER les boutons).
// Les contrôles de sécurité réels restent faits côté serveur dans
// traitement_creation_evenement.php, traitement_modif_evenement.php et
// traitement_suppression_evenement.php (403 si l'utilisateur n'a pas les droits).

require_once __DIR__ . '/authentification.php';

// Rôles autorisés à organiser des événements.
const ROLES_ORGANISATEURS = ['medecin', 'partenaire'];

/** L'utilisateur connecté peut-il créer un événement ? */
function peutCreerEvenement(): bool
{
    return isLoggedIn()
        && in_array($_SESSION['role'] ?? '', ROLES_ORGANISATEURS, true);
}

/**
 * L'utilisateur connecté peut-il modifier / supprimer cet événement ?
 * Il faut avoir un rôle autorisé ET figurer parmi les organisateurs de l'événement.
 *
 * @param array $organisateurs résultat de EvenementRepository::getOrganisateurs()
 */
function peutGererEvenement(array $organisateurs): bool
{
    if (!peutCreerEvenement()) {
        return false;
    }

    $idUtilisateur = (int) $_SESSION['utilisateur_id'];
    foreach ($organisateurs as $organisateur) {
        if ((int) $organisateur['id_utilisateur'] === $idUtilisateur) {
            return true;
        }
    }
    return false;
}