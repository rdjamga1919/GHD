<?php

require_once __DIR__ . '/../src/traitement/securite.php';
require_once __DIR__ . '/../src/traitement/evenement_traitement.php';

exigerConnexion();
exigerRole(ROLES_CREATION_EVENEMENT);

$repo    = new EvenementRepository();
$valeurs = valeursVidesEvenement();
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCsrf();
    [$ok, $valeurs, $erreurs] = traiterCreation($repo, $_POST, utilisateurId());

    if ($ok) {
        definirFlash('succes', "L'événement a bien été créé.");
        rediriger(URL_LISTE_EVENEMENTS);
    }
}

$titrePage        = 'Créer un événement';
$actionUrl        = 'evenement_creer.php';
$modeModification = false;
$nbInscrits       = 0;

require __DIR__ . '/../views/evenement_formulaire.php';