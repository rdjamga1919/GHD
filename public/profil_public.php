<?php
session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/UtilisateurRepository.php';

// Réservé aux utilisateurs connectés
requireLogin();

/**
 * Affiche une page d'erreur simple (404) avec un lien de retour vers l'annuaire.
 */
function afficherProfilIntrouvable(): void
{
    http_response_code(404);
    $titrePage = 'Profil introuvable';
    require __DIR__ . '/../views/profils/public/_head.php';
    ?>
    <h1>Profil introuvable</h1>
    <div class="card">
        <p>Ce profil n'existe pas ou n'est pas accessible.</p>
    </div>
    <?php
    require __DIR__ . '/../views/profils/public/_foot.php';
    exit;
}

/**
 * Retourne l'URL uniquement si elle est en http(s), sinon null.
 * Évite les liens de type "javascript:..." issus de la base de données.
 */
function urlSure(?string $url): ?string
{
    $url = trim((string) $url);
    return preg_match('#^https?://#i', $url) ? $url : null;
}

// 1. Validation de l'identifiant reçu dans l'URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($id === false || $id === null) {
    afficherProfilIntrouvable();
}

// Si l'utilisateur consulte son propre profil, on l'envoie vers "Mon profil"
if ($id === (int) $_SESSION['utilisateur_id']) {
    header('Location: profil.php');
    exit;
}

// 2. Récupération de l'utilisateur et de son rôle
$utilisateur = (new UtilisateurRepository())->findById($id);

// Profil inexistant, non validé, ou rôle hors annuaire (ex. gestionnaire) : 404
if (
    $utilisateur === null
    || !$utilisateur->estValide()
    || !in_array($utilisateur->getRole(), ['etudiant', 'medecin', 'partenaire'], true)
) {
    afficherProfilIntrouvable();
}

// 3. Récupération des données spécifiques au rôle
switch ($utilisateur->getRole()) {

    case 'etudiant':
        require_once __DIR__ . '/../src/repository/EtudiantRepository.php';
        require_once __DIR__ . '/../src/repository/EtablissementRepository.php';

        $etudiant = (new EtudiantRepository())->findById($id);
        if ($etudiant === null) {
            afficherProfilIntrouvable();
        }

        $etablissement = (new EtablissementRepository())->findById($etudiant->getRefEtablissement());
        $vue = 'etudiantPublicView.php';
        break;

    case 'medecin':
        require_once __DIR__ . '/../src/repository/MedecinRepository.php';
        require_once __DIR__ . '/../src/repository/EnseignerRepository.php';

        $repoMedecin = new MedecinRepository();
        $medecin = $repoMedecin->findById($id);
        if ($medecin === null) {
            afficherProfilIntrouvable();
        }

        $specialites    = $repoMedecin->getSpecialites($medecin->getIdMedecin());
        $hopitaux       = $repoMedecin->getHopitaux($medecin->getIdMedecin());
        $etablissements = (new EnseignerRepository())->getEtablissementsDuMedecin($medecin->getIdMedecin());
        $vue = 'medecinPublicView.php';
        break;

    case 'partenaire':
        require_once __DIR__ . '/../src/repository/PartenaireRepository.php';
        require_once __DIR__ . '/../src/repository/EntrepriseRepository.php';

        $partenaire = (new PartenaireRepository())->findById($id);
        if ($partenaire === null) {
            afficherProfilIntrouvable();
        }

        $entreprise = (new EntrepriseRepository())->getEntreprise($partenaire->getRefEntreprise());
        $vue = 'partenairePublicView.php';
        break;
}

// 4. Affichage de la vue publique correspondante
$titrePage = 'Profil de ' . $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();
require __DIR__ . '/../views/profils/public/_head.php';
require __DIR__ . '/../views/profils/public/' . $vue;
require __DIR__ . '/../views/profils/public/_foot.php';