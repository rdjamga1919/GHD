<?php
// config/routes.php
// Seul endroit où déclarer les pages. null = la page n'existe pas encore (ou est encore vide), le lien est caché.
return [
    'accueil'      => '/GHD/public/index.php',
    'connexion'    => '/GHD/public/connexion.php',
    'inscription'  => '/GHD/public/inscription.php',
    'deconnexion'  => '/GHD/src/traitement/deconnexion.php',
    'profil'       => '/GHD/public/profil.php',
    'annuaire'     => '/GHD/public/annuaire.php',
    'offres'       => '/GHD/public/offres.php',
    'candidatures' => '/GHD/public/mes_candidatures.php',
    'mes_offres'   => null, // pas de page « Mes offres » pour l'instant
    'evenements'   => '/GHD/public/evenements.php',
    'forum'        => '/GHD/public/forum.php',
    'admin'        => '/GHD/public/admin/dashboard.php',
    'admin_utilisateurs' => '/GHD/public/admin/GestionUtilisateurs.php',
    'admin_specialites'  => null, // page encore vide : lien caché jusqu'à l'intégration
    'admin_hopitaux'     => null, // page encore vide : lien caché jusqu'à l'intégration
];