<?php
// src/traitement/traitement_creation_evenement.php
// Prépare (GET) et traite (POST) le formulaire de création d'un événement.
// Inclus par public/evenement_creer.php : la session est déjà démarrée et
// requireLogin() a déjà été appelé.

if (!defined('APP_ENTRY')) {
    http_response_code(403);
    exit('Accès direct interdit.');
}

require_once __DIR__ . '/../repository/EvenementRepository.php';
require_once __DIR__ . '/../modele/Evenement.php';

$repo = new EvenementRepository();

// Jeton CSRF (créé une fois par session, utilisé en GET et en POST)
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

/* ------------------------------------------------------------------ */
/* Situation B : requête POST                                          */
/* ------------------------------------------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Jeton CSRF
    if (!hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {
        $_SESSION['flash'] = ['erreur', 'Requête invalide.'];
        header('Location: evenement_creer.php');
        exit;
    }

    // 2. Autorisation côté serveur (ne jamais se fier à la seule page publique)
    if (!isAdmin()) {
        http_response_code(403);
        exit('Accès refusé.');
    }

    // 3. Récupération, vérification du type et nettoyage des champs
    $champs = [
        'titre', 'description', 'type_evenement', 'adresse_lieu',
        'element_requis', 'nombre_place', 'date_heure_evenement',
    ];

    $erreurs = [];
    $valeurs = [];

    foreach ($champs as $champ) {
        $brut = $_POST[$champ] ?? '';

        // Un tableau (ex. titre[]=x) ou autre type inattendu est refusé
        if (!is_string($brut)) {
            $erreurs[] = 'Données invalides.';
            $brut = '';
        }

        // Suppression des caractères de contrôle (sauf tabulation et retours
        // à la ligne), puis des espaces en début et fin
        $nettoye = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $brut);
        $valeurs[$champ] = trim($nettoye ?? '');
    }

    // 4. Valeurs obligatoires et longueurs maximales (adapter à la BDD)
    if ($valeurs['titre'] === '') {
        $erreurs[] = 'Le titre est obligatoire.';
    } elseif (mb_strlen($valeurs['titre']) > 150) {
        $erreurs[] = 'Le titre ne doit pas dépasser 150 caractères.';
    }

    if ($valeurs['type_evenement'] === '') {
        $erreurs[] = "Le type d'événement est obligatoire.";
    } elseif (mb_strlen($valeurs['type_evenement']) > 50) {
        $erreurs[] = "Le type d'événement ne doit pas dépasser 50 caractères.";
    }

    if ($valeurs['adresse_lieu'] === '') {
        $erreurs[] = "L'adresse du lieu est obligatoire.";
    } elseif (mb_strlen($valeurs['adresse_lieu']) > 255) {
        $erreurs[] = "L'adresse ne doit pas dépasser 255 caractères.";
    }

    if (mb_strlen($valeurs['description']) > 2000) {
        $erreurs[] = 'La description ne doit pas dépasser 2000 caractères.';
    }

    if (mb_strlen($valeurs['element_requis']) > 255) {
        $erreurs[] = 'Les éléments requis ne doivent pas dépasser 255 caractères.';
    }

    // 5. Capacité : entier entre 1 et 1000
    $nombrePlace = filter_var(
        $valeurs['nombre_place'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1, 'max_range' => 1000]]
    );
    if ($nombrePlace === false) {
        $erreurs[] = 'Le nombre de places doit être un entier entre 1 et 1000.';
    }

    // 6. Date et heure (format du champ datetime-local), dans le futur
    $dateHeure = DateTimeImmutable::createFromFormat(
        'Y-m-d\TH:i',
        $valeurs['date_heure_evenement']
    );
    // Le second test refuse les dates « corrigées » par PHP (ex. 31 février)
    if ($dateHeure === false
        || $dateHeure->format('Y-m-d\TH:i') !== $valeurs['date_heure_evenement']) {
        $erreurs[] = 'La date et l\'heure sont invalides.';
    } elseif ($dateHeure <= new DateTimeImmutable('now')) {
        $erreurs[] = "La date de l'événement doit être dans le futur.";
    }

    // 7. En cas d'erreur : on conserve la saisie et on revient au formulaire
    if ($erreurs !== []) {
        $_SESSION['ancien'] = $valeurs;
        $_SESSION['flash'] = ['erreur', implode(' ', array_unique($erreurs))];
        header('Location: evenement_creer.php');
        exit;
    }

    // 8. Création de l'objet et enregistrement
    try {
        $evenement = new Evenement(
            null,                                       // id_evenement (généré par la BDD)
            $dateHeure->format('Y-m-d H:i:s'),          // date_heure_evenement
            $valeurs['element_requis'],                 // element_requis
            $nombrePlace,                               // nombre_place
            $valeurs['type_evenement'],                 // type_evenement
            $valeurs['adresse_lieu'],                   // adresse_lieu
            $valeurs['description'],                    // description
            $valeurs['titre']                           // titre
        );

        $idEvenement = $repo->insert($evenement);

        // Succès : on efface la saisie et on renouvelle le jeton
        unset($_SESSION['ancien']);
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        $_SESSION['flash'] = ['succes', 'Événement créé avec succès.'];

        header('Location: evenement_detail.php?id=' . (int) $idEvenement);
        exit;

    } catch (Throwable $e) {
        error_log('Création événement : ' . $e->getMessage());
        $_SESSION['ancien'] = $valeurs;
        $_SESSION['flash'] = ['erreur', "Erreur lors de la création de l'événement."];
        header('Location: evenement_creer.php');
        exit;
    }
}

/* ------------------------------------------------------------------ */
/* Situation A : requête GET (aucun événement n'est créé)              */
/* ------------------------------------------------------------------ */

// Saisie précédente en cas d'erreur, pour préremplir le formulaire
$valeurs = $_SESSION['ancien'] ?? [];
unset($_SESSION['ancien']);

// Message flash éventuel : ['succes' | 'erreur', 'message']
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Jeton pour le champ caché de la vue
$csrf = $_SESSION['csrf'];