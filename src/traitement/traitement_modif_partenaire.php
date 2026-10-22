<?php

require_once __DIR__ . '/../repository/PartenaireRepository.php';
require_once __DIR__ . '/../repository/EntrepriseRepository.php';

$repo = new PartenaireRepository();
$repoEntreprise = new EntrepriseRepository();

// Récupérer le partenaire connecté
$partenaire = $repo->findById((int) $_SESSION['utilisateur_id']);

if ($partenaire === null) {
    http_response_code(404);
    exit("Profil partenaire introuvable.");
}

// Récupérer les entreprises
$entreprises = $repoEntreprise->getAllEntreprises();

// Protection du formulaire
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $poste = trim($_POST['poste'] ?? '');
    $refEntreprise = filter_input(INPUT_POST, 'ref_entreprise', FILTER_VALIDATE_INT);

    if (!hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {

        $_SESSION['flash'] = ['erreur', 'Requête invalide.'];

    } elseif ($poste === '' || !$refEntreprise) {

        $_SESSION['flash'] = ['erreur', 'Veuillez remplir tous les champs.'];

    } elseif ($repoEntreprise->findById($refEntreprise) === null) {

        $_SESSION['flash'] = ['erreur', 'Entreprise invalide.'];

    } else {

        try {
            // Modifier l'objet partenaire
            $partenaire->setPoste($poste);
            $partenaire->setRefEntreprise($refEntreprise);

            // Enregistrer les modifications
            $repo->update($partenaire);

            $_SESSION['flash'] = ['succes', 'Profil modifié avec succès.'];

        } catch (Throwable $e) {
            error_log($e->getMessage());
            $_SESSION['flash'] = ['erreur', 'Erreur lors de la modification.'];
        }
    }

    header('Location: modifier_profil.php');
    exit;
}

// Récupérer le message
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);