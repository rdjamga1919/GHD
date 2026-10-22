<?php
require_once __DIR__ . '/traitement_verif_session.php';
require_once __DIR__ . '/../repository/EntrepriseRepository.php';
require_once __DIR__ . '/../repository/PartenaireRepository.php';

const NB_MAX_CREATIONS_PAR_SESSION = 3;

function redirection($page)
{
    header('Location: ' . $page);
    exit;
}

// Retourne l'URL normalisée (http/https, hôte en minuscules, sans "/" final) ou null si invalide.
// La normalisation permet de détecter les doublons (https://X.com/ = https://x.com).
function normaliserSiteWeb($siteWeb)
{
    $siteWeb = trim($siteWeb);
    if ($siteWeb === '' || mb_strlen($siteWeb) > 255 || !filter_var($siteWeb, FILTER_VALIDATE_URL)) {
        return null;
    }
    $parties = parse_url($siteWeb);
    $scheme = strtolower($parties['scheme'] ?? '');
    $hote = strtolower($parties['host'] ?? '');
    if (!in_array($scheme, ['http', 'https'], true) || $hote === '' || strpos($hote, '.') === false) {
        return null;
    }
    $resultat = $scheme . '://' . $hote;
    if (!empty($parties['port'])) {
        $resultat .= ':' . $parties['port'];
    }
    if (!empty($parties['path'])) {
        $resultat .= rtrim($parties['path'], '/');
    }
    return mb_strlen($resultat) > 255 ? null : $resultat;
}

$pageEntreprise = '../../public/entreprise.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erreurs'] = ["Méthode non autorisée."];
    redirection($pageEntreprise);
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$pageRetour = (isset($_POST['retour']) && $_POST['retour'] === 'profil') ? '../../public/profil.php' : $pageEntreprise;

// Protection CSRF
if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {
    $_SESSION['erreurs'] = ["Requête invalide, veuillez réessayer."];
    redirection($pageRetour);
}

// L'utilisateur concerné est TOUJOURS celui de la session (jamais un id venant du formulaire)
$connecte = isset($_SESSION['utilisateur_id']);
$idUtilisateur = $connecte ? (int) $_SESSION['utilisateur_id'] : 0;
$role = $_SESSION['role'] ?? null;

$entrepriseRepository = new EntrepriseRepository();
$partenaireRepository = new PartenaireRepository();

