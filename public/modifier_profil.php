<?php

session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';

requireLogin();

switch ($_SESSION['role']) {

    case 'medecin':
        require_once __DIR__ . '/../src/traitement/traitement_modif_medecin.php';
        require_once __DIR__ . '/../views/profils/modification/medecinViewModif.php';
        break;

    case 'etudiant':
        require_once __DIR__ . '/../src/traitement/traitement_modif_etudiant.php';
        require_once __DIR__ . '/../views/profils/modification/etudiantViewModif.php';
        break;

    case 'partenaire':
        require_once __DIR__ . '/../src/traitement/traitement_modif_partenaire.php';
        require_once __DIR__ . '/../views/profils/modification/partenaireViewModif.php';
        break;

    default:
        http_response_code(403);
        echo "Accès non autorisé.";
        break;
}