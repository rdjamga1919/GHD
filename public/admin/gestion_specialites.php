<?php
// public/admin/gestion_specialites.php
// Administration des spécialités : liste, consultation, création, modification, suppression.
//   gestion_specialites.php                      -> liste
//   gestion_specialites.php?action=voir&id=N     -> consultation
//   gestion_specialites.php?action=creer         -> formulaire de création
//   gestion_specialites.php?action=modifier&id=N -> formulaire de modification
//   POST (action=creer|modifier|supprimer)       -> écritures (CSRF obligatoire)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../src/inclues/authentification.php';
requireRole('gestionnaire'); // contrôle côté serveur : 302 vers la connexion ou 403

require_once __DIR__ . '/../../src/traitement/admin/traitementGestionSpecialites.php';

$vues = __DIR__ . '/../../views/admin/specialites/';
$repository = new SpecialiteRepository();

/* ---------- Écritures : POST uniquement ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionPost = $_POST['action'] ?? '';

    if ($actionPost === 'supprimer') {
        specTraiterSuppression(); // redirige toujours
    }

    if ($actionPost === 'creer') {
        $resultat = specTraiterCreation(); // redirige si succès
        $pageTitle = 'Ajouter une spécialité';
        $titre = $pageTitle;
        $action = SPEC_PAGE . '?action=creer';
        $champAction = 'creer';
        $idSpecialite = null;
        $texteBouton = 'Créer';
        $libelle = $resultat['libelle'];
        $erreur = $resultat['erreur'];
        require $vues . 'formulaireSpecialiteView.php';
        exit;
    }

    if ($actionPost === 'modifier') {
        $id = specIdValide($_GET['id'] ?? null);
        if ($id === null) {
            specAjouterFlash('erreur', "Identifiant de spécialité invalide.");
            specRediriger(SPEC_PAGE);
        }
        try {
            $existe = $repository->findById($id) !== null;
        } catch (SpecialiteException $e) {
            specAjouterFlash('erreur', $e->getMessage());
            specRediriger(SPEC_PAGE);
        }
        if (!$existe) {
            specAjouterFlash('erreur', "Cette spécialité n'existe pas.");
            specRediriger(SPEC_PAGE);
        }

        $resultat = specTraiterModification($id); // redirige si succès
        $pageTitle = 'Modifier une spécialité';
        $titre = $pageTitle;
        $action = SPEC_PAGE . '?action=modifier&id=' . urlencode((string) $id);
        $champAction = 'modifier';
        $idSpecialite = $id;
        $texteBouton = 'Enregistrer';
        $libelle = $resultat['libelle'];
        $erreur = $resultat['erreur'];
        require $vues . 'formulaireSpecialiteView.php';
        exit;
    }

    // Action POST inconnue
    specAjouterFlash('erreur', "Action non reconnue.");
    specRediriger(SPEC_PAGE);
}

/* ---------- Lectures : GET ---------- */
$action = $_GET['action'] ?? 'liste';

if ($action === 'creer') {
    $pageTitle = 'Ajouter une spécialité';
    $titre = $pageTitle;
    $action = SPEC_PAGE . '?action=creer';
    $champAction = 'creer';
    $idSpecialite = null;
    $texteBouton = 'Créer';
    $libelle = '';
    $erreur = null;
    require $vues . 'formulaireSpecialiteView.php';
    exit;
}

if ($action === 'modifier' || $action === 'voir') {
    $id = specIdValide($_GET['id'] ?? null);
    if ($id === null) {
        specAjouterFlash('erreur', "Identifiant de spécialité invalide.");
        specRediriger(SPEC_PAGE);
    }

    try {
        $specialite = $repository->findById($id);
        $nbMedecins = $specialite !== null ? $repository->compterUtilisations($id) : 0;
    } catch (SpecialiteException $e) {
        specAjouterFlash('erreur', $e->getMessage());
        specRediriger(SPEC_PAGE);
    }

    if ($specialite === null) {
        specAjouterFlash('erreur', "Cette spécialité n'existe pas.");
        specRediriger(SPEC_PAGE);
    }

    if ($action === 'voir') {
        $pageTitle = 'Spécialité : ' . $specialite->getLibelle();
        require $vues . 'detailSpecialiteView.php';
        exit;
    }

    $pageTitle = 'Modifier une spécialité';
    $titre = $pageTitle;
    $action = SPEC_PAGE . '?action=modifier&id=' . urlencode((string) $id);
    $champAction = 'modifier';
    $idSpecialite = $id;
    $texteBouton = 'Enregistrer';
    $libelle = $specialite->getLibelle();
    $erreur = null;
    require $vues . 'formulaireSpecialiteView.php';
    exit;
}

// Par défaut : la liste
$pageTitle = 'Administration des spécialités';
$flash = specLireFlash();
$specialites = [];
$nbMedecins = [];
$erreurChargement = null;

try {
    $specialites = $repository->findAll();
    foreach ($specialites as $specialite) {
        $nbMedecins[$specialite->getIdSpecialite()] = $repository->compterUtilisations($specialite->getIdSpecialite());
    }
} catch (SpecialiteException $e) {
    $erreurChargement = $e->getMessage();
}

require $vues . 'listeSpecialitesView.php';