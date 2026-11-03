<?php
session_start();

require_once __DIR__ . '/../inclues/authentification.php';
require_once __DIR__ . '/../repository/UtilisateurRepository.php';
require_once __DIR__ . '/../repository/EvenementRepository.php';
require_once __DIR__ . '/../repository/InscriptionEvenementRepository.php';

function redirection($page)
{
    header('Location: ' . $page);
    exit;
}

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirection('../../public/evenements.php');
}

if (!in_array($_SESSION['role'] ?? '', InscriptionEvenementRepository::ROLES_AUTORISES, true)) {
    http_response_code(403);
    exit('Accès refusé.');
}

$idUtilisateur = (int) $_SESSION['utilisateur_id'];

$idEvenement = filter_var($_POST['id_evenement'] ?? null, FILTER_VALIDATE_INT);
$action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';

$pageEvenement = ($idEvenement !== false && $idEvenement > 0)
    ? '../../public/evenement_detail.php?id=' . $idEvenement
    : '../../public/evenements.php';

try {
    $utilisateur = (new UtilisateurRepository())->findById($idUtilisateur);
    if ($utilisateur === null || !$utilisateur->estValide()) {
        http_response_code(403);
        exit('Accès refusé.');
    }

    if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {
        $_SESSION['flash'] = ['erreur', 'Requête invalide, veuillez réessayer.'];
        redirection($pageEvenement);
    }

    if ($idEvenement === false || $idEvenement <= 0 || !in_array($action, ['inscrire', 'desinscrire'], true)) {
        $_SESSION['flash'] = ['erreur', 'Requête invalide.'];
        redirection('../../public/evenements.php');
    }

    $evenement = (new EvenementRepository())->getEvenement($idEvenement);
    if ($evenement === null) {
        $_SESSION['flash'] = ['erreur', "Cet événement n'existe pas."];
        redirection($pageEvenement);
    }

    $inscriptionRepository = new InscriptionEvenementRepository();

    if ($action === 'inscrire') {
        $resultat = $inscriptionRepository->inscrire($idEvenement, $idUtilisateur);

        $erreurs = [
            'introuvable' => "Cet événement n'existe pas.",
            'passe' => 'Cet événement est terminé : les inscriptions sont closes.',
            'complet' => 'Cet événement est complet.',
            'deja_inscrit' => 'Vous êtes déjà inscrit à cet événement.',
        ];

        if ($resultat === 'ok') {
            $_SESSION['flash'] = ['succes', "Votre inscription à « " . $evenement->getTitre() . " » est confirmée."];
        } else {
            $_SESSION['flash'] = ['erreur', $erreurs[$resultat] ?? "L'inscription n'a pas pu être enregistrée."];
        }
    } else {
        $etat = $inscriptionRepository->getEtat($idEvenement);

        if ($etat !== null && $etat['passe']) {
            $_SESSION['flash'] = ['erreur', 'Cet événement est terminé : la désinscription est impossible.'];
        } elseif ($inscriptionRepository->desinscrire($idEvenement, $idUtilisateur)) {
            $_SESSION['flash'] = ['succes', "Votre inscription à « " . $evenement->getTitre() . " » a été annulée."];
        } else {
            $_SESSION['flash'] = ['erreur', "Vous n'êtes pas inscrit à cet événement."];
        }
    }

    redirection($pageEvenement);

} catch (PDOException $e) {
    error_log("Erreur traitement_inscription_evenement : " . $e->getMessage());
    $_SESSION['flash'] = ['erreur', "Une erreur est survenue. Votre demande n'a pas été enregistrée."];
    redirection($pageEvenement);
}