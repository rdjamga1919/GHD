<?php

/**
 * Sécurité commune : session, authentification, rôles, CSRF,
 * messages flash, échappement HTML et redirections.
 *
 * ADAPTATION : le seul endroit qui connaît la structure de la session est
 * utilisateurId() / utilisateurRole(). Si ton système de session utilise
 * d'autres clés que $_SESSION['id_utilisateur'] et $_SESSION['role'],
 * modifie uniquement ces deux fonctions.
 */

// URLs RELATIVES (indépendantes du dossier d'installation).
const URL_CONNEXION        = 'connexion.php';
const URL_LISTE_EVENEMENTS = 'evenements.php';

const ROLE_GESTIONNAIRE        = 'gestionnaire';
const ROLES_CREATION_EVENEMENT = ['gestionnaire', 'medecin', 'partenaire'];

// Réutilise la session déjà ouverte, sinon la démarre.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------- Session ----------------------------

function utilisateurId(): ?int
{
    return isset($_SESSION['id_utilisateur']) ? (int) $_SESSION['id_utilisateur'] : null;
}

function utilisateurRole(): ?string
{
    return isset($_SESSION['role']) ? (string) $_SESSION['role'] : null;
}

function rediriger(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function exigerConnexion(): void
{
    if (utilisateurId() === null) {
        definirFlash('erreur', 'Veuillez vous connecter pour accéder à cette page.');
        rediriger(URL_CONNEXION);
    }
}

function exigerRole(array $rolesAutorises): void
{
    if (!in_array(utilisateurRole(), $rolesAutorises, true)) {
        definirFlash('erreur', "Vous n'avez pas l'autorisation d'effectuer cette action.");
        rediriger(URL_LISTE_EVENEMENTS);
    }
}

// ------------------------- Messages flash -------------------------

function definirFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Retourne le HTML du message flash (puis l'efface). À appeler dans les vues.
 */
function afficherFlash(): string
{
    if (empty($_SESSION['flash'])) {
        return '';
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    $classe = $flash['type'] === 'succes' ? 'message-succes' : 'message-erreur';
    return '<p class="message ' . $classe . '" role="alert">' . echapper($flash['message']) . '</p>';
}

// ------------------------------ CSRF ------------------------------

function jetonCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function champCsrf(): string
{
    return '<input type="hidden" name="csrf_token" value="' . echapper(jetonCsrf()) . '">';
}

function verifierCsrf(): void
{
    $envoye = $_POST['csrf_token'] ?? '';
    if (!is_string($envoye) || !hash_equals(jetonCsrf(), $envoye)) {
        http_response_code(403);
        exit('Requête refusée : jeton de sécurité invalide. Rechargez la page et réessayez.');
    }
}

// ---------------------------- Échappement ----------------------------

function echapper($valeur): string
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}