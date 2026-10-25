<?php

session_start();

require_once __DIR__ . '/../../inclues/authentification.php';
require_once __DIR__ . '/../../bdd/Bdd.php';

// Corriger ici la destination MAMP sans modifier le helper partagé.
if (!isLoggedIn()) {
    header('Location: /GHD/public/connexion.php');
    exit;
}
requireRole('gestionnaire');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /GHD/public/admin/GestionUtilisateurs.php');
    exit;
}

$csrfToken = $_POST['csrf_token'] ?? null;
$csrfSession = $_SESSION['csrf_gestion_utilisateurs'] ?? null;
if (!is_string($csrfToken) || !is_string($csrfSession) || $csrfSession === '' ||
    !hash_equals($csrfSession, $csrfToken)) {
    http_response_code(403);
    exit('Requête invalide : veuillez recharger la page.');
}

$idUtilisateur = filter_var($_POST['id_utilisateur'] ?? null, FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]);
$action = $_POST['action'] ?? '';

if ($idUtilisateur === false || !in_array($action, ['valider', 'refuser'], true)) {
    $_SESSION['message'] = 'Requête invalide.';
    $_SESSION['messageType'] = 'error';
    header('Location: /GHD/public/admin/GestionUtilisateurs.php');
    exit;
}

try {
    $database = (new Bdd())->getConnexionBdd();
} catch (PDOException $exception) {
    error_log('[GDH][gestion-utilisateurs] Echec de la connexion BDD pour une action.');
    $_SESSION['message'] = 'Impossible de se connecter à la base de données.';
    $_SESSION['messageType'] = 'error';
    header('Location: /GHD/public/admin/GestionUtilisateurs.php');
    exit;
}

try {
    $statement = $database->prepare("
        SELECT nom, prenom
        FROM Utilisateur
        WHERE id_utilisateur = :id AND est_valide = 0
    ");
    $statement->execute(['id' => $idUtilisateur]);
    $compte = $statement->fetch();

    if ($compte === false) {
        $_SESSION['message'] = "Ce compte n'existe plus ou a déjà été traité.";
        $_SESSION['messageType'] = 'error';
        header('Location: /GHD/public/admin/GestionUtilisateurs.php');
        exit;
    }

    $nomComplet = $compte['prenom'] . ' ' . $compte['nom'];

    if ($action === 'valider') {
        $statement = $database->prepare("
            UPDATE Utilisateur SET est_valide = 1 WHERE id_utilisateur = :id AND est_valide = 0
        ");
        $statement->execute(['id' => $idUtilisateur]);

        if ($statement->rowCount() === 1) {
            $_SESSION['message'] = "Le compte de {$nomComplet} a été validé.";
            $_SESSION['messageType'] = 'success';
        } else {
            $_SESSION['message'] = "Ce compte n'existe plus ou a déjà été traité.";
            $_SESSION['messageType'] = 'error';
        }
    } else {
        $statement = $database->prepare("
            DELETE FROM Utilisateur WHERE id_utilisateur = :id AND est_valide = 0
        ");
        $statement->execute(['id' => $idUtilisateur]);

        if ($statement->rowCount() === 1) {
            $_SESSION['message'] = "Le compte de {$nomComplet} a été refusé.";
            $_SESSION['messageType'] = 'success';
        } else {
            $_SESSION['message'] = "Ce compte n'existe plus ou a déjà été traité.";
            $_SESSION['messageType'] = 'error';
        }
    }
} catch (PDOException $exception) {
    error_log('[GDH][gestion-utilisateurs] Echec de la lecture ou du traitement du compte.');
    $_SESSION['message'] = "Une erreur est survenue lors du traitement du compte.";
    $_SESSION['messageType'] = 'error';
}

header('Location: /GHD/public/admin/GestionUtilisateurs.php');
exit;
