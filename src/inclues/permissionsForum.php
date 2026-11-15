<?php

/*
 * Règles de permissions du forum (réponses).
 * Elles utilisent le rôle et l'id de l'utilisateur connecté (session).
 *
 * session_start() doit être appelé AVANT d'utiliser ces fonctions.
 */

// Lecture / réponse selon l'accès du canal : 'generale', 'medecins' ou 'etudiants'
function peutLireCanal(string $acces): bool
{
    $role = $_SESSION['role'] ?? '';

    if ($role === 'gestionnaire') {
        return true;                    // le gestionnaire voit tous les canaux
    }
    if ($acces === 'generale') {
        return $role !== '';            // tout utilisateur connecté
    }
    if ($acces === 'medecins') {
        return $role === 'medecin';
    }
    if ($acces === 'etudiants') {
        return $role === 'etudiant';
    }
    return false;
}

// L'utilisateur connecté est-il l'auteur de la réponse ?
function estAuteurReponse(ReponseForum $reponse): bool
{
    return isset($_SESSION['id_utilisateur'])
        && (int) $reponse->getRefUtilisateur() === (int) $_SESSION['id_utilisateur'];
}

// Modifier : seulement l'auteur
function peutModifierReponse(ReponseForum $reponse): bool
{
    return estAuteurReponse($reponse);
}

// Supprimer : l'auteur ou le gestionnaire (modération)
function peutSupprimerReponse(ReponseForum $reponse): bool
{
    return estAuteurReponse($reponse) || ($_SESSION['role'] ?? '') === 'gestionnaire';
}


/*
 * Protection CSRF.
 *
 * Un "jeton" secret est créé et gardé en session. Il est ajouté dans chaque
 * formulaire (champ caché). Le traitement vérifie que le jeton reçu est le bon.
 * Un site pirate ne connaît pas ce jeton : il ne peut pas envoyer de formulaire
 * à la place de l'utilisateur connecté.
 */

// Retourne le jeton de la session (et le crée s'il n'existe pas encore)
function genererTokenCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// À afficher dans chaque formulaire avec : echo champCsrf();
function champCsrf(): string
{
    return '<input type="hidden" name="csrf_token" value="' . genererTokenCsrf() . '">';
}

// Vérifie le jeton reçu dans le formulaire (à utiliser dans le traitement)
function verifierTokenCsrf($tokenRecu): bool
{
    return !empty($_SESSION['csrf_token'])
        && is_string($tokenRecu)
        && hash_equals($_SESSION['csrf_token'], $tokenRecu);
}