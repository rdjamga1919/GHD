
<?php

session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';

requireLogin();

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

        break;

    case 'partenaire':
        http_response_code(501);
        exit("Le profil partenaire n'est pas encore disponible.");

    default:
        http_response_code(403);
        exit("Accès non autorisé.");
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mon profil</title>
</head>

<body>

<?php

switch ($_SESSION['role']) {

    case 'etudiant':
        require __DIR__ . '/../views/profils/etudiantView.php';
        break;

    case 'medecin':
        require __DIR__ . '/../views/profils/medecinView.php';
        break;
}

?>

</body>
</html>