<?php
//page : src/inclues/authentification
// Ce fichier suppose que session_start() a déjà été appelé par la page
// qui l'inclut. Il s'appuie sur les clés de session posées par
// connexion_traitement.php : $_SESSION['utilisateur_id'] et $_SESSION['role'].

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
        header('Location: /GHD/public/connexion.php');
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
