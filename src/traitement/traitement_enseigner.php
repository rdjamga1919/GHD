<?php
// authentification.php ne démarre pas la session : on le fait ici avant de l'inclure
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../inclues/authentification.php';
require_once __DIR__ . '/../repository/MedecinRepository.php';
require_once __DIR__ . '/../repository/EnseignerRepository.php';

// Les formulaires d'ajout et de retrait se trouvent sur la page de modification du profil
$retour = '../../public/modifier_profil.php';

// Visiteur non connecté : redirigé vers la connexion.
// Connecté mais pas médecin (étudiant, partenaire...) : refus 403.
requireRole('medecin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $retour);
    exit;
}

// Protection CSRF : le jeton du formulaire doit correspondre à celui de la session
$jetonSession = $_SESSION['csrf'] ?? '';
$jetonFormulaire = (string) ($_POST['csrf'] ?? '');
if ($jetonSession === '' || !hash_equals($jetonSession, $jetonFormulaire)) {
    $_SESSION['flash'] = ['erreur', 'Requête invalide, veuillez réessayer.'];
    header('Location: ' . $retour);
    exit;
}

// L'identifiant du médecin vient TOUJOURS de la session, jamais du formulaire
$medecin = (new MedecinRepository())->findById((int) $_SESSION['utilisateur_id']);
if ($medecin === null) {
    $_SESSION['flash'] = ['erreur', 'Profil médecin introuvable.'];
    header('Location: ' . $retour);
    exit;
}

$idMedecin = $medecin->getIdMedecin();
$idEtablissement = (int) ($_POST['id_etablissement'] ?? 0);
$action = $_POST['action'] ?? '';
$repo = new EnseignerRepository();

if ($idEtablissement <= 0 || !in_array($action, ['ajouter', 'supprimer'], true)) {
    $_SESSION['flash'] = ['erreur', 'Modification invalide : aucune donnée enregistrée.'];
    header('Location: ' . $retour);
    exit;
}

try {
    if ($action === 'ajouter') {
        $_SESSION['flash'] = $repo->ajouter($idMedecin, $idEtablissement)
            ? ['succes', 'Établissement ajouté.']
            : ['erreur', 'Établissement introuvable ou déjà dans votre liste.'];
    } else {
        $_SESSION['flash'] = $repo->supprimer($idMedecin, $idEtablissement)
            ? ['succes', 'Établissement retiré.']
            : ['erreur', "Cet établissement n'était pas dans votre liste."];
    }
} catch (Throwable $e) {
    $_SESSION['flash'] = ['erreur', "Une erreur est survenue : aucune modification n'a été enregistrée."];
}

header('Location: ' . $retour);
exit;