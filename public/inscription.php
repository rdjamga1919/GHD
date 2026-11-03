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
    <title>Inscription – GHD-HSP</title>
    <link rel="stylesheet" href="variables.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
</head>
<body style="display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px;">

<main class="card" style="width: 100%; max-width: 480px; padding: 32px;">

    <div class="page-header" style="text-align: center; margin-bottom: 24px;">
        <h1 class="page-title" style="font-size: 24px;">Créer un compte</h1>
    </div>

    <?php if ($message !== ''): ?>
        <div class="alert alert-<?= htmlspecialchars($messageType) === 'error' ? 'error' : 'success' ?>">
            <div class="alert-body">
                <span class="alert-text"><?= htmlspecialchars($message) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="/GHD/src/traitement/inscription_traitement.php" style="display: flex; flex-direction: column; gap: var(--space-md);">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_inscription'], ENT_QUOTES, 'UTF-8') ?>">

        <!-- Nom -->
        <div class="form-group">
            <label for="nom">Nom</label>
            <input
                    type="text"
                    id="nom"
                    name="nom"
                    class="form-control"
                    maxlength="100"
                    required
                    placeholder="Votre nom"
                    value="<?= htmlspecialchars($nom ?? '', ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <!-- Prénom -->
        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input
                    type="text"
                    id="prenom"
                    name="prenom"
                    class="form-control"
                    maxlength="100"
                    required
                    placeholder="Votre prénom"
                    value="<?= htmlspecialchars($prenom ?? '', ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Adresse email</label>
            <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    maxlength="150"
                    required
                    placeholder="votre@email.com"
                    value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <!-- Mot de passe -->
        <div class="form-group">
            <label for="mot_de_passe">Mot de passe</label>
            <input
                    type="password"
                    id="mot_de_passe"
                    name="mot_de_passe"
                    class="form-control"
                    minlength="8"
                    required
                    placeholder="••••••••"
            >
        </div>

        <!-- Confirmation -->
        <div class="form-group">
            <label for="confirmation">Confirmer le mot de passe</label>
            <input
                    type="password"
                    id="confirmation"
                    name="confirmation"
                    class="form-control"
                    minlength="8"
                    required
                    placeholder="••••••••"
            >
        </div>

        <!-- Rôle -->
        <div class="form-group">
            <label for="role">Rôle</label>
            <select id="role" name="role" class="form-control" required>
                <option value="">-- Choisir un rôle --</option>
                <option value="etudiant" <?= $role === 'etudiant' ? 'selected' : '' ?>>Étudiant</option>
                <option value="medecin" <?= $role === 'medecin' ? 'selected' : '' ?>>Médecin</option>
                <option value="partenaire" <?= $role === 'partenaire' ? 'selected' : '' ?>>Partenaire</option>
            </select>
        </div>

        <div data-role="etudiant" hidden style="display: flex; flex-direction: column; gap: var(--space-md);">
            <div class="form-group">
                <label for="formation">Formation</label>
                <input type="text" id="formation" name="formation" class="form-control" maxlength="150"
                       placeholder="Intitulé de votre formation"
                       value="<?= htmlspecialchars($valeurs['formation'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled>
            </div>
            <div class="form-group">
                <label for="ref_etablissement">Établissement</label>
                <select id="ref_etablissement" name="ref_etablissement" class="form-control" disabled>
                    <option value="">-- Choisir un établissement --</option>
                    <?php foreach ($etablissements as $etablissement): ?>
                        <option value="<?= (int) $etablissement->getIdEtablissement() ?>"
                                <?= (string) ($valeurs['ref_etablissement'] ?? '') === (string) $etablissement->getIdEtablissement() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($etablissement->getNom(), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($etablissements)): ?>
                    <p class="caption">Aucun établissement disponible : l'inscription étudiante nécessite un établissement existant.</p>
                <?php endif; ?>
            </div>
        </div>

        <div data-role="medecin" hidden style="display: flex; flex-direction: column; gap: var(--space-md);">
            <div class="form-group">
                <label for="numero_rpps">Numéro RPPS</label>
                <input type="text" id="numero_rpps" name="numero_rpps" class="form-control" maxlength="20"
                       placeholder="Votre numéro RPPS"
                       value="<?= htmlspecialchars($valeurs['numero_rpps'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled>
            </div>
        </div>

        <div data-role="partenaire" hidden style="display: flex; flex-direction: column; gap: var(--space-md);">
            <div class="form-group">
                <label for="poste">Poste</label>
                <input type="text" id="poste" name="poste" class="form-control" maxlength="100"
                       placeholder="Votre poste au sein de l'entreprise"
                       value="<?= htmlspecialchars($valeurs['poste'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled>
            </div>
            <div class="form-group">
                <label for="ref_entreprise">Entreprise</label>
                <select id="ref_entreprise" name="ref_entreprise" class="form-control" disabled>
                    <option value="">-- Choisir une entreprise --</option>
                    <?php foreach ($entreprises as $entreprise): ?>
                        <option value="<?= (int) $entreprise->getIdEntreprise() ?>"
                                <?= (string) ($valeurs['ref_entreprise'] ?? '') === (string) $entreprise->getIdEntreprise() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($entreprise->getNom(), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($entreprises)): ?>
                    <p class="caption">Aucune entreprise disponible : l'inscription partenaire nécessite une entreprise existante.</p>
                <?php endif; ?>
            </div>
        </div>

        <noscript style="display: block; font-size: 12px; color: var(--warning); text-align: center;">JavaScript est nécessaire pour afficher les champs du rôle sélectionné.</noscript>

        <button type="submit" class="btn btn-block" style="margin-top: 8px;">S'inscrire</button>

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