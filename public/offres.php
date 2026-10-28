<?php
session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/OffreRepository.php';

// Vérification que l'utilisateur est connecté
requireLogin();

// Récupération des offres et des messages flash
$offres = (new OffreRepository())->getOffresAvecEntreprise();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Vérification si l'utilisateur est un partenaire
$estPartenaire = ($_SESSION['role'] ?? '') === 'partenaire';

// Inclusion de la vue associée pour l'affichage de la liste
require_once __DIR__ . '/../views/offres/liste.php';