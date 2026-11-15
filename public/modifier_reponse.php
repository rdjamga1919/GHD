<?php
session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/inclues/permissionsForum.php';
require_once __DIR__ . '/../src/repository/ReponseForumRepository.php';

exigerConnexion();

// 1. Vérifier l'identifiant de la réponse (entier supérieur ou égal à 1)
$idReponse = filter_var($_GET['id_reponse'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

// 2. Vérifier que la réponse existe
$reponseRepository = new ReponseForumRepository();
$reponse = $idReponse ? $reponseRepository->findById($idReponse) : null;

if ($reponse === null) {
    $_SESSION['erreurs'] = ["Cette réponse n'existe pas."];
    header('Location: ' . URL_BASE . '/public/forum.php');
    exit;
}

// 3. Vérifier que l'utilisateur connecté est l'auteur
$urlPublication = URL_BASE . '/public/reponses.php?id_post=' . (int) $reponse->getRefPost();

if (!peutModifierReponse($reponse)) {
    $_SESSION['erreurs'] = ["Vous ne pouvez modifier que vos propres réponses."];
    header('Location: ' . $urlPublication);
    exit;
}

// Messages et texte déjà saisi (si le formulaire a été refusé)
$erreurs = $_SESSION['erreurs'] ?? [];
$succes  = $_SESSION['succes']  ?? [];
$valeurs = $_SESSION['valeurs'] ?? [];
unset($_SESSION['erreurs'], $_SESSION['succes'], $_SESSION['valeurs']);

// Formulaire prérempli avec le contenu existant (ou avec la saisie refusée)
$contenu = $valeurs['contenu'] ?? $reponse->getContenu();

$active = 'forum';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier ma réponse – GDH</title>
    <link rel="stylesheet" href="<?= URL_BASE ?>/ressources/css/forum_reponses.css">
</head>
<body>

<?php include __DIR__ . '/../src/inclues/header.php'; ?>

<main class="forum-reponses">

    <h1>Modifier ma réponse</h1>

    <?php require __DIR__ . '/../views/forum/reponses/messagesView.php'; ?>

    <?php require __DIR__ . '/../views/forum/reponses/modifierReponseView.php'; ?>

    <a class="lien-retour" href="<?= htmlspecialchars($urlPublication) ?>">← Retour aux réponses</a>

</main>

<?php include __DIR__ . '/../src/inclues/footer.php'; ?>

</body>
</html>