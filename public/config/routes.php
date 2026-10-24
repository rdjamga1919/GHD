<?php
// config/routes.php
// Seul endroit où déclarer les pages. null = la page n'existe pas encore, le lien est caché.
return [
    'accueil'      => '/index.php',
    'connexion'    => '/login.php',
    'inscription'  => '/register.php',
    'deconnexion'  => '/logout.php',
    'profil'       => '/profil.php',
    'annuaire'     => '/annuaire.php',
    'offres'       => '/offres.php',
    'candidatures' => null,
    'mes_offres'   => null,
    'evenements'   => null,
    'forum'        => null,
    'admin'        => null,
];