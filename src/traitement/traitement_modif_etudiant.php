
<?php

require_once __DIR__ . '/../repository/EtudiantRepository.php';
require_once __DIR__ . '/../repository/EtablissementRepository.php';

// Création des repositories
$repo = new EtudiantRepository();
$repoEtablissement = new EtablissementRepository();

// Récupérer l'étudiant connecté
$etudiant = $repo->findById((int) $_SESSION['utilisateur_id']);

if ($etudiant === null) {
    http_response_code(404);
    exit("Profil étudiant introuvable.");
}

// Récupérer tous les établissements
$etablissements = $repoEtablissement->findAll();

// Créer le jeton CSRF
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupérer les données envoyées
    $formation = trim($_POST['formation'] ?? '');
    $idEtablissement = filter_input(
        INPUT_POST,
        'etablissement',
        FILTER_VALIDATE_INT
    );

    // Vérifier le jeton CSRF
    if (!hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {

        $_SESSION['flash'] = ['erreur', 'Requête invalide.'];

    } elseif ($formation === '') {

        $_SESSION['flash'] = ['erreur', 'La formation est obligatoire.'];

    } elseif (!$idEtablissement || $idEtablissement <= 0) {

        $_SESSION['flash'] = ['erreur', 'Veuillez choisir un établissement.'];

    } elseif ($repoEtablissement->findById($idEtablissement) === null) {

        $_SESSION['flash'] = ['erreur', "L'établissement sélectionné n'existe pas."];

    } else {

        try {
            // Modifier les propriétés de l'étudiant
            $etudiant->setFormation($formation);
            $etudiant->setRefEtablissement($idEtablissement);

            // Enregistrer dans la BDD
            $repo->update($etudiant);

            $_SESSION['flash'] = ['succes', 'Profil modifié avec succès.'];

        } catch (Throwable $e) {
            $_SESSION['flash'] = ['erreur', 'Erreur lors de la modification.'];
        }
    }

    // Revenir à la page de modification
    header('Location: modifier_profil.php');
    exit;
}

// Récupérer le message de confirmation
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
