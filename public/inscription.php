<?php
// Vue du formulaire : toutes les créations sont réalisées par le traitement dédié.
session_start();
require_once __DIR__ . '/../src/repository/EtablissementRepository.php';
require_once __DIR__ . '/../src/repository/EntrepriseRepository.php';

$message = $_SESSION['inscription_message'] ?? '';
$messageType = $_SESSION['inscription_message_type'] ?? '';
$valeurs = $_SESSION['inscription_valeurs'] ?? [];
unset($_SESSION['inscription_message'], $_SESSION['inscription_message_type'], $_SESSION['inscription_valeurs']);
$nom = $valeurs['nom'] ?? '';
$prenom = $valeurs['prenom'] ?? '';
$email = $valeurs['email'] ?? '';
$role = $valeurs['role'] ?? '';
if (!isset($_SESSION['csrf_inscription'])) {
    $_SESSION['csrf_inscription'] = bin2hex(random_bytes(32));
}
$etablissements = [];
$entreprises = [];
try {
    $etablissements = (new EtablissementRepository())->findAll();
    $entreprises = (new EntrepriseRepository())->findAll();
} catch (PDOException $exception) {
    $message = 'Impossible de charger les établissements et entreprises. Veuillez réessayer.';
    $messageType = 'error';
}
?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Inscription - GDH</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            font-family: Arial, sans-serif;

            background-color: #f4f4f4;
        }

        .formulaire {
            width: 100%;
            max-width: 450px;

            padding: 30px;

            background-color: white;

            border-radius: 10px;

            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-top: 0;
            margin-bottom: 25px;

            text-align: center;
        }

        .champ {
            margin-bottom: 18px;
        }

        label {
            display: block;

            margin-bottom: 6px;

            font-weight: bold;
        }

        input,
        select {
            width: 100%;

            padding: 11px;

            border: 1px solid #ccc;
            border-radius: 5px;

            font-size: 15px;
        }

        input:focus,
        select:focus {
            outline: none;

            border-color: #2563eb;
        }

        button {
            width: 100%;

            padding: 12px;

            margin-top: 5px;

            border: none;
            border-radius: 5px;

            background-color: #2563eb;

            color: white;

            font-size: 16px;
            font-weight: bold;

            cursor: pointer;
        }

        button:hover {
            background-color: #1d4ed8;
        }

        .message {
            margin-bottom: 20px;

            padding: 12px;

            border-radius: 5px;

            text-align: center;
        }

        .message.error {
            background-color: #fee2e2;

            color: #991b1b;
        }

        .message.success {
            background-color: #dcfce7;

            color: #166534;
        }

    </style>

</head>

<body>

