<?php
session_start();

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Candidature.php';
require_once __DIR__ . '/../inclues/authentification.php';
require_once __DIR__ . '/../repository/CandidatureRepository.php';
require_once __DIR__ . '/../repository/OffreRepository.php';
require_once __DIR__ . '/../repository/EtudiantRepository.php';

const MOTIVATION_MIN = 20;
const MOTIVATION_MAX = 2000;

function redirection($page)
{
    header('Location: ' . $page);
    exit;
}

// Session + rôle : visiteur -> page de connexion, autre rôle qu'étudiant -> 403
requireRole('etudiant');

// Accès direct par l'URL (GET) : on renvoie simplement vers la liste des offres
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirection('../../public/offres.php');
}

// Le candidat est TOUJOURS l'étudiant de la session, confirmé en BDD (jamais un champ du formulaire)
$idUtilisateur = (int) $_SESSION['utilisateur_id'];

$idOffre = filter_var($_POST['id_offre'] ?? null, FILTER_VALIDATE_INT);
$pageFormulaire = '../../public/postuler.php' . (($idOffre !== false && $idOffre > 0) ? '?id=' . $idOffre : '');
try {
    $etudiant = (new EtudiantRepository())->findById($idUtilisateur);
    if ($etudiant === null || !$etudiant->estValide()) {
        http_response_code(403);
        exit('Accès refusé.');
    }

    // Protection CSRF
    if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {
        $_SESSION['erreurs'] = ["Requête invalide, veuillez réessayer."];
        redirection($pageFormulaire);
    }

    // Offre : identifiant valide, existante et ouverte
    // (postuler.php affiche lui-même « Cette offre n'existe pas. » pour une offre invalide ou inexistante)
    if ($idOffre === false || $idOffre === null || $idOffre <= 0) {
        redirection('../../public/postuler.php');
    }

    $offreRepository = new OffreRepository();
    $offre = $offreRepository->getOffre($idOffre);
    if ($offre === null) {
        redirection($pageFormulaire);
    }
    if ($offre->getEtat() !== 'ouverte') {
        $_SESSION['erreurs'] = ["Cette offre est fermée : vous ne pouvez plus postuler."];
        redirection('../../public/offre_detail.php?id=' . $idOffre);
    }

    // Message de motivation
    $motivation = is_string($_POST['motivation'] ?? null) ? trim($_POST['motivation']) : '';
    $_SESSION['valeurs'] = ['motivation' => $motivation];

    if ($motivation === '') {
        $_SESSION['erreurs'] = ["Le message de motivation est obligatoire."];
        redirection($pageFormulaire);
    }
    if (mb_strlen($motivation) < MOTIVATION_MIN) {
        $_SESSION['erreurs'] = ["Le message de motivation doit contenir au moins " . MOTIVATION_MIN . " caractères."];
        redirection($pageFormulaire);
    }
    if (mb_strlen($motivation) > MOTIVATION_MAX) {
        $_SESSION['erreurs'] = ["Le message de motivation ne doit pas dépasser " . MOTIVATION_MAX . " caractères."];
        redirection($pageFormulaire);
    }

    // Refus des doublons
    $candidatureRepository = new CandidatureRepository();
    if ($candidatureRepository->candidatureExiste($idUtilisateur, $idOffre)) {
        unset($_SESSION['valeurs']);
        $_SESSION['erreurs'] = ["Vous avez déjà postulé à cette offre."];
        redirection('../../public/offre_detail.php?id=' . $idOffre);
    }

    try {
        $candidatureRepository->ajouterCandidature(
            new Candidature(null, $motivation, date('Y-m-d H:i:s'), 'en_attente', $idUtilisateur, $idOffre)
        );
    } catch (PDOException $e) {
        // Deux envois simultanés : la contrainte UNIQUE (ref_utilisateur, ref_offre) refuse le second
        if ($e->getCode() === '23000') {
            unset($_SESSION['valeurs']);
            $_SESSION['erreurs'] = ["Vous avez déjà postulé à cette offre."];
            redirection('../../public/offre_detail.php?id=' . $idOffre);
        }
        throw $e;
    }
    unset($_SESSION['valeurs']);
    $_SESSION['succes'] = ["Votre candidature à l'offre « " . $offre->getTitre() . " » a bien été enregistrée."];
    redirection('../../public/offre_detail.php?id=' . $idOffre);

} catch (PDOException $e) {
    error_log("Erreur traitement_candidature : " . $e->getMessage());
    $_SESSION['erreurs'] = ["Une erreur est survenue. Votre candidature n'a pas été enregistrée."];
    redirection($pageFormulaire);
}