<?php

session_start();

require_once __DIR__ . '/../inclues/authentification.php';
require_once __DIR__ . '/../repository/EtablissementRepository.php';

// Seuls les gestionnaires peuvent créer et modifier.
requireRole('gestionnaire');

$pageRetour = '../../public/etablissement.php';

// Accepter uniquement les formulaires POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $pageRetour);
    exit;
}

// Vérifier le jeton CSRF
$csrfSession = (string) ($_SESSION['csrf'] ?? '');
$csrfFormulaire = is_string($_POST['csrf'] ?? null)
    ? $_POST['csrf']
    : '';
if (
    $csrfSession === '' ||
    !hash_equals($csrfSession, $csrfFormulaire)
) {
    $_SESSION['erreurs_etablissement'] = [
        'Requête invalide. Veuillez réessayer.'
    ];

    header('Location: ' . $pageRetour, true, 303);
    exit;
}

$repo = new EtablissementRepository();

$action = $_POST['action'] ?? 'creer';
$id = null;

// Préparer la modification
if ($action === 'modifier') {

    $id = filter_var(
        $_POST['id_etablissement'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$id || $id <= 0 || $repo->findById($id) === null) {
        $_SESSION['erreurs_etablissement'] = [
            'Établissement introuvable.'
        ];

        header('Location: ' . $pageRetour, true, 303);
        exit;
    }

    $pageRetour .= '?id=' . $id . '&modifier=1';

} elseif ($action !== 'creer') {

    http_response_code(400);
    exit('Action inconnue.');
}

// Récupérer les données du formulaire
$nom = is_string($_POST['nom_etablissement'] ?? null)
    ? trim($_POST['nom_etablissement'])
    : '';

$adresse = is_string($_POST['adresse'] ?? null)
    ? trim($_POST['adresse'])
    : '';

$siteWeb = is_string($_POST['site_web'] ?? null)
    ? trim($_POST['site_web'])
    : '';

// Conserver les champs en cas d'erreur
$_SESSION['ancien_etablissement'] = [
    'nom_etablissement' => $nom,
    'adresse' => $adresse,
    'site_web' => $siteWeb
];

$erreurs = [];

// Vérifier le nom
if ($nom === '' || mb_strlen($nom) > 150) {
    $erreurs[] = 'Le nom est invalide (150 caractères maximum).';
}

// Vérifier l'adresse
if ($adresse === '' || mb_strlen($adresse) > 255) {
    $erreurs[] = "L'adresse est invalide (255 caractères maximum).";
}

// Vérifier le site web
$protocole = strtolower(
    (string) parse_url($siteWeb, PHP_URL_SCHEME)
);

if (
    mb_strlen($siteWeb) > 255 ||
    filter_var($siteWeb, FILTER_VALIDATE_URL) === false ||
    !in_array($protocole, ['http', 'https'], true)
) {
    $erreurs[] = 'Le site web doit être une URL HTTP ou HTTPS valide.';
}

// Vérifier qu'un autre établissement n'a pas ce site
if (empty($erreurs)) {

    $existant = $repo->getEtablissementBySiteWeb($siteWeb);

    if (
        $existant !== null &&
        (int) $existant->getIdEtablissement() !== (int) $id
    ) {
        $erreurs[] = 'Un autre établissement possède déjà ce site web.';
    }
}

// Afficher les erreurs
if (!empty($erreurs)) {
    $_SESSION['erreurs_etablissement'] = $erreurs;

    header('Location: ' . $pageRetour, true, 303);
    exit;
}

// Créer ou modifier l'établissement
try {

    $etablissement = new Etablissement(
        $id,
        $nom,
        $adresse,
        $siteWeb
    );

    if ($action === 'modifier') {

        $repo->update($etablissement);

        $_SESSION['succes_etablissement'] =
            "L'établissement a été modifié avec succès.";

        $pageRetour = '../../public/etablissement.php?id=' . $id;

    } else {

        $repo->create($etablissement);

        $_SESSION['succes_etablissement'] =
            "L'établissement a été ajouté avec succès.";

    }

    unset($_SESSION['ancien_etablissement']);

} catch (PDOException $e) {

    error_log($e->getMessage());

    $_SESSION['erreurs_etablissement'] = [
        $e->getCode() === '23000'
            ? 'Un autre établissement possède déjà ce site web.'
            : "Une erreur est survenue lors de l'enregistrement."
    ];
}

header('Location: ' . $pageRetour, true, 303);
exit;