<main class="formulaire">

    <h1>Créer un compte</h1>

    <?php if ($message !== ''): ?>

        <div class="message <?= htmlspecialchars($messageType) ?>">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>


    <form method="POST" action="/GHD/src/traitement/inscription_traitement.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_inscription'], ENT_QUOTES, 'UTF-8') ?>">

        <!-- Nom -->
        <div class="champ">

            <label for="nom">
                Nom
            </label>

            <input
                type="text"
                id="nom"
                name="nom"
                maxlength="100"
                required
                value="<?= htmlspecialchars($nom ?? '') ?>"
            >

        </div>


        <!-- Prénom -->
        <div class="champ">

            <label for="prenom">
                Prénom
            </label>

            <input
                type="text"
                id="prenom"
                name="prenom"
                maxlength="100"
                required
                value="<?= htmlspecialchars($prenom ?? '') ?>"
            >

        </div>


        <!-- Email -->
        <div class="champ">

            <label for="email">
                Adresse email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                maxlength="150"
                required
                value="<?= htmlspecialchars($email ?? '') ?>"
            >

        </div>


        <!-- Mot de passe -->
        <div class="champ">

            <label for="mot_de_passe">
                Mot de passe
            </label>

            <input
                type="password"
                id="mot_de_passe"
                name="mot_de_passe"
                minlength="8"
                required
            >

        </div>


        <!-- Confirmation -->
        <div class="champ">

            <label for="confirmation">
                Confirmer le mot de passe
            </label>

            <input
                type="password"
                id="confirmation"
                name="confirmation"
                minlength="8"
                required
            >

        </div>


        <!-- Rôle -->
        <div class="champ">

            <label for="role">
                Rôle
            </label>

            <select
                id="role"
                name="role"
                required
            >

                <option value="">
                    -- Choisir un rôle --
                </option>

                <option value="etudiant" <?= $role === 'etudiant' ? 'selected' : '' ?>>
                    Étudiant
                </option>

                <option value="medecin" <?= $role === 'medecin' ? 'selected' : '' ?>>
                    Médecin
                </option>

                <option value="partenaire" <?= $role === 'partenaire' ? 'selected' : '' ?>>
                    Partenaire
                </option>

            </select>

        </div>


        <div data-role="etudiant" hidden>
            <div class="champ">
                <label for="formation">Formation</label>
                <input type="text" id="formation" name="formation" maxlength="150"
                       value="<?= htmlspecialchars($valeurs['formation'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled>
            </div>
            <div class="champ">
                <label for="ref_etablissement">Établissement</label>
                <select id="ref_etablissement" name="ref_etablissement" disabled>
                    <option value="">-- Choisir un établissement --</option>
                    <?php foreach ($etablissements as $etablissement): ?>
                        <option value="<?= (int) $etablissement->getIdEtablissement() ?>"
                            <?= (string) ($valeurs['ref_etablissement'] ?? '') === (string) $etablissement->getIdEtablissement() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($etablissement->getNom(), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($etablissements)): ?>
                    <p>Aucun établissement disponible : l'inscription étudiante nécessite un établissement existant.</p>
                <?php endif; ?>
            </div>
        </div>

        <div data-role="medecin" hidden>
            <div class="champ">
                <label for="numero_rpps">Numéro RPPS</label>
                <input type="text" id="numero_rpps" name="numero_rpps" maxlength="20"
                       value="<?= htmlspecialchars($valeurs['numero_rpps'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled>
            </div>
        </div>

        <div data-role="partenaire" hidden>
            <div class="champ">
                <label for="poste">Poste</label>
                <input type="text" id="poste" name="poste" maxlength="100"
                       value="<?= htmlspecialchars($valeurs['poste'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled>
            </div>
            <div class="champ">
                <label for="ref_entreprise">Entreprise</label>
                <select id="ref_entreprise" name="ref_entreprise" disabled>
                    <option value="">-- Choisir une entreprise --</option>
                    <?php foreach ($entreprises as $entreprise): ?>
                        <option value="<?= (int) $entreprise->getIdEntreprise() ?>"
                            <?= (string) ($valeurs['ref_entreprise'] ?? '') === (string) $entreprise->getIdEntreprise() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($entreprise->getNom(), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($entreprises)): ?>
                    <p>Aucune entreprise disponible : l'inscription partenaire nécessite une entreprise existante.</p>
                <?php endif; ?>
            </div>
        </div>
        <noscript>JavaScript est nécessaire pour afficher les champs du rôle sélectionné.</noscript>

        <button type="submit">
            S'inscrire
        </button>

    </form>

</main>

<script>
    const selectionRole = document.getElementById('role');
    function afficherChampsRole() {
        document.querySelectorAll('[data-role]').forEach(function (bloc) {
            const actif = bloc.dataset.role === selectionRole.value;
            bloc.hidden = !actif;
            bloc.querySelectorAll('input, select').forEach(function (champ) {
                champ.disabled = !actif;
                champ.required = actif;
            });
        });
    }
    selectionRole.addEventListener('change', afficherChampsRole);
    afficherChampsRole();
</script>

</body>

</html>
