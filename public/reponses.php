<?php
session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/inclues/permissionsForum.php';
require_once __DIR__ . '/../src/repository/ReponseForumRepository.php';

exigerConnexion();

// 1. Vérifier l'identifiant de la publication (entier supérieur ou égal à 1)
$idPost = filter_var($_GET['id_post'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

// 2. Vérifier que la publication existe
$reponseRepository = new ReponseForumRepository();
$publication = $idPost ? $reponseRepository->findPublication($idPost) : null;

if ($publication === null) {
    $_SESSION['erreurs'] = ["Cette publication n'existe pas."];
    header('Location: ' . URL_BASE . '/public/forum.php');
    exit;
}

// 3. Vérifier que l'utilisateur a le droit de lire ce canal
if (!peutLireCanal($publication['acces'])) {
    $_SESSION['erreurs'] = ["Vous n'avez pas accès à cette publication."];
    header('Location: ' . URL_BASE . '/public/forum.php');
    exit;
}

// 4. Récupérer les réponses (déjà triées de la plus ancienne à la plus récente)
$reponses = $reponseRepository->findByPublication($idPost);

$erreurs = $_SESSION['erreurs'] ?? [];
$succes  = $_SESSION['succes']  ?? [];
unset($_SESSION['erreurs'], $_SESSION['succes']);

$active = 'forum';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réponses – GDH</title>
    <link rel="stylesheet" href="<?= URL_BASE ?>/ressources/css/forum_reponses.css">
</head>
<body>

<?php include __DIR__ . '/../src/inclues/header.php'; ?>

<main class="forum-reponses">

    <h1>Réponses à « <?= htmlspecialchars($publication['titre']) ?> »</h1>

    <?php require __DIR__ . '/../views/forum/reponses/messagesView.php'; ?>

    <div class="barre-haut">
        <a class="btn" href="creer_reponse.php?id_post=<?= (int) $idPost ?>">Répondre</a>
    </div>

    <?php require __DIR__ . '/../views/forum/reponses/listeReponsesView.php'; ?>

</main>

<?php include __DIR__ . '/../src/inclues/footer.php'; ?>

</body>
</html>