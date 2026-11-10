<?php
ob_start();
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
<?php require __DIR__ . '/../views/layouts/header.php'; ?>

<section class="formulaire">

    <h1 class="page-title">Créer un compte</h1>

    <?php if ($message !== ''): ?>
        <div class="alert <?= htmlspecialchars($messageType) === 'error' ? 'alert-error' : 'alert-success' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form class="card" method="POST" action="/GHD/src/traitement/inscription_traitement.php">
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
             class="form-control">
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
             class="form-control">
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
             class="form-control">
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
             class="form-control">
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
             class="form-control">
        </div>

        <!-- Rôle -->
        <div class="champ">
            <label for="role">Rôle</label>
            <select id="role" name="role" required class="form-control">
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
                       value="<?= htmlspecialchars($valeurs['formation'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled class="form-control">
            </div>
            <div class="champ">
                <label for="ref_etablissement">Établissement</label>
                <select id="ref_etablissement" name="ref_etablissement" disabled class="form-control">
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
                       value="<?= htmlspecialchars($valeurs['numero_rpps'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled class="form-control">
            </div>
        </div>

        <div data-role="partenaire" hidden>
            <div class="champ">
                <label for="poste">Poste</label>
                <input type="text" id="poste" name="poste" maxlength="100"
                       placeholder="Votre poste au sein de l'entreprise"
                       value="<?= htmlspecialchars($valeurs['poste'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled class="form-control">
            </div>
            <div class="champ">
                <label for="ref_entreprise">Entreprise</label>
                <select id="ref_entreprise" name="ref_entreprise" disabled class="form-control">
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

</section>

<!-- Lien vers la connexion -->
<div class="content-note">
    <p class="lead">
        Vous avez déjà un compte ?
        <a href="connexion.php" class="link-emphasis">Connectez-vous</a>
    </p>
</div>

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

<?php require __DIR__ . '/../views/layouts/footer.php'; ?>
