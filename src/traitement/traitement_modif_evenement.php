<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../inclues/authentification.php';
require_once __DIR__ . '/../repository/EvenementRepository.php';

requireLogin();

if (!hasRole('partenaire') && !hasRole('medecin')) {
    http_response_code(403);
    exit('Accès refusé.');
}

$publicDir = '../../public/';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $publicDir . 'evenements.php');
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = [$type, $message];
}

if (
    !isset($_SESSION['csrf'], $_POST['csrf']) ||
    !is_string($_POST['csrf']) ||
    !hash_equals($_SESSION['csrf'], $_POST['csrf'])
) {
    flash('erreur', 'Requête invalide, veuillez réessayer.');
    header('Location: ' . $publicDir . 'evenements.php');
    exit;
}

$idUtilisateur = (int) $_SESSION['utilisateur_id'];

$repo = new EvenementRepository();

$action = $_POST['action'] ?? '';

// Récupérer et valider l'identifiant
$idEvenement = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($idEvenement === false || $idEvenement === null) {
    flash('erreur', 'Identifiant d’événement invalide.');
    header('Location: ' . $publicDir . 'evenements.php');
    exit;
}

// Prochaine étape : récupérer l'événement avec le repository.
