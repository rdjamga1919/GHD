<?php

require_once __DIR__ . '/../repository/MedecinRepository.php';

require_once __DIR__ . '/../repository/EnseignerRepository.php';
require_once __DIR__ . '/../repository/EtablissementRepository.php';

$repo = new MedecinRepository();

$medecin = $repo->findById((int) $_SESSION['utilisateur_id']);

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Traitement des formulaires
if ($medecin !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $actions = [
        'ajouter_specialite' => ['ajouterSpecialite', 'Spécialité ajoutée.', 'Spécialité invalide ou déjà présente.'],
        'retirer_specialite' => ['retirerSpecialite', 'Spécialité retirée.', "Cette spécialité n'est pas dans votre profil."],
        'ajouter_hopital' => ['ajouterHopital', "Hôpital d'exercice ajouté.", 'Hôpital invalide ou déjà présent.'],
        'retirer_hopital' => ['retirerHopital', "Hôpital d'exercice retiré.", "Cet hôpital n'est pas dans votre profil."]
    ];

    $action = $_POST['action'] ?? '';
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {

        $_SESSION['flash'] = ['erreur', 'Requête invalide, veuillez réessayer.'];

    } elseif (!isset($actions[$action]) || $id === false || $id === null || $id <= 0) {

        $_SESSION['flash'] = ['erreur', 'Modification invalide.'];

    } else {

        [$methode, $messageOk, $messageKo] = $actions[$action];

        try {
            $ok = $repo->$methode($medecin->getIdMedecin(), $id);

            $_SESSION['flash'] = $ok
                ? ['succes', $messageOk]
                : ['erreur', $messageKo];

        } catch (Throwable $e) {
            $_SESSION['flash'] = ['erreur', "Une erreur est survenue : aucune modification n'a été enregistrée."];
        }
    }

    header('Location: modifier_profil.php');
    exit;
}

// Récupération du message
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Préparation des données pour la vue
$specialites = [];
$hopitaux = [];
$specialitesDispo = [];
$hopitauxDispo = [];
$etablissements = [];
$resultats = [];
$recherche = trim($_GET['q'] ?? '');

if ($medecin !== null) {

    $idMedecin = $medecin->getIdMedecin();

    $specialites = $repo->getSpecialites($idMedecin);
    $hopitaux = $repo->getHopitaux($idMedecin);

    $idsSpecialites = array_column($specialites, 'id_specialite');
    $idsHopitaux = array_column($hopitaux, 'id_hopital');

    $specialitesDispo = array_filter(
        $repo->getToutesSpecialites(),
        fn($s) => !in_array($s['id_specialite'], $idsSpecialites)
    );

    $hopitauxDispo = array_filter(
        $repo->getTousHopitaux(),
        fn($h) => !in_array($h['id_hopital'], $idsHopitaux)
    );

    $etablissements = (new EnseignerRepository())->getEtablissementsDuMedecin($idMedecin);
    $dejaAssocie = false;
    if ($recherche !== '') {
        $idsEtablissements = array_column($etablissements, 'id_etablissement');

        foreach ((new EtablissementRepository())->rechercherParNom($recherche) as $e) {
            if (in_array($e->getIdEtablissement(), $idsEtablissements)) {
                $dejaAssocie = true;
            } else {
                $resultats[] = $e;
            }
        }
    }
}
