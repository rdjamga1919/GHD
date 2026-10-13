<?php
require_once __DIR__ . '/../src/traitement/traitement_verif_session.php';
exigerConnexion();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$medecin = null;

if ($_SESSION['role'] === 'medecin') {
    require_once __DIR__ . '/../src/repository/MedecinRepository.php';

    $repo = new MedecinRepository();
    $medecin = $repo->findById((int) $_SESSION['utilisateur_id']);
}

if ($medecin !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $actions = [
            'ajouter_specialite' => ['ajouterSpecialite', 'Spécialité ajoutée.', 'Spécialité invalide ou déjà présente.'],
            'retirer_specialite' => ['retirerSpecialite', 'Spécialité retirée.', "Cette spécialité n'est pas dans votre profil."],
            'ajouter_hopital'    => ['ajouterHopital', "Hôpital d'exercice ajouté.", 'Hôpital invalide ou déjà présent.'],
            'retirer_hopital'    => ['retirerHopital', "Hôpital d'exercice retiré.", "Cet hôpital n'est pas dans votre profil."],
    ];

    $action = $_POST['action'] ?? '';
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {
        $_SESSION['flash'] = ['erreur', 'Requête invalide, veuillez réessayer.'];
    } elseif (!isset($actions[$action]) || $id === false || $id === null || $id <= 0) {
        $_SESSION['flash'] = ['erreur', 'Modification invalide : aucune donnée enregistrée.'];
    } else {
        [$methode, $messageOk, $messageKo] = $actions[$action];
        try {
            $ok = $repo->$methode($medecin->getIdMedecin(), $id);
            $_SESSION['flash'] = $ok ? ['succes', $messageOk] : ['erreur', $messageKo];
        } catch (Throwable $e) {
            $_SESSION['flash'] = ['erreur', "Une erreur est survenue : aucune modification n'a été enregistrée."];
        }
    }

    header('Location: modifier_profil.php');
    exit;
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

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
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier mon profil</title>
</head>
<body>

<?php if ($flash !== null): ?>
    <p class="message message-<?= $flash[0] === 'succes' ? 'succes' : 'erreur' ?>">
        <?= htmlspecialchars($flash[1]) ?>
    </p>
<?php endif; ?>

<?php if ($medecin !== null): ?>

    <h2>Modifier mon profil</h2>

    <h3>Mes spécialités</h3>
    <?php if (empty($specialites)): ?>
        <p>Aucune spécialité renseignée.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($specialites as $s): ?>
                <li>
                    <?= htmlspecialchars($s['libelle_specialite']) ?>
                    <form method="post" class="form-retrait">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                        <input type="hidden" name="action" value="retirer_specialite">
                        <input type="hidden" name="id" value="<?= (int) $s['id_specialite'] ?>">
                        <button type="submit">Retirer</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (empty($specialitesDispo)): ?>
        <p>Aucune autre spécialité disponible.</p>
    <?php else: ?>
        <form method="post">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="ajouter_specialite">
            <select name="id" required>
                <option value="">-- Choisir une spécialité --</option>
                <?php foreach ($specialitesDispo as $s): ?>
                    <option value="<?= (int) $s['id_specialite'] ?>">
                        <?= htmlspecialchars($s['libelle_specialite']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Ajouter</button>
        </form>
    <?php endif; ?>

    <h3>Mes hôpitaux d'exercice</h3>
    <?php if (empty($hopitaux)): ?>
        <p>Aucun hôpital renseigné.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($hopitaux as $h): ?>
                <li>
                    <?= htmlspecialchars($h['nom_hopital']) ?> (<?= htmlspecialchars($h['commune']) ?>)
                    <form method="post" class="form-retrait">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                        <input type="hidden" name="action" value="retirer_hopital">
                        <input type="hidden" name="id" value="<?= (int) $h['id_hopital'] ?>">
                        <button type="submit">Retirer</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (empty($hopitauxDispo)): ?>
        <p>Aucun autre hôpital disponible.</p>
    <?php else: ?>
        <form method="post">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="ajouter_hopital">
            <select name="id" required>
                <option value="">-- Choisir un hôpital --</option>
                <?php foreach ($hopitauxDispo as $h): ?>
                    <option value="<?= (int) $h['id_hopital'] ?>">
                        <?= htmlspecialchars($h['nom_hopital']) ?> (<?= htmlspecialchars($h['commune']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Ajouter</button>
        </form>
    <?php endif; ?>

    <p><a href="profil.php">Retour à mon profil</a></p>

<?php endif; ?>

</body>
</html>