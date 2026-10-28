<?php
session_start();
require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/EntrepriseRepository.php';
require_once __DIR__ . '/../src/repository/PartenaireRepository.php';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// La consultation est ouverte à tous (visiteurs compris).
// Création : visiteurs (futurs partenaires), partenaires et gestionnaires. Sélection : partenaires.
$connecte = isset($_SESSION['utilisateur_id']);
$role = $_SESSION['role'] ?? null;
$estPartenaire = $connecte && $role === 'partenaire';
$peutCreer = !$connecte || in_array($role, ['partenaire', 'gestionnaire'], true);

$entrepriseRepository = new EntrepriseRepository();
$partenaireRepository = new PartenaireRepository();

$erreurs = $_SESSION['erreurs'] ?? [];
$succes = $_SESSION['succes'] ?? [];
$valeurs = $_SESSION['valeurs'] ?? [];
$existante = $_SESSION['entreprise_existante'] ?? null;
unset($_SESSION['erreurs'], $_SESSION['succes'], $_SESSION['valeurs'], $_SESSION['entreprise_existante']);

$nomSaisi = $valeurs['nom_entreprise'] ?? '';
$adresseSaisie = $valeurs['adresse'] ?? '';
$siteWebSaisi = $valeurs['site_web'] ?? '';

// Entreprise actuelle du partenaire connecté (pour afficher « Votre entreprise »)
$monEntrepriseId = null;
if ($estPartenaire) {
    $monPartenaire = $partenaireRepository->findById((int) $_SESSION['utilisateur_id']);
    if ($monPartenaire !== null) {
        $monEntrepriseId = $monPartenaire->getRefEntreprise();
    }
}

$recherche = trim($_GET['recherche'] ?? '');
$entreprise = null;
$partenaires = [];

// Fiche détaillée : entreprise.php?id=...
if (isset($_GET['id'])) {
    $idEntreprise = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($idEntreprise !== false && $idEntreprise > 0) {
        $entreprise = $entrepriseRepository->getEntreprise($idEntreprise);
    }
    if ($entreprise === null) {
        $erreurs[] = "Entreprise introuvable.";
    } else {
        $partenaires = $partenaireRepository->getPartenairesParEntreprise($entreprise->getIdEntreprise());
    }
}

