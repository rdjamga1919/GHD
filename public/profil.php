<?php
require_once __DIR__ . '/../src/inclues/authentification.php';
requireLogin();

// Jeton anti-CSRF utilisé par les formulaires d'ajout et de retrait
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$medecin = null;
$specialites = [];
$hopitaux = [];
$etablissements = [];
$resultats = [];
$recherche = trim($_GET['q'] ?? '');
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if ($_SESSION['role'] === 'medecin') {
    require_once __DIR__ . '/../src/repository/MedecinRepository.php';
    require_once __DIR__ . '/../src/repository/EnseignerRepository.php';
    require_once __DIR__ . '/../src/repository/EtablissementRepository.php';

    $repo = new MedecinRepository();
    // On utilise l'identifiant de la SESSION, jamais un id passé dans l'URL
    $medecin = $repo->findById((int) $_SESSION['utilisateur_id']);

    if ($medecin !== null) {
        $specialites = $repo->getSpecialites($medecin->getIdMedecin());
        $hopitaux = $repo->getHopitaux($medecin->getIdMedecin());
        $etablissements = (new EnseignerRepository())->getEtablissementsDuMedecin($medecin->getIdMedecin());

        if ($recherche !== '') {
            $dejaIds = array_column($etablissements, 'id_etablissement');
            foreach ((new EtablissementRepository())->rechercherParNom($recherche) as $e) {
                if (!in_array($e->getIdEtablissement(), $dejaIds)) {
                    $resultats[] = $e;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon profil</title>
</head>
<body>

<?php if (is_array($flash)): ?>
    <p class="message message-<?= $flash[0] === 'succes' ? 'succes' : 'erreur' ?>">
        <?= htmlspecialchars($flash[1]) ?>
    </p>
<?php endif; ?>

<?php if ($medecin !== null): ?>

    <h2>Mon profil</h2>
    <p>Nom : <?= htmlspecialchars($medecin->getNom()) ?></p>
    <p>Prénom : <?= htmlspecialchars($medecin->getPrenom()) ?></p>
    <p>Email : <?= htmlspecialchars($medecin->getEmail()) ?></p>
    <p>N° RPPS : <?= htmlspecialchars($medecin->getNumeroRpps()) ?></p>

    <h3>Spécialités</h3>
    <?php if (empty($specialites)): ?>
        <p>Aucune spécialité renseignée.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($specialites as $s): ?>
                <li><?= htmlspecialchars($s['libelle_specialite']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h3>Hôpitaux d'exercice</h3>
    <?php if (empty($hopitaux)): ?>
        <p>Aucun hôpital renseigné.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($hopitaux as $h): ?>
                <li><?= htmlspecialchars($h['nom_hopital']) ?> (<?= htmlspecialchars($h['commune']) ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h3>Je suis professeur dans…</h3>
    <?php if (empty($etablissements)): ?>
        <p>Aucun établissement renseigné.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($etablissements as $e): ?>
                <li>
                    <?= htmlspecialchars($e['nom_etablissement']) ?>
                    <?php if (!empty($e['adresse'])): ?>
                        (<?= htmlspecialchars($e['adresse']) ?>)
                    <?php endif; ?>
                    <form method="post" action="../src/traitement/traitement_enseigner.php" class="form-retrait">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id_etablissement" value="<?= (int) $e['id_etablissement'] ?>">
                        <button type="submit">Retirer</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h4>Ajouter un établissement</h4>
    <form method="get" action="profil.php">
        <input type="text" name="q" value="<?= htmlspecialchars($recherche) ?>" placeholder="Nom de l'établissement">
        <button type="submit">Rechercher</button>
    </form>

    <?php if ($recherche !== ''): ?>
        <?php if (empty($resultats)): ?>
            <p>Aucun établissement trouvé pour « <?= htmlspecialchars($recherche) ?> ».</p>
            <!-- À brancher sur la page de création d'établissement de Serge (ne pas dupliquer son formulaire) -->
            <p><a href="etablissement.php">Votre établissement n'existe pas ? Le créer</a></p>
        <?php else: ?>
            <ul>
                <?php foreach ($resultats as $e): ?>
                    <li>
                        <?= htmlspecialchars($e->getNom()) ?>
                        <form method="post" action="../src/traitement/traitement_enseigner.php" class="form-retrait">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                            <input type="hidden" name="action" value="ajouter">
                            <input type="hidden" name="id_etablissement" value="<?= (int) $e->getIdEtablissement() ?>">
                            <button type="submit">Ajouter</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>

    <p><a href="modifier_profil.php">Modifier mon profil</a></p>

<?php endif; ?>

</body>
</html>