try {
    if ($action === 'creer') {

        $nom = isset($_POST['nom_entreprise']) ? trim($_POST['nom_entreprise']) : '';
        $adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : '';
        $siteWebSaisi = isset($_POST['site_web']) ? trim($_POST['site_web']) : '';

        $_SESSION['valeurs'] = ['nom_entreprise' => $nom, 'adresse' => $adresse, 'site_web' => $siteWebSaisi];

        // Autorisation : visiteur (futur partenaire en cours d'inscription), partenaire ou gestionnaire.
        // Étudiants et médecins connectés n'ont pas à créer d'entreprise.
        if ($connecte && !in_array($role, ['partenaire', 'gestionnaire'], true)) {
            $_SESSION['erreurs'] = ["Vous n'êtes pas autorisé à créer une entreprise."];
            redirection($pageEntreprise);
        }

        // Anti-spam pour les visiteurs non connectés
        if (!$connecte && ($_SESSION['nb_creations_entreprise'] ?? 0) >= NB_MAX_CREATIONS_PAR_SESSION) {
            $_SESSION['erreurs'] = ["Nombre maximum de créations atteint. Connectez-vous ou réessayez plus tard."];
            redirection($pageEntreprise);
        }

        $erreurs = [];
        if ($nom === '') {
            $erreurs[] = "Le nom de l'entreprise est obligatoire.";
        } elseif (mb_strlen($nom) > 150) {
            $erreurs[] = "Le nom ne doit pas dépasser 150 caractères.";
        }
        if ($adresse === '') {
            $erreurs[] = "L'adresse est obligatoire.";
        } elseif (mb_strlen($adresse) > 255) {
            $erreurs[] = "L'adresse ne doit pas dépasser 255 caractères.";
        }
        $siteWeb = normaliserSiteWeb($siteWebSaisi);
        if ($siteWebSaisi === '') {
            $erreurs[] = "Le site web est obligatoire.";
        } elseif ($siteWeb === null) {
            $erreurs[] = "Le site web est invalide (exemple : https://www.entreprise.fr).";
        }

        if (!empty($erreurs)) {
            $_SESSION['erreurs'] = $erreurs;
            redirection($pageEntreprise);
        }

        // Anti-doublon : même site web = même entreprise
        $existante = $entrepriseRepository->getEntrepriseBySiteWeb($siteWeb);
        if ($existante !== null) {
            $_SESSION['entreprise_existante'] = ['id' => $existante->getIdEntreprise(), 'nom' => $existante->getNom()];
            $_SESSION['erreurs'] = ["Une entreprise avec ce site web existe déjà : vous pouvez la sélectionner."];
            redirection($pageEntreprise);
        }

        try {
            $idNouvelle = $entrepriseRepository->ajouterEntreprise(new Entreprise(null, $nom, $adresse, $siteWeb));
        } catch (PDOException $e) {
            // Doublon créé entre-temps par quelqu'un d'autre (contrainte UNIQUE sur site_web)
            if ($e->getCode() === '23000') {
                $existante = $entrepriseRepository->getEntrepriseBySiteWeb($siteWeb);
                if ($existante !== null) {
                    $_SESSION['entreprise_existante'] = ['id' => $existante->getIdEntreprise(), 'nom' => $existante->getNom()];
                    $_SESSION['erreurs'] = ["Une entreprise avec ce site web existe déjà : vous pouvez la sélectionner."];
                    redirection($pageEntreprise);
                }
            }
            throw $e;
        }

        $_SESSION['nb_creations_entreprise'] = ($_SESSION['nb_creations_entreprise'] ?? 0) + 1;
        unset($_SESSION['valeurs']);
        $_SESSION['succes'] = ["L'entreprise « " . $nom . " » a été créée avec succès."];
        redirection($pageEntreprise . '?id=' . $idNouvelle);

    } elseif ($action === 'selectionner') {

        // Seul un partenaire connecté peut rattacher SON profil à une entreprise
        if (!$connecte || $role !== 'partenaire') {
            $_SESSION['erreurs'] = ["Seul un partenaire connecté peut sélectionner une entreprise."];
            redirection($pageRetour);
        }

        $refEntreprise = filter_var($_POST['ref_entreprise'] ?? null, FILTER_VALIDATE_INT);
        if ($refEntreprise === false || $refEntreprise === null || $refEntreprise <= 0) {
            $_SESSION['erreurs'] = ["Identifiant d'entreprise invalide."];
            redirection($pageRetour);
        }

        $entreprise = $entrepriseRepository->getEntreprise($refEntreprise);
        if ($entreprise === null) {
            $_SESSION['erreurs'] = ["Cette entreprise n'existe pas."];
            redirection($pageRetour);
        }

        $partenaire = $partenaireRepository->findById($idUtilisateur);
        if ($partenaire === null) {
            $_SESSION['erreurs'] = ["Profil partenaire introuvable."];
            redirection($pageRetour);
        }

        if ($partenaire->getRefEntreprise() === $refEntreprise) {
            $_SESSION['succes'] = ["« " . $entreprise->getNom() . " » est déjà votre entreprise."];
            redirection($pageRetour);
        }

        $partenaireRepository->modifierPartenaire($idUtilisateur, $refEntreprise);
        $_SESSION['succes'] = ["Votre entreprise a été mise à jour : « " . $entreprise->getNom() . " »."];

    } else {
        $_SESSION['erreurs'] = ["Action non reconnue."];
    }

} catch (PDOException $e) {
    error_log("Erreur traitement_entreprise : " . $e->getMessage());
    $_SESSION['erreurs'] = ["Une erreur est survenue. Aucune modification n'a été enregistrée."];
}

redirection($pageRetour);