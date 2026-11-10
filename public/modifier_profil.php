<?php
ob_start();
session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';

// Vérification que l'utilisateur est connecté
requireLogin();

// Routage en fonction du rôle de l'utilisateur
switch ($_SESSION['role']) {

    case 'medecin':
        require_once __DIR__ . '/../src/traitement/traitement_modif_medecin.php';
        require __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/profils/modification/medecinViewModif.php';
        require __DIR__ . '/../views/layouts/footer.php';
        break;

    case 'etudiant':
        require_once __DIR__ . '/../src/traitement/traitement_modif_etudiant.php';
        require __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/profils/modification/etudiantViewModif.php';
        require __DIR__ . '/../views/layouts/footer.php';
        break;

    case 'partenaire':
        require_once __DIR__ . '/../src/traitement/traitement_modif_partenaire.php';
        require __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/profils/modification/partenaireViewModif.php';
        require __DIR__ . '/../views/layouts/footer.php';
        break;

    default:
        http_response_code(403);
        echo "Accès non autorisé.";
        break;
}
