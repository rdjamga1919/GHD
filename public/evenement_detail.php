<?php
ob_start();

session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/EvenementRepository.php';
require_once __DIR__ . '/../src/repository/InscriptionEvenementRepository.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$evenement = null;

if ($id !== false && $id !== null && $id > 0) {
    $evenement = (new EvenementRepository())->getEvenement($id);
}

if ($evenement === null) {
    http_response_code(404);
}

if (isLoggedIn() && empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$connecte = isLoggedIn();
$roleAutorise = in_array($_SESSION['role'] ?? '', InscriptionEvenementRepository::ROLES_AUTORISES, true);
$etat = null;
$estInscrit = false;

if ($evenement !== null) {
    $inscriptionRepository = new InscriptionEvenementRepository();
    $etat = $inscriptionRepository->getEtat($id);

    if ($connecte) {
        $estInscrit = $inscriptionRepository->estInscrit($id, (int) $_SESSION['utilisateur_id']);
    }
}

require __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/evenements/detail.php';
        require __DIR__ . '/../views/layouts/footer.php';
