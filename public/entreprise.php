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
    <title>Entreprises – GHD-HSP</title>
    <link rel="stylesheet" href="variables.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
</head>
<body>

<main class="container" style="max-width: 1000px;">

    <div class="page-header">
        <h1 class="page-title">Entreprises partenaires</h1>
    </div>

    <?php if (!empty($succes)): ?>
        <div class="alert alert-success">
            <div class="alert-body">
                <?php foreach ($succes as $msg): ?>
                    <p class="alert-text"><?= htmlspecialchars($msg) ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($erreurs)): ?>
        <div class="alert alert-error">
            <div class="alert-body">
                <?php foreach ($erreurs as $msg): ?>
                    <p class="alert-text"><?= htmlspecialchars($msg) ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($existante !== null): ?>
        <div class="alert alert-info">
            <div class="alert-body">
                <p class="alert-text">
                    Entreprise déjà enregistrée :
                    <a href="entreprise.php?id=<?= (int) $existante['id'] ?>" style="font-weight: bold; color: inherit; text-decoration: underline;"><?= htmlspecialchars($existante['nom']) ?></a>
                </p>
                <?php if ($estPartenaire): ?>
                    <form method="post" action="../src/traitement/traitement_entreprise.php" style="margin-top: 10px;">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                        <input type="hidden" name="action" value="selectionner">
                        <input type="hidden" name="ref_entreprise" value="<?= (int) $existante['id'] ?>">
                        <button type="submit" class="btn">Sélectionner cette entreprise</button>
                    </form>
                <?php elseif (!$connecte): ?>
                    <p class="alert-text" style="margin-top: 10px;"><a class="btn btn-secondary" href="inscription.php?entreprise=<?= (int) $existante['id'] ?>">Continuer mon inscription avec cette entreprise</a></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($entreprise !== null): ?>

        <!-- Fiche détaillée -->
        <div class="card">
            <h2 class="card-title"><?= htmlspecialchars($entreprise->getNom()) ?></h2>
            <div style="display: flex; flex-direction: column; gap: var(--space-ms);">
                <p class="lead"><strong>Adresse :</strong> <?= htmlspecialchars($entreprise->getAdresse()) ?></p>
                <p class="lead">
                    <strong>Site web :</strong>
                    <a href="<?= htmlspecialchars($entreprise->getSiteWeb()) ?>" target="_blank" rel="noopener noreferrer">
                        <?= htmlspecialchars($entreprise->getSiteWeb()) ?>
                    </a>
                </p>

                <div class="cluster" style="margin-top: 10px;">
                    <?php if ($estPartenaire && $monEntrepriseId === $entreprise->getIdEntreprise()): ?>
                        <span class="tag tag--success">Votre entreprise</span>
                    <?php elseif ($estPartenaire): ?>
                        <form method="post" action="../src/traitement/traitement_entreprise.php" style="margin: 0;">
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
        </div>

        <div class="card">
            <h2 class="card-title">Partenaires rattachés (<?= count($partenaires) ?>)</h2>
            <div class="table-wrap">
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
                            <td colspan="3" style="text-align: center; padding: 30px;">Aucun partenaire rattaché à cette entreprise.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php else: ?>

        <!-- Recherche par nom -->
        <div class="card">
            <h2 class="card-title">Rechercher une entreprise</h2>
            <form method="get" action="entreprise.php">
                <div class="cluster" style="align-items: flex-end;">
                    <div class="form-group" style="flex: 1; min-width: 220px;">
                        <label for="recherche">Nom de l'entreprise</label>
                        <input type="text" id="recherche" name="recherche" class="form-control" maxlength="150"
                               value="<?= htmlspecialchars($recherche) ?>" placeholder="Ex : Medi">
                    </div>
                    <div class="cluster">
                        <button type="submit" class="btn">Rechercher</button>
                        <?php if ($recherche !== ''): ?>
                            <a class="btn btn-secondary" href="entreprise.php">Réinitialiser</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Liste -->
        <div class="card">
            <h2 class="card-title">
                <?= $recherche !== '' ? 'Résultats pour « ' . htmlspecialchars($recherche) . ' »' : 'Entreprises enregistrées' ?>
                (<?= count($entreprises) ?>)
            </h2>
            <div class="table-wrap">
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
                                    <a class="btn btn-secondary" href="entreprise.php?id=<?= (int) $e->getIdEntreprise() ?>" style="padding: 6px 12px; min-height: 34px; font-size: 12px;">Voir la fiche</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px;">
                                <?= $recherche !== ''
                                        ? 'Aucune entreprise ne correspond à « ' . htmlspecialchars($recherche) . ' ».'
                                        : 'Aucune entreprise enregistrée.' ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Création -->
        <div class="card" id="creation">
            <h2 class="card-title">Ajouter une entreprise</h2>
            <?php if (!$peutCreer): ?>
                <p class="lead" style="text-align: center;">Seuls les partenaires peuvent ajouter une entreprise.</p>
            <?php else: ?>
                <?php if (!$connecte): ?>
                    <p class="lead">Vous n'êtes pas connecté : créez l'entreprise puis poursuivez votre
                        <a href="inscription.php">inscription</a>, ou <a href="connexion.php">connectez-vous</a>.</p>
                <?php endif; ?>
                <form method="post" action="../src/traitement/traitement_entreprise.php" style="display: flex; flex-direction: column; gap: var(--space-md);">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                    <input type="hidden" name="action" value="creer">

                    <div class="grid-2">
                        <div class="form-group">
                            <label for="nom_entreprise">Nom</label>
                            <input type="text" id="nom_entreprise" name="nom_entreprise" class="form-control" required maxlength="150"
                                   value="<?= htmlspecialchars($nomSaisi) ?>" placeholder="Nom de l'entreprise">
                        </div>
                        <div class="form-group">
                            <label for="adresse">Adresse</label>
                            <input type="text" id="adresse" name="adresse" class="form-control" required maxlength="255"
                                   value="<?= htmlspecialchars($adresseSaisie) ?>" placeholder="Adresse complète">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="site_web">Site web</label>
                        <input type="url" id="site_web" name="site_web" class="form-control" required maxlength="255"
                               placeholder="https://www.entreprise.fr"
                               value="<?= htmlspecialchars($siteWebSaisi) ?>">
                    </div>

                    <div>
                        <button type="submit" class="btn">➕ Ajouter l'entreprise</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</main>

</body>
</html>