// Liste (avec recherche par nom)
$entreprises = [];
if ($entreprise === null) {
    $entreprises = ($recherche !== '')
            ? $entrepriseRepository->rechercherEntreprises($recherche)
            : $entrepriseRepository->getAllEntreprises();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entreprises – GHD</title>
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>
<body>

<main class="container">

    <h1 class="page-title">Entreprises</h1>

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

    <?php if ($existante !== null): ?>
        <div class="card">
            <p>
                Entreprise déjà enregistrée :
                <a href="entreprise.php?id=<?= (int) $existante['id'] ?>"><?= htmlspecialchars($existante['nom']) ?></a>
            </p>
            <?php if ($estPartenaire): ?>
                <form method="post" action="../src/traitement/traitement_entreprise.php">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                    <input type="hidden" name="action" value="selectionner">
                    <input type="hidden" name="ref_entreprise" value="<?= (int) $existante['id'] ?>">
                    <button type="submit" class="btn">Sélectionner cette entreprise</button>
                </form>
            <?php elseif (!$connecte): ?>
                <p><a class="btn btn-secondary" href="inscription.php?entreprise=<?= (int) $existante['id'] ?>">Continuer mon inscription avec cette entreprise</a></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($entreprise !== null): ?>

        <div class="card">
            <h2><?= htmlspecialchars($entreprise->getNom()) ?></h2>
            <p><strong>Adresse :</strong> <?= htmlspecialchars($entreprise->getAdresse()) ?></p>
            <p>
                <strong>Site web :</strong>
                <a href="<?= htmlspecialchars($entreprise->getSiteWeb()) ?>" target="_blank" rel="noopener noreferrer">
                    <?= htmlspecialchars($entreprise->getSiteWeb()) ?>
                </a>
            </p>

            <div>
                <?php if ($estPartenaire && $monEntrepriseId === $entreprise->getIdEntreprise()): ?>
                    <span class="tag">Votre entreprise</span>
                <?php elseif ($estPartenaire): ?>
                    <form method="post" action="../src/traitement/traitement_entreprise.php">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                        <input type="hidden" name="action" value="selectionner">
                        <input type="hidden" name="ref_entreprise" value="<?= (int) $entreprise->getIdEntreprise() ?>">
                        <input type="hidden" name="retour" value="profil">
                        <button type="submit" class="btn">C'est mon entreprise</button>
                    </form>
                <?php elseif (!$connecte): ?>
                    <a class="btn" href="inscription.php?entreprise=<?= (int) $entreprise->getIdEntreprise() ?>">S'inscrire avec cette entreprise</a>
                    <a class="btn btn-secondary" href="connexion.php">Se connecter</a>
                <?php endif; ?>
                <a class="btn btn-secondary" href="entreprise.php">← Retour à la liste</a>
            </div>
        </div>

        <div class="card">
            <h2>Partenaires rattachés (<?= count($partenaires) ?>)</h2>
            <table class="table">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Poste</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($partenaires)): ?>
                    <?php foreach ($partenaires as $partenaire): ?>
                        <tr>
                            <td><?= htmlspecialchars($partenaire->getNom()) ?></td>
                            <td><?= htmlspecialchars($partenaire->getPrenom()) ?></td>
                            <td><?= htmlspecialchars($partenaire->getPoste()) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">Aucun partenaire rattaché à cette entreprise.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>

        <div class="card">
            <h2>Rechercher une entreprise</h2>
            <form method="get" action="entreprise.php">
                <div class="form-group">
                    <label for="recherche">Nom de l'entreprise</label>
                    <input type="text" id="recherche" name="recherche" class="form-control" maxlength="150"
                           value="<?= htmlspecialchars($recherche) ?>" placeholder="Ex : Medi">
                </div>
                <button type="submit" class="btn">Rechercher</button>
                <?php if ($recherche !== ''): ?>
                    <a class="btn btn-secondary" href="entreprise.php">Réinitialiser</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h2>
                <?= $recherche !== '' ? 'Résultats pour « ' . htmlspecialchars($recherche) . ' »' : 'Entreprises enregistrées' ?>
                (<?= count($entreprises) ?>)
            </h2>
            <table class="table">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Adresse</th>
                    <th>Site web</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($entreprises)): ?>
                    <?php foreach ($entreprises as $e): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($e->getNom()) ?></strong></td>
                            <td><?= htmlspecialchars($e->getAdresse()) ?></td>
                            <td>
                                <a href="<?= htmlspecialchars($e->getSiteWeb()) ?>" target="_blank" rel="noopener noreferrer">
                                    <?= htmlspecialchars($e->getSiteWeb()) ?>
                                </a>
                            </td>
                            <td>
                                <a class="btn btn-secondary" href="entreprise.php?id=<?= (int) $e->getIdEntreprise() ?>">Voir la fiche</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">
                            <?= $recherche !== ''
                                    ? 'Aucune entreprise ne correspond à « ' . htmlspecialchars($recherche) . ' ».'
                                    : 'Aucune entreprise enregistrée.' ?>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card" id="creation">
            <h2>Ajouter une entreprise</h2>
            <?php if (!$peutCreer): ?>
                <p>Seuls les partenaires peuvent ajouter une entreprise.</p>
            <?php else: ?>
                <?php if (!$connecte): ?>
                    <p>Vous n'êtes pas connecté : créez l'entreprise puis poursuivez votre
                        <a href="inscription.php">inscription</a>, ou <a href="connexion.php">connectez-vous</a>.</p>
                <?php endif; ?>
                <form method="post" action="../src/traitement/traitement_entreprise.php">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                    <input type="hidden" name="action" value="creer">
                    <div class="form-group">
                        <label for="nom_entreprise">Nom</label>
                        <input type="text" id="nom_entreprise" name="nom_entreprise" class="form-control" required maxlength="150"
                               value="<?= htmlspecialchars($nomSaisi) ?>">
                    </div>
                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <input type="text" id="adresse" name="adresse" class="form-control" required maxlength="255"
                               value="<?= htmlspecialchars($adresseSaisie) ?>">
                    </div>
                    <div class="form-group">
                        <label for="site_web">Site web</label>
                        <input type="url" id="site_web" name="site_web" class="form-control" required maxlength="255"
                               placeholder="https://www.entreprise.fr"
                               value="<?= htmlspecialchars($siteWebSaisi) ?>">
                    </div>
                    <button type="submit" class="btn">➕ Ajouter</button>
                </form>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</main>

</body>
</html>