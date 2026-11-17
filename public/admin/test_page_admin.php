<?php
ob_start();
session_start();
require_once __DIR__ . '/../../src/inclues/authentification.php';
requireRole('gestionnaire');
?>
<?php require __DIR__ . '/../../views/layouts/header.php'; ?>

<h1 class="page-title">Page réservée au gestionnaire</h1>
<p>Si tu vois cette page, c'est que requireRole('gestionnaire') fonctionne.</p>

<h2>Critères à vérifier manuellement avec cette page</h2>
<ul>
    <li>Non connecté → doit rediriger vers /connexion.php</li>
    <li>Connecté en étudiant/médecin/partenaire → doit afficher "Accès refusé." (403)</li>
    <li>Connecté en gestionnaire → doit afficher cette page</li>
</ul>

<p><a href="/GHD/src/traitement/deconnexion.php">Se déconnecter</a></p>
<?php require __DIR__ . '/../../views/layouts/footer.php'; ?>
