<?php
require_once __DIR__ . '/traitement_verif_session.php';
require_once __DIR__ . '/../repository/MedecinRepository.php';
require_once __DIR__ . '/../repository/EnseignerRepository.php';

exigerConnexion();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['role'] ?? '') !== 'medecin') {
    header('Location: ../../public/profil.php');
    exit;
}

$medecin = (new MedecinRepository())->findById((int) $_SESSION['utilisateur_id']);
if ($medecin === null) {
    header('Location: ../../public/profil.php');
    exit;
}

$idMedecin = $medecin->getIdMedecin();
$idEtablissement = (int) ($_POST['id_etablissement'] ?? 0);
$action = $_POST['action'] ?? '';
$repo = new EnseignerRepository();

if ($idEtablissement > 0) {
    if ($action === 'ajouter') {
        $_SESSION['flash'] = $repo->ajouter($idMedecin, $idEtablissement)
            ? 'Établissement ajouté.'
            : 'Établissement introuvable ou déjà dans votre liste.';
    } elseif ($action === 'supprimer') {
        $_SESSION['flash'] = $repo->supprimer($idMedecin, $idEtablissement)
            ? 'Établissement retiré.'
            : 'Cet établissement n\'était pas dans votre liste.';
    }
}

header('Location: ../../public/profil.php');
exit;