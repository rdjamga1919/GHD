<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool
{
    return isset($_SESSION['utilisateur_id']);
}

function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? null) === 'gestionnaire';
}

function hasRole(string $role): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? null) === $role;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . urlConnexion());
        exit;
    }
}

function requireRole(string $role): void
{
    requireLogin();
    if (!hasRole($role)) {
        http_response_code(403);
        exit('Accès refusé.');
    }
}

function urlConnexion(): string
{
    $racineProjet = str_replace('\\', '/', (string) realpath(__DIR__ . '/../..'));
    $racineWeb = str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));

    if ($racineWeb !== '' && stripos($racineProjet, $racineWeb) === 0) {
        return substr($racineProjet, strlen($racineWeb)) . '/public/connexion.php';
    }

    return '/public/connexion.php';
}