<?php
session_start();
require_once __DIR__ . '/../src/includes/auth.php';
requireRole('gestionnaire');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test permissions — Admin</title>
</head>
<body>
<h1>Page réservée au gestionnaire</h1>
<p>Si tu vois cette page, c'est que requireRole('gestionnaire') fonctionne.</p>

<h2>Critères à vérifier manuellement avec cette page</h2>
<ul>
    <li>Non connecté → doit rediriger vers /connexion.php</li>
    <li>Connecté en étudiant/médecin/partenaire → doit afficher "Accès refusé." (403)</li>
    <li>Connecté en gestionnaire → doit afficher cette page</li>
</ul>

<p><a href="/src/traitement/deconnexion.php">Se déconnecter</a></p>
</body>
</html>