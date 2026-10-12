<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function exigerConnexion(?string $roleAttendu = null): void
{
    // 1. Pas connecté ? On renvoie vers la connexion
    if (!isset($_SESSION['utilisateur_id'])) {
        header('Location: ../public/connexion.php');
        exit;
    }

    // 2. Connecté, mais pas le bon rôle ? On renvoie aussi
    if ($roleAttendu !== null && $_SESSION['role'] !== $roleAttendu) {
        header('Location: ../public/connexion.php');
        exit;
    }
}