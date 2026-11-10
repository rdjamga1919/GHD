<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../inclues/authentification.php';
require_once __DIR__ . '/../repository/EvenementRepository.php';

requireLogin();

// Vérification du rôle
if (!hasRole('medecin') && !hasRole('partenaire')) {
    http_response_code(403);
    exit('Accès refusé.');
}

$methode = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (!in_array($methode, ['GET', 'POST'], true)) {
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

$repo = new EvenementRepository();
$evenement = $repo->getEvenement($idEvenement);

if ($evenement === null) {
    http_response_code(404);
    exit('Événement introuvable.');
}

// Vérifier que l'utilisateur est organisateur
$idUtilisateur = (int) ($_SESSION['utilisateur_id'] ?? 0);
$autorise = false;

if ($idUtilisateur > 0) {
    $organisateurs = $repo->getOrganisateurs($idEvenement);

    foreach ($organisateurs as $organisateur) {
        if ((int) $organisateur['id_utilisateur'] === $idUtilisateur) {
            $autorise = true;
            break;
        }
    }
}

if (!$autorise) {
    http_response_code(403);
    exit('Vous ne pouvez pas supprimer cet événement.');
}

// Jeton CSRF
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$csrf = $_SESSION['csrf'];
$erreur = null;

// Nombre d'inscrits pour la confirmation
$details = $repo->getEvenementDetail($idEvenement);
$nbInscrits = (int) ($details['nb_inscrits'] ?? 0);

if ($methode === 'POST') {

    if (($_POST['action'] ?? '') !== 'supprimer') {
        http_response_code(400);
        exit('Action invalide.');
    }

    if (
        !isset($_POST['csrf']) ||
        !is_string($_POST['csrf']) ||
        !hash_equals($csrf, $_POST['csrf'])
    ) {
        http_response_code(403);
        exit('Jeton de sécurité invalide.');
    }

    // Règle conservatrice : ne pas supprimer
    // un événement qui possède des inscriptions.
    if ($nbInscrits > 0) {
        $erreur = 'Impossible de supprimer cet événement : '
            . $nbInscrits . ' personne(s) sont déjà inscrite(s).';
    } else {
        try {
            $repo->supprimerEvenement($idEvenement);

            $_SESSION['flash'] = [
                'succes',
                'Événement supprimé avec succès.'
            ];

            header('Location: evenements.php', true, 303);
            exit;

        } catch (Throwable $e) {
            error_log($e->getMessage());
            $erreur = 'Impossible de supprimer cet événement. '
                . 'Vérifie les données associées.';
        }
    }
}

$titrePage = "Supprimer l'événement - GHD-HSP";
