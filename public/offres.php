<?php

session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/OffreRepository.php';

requireLogin();

$offres = (new OffreRepository())->getOffresAvecEntreprise();

require_once __DIR__ . '/../views/offres/liste.php';