<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deconnexion'])) {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }

    session_destroy();
    header('Location: connexion.php');
    exit;
}

require_once __DIR__ . '/../src/inclues/authentification.php';

// Vérification que l'utilisateur est connecté
requireLogin();

// Récupération des données spécifiques en fonction du rôle
switch ($_SESSION['role']) {

    case 'etudiant':
        require_once __DIR__ . '/../src/repository/EtudiantRepository.php';
        require_once __DIR__ . '/../src/repository/EtablissementRepository.php';

        $repo = new EtudiantRepository();
        $etudiant = $repo->findById((int) $_SESSION['utilisateur_id']);

        if ($etudiant === null) {
            http_response_code(404);
            exit("Profil étudiant introuvable.");
        }

        // Récupérer l'établissement de l'étudiant
        $repoEtablissement = new EtablissementRepository();
        $etablissement = $repoEtablissement->findById(
                $etudiant->getRefEtablissement()
        );
        break;

    case 'medecin':
        require_once __DIR__ . '/../src/repository/MedecinRepository.php';

        $repo = new MedecinRepository();
        $medecin = $repo->findById((int) $_SESSION['utilisateur_id']);

        if ($medecin === null) {
            http_response_code(404);
            exit("Profil médecin introuvable.");
        }

        $specialites = $repo->getSpecialites($medecin->getIdMedecin());
        $hopitaux = $repo->getHopitaux($medecin->getIdMedecin());

        require_once __DIR__ . '/../src/repository/EnseignerRepository.php';
        $etablissements = (new EnseignerRepository())->getEtablissementsDuMedecin($medecin->getIdMedecin());
        break;

    case 'partenaire':
        require_once __DIR__ . '/../src/repository/PartenaireRepository.php';
        require_once __DIR__ . '/../src/repository/EntrepriseRepository.php';

        // Récupérer le partenaire connecté
        $repo = new PartenaireRepository();
        $partenaire = $repo->findById(
                (int) $_SESSION['utilisateur_id']
        );

        if ($partenaire === null) {
            http_response_code(404);
            exit("Profil partenaire introuvable.");
        }

        // Récupérer son entreprise
        $repoEntreprise = new EntrepriseRepository();
        $entreprise = $repoEntreprise->getEntreprise(
                $partenaire->getRefEntreprise()
        );
        break;

    default:
        http_response_code(403);
        exit("Accès non autorisé.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil – GHD-HSP</title>
</head>
<body>

<?php
// Inclusion de la vue spécifique au rôle de l'utilisateur
switch ($_SESSION['role']) {
    case 'etudiant':
        require __DIR__ . '/../views/profils/etudiantView.php';
        break;

    case 'medecin':
        require __DIR__ . '/../views/profils/medecinView.php';
        break;

    case 'partenaire':
        require __DIR__ . '/../views/profils/partenaireView.php';
        break;
}
?>

<form method="post" action="profil.php">
    <button type="submit" name="deconnexion">Se déconnecter</button>
</form>

</body>
</html>