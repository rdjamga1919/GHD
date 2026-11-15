<?php
/*
 * Traitement des réponses du forum.
 * Le champ caché "action" du formulaire indique quoi faire : creer, modifier ou supprimer.
 */

session_start();

require_once __DIR__ . '/../../inclues/authentification.php';
require_once __DIR__ . '/../../inclues/permissionsForum.php';
require_once __DIR__ . '/../../repository/ReponseForumRepository.php';


/* ------------------------------------------------------------------
   Petites fonctions utiles
   ------------------------------------------------------------------ */

// Redirige vers une page, puis arrête le script
function allerA(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function urlForum(): string
{
    return URL_BASE . '/public/forum.php';
}

function urlPublication(int $idPost): string
{
    return URL_BASE . '/public/reponses.php?id_post=' . $idPost;
}

// Lit un champ texte du formulaire (sans espaces au début et à la fin)
function lireTexte(string $nom): string
{
    if (!isset($_POST[$nom]) || !is_string($_POST[$nom])) {
        return '';
    }
    return str_replace("\r\n", "\n", trim($_POST[$nom]));
}

// Lit un identifiant du formulaire : un entier >= 1, sinon false
function lireId(string $nom)
{
    return filter_var($_POST[$nom] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
}

// Vérifie le contenu d'une réponse et retourne la liste des erreurs
function verifierContenu(string $contenu): array
{
    $erreurs = [];

    if ($contenu === '') {
        $erreurs[] = "Le contenu de la réponse est obligatoire.";
    } elseif (mb_strlen($contenu) > 2000) {
        $erreurs[] = "La réponse ne doit pas dépasser 2000 caractères.";
    }

    return $erreurs;
}


/* ------------------------------------------------------------------
   Les 3 actions
   ------------------------------------------------------------------ */

function creerReponse(ReponseForumRepository $reponseRepository): void
{
    $idPost  = lireId('id_post');
    $contenu = lireTexte('contenu');

    // 1. La publication doit exister
    $publication = $idPost ? $reponseRepository->findPublication($idPost) : null;
    if ($publication === null) {
        $_SESSION['erreurs'] = ["Cette publication n'existe pas."];
        allerA(urlForum());
    }

    // 2. L'utilisateur doit avoir accès au canal de la publication
    if (!peutLireCanal($publication['acces'])) {
        $_SESSION['erreurs'] = ["Vous n'avez pas accès à cette publication."];
        allerA(urlForum());
    }

    $urlFormulaire = URL_BASE . '/public/creer_reponse.php?id_post=' . $idPost;

    // 3. Le contenu ne doit pas être vide
    $erreurs = verifierContenu($contenu);
    if (!empty($erreurs)) {
        $_SESSION['erreurs'] = $erreurs;
        $_SESSION['valeurs'] = ['contenu' => $contenu];
        allerA($urlFormulaire);
    }

    // 4. Enregistrement (l'auteur vient de la SESSION, jamais du formulaire)
    $reponse = new ReponseForum(null, $idPost, $contenu, null, (int) $_SESSION['id_utilisateur']);

    try {
        $reponseRepository->insert($reponse);
    } catch (PDOException $e) {
        error_log("Erreur création réponse : " . $e->getMessage());
        $_SESSION['erreurs'] = ["Une erreur est survenue lors de l'enregistrement. Veuillez réessayer."];
        $_SESSION['valeurs'] = ['contenu' => $contenu];
        allerA($urlFormulaire);
    }

    $_SESSION['succes'] = ["Votre réponse a bien été publiée."];
    allerA(urlPublication($idPost));
}

function modifierReponse(ReponseForumRepository $reponseRepository): void
{
    $idReponse = lireId('id_reponse');
    $contenu   = lireTexte('contenu');

    // 1. La réponse doit exister
    $reponse = $idReponse ? $reponseRepository->findById($idReponse) : null;
    if ($reponse === null) {
        $_SESSION['erreurs'] = ["Cette réponse n'existe pas."];
        allerA(urlForum());
    }

    $urlPublication = urlPublication((int) $reponse->getRefPost());

    // 2. Seul l'auteur peut modifier sa réponse
    if (!peutModifierReponse($reponse)) {
        $_SESSION['erreurs'] = ["Vous ne pouvez modifier que vos propres réponses."];
        allerA($urlPublication);
    }

    $urlFormulaire = URL_BASE . '/public/modifier_reponse.php?id_reponse=' . $idReponse;

    // 3. Le contenu ne doit pas être vide
    $erreurs = verifierContenu($contenu);
    if (!empty($erreurs)) {
        $_SESSION['erreurs'] = $erreurs;
        $_SESSION['valeurs'] = ['contenu' => $contenu];
        allerA($urlFormulaire);
    }

    // 4. Enregistrement
    try {
        $reponse->setContenu($contenu);
        $reponseRepository->update($reponse);
    } catch (PDOException $e) {
        error_log("Erreur modification réponse : " . $e->getMessage());
        $_SESSION['erreurs'] = ["Une erreur est survenue lors de l'enregistrement. Veuillez réessayer."];
        $_SESSION['valeurs'] = ['contenu' => $contenu];
        allerA($urlFormulaire);
    }

    $_SESSION['succes'] = ["Votre réponse a bien été modifiée."];
    allerA($urlPublication);
}

function supprimerReponse(ReponseForumRepository $reponseRepository): void
{
    $idReponse = lireId('id_reponse');

    // 1. La réponse doit exister
    $reponse = $idReponse ? $reponseRepository->findById($idReponse) : null;
    if ($reponse === null) {
        $_SESSION['erreurs'] = ["Cette réponse n'existe pas."];
        allerA(urlForum());
    }

    $urlPublication = urlPublication((int) $reponse->getRefPost());

    // 2. Seul l'auteur ou le gestionnaire peut supprimer
    if (!peutSupprimerReponse($reponse)) {
        $_SESSION['erreurs'] = ["Vous ne pouvez supprimer que vos propres réponses."];
        allerA($urlPublication);
    }

    // 3. Suppression
    try {
        $reponseRepository->delete($idReponse);
        $_SESSION['succes'] = ["La réponse a été supprimée."];
    } catch (PDOException $e) {
        error_log("Erreur suppression réponse : " . $e->getMessage());
        $_SESSION['erreurs'] = ["Une erreur est survenue lors de la suppression. Veuillez réessayer."];
    }

    allerA($urlPublication);
}


/* ------------------------------------------------------------------
   Programme principal
   ------------------------------------------------------------------ */

// Il faut être connecté
exigerConnexion();

// On accepte uniquement les formulaires envoyés en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    allerA(urlForum());
}

// Protection CSRF : le jeton du formulaire doit être le bon
if (!verifierTokenCsrf($_POST['csrf_token'] ?? null)) {
    $_SESSION['erreurs'] = ["Formulaire invalide ou expiré. Veuillez réessayer."];
    allerA(urlForum());
}

$reponseRepository = new ReponseForumRepository();
$action = $_POST['action'] ?? '';

if ($action === 'creer') {
    creerReponse($reponseRepository);
} elseif ($action === 'modifier') {
    modifierReponse($reponseRepository);
} elseif ($action === 'supprimer') {
    supprimerReponse($reponseRepository);
}

// Si on arrive ici, l'action est inconnue
$_SESSION['erreurs'] = ["Action non reconnue."];
allerA(urlForum());