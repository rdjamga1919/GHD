<?php

require_once __DIR__ . '/../src/traitement/securite.php';
require_once __DIR__ . '/../src/traitement/evenement_traitement.php';

exigerConnexion();

$repo = new EvenementRepository();
$id   = lireIdentifiant($_GET);

// Événement inexistant
$evenement = $id !== null ? $repo->findById($id) : null;
if ($evenement === null) {
    definirFlash('erreur', "Cet événement n'existe pas.");
    rediriger(URL_LISTE_EVENEMENTS);
}

// Permissions (vérifiées côté serveur à chaque requête)
if (!peutGererEvenement($repo, $id)) {
    definirFlash('erreur', "Vous n'avez pas l'autorisation de modifier cet événement.");
    rediriger(URL_LISTE_EVENEMENTS);
}

$nbInscrits = $repo->compterInscrits($id);
$valeurs    = valeursDepuisEvenement($evenement); // préremplissage
$erreurs    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifierCsrf();
    [$ok, $valeurs, $erreurs] = traiterModification($repo, $id, $_POST);

    if ($ok) {
        definirFlash('succes', "L'événement a bien été modifié.");
        rediriger(URL_LISTE_EVENEMENTS);
    }
}

$titrePage        = "Modifier l'événement";
$actionUrl        = 'evenement_modifier.php?id=' . $id;
$modeModification = true;

require __DIR__ . '/../views/evenement_formulaire.php';