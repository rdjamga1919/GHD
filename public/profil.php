<?php
require_once __DIR__ . '/../src/traitement/traitement_verif_session.php';
exigerConnexion();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$erreurs = $_SESSION['erreurs'] ?? [];
$succes = $_SESSION['succes'] ?? [];
unset($_SESSION['erreurs'], $_SESSION['succes']);

$medecin = null;
$specialites = [];
$hopitaux = [];

$partenaire = null;
$entreprise = null;
$toutesEntreprises = [];

if ($_SESSION['role'] === 'medecin') {
    require_once __DIR__ . '/../src/repository/MedecinRepository.php';

    $repo = new MedecinRepository();
    $medecin = $repo->findById((int) $_SESSION['utilisateur_id']);

    if ($medecin !== null) {
        $specialites = $repo->getSpecialites($medecin->getIdMedecin());
        $hopitaux = $repo->getHopitaux($medecin->getIdMedecin());
    }
}

if ($_SESSION['role'] === 'partenaire') {
    require_once __DIR__ . '/../src/repository/PartenaireRepository.php';
    require_once __DIR__ . '/../src/repository/EntrepriseRepository.php';

    $partenaireRepository = new PartenaireRepository();
    $entrepriseRepository = new EntrepriseRepository();

    // Profil de la session uniquement
    $partenaire = $partenaireRepository->findById((int) $_SESSION['utilisateur_id']);

    if ($partenaire !== null) {
        $entreprise = $entrepriseRepository->getEntreprise($partenaire->getRefEntreprise());
        $toutesEntreprises = $entrepriseRepository->getAllEntreprises();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil</title>
    <link rel="stylesheet" href="../ressources/js/ressources/style.css">
</head>
<body>

<?php if (!empty($succes)): ?>
    <div class="alert alert-success">
        <?php foreach ($succes as $msg): ?>
            <p><?= htmlspecialchars($msg) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($erreurs)): ?>
    <div class="alert alert-error">
        <?php foreach ($erreurs as $msg): ?>
            <p><?= htmlspecialchars($msg) ?></p>
        <?php endforeach; ?>
    </div>
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

    <a href="modifier_profil.php">Modifier mon profil</a>

<?php endif; ?>

<?php if ($partenaire !== null): ?>

    <h2>Mon profil</h2>
    <p>Nom : <?= htmlspecialchars($partenaire->getNom()) ?></p>
    <p>Prénom : <?= htmlspecialchars($partenaire->getPrenom()) ?></p>
    <p>Email : <?= htmlspecialchars($partenaire->getEmail()) ?></p>
    <p>Poste : <?= htmlspecialchars($partenaire->getPoste()) ?></p>

    <h3>Mon entreprise</h3>
    <?php if ($entreprise === null): ?>
        <p>Aucune entreprise renseignée.</p>
    <?php else: ?>
        <p>Nom : <a href="entreprise.php?id=<?= (int) $entreprise->getIdEntreprise() ?>"><?= htmlspecialchars($entreprise->getNom()) ?></a></p>
        <p>Adresse : <?= htmlspecialchars($entreprise->getAdresse()) ?></p>
        <p>Site web :
            <a href="<?= htmlspecialchars($entreprise->getSiteWeb()) ?>" target="_blank" rel="noopener noreferrer">
                <?= htmlspecialchars($entreprise->getSiteWeb()) ?>
            </a>
        </p>
    <?php endif; ?>

    <h3>Changer d'entreprise</h3>
    <form method="post" action="../src/traitement/traitement_entreprise.php">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
        <input type="hidden" name="action" value="selectionner">
        <input type="hidden" name="retour" value="profil">
        <div class="form-group">
            <label for="ref_entreprise">Mon entreprise</label>
            <select id="ref_entreprise" name="ref_entreprise" required>
                <option value="">-- Choisir une entreprise --</option>
                <?php foreach ($toutesEntreprises as $e): ?>
                    <option value="<?= (int) $e->getIdEntreprise() ?>"
                            <?= $e->getIdEntreprise() === $partenaire->getRefEntreprise() ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e->getNom()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer mon entreprise</button>
    </form>
    <p><a href="entreprise.php#creation">Mon entreprise n'est pas dans la liste</a></p>

<?php endif; ?>

</body>
</html>