<?php
// views/layouts/header.php

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
    <title><?= htmlspecialchars($pageTitle) ?> – GHD-HSP</title>
</head>
<body>
<header>
    <a href="/index.php"><strong>GHD-HSP</strong></a>
    <?php require __DIR__ . '/navbar.php'; ?>
</header>
<main>