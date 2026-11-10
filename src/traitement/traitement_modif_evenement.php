<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../inclues/authentification.php';
require_once __DIR__ . '/../repository/EvenementRepository.php';

requireLogin();

// Seuls les médecins et partenaires peuvent modifier un événement
if (!hasRole('medecin') && !hasRole('partenaire')) {
    http_response_code(403);
    exit('Accès refusé.');
}

$repo = new EvenementRepository();
$idUtilisateur = (int) ($_SESSION['utilisateur_id'] ?? 0);

// Fonction pour enregistrer un message flash
function flashEvenement(string $type, string $message): void
{
    $_SESSION['flash'] = [$type, $message];
}

// Vérifie que l'utilisateur organise cet événement
function peutModifierEvenement(
    EvenementRepository $repo,
    int $idEvenement,
    int $idUtilisateur
): bool {
    if ($idUtilisateur <= 0) {
        return false;
    }

    $organisateurs = $repo->getOrganisateurs($idEvenement);

    foreach ($organisateurs as $organisateur) {
        if ((int) $organisateur['id_utilisateur'] === $idUtilisateur) {
            return true;
        }
    }

    return false;
}

// Récupération de l'identifiant selon la méthode HTTP
$methode = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($methode !== 'GET' && $methode !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée.');
}

$source = $methode === 'POST' ? $_POST : $_GET;

$idEvenement = filter_var(
    $source['id_evenement'] ?? $source['id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($idEvenement === false || $idEvenement === null) {
    http_response_code(400);
    exit('Identifiant d’événement invalide.');
}

// Récupération de l'événement
$evenement = $repo->getEvenement($idEvenement);

if ($evenement === null) {
    http_response_code(404);
    exit('Événement introuvable.');
}

// Vérification des droits en GET ET en POST
if (!peutModifierEvenement($repo, $idEvenement, $idUtilisateur)) {
    http_response_code(403);
    exit('Vous ne pouvez pas modifier cet événement.');
}

// Création du jeton CSRF pour le formulaire
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$csrf = $_SESSION['csrf'];

// Récupération des messages
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$erreurs = [];

// Traitement du formulaire
if ($methode === 'POST') {

    // Vérification de l'action
    if (($_POST['action'] ?? '') !== 'modifier') {
        http_response_code(400);
        exit('Action invalide.');
    }

    // Vérification du CSRF
    if (
        !isset($_POST['csrf']) ||
        !is_string($_POST['csrf']) ||
        !hash_equals($csrf, $_POST['csrf'])
    ) {
        http_response_code(403);
        exit('Requête invalide. Veuillez réessayer.');
    }

    // Récupération des champs
    $champs = [
        'titre',
        'description',
        'type_evenement',
        'adresse_lieu',
        'element_requis',
        'nombre_place',
        'date_heure_evenement'
    ];

    $valeurs = [];

    foreach ($champs as $champ) {
        $valeur = $_POST[$champ] ?? '';

        if (!is_string($valeur)) {
            $erreurs[] = "Le champ $champ est invalide.";
            $valeur = '';
        }

        $valeurs[$champ] = trim($valeur);
    }

    $titre = $valeurs['titre'];
    $description = $valeurs['description'];
    $typeEvenement = $valeurs['type_evenement'];
    $adresseLieu = $valeurs['adresse_lieu'];
    $elementRequis = $valeurs['element_requis'];

    // Validation des champs obligatoires
    if ($titre === '') {
        $erreurs[] = 'Le titre est obligatoire.';
    }

    if ($typeEvenement === '') {
        $erreurs[] = 'Le type d’événement est obligatoire.';
    }

    if ($adresseLieu === '') {
        $erreurs[] = 'L’adresse est obligatoire.';
    }

    // Validation des longueurs
    if (mb_strlen($titre) > 150) {
        $erreurs[] = 'Le titre ne doit pas dépasser 150 caractères.';
    }

    if (mb_strlen($description) > 2000) {
        $erreurs[] = 'La description est trop longue.';
    }

    if (mb_strlen($typeEvenement) > 50) {
        $erreurs[] = 'Le type d’événement est trop long.';
    }

    if (mb_strlen($adresseLieu) > 255) {
        $erreurs[] = 'L’adresse est trop longue.';
    }

    if (mb_strlen($elementRequis) > 255) {
        $erreurs[] = 'Les éléments requis sont trop longs.';
    }

    // Validation du nombre de places
    $nombrePlace = filter_var(
        $valeurs['nombre_place'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1, 'max_range' => 1000]]
    );

    if ($nombrePlace === false) {
        $erreurs[] = 'Le nombre de places doit être compris entre 1 et 1000.';
    }

    // Validation de la date et de l'heure
    $dateHeure = DateTimeImmutable::createFromFormat(
        '!Y-m-d\TH:i',
        $valeurs['date_heure_evenement']
    );

    if (
        $dateHeure === false ||
        $dateHeure->format('Y-m-d\TH:i') !== $valeurs['date_heure_evenement']
    ) {
        $erreurs[] = 'La date et l’heure sont invalides.';
    }

    // Vérifier que la capacité ne descend pas sous les inscriptions
    if ($nombrePlace !== false) {
        $details = $repo->getEvenementDetail($idEvenement);
        $nbInscrits = (int) ($details['nb_inscrits'] ?? 0);

        if ($nombrePlace < $nbInscrits) {
            $erreurs[] = "Impossible de définir moins de $nbInscrits places : des personnes sont déjà inscrites.";
        }
    }

    // Enregistrement si toutes les données sont valides
    if (empty($erreurs)) {

        $evenement->setTitre($titre);
        $evenement->setDescription($description);
        $evenement->setTypeEvenement($typeEvenement);
        $evenement->setAdresseLieu($adresseLieu);
        $evenement->setElementRequis($elementRequis);
        $evenement->setNombrePlace($nombrePlace);
        $evenement->setDateHeureEvenement(
            $dateHeure->format('Y-m-d H:i:s')
        );

        try {
            $repo->modifierEvenement($evenement);

            flashEvenement(
                'succes',
                'L’événement a été modifié avec succès.'
            );

            header(
                'Location: evenement_detail.php?id=' . $idEvenement,
                true,
                303
            );
            exit;

        } catch (Throwable $e) {
            error_log($e->getMessage());
            $erreurs[] = 'Une erreur est survenue pendant la modification.';
        }
    }

    // En cas d'erreur, les valeurs saisies sont conservées
    if (!empty($erreurs)) {
        $flash = ['erreur', implode(' ', $erreurs)];
    }
}

// Variables utilisées par la vue
$titrePage = "Modifier l'événement - GHD-HSP";
$action = 'modifier';
