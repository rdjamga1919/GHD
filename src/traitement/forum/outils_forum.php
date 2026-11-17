<?php

require_once __DIR__ . '/../../inclues/authentification.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Chemins du module
const URL_LISTE_SECTIONS = '/GHD/public/forum.php';
const URL_DETAIL_SECTION = '/GHD/public/section.php';
const URL_CREER_SECTION = '/GHD/public/creer_section.php';
const URL_MODIFIER_SECTION = '/GHD/public/modifier_section.php';

// Vérifier la connexion avec le système existant
function exigerConnexionForum(): void
{
    requireLogin();
}

// Le rôle gestionnaire correspond à l'administrateur
function estAdminForum(): bool
{
    return isAdmin();
}

function exigerAdminForum(): void
{
    exigerConnexionForum();

    if (!estAdminForum()) {
        http_response_code(403);
        exit('Accès refusé.');
    }
}

// Vérification des identifiants reçus par URL ou formulaire
function lireIdSection($valeur): ?int
{
    $id = filter_var(
        $valeur,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    return $id === false ? null : $id;
}

// Échapper les données affichées dans les vues
function echapper($valeur): string
{
    return htmlspecialchars(
        (string) $valeur,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// Générer un jeton CSRF
function jetonCsrfForum(): string
{
    if (empty($_SESSION['csrf_forum'])) {
        $_SESSION['csrf_forum'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_forum'];
}

// Champ caché à placer dans les formulaires
function champCsrf(): string
{
    return '<input type="hidden" name="csrf_forum" value="'
        . echapper(jetonCsrfForum())
        . '">';
}

// Vérification CSRF uniquement lors des requêtes POST
function verifierCsrfForum(): bool
{
    $jeton = $_POST['csrf_forum'] ?? null;

    return is_string($jeton)
        && hash_equals(jetonCsrfForum(), $jeton);
}

// Messages temporaires après redirection
function lireMessageFlashForum(): ?array
{
    $flash = $_SESSION['flash_forum'] ?? null;
    unset($_SESSION['flash_forum']);

    return is_array($flash) ? $flash : null;
}

function redirigerAvecMessageForum(
    string $url,
    string $type,
    string $message
): void {
    $_SESSION['flash_forum'] = [
        'type' => $type,
        'message' => $message
    ];

    header('Location: ' . $url, true, 303);
    exit;
}

// Autorisation de consultation d'un canal
function peutConsulterCanalForum(CanalForum $canal): bool
{
    if (!isLoggedIn()) {
        return false;
    }

    if (isAdmin()) {
        return true;
    }

    $role = $_SESSION['role'] ?? null;

    return match ($canal->getAcces()) {
        'generale' => true,
        'etudiants' => $role === 'etudiant',
        'medecins' => $role === 'medecin',
        default => false,
    };
}