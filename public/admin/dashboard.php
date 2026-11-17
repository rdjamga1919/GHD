<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../src/inclues/authentification.php';
require_once __DIR__ . '/../../src/repository/AdminDashboardRepository.php';

// Réservé aux gestionnaires : redirection vers la connexion si non connecté, 403 sinon.
requireRole('gestionnaire');

$statistiques = null;
$erreur = null;

try {
    $statistiques = (new AdminDashboardRepository())->getStatistiques();
} catch (PDOException $e) {
    error_log('[GHD][admin] Tableau de bord : ' . $e->getMessage());
    $erreur = 'Les statistiques sont momentanément indisponibles.';
}

// Accès rapides : seules les routes déclarées (non null) dans config/routes.php sont affichées.
$routes = require __DIR__ . '/../../config/routes.php';
$accesRapides = [];
foreach ([
    'admin_utilisateurs' => 'Gérer les utilisateurs',
    'admin_specialites'  => 'Gérer les spécialités',
    'admin_hopitaux'     => 'Gérer les hôpitaux',
] as $cle => $libelle) {
    if (!empty($routes[$cle])) {
        $accesRapides[] = ['url' => $routes[$cle], 'libelle' => $libelle];
    }
}

$pageTitle = 'Tableau de bord';
$activePage = 'admin';
$breadcrumbs = [
    ['label' => 'Accueil', 'url' => '/GHD/public/index.php'],
    ['label' => 'Administration', 'url' => null],
    ['label' => 'Tableau de bord', 'url' => null],
];

require __DIR__ . '/../../views/layouts/header.php';
require __DIR__ . '/../../views/admin/dashboardView.php';
require __DIR__ . '/../../views/layouts/footer.php';
