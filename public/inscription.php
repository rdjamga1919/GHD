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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - GHD-HSP</title>

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
            --info: #1d4ed8;
            --info-bg: #eff6ff;
            --success: #15803d;
            --success-bg: #f0fdf4;
            --warning: #b45309;
            --warning-bg: #fffbeb;
            --danger: #b91c1c;
            --danger-bg: #fef2f2;
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

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: system-ui, -apple-system, sans-serif;
            background-color: var(--surface-soft);
            color: var(--text);
            padding: 20px;
        }

        /* Composants GHD-HSP */
        .formulaire {
            width: 100%;
            max-width: 480px;
            padding: 32px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 3px 12px rgba(16, 47, 64, 0.025);
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .formulaire:hover {
            border-color: var(--border-strong);
            box-shadow: var(--shadow);
        }

        h1 {
            margin-top: 0;
            margin-bottom: 24px;
            text-align: center;
            color: var(--navy);
            font-size: 24px;
            letter-spacing: -0.02em;
        }

        .champ {
            margin-bottom: 18px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            color: var(--text);
            font-size: 13px;
            line-height: 21px;
            font-weight: 600;
        }

        .form-control, input[type="text"], input[type="email"], input[type="password"], select {
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

        .form-control:hover, input:hover, select:hover {
            border-color: var(--border-strong);
        }

        .form-control:focus, input:focus, select:focus {
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
            cursor: pointer;
            width: 100%;
            margin-top: 8px;
            transition: background var(--transition), border-color var(--transition), box-shadow var(--transition), transform var(--transition);
        }

        .btn:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .alert {
            padding: 15px 17px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.5;
            text-align: center;
        }

        .alert-error, .message.error {
            background: var(--danger-bg);
            border: 1px solid #f3d8dc;
            color: var(--danger);
        }

        .alert-success, .message.success {
            background: var(--success-bg);
            border: 1px solid #d7eddf;
            color: var(--success);
        }

        p {
            font-size: 12px;
            color: var(--text-muted);
            margin: 4px 0 0 0;
        }

        noscript {
            display: block;
            margin-bottom: 15px;
            font-size: 12px;
            color: var(--warning);
            text-align: center;
        }
    </style>
</head>
<body>

<main class="formulaire">

    <h1>Créer un compte</h1>

    <?php if ($message !== ''): ?>
        <div class="alert <?= htmlspecialchars($messageType) === 'error' ? 'alert-error' : 'alert-success' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/GHD/src/traitement/inscription_traitement.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_inscription'], ENT_QUOTES, 'UTF-8') ?>">

        <!-- Nom -->
        <div class="champ">
            <label for="nom">Nom</label>
            <input
                    type="text"
                    id="nom"
                    name="nom"
                    maxlength="100"
                    required
                    placeholder="Votre nom"
                    value="<?= htmlspecialchars($nom ?? '', ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <!-- Prénom -->
        <div class="champ">
            <label for="prenom">Prénom</label>
            <input
                    type="text"
                    id="prenom"
                    name="prenom"
                    maxlength="100"
                    required
                    placeholder="Votre prénom"
                    value="<?= htmlspecialchars($prenom ?? '', ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <!-- Email -->
        <div class="champ">
            <label for="email">Adresse email</label>
            <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="150"
                    required
                    placeholder="votre@email.com"
                    value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <!-- Mot de passe -->
        <div class="champ">
            <label for="mot_de_passe">Mot de passe</label>
            <input
                    type="password"
                    id="mot_de_passe"
                    name="mot_de_passe"
                    minlength="8"
                    required
                    placeholder="••••••••"
            >
        </div>

        <!-- Confirmation -->
        <div class="champ">
            <label for="confirmation">Confirmer le mot de passe</label>
            <input
                    type="password"
                    id="confirmation"
                    name="confirmation"
                    minlength="8"
                    required
                    placeholder="••••••••"
            >
        </div>

        <!-- Rôle -->
        <div class="champ">
            <label for="role">Rôle</label>
            <select id="role" name="role" required>
                <option value="">-- Choisir un rôle --</option>
                <option value="etudiant" <?= $role === 'etudiant' ? 'selected' : '' ?>>Étudiant</option>
                <option value="medecin" <?= $role === 'medecin' ? 'selected' : '' ?>>Médecin</option>
                <option value="partenaire" <?= $role === 'partenaire' ? 'selected' : '' ?>>Partenaire</option>
            </select>
        </div>

        <div data-role="etudiant" hidden>
            <div class="champ">
                <label for="formation">Formation</label>
                <input type="text" id="formation" name="formation" maxlength="150"
                       placeholder="Intitulé de votre formation"
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
                       placeholder="Votre numéro RPPS"
                       value="<?= htmlspecialchars($valeurs['numero_rpps'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled>
            </div>
        </div>

        <div data-role="partenaire" hidden>
            <div class="champ">
                <label for="poste">Poste</label>
                <input type="text" id="poste" name="poste" maxlength="100"
                       placeholder="Votre poste au sein de l'entreprise"
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

        <button type="submit" class="btn">S'inscrire</button>

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
    afficherChsmpsRole = afficherChampsRole(); // safety execution
    afficherChampsRole();
</script>

</body>
</html>