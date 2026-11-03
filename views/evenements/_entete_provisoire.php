<?php
// Repli PROVISOIRE : utilisé seulement tant que views/layouts/header.php n'existe pas sur test.
// À supprimer (avec _pied_provisoire.php) une fois l'accueil intégré.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = $pageTitle ?? 'GHD-HSP';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> – GHD-HSP</title>
</head>
<body>
<header>
    <a href="index.php"><strong>GHD-HSP</strong></a>
    <nav>
        <a href="evenements.php">Événements</a>
        <a href="connexion.php">Connexion</a>
    </nav>
</header>
<main>
