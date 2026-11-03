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

    <style>
        :root {
            --primary: #126779;
            --primary-hover: #0f5766;
            --primary-pressed: #0b434f;
            --navy: #102f40;
            --accent: #62c8ba;
            --focus: #258cba;
            --surface: #ffffff;
            --surface-soft: #f8fafb;
            --surface-teal: #f0f7f7;
            --surface-disabled: #e2e8f0;
            --text: #1e293b;
            --text-muted: #64748b;
            --text-disabled: #94a3b8;
            --text-on-navy: #e2e8f0;
            --text-on-navy-muted: #94a3b8;
            --info: #1d4ed8;
            --info-bg: #eff6ff;
            --success: #15803d;
            --success-bg: #f0fdf4;
            --warning: #b45309;
            --warning-bg: #fffbeb;
            --danger: #b91c1c;
            --danger-bg: #fef2f2;
            --role-partenaire-bg: #f3e8ff;
            --role-partenaire-text: #6b21a8;
            --border: #e2e8f0;
            --border-strong: #cbd5e1;
            --border-accent: #62c8ba;
            --radius-md: 8px;
            --radius-lg: 16px;
            --transition: 0.2s ease-in-out;
            --space-xs: 4px;
            --space-sm: 8px;
            --space-ms: 12px;
            --space-md: 16px;
            --space-lg: 24px;
            --space-xl: 32px;
            --shadow: 0 10px 25px -5px rgba(16, 47, 64, 0.1);
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background-color: var(--surface-soft);
            color: var(--text);
        }

        .page-header h1 {
            color: var(--navy);
            font-size: 24px;
            margin-bottom: 20px;
            letter-spacing: -0.02em;
        }

        /* GHD-HSP Composants */
        .card {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 24px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 3px 12px rgba(16, 47, 64, 0.025);
            margin-bottom: 24px;
        }

        .card-header {
            color: var(--navy);
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        p {
            font-size: 14px;
            line-height: 1.5;
            color: var(--text-muted);
            margin: 0;
        }

        .form-row {
            display: flex;
            gap: var(--space-md);
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
            min-width: 220px;
        }

        .form-group label {
            color: var(--text);
            font-size: 13px;
            font-weight: 600;
        }

        .form-control, input[type="text"], input[type="url"] {
            display: block;
            width: 100%;
            min-height: 46px;
            padding: 11px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--surface);
            color: var(--text);
            font: inherit;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .form-control:hover, input[type="text"]:hover, input[type="url"]:hover {
            border-color: var(--border-strong);
        }

        .form-control:focus, input[type="text"]:focus, input[type="url"]:focus {
            outline: none;
            border-color: var(--focus);
            box-shadow: 0 0 0 4px rgba(37, 140, 186, 0.11);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 44px;
            padding: 11px 18px;
            border: 1px solid var(--primary);
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #147786, var(--primary));
            box-shadow: 0 3px 8px rgba(18, 103, 121, 0.10);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background var(--transition), border-color var(--transition), box-shadow var(--transition), transform var(--transition);
        }

        .btn:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-ghost {
            background: var(--surface);
            border-color: var(--border);
            box-shadow: none;
            color: var(--primary);
        }

        .btn-ghost:hover {
            background: var(--surface-teal);
            border-color: var(--border-accent);
            color: var(--primary);
        }

        .alert {
            padding: 15px 17px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.5;
        }

        .alert-success { background: var(--success-bg); border: 1px solid #d7eddf; color: var(--success); }
        .alert-error { background: var(--danger-bg); border: 1px solid #f3d8dc; color: var(--danger); }
        .alert-info { background: var(--info-bg); border: 1px solid #d9e9f8; color: var(--info); }

        .badge-actif {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 7px;
            background: var(--success-bg);
            color: var(--success);
            font-size: 11px;
            font-weight: 700;
        }

        /* Tableau GHD-HSP */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            margin-top: 10px;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            vertical-align: middle;
            border-bottom: 1px solid #edf1f3;
        }

        th {
            background: var(--surface-soft);
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        td {
            color: var(--text-muted);
            font-size: 13px;
        }

        tbody tr:hover {
            background: #f5fafb;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .empty-msg {
            text-align: center;
            padding: 30px;
            color: var(--text-muted);
        }

        .actions {
            display: flex;
            gap: var(--space-sm);
            align-items: center;
            flex-wrap: wrap;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<main class="content">

    <div class="page-header">
        <h1>Entreprises partenaires</h1>
    </div>

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
        <div class="alert alert-info">
            <p>
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
                <p style="margin-top: 10px;"><a class="btn btn-ghost" href="inscription.php?entreprise=<?= (int) $existante['id'] ?>">Continuer mon inscription avec cette entreprise</a></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($entreprise !== null): ?>

        <!-- Fiche détaillée -->
        <div class="card">
            <div class="card-header"><?= htmlspecialchars($entreprise->getNom()) ?></div>
            <div class="card-body">
                <p><strong>Adresse :</strong> <?= htmlspecialchars($entreprise->getAdresse()) ?></p>
                <p>
                    <strong>Site web :</strong>
                    <a href="<?= htmlspecialchars($entreprise->getSiteWeb()) ?>" target="_blank" rel="noopener noreferrer" style="color: var(--primary);">
                        <?= htmlspecialchars($entreprise->getSiteWeb()) ?>
                    </a>
                </p>

                <div class="actions">
                    <?php if ($estPartenaire && $monEntrepriseId === $entreprise->getIdEntreprise()): ?>
                        <span class="badge-actif">Votre entreprise</span>
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
                        <a class="btn btn-ghost" href="connexion.php">Se connecter</a>
                    <?php endif; ?>
                    <a class="btn btn-ghost" href="entreprise.php">← Retour à la liste</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Partenaires rattachés (<?= count($partenaires) ?>)</div>
            <table>
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
                        <td colspan="3" class="empty-msg">Aucun partenaire rattaché à cette entreprise.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>

        <!-- Recherche par nom -->
        <div class="card">
            <div class="card-header">Rechercher une entreprise</div>
            <div class="card-body">
                <form method="get" action="entreprise.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="recherche">Nom de l'entreprise</label>
                            <input type="text" id="recherche" name="recherche" maxlength="150"
                                   value="<?= htmlspecialchars($recherche) ?>" placeholder="Ex : Medi">
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="submit" class="btn">Rechercher</button>
                            <?php if ($recherche !== ''): ?>
                                <a class="btn btn-ghost" href="entreprise.php">Réinitialiser</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste -->
        <div class="card">
            <div class="card-header">
                <?= $recherche !== '' ? 'Résultats pour « ' . htmlspecialchars($recherche) . ' »' : 'Entreprises enregistrées' ?>
                (<?= count($entreprises) ?>)
            </div>
            <table>
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
                                <a href="<?= htmlspecialchars($e->getSiteWeb()) ?>" target="_blank" rel="noopener noreferrer" style="color: var(--primary);">
                                    <?= htmlspecialchars($e->getSiteWeb()) ?>
                                </a>
                            </td>
                            <td>
                                <a class="btn btn-ghost" href="entreprise.php?id=<?= (int) $e->getIdEntreprise() ?>" style="padding: 6px 12px; min-height: 34px;">Voir la fiche</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty-msg">
                            <?= $recherche !== ''
                                    ? 'Aucune entreprise ne correspond à « ' . htmlspecialchars($recherche) . ' ».'
                                    : 'Aucune entreprise enregistrée.' ?>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Création -->
        <div class="card" id="creation">
            <div class="card-header">Ajouter une entreprise</div>
            <div class="card-body">
                <?php if (!$peutCreer): ?>
                    <p class="empty-msg">Seuls les partenaires peuvent ajouter une entreprise.</p>
                <?php else: ?>
                    <?php if (!$connecte): ?>
                        <p>Vous n'êtes pas connecté : créez l'entreprise puis poursuivez votre
                            <a href="inscription.php" style="color: var(--primary);">inscription</a>, ou <a href="connexion.php" style="color: var(--primary);">connectez-vous</a>.</p>
                    <?php endif; ?>
                    <form method="post" action="../src/traitement/traitement_entreprise.php">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                        <input type="hidden" name="action" value="creer">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom_entreprise">Nom</label>
                                <input type="text" id="nom_entreprise" name="nom_entreprise" required maxlength="150"
                                       value="<?= htmlspecialchars($nomSaisi) ?>" placeholder="Nom de l'entreprise">
                            </div>
                            <div class="form-group">
                                <label for="adresse">Adresse</label>
                                <input type="text" id="adresse" name="adresse" required maxlength="255"
                                       value="<?= htmlspecialchars($adresseSaisie) ?>" placeholder="Adresse complète">
                            </div>
                            <div class="form-group">
                                <label for="site_web">Site web</label>
                                <input type="url" id="site_web" name="site_web" required maxlength="255"
                                       placeholder="https://www.entreprise.fr"
                                       value="<?= htmlspecialchars($siteWebSaisi) ?>">
                            </div>
                            <div>
                                <button type="submit" class="btn">➕ Ajouter</button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

    <?php endif; ?>

</main>

</body>
</html>