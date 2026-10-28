<?php

session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/EtablissementRepository.php';

$repo = new EtablissementRepository();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$peutCreer = isAdmin();

$erreurs = $_SESSION['erreurs_etablissement'] ?? [];
$succes = $_SESSION['succes_etablissement'] ?? '';
$ancien = $_SESSION['ancien_etablissement'] ?? [];

unset(
        $_SESSION['erreurs_etablissement'],
        $_SESSION['succes_etablissement'],
        $_SESSION['ancien_etablissement']
);

// Liste ou fiche détaillée
$recherche = is_string($_GET['q'] ?? null)
        ? trim($_GET['q'])
        : '';
$etablissement = null;
$etablissements = [];

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id !== false && $id > 0) {
        $etablissement = $repo->findById($id);
    }

    if ($etablissement === null) {
        http_response_code(404);
        $erreurs[] = "Établissement introuvable.";
    }
} else {
    $etablissements = ($recherche !== '')
            ? $repo->rechercherParNom($recherche)
            : $repo->findAll();
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Établissements - GHD</title>
</head>

<body>

<main>

    <h1>Établissements d'enseignement</h1>

    <?php foreach ($erreurs as $erreur): ?>
        <p style="color: red;">
            <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endforeach; ?>

    <?php if ($succes !== ''): ?>
        <p style="color: green;">
            <?= htmlspecialchars($succes, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($etablissement !== null): ?>

        <h2>
            <?= htmlspecialchars($etablissement->getNom(), ENT_QUOTES, 'UTF-8') ?>
        </h2>

        <p>
            <strong>Adresse :</strong>
            <?= htmlspecialchars($etablissement->getAdresse(), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p>
            <strong>Site web :</strong>
            <a
                    href="<?= htmlspecialchars($etablissement->getSiteWeb(), ENT_QUOTES, 'UTF-8') ?>"
                    target="_blank"
                    rel="noopener noreferrer"
            >
                <?= htmlspecialchars($etablissement->getSiteWeb(), ENT_QUOTES, 'UTF-8') ?>
            </a>
        </p>

        <a href="etablissement.php">Retour à la liste</a>


        <?php if ($peutCreer): ?>

            <?php if (!isset($_GET['modifier'])): ?>

                <p>
                    <a href="etablissement.php?id=<?= (int) $etablissement->getIdEtablissement() ?>&modifier=1">
                        Modifier l'établissement
                    </a>
                </p>

            <?php else: ?>

                <h2>Modifier l'établissement</h2>

                <form method="post" action="../src/traitement/traitement_etablissement.php">

                    <input type="hidden" name="csrf"
                           value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">

                    <input type="hidden" name="action" value="modifier">

                    <input type="hidden" name="id_etablissement"
                           value="<?= (int) $etablissement->getIdEtablissement() ?>">

                    <p>
                        <label for="nom">Nom de l'établissement</label>
                        <input type="text" id="nom" name="nom_etablissement"
                               maxlength="150" required
                               value="<?= htmlspecialchars($ancien['nom_etablissement'] ?? $etablissement->getNom(), ENT_QUOTES, 'UTF-8') ?>">
                    </p>

                    <p>
                        <label for="adresse">Adresse</label>
                        <input type="text" id="adresse" name="adresse"
                               maxlength="255" required
                               value="<?= htmlspecialchars($ancien['adresse'] ?? $etablissement->getAdresse(), ENT_QUOTES, 'UTF-8') ?>">
                    </p>

                    <p>
                        <label for="site_web">Site web</label>
                        <input type="url" id="site_web" name="site_web"
                               maxlength="255" required
                               value="<?= htmlspecialchars($ancien['site_web'] ?? $etablissement->getSiteWeb(), ENT_QUOTES, 'UTF-8') ?>">
                    </p>

                    <button type="submit">Enregistrer les modifications</button>

                    <a href="etablissement.php?id=<?= (int) $etablissement->getIdEtablissement() ?>">
                        Annuler
                    </a>

                </form>

            <?php endif; ?>

        <?php endif; ?>


    <?php elseif (!isset($_GET['id'])): ?>

        <h2>Rechercher un établissement</h2>

        <form method="get" action="etablissement.php">
            <label for="recherche">Nom</label>

            <input
                    type="text"
                    id="recherche"
                    name="q"
                    value="<?= htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Rechercher un établissement"
            >

            <button type="submit">Rechercher</button>
            <a href="etablissement.php">Réinitialiser</a>
        </form>

        <h2>Liste des établissements</h2>

        <?php if (empty($etablissements)): ?>

            <p>Aucun établissement trouvé.</p>

        <?php else: ?>

            <table border="1" cellpadding="8">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Adresse</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($etablissements as $e): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($e->getNom(), ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($e->getAdresse(), ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <a href="etablissement.php?id=<?= (int) $e->getIdEtablissement() ?>">
                                Voir la fiche
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>

    <?php endif; ?>

    <?php if ($peutCreer && !isset($_GET['id'])): ?>

        <h2>Ajouter un établissement</h2>

        <form
                method="post"
                action="../src/traitement/traitement_etablissement.php"
        >
            <input
                    type="hidden"
                    name="csrf"
                    value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>"
            >
            <p>
                <label for="nom">Nom de l'établissement</label>
                <input
                        type="text"
                        id="nom"
                        name="nom_etablissement"
                        maxlength="150"
                        required
                        value="<?= htmlspecialchars($ancien['nom_etablissement'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >
            </p>

            <p>
                <label for="adresse">Adresse</label>
                <input
                        type="text"
                        id="adresse"
                        name="adresse"
                        maxlength="255"
                        required
                        value="<?= htmlspecialchars($ancien['adresse'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >
            </p>

            <p>
                <label for="site_web">Site web</label>
                <input
                        type="url"
                        id="site_web"
                        name="site_web"
                        maxlength="255"
                        required
                        placeholder="https://www.exemple.fr"
                        value="<?= htmlspecialchars($ancien['site_web'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >
            </p>

            <button type="submit">Ajouter l'établissement</button>

        </form>

    <?php endif; ?>

</main>

</body>
</html>
