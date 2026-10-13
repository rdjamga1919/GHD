<?php
//page : public/admin/gestionUtilisateur
session_start();
require_once __DIR__ . '/../../src/inclues/authentification.php';
// Le helper commun conserve une URL de connexion hors du sous-dossier MAMP.
if (!isLoggedIn()) {
    header('Location: /GHD/public/connexion.php');
    exit;
}
requireRole('gestionnaire');

require_once __DIR__ . '/../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../src/modele/Utilisateur.php';
require_once __DIR__ . '/../../src/repository/UtilisateurRepository.php';

$comptesEnAttente = [];
$erreurChargement = false;
try {
    $utilisateurRepository = new UtilisateurRepository();
    $comptesEnAttente = $utilisateurRepository->getUtilisateursEnAttente();
} catch (PDOException $exception) {
    $erreurChargement = true;
    http_response_code(503);
    error_log('[GDH][gestion-utilisateurs] Echec du chargement des comptes en attente.');
}

if (!isset($_SESSION['csrf_gestion_utilisateurs'])) {
    $_SESSION['csrf_gestion_utilisateurs'] = bin2hex(random_bytes(32));
}

$libellesRole = [
        'etudiant'     => 'Étudiant',
        'medecin'      => 'Médecin',
        'partenaire'   => 'Partenaire',
        'gestionnaire' => 'Gestionnaire',
];

$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['messageType'] ?? '';
unset($_SESSION['message'], $_SESSION['messageType']);

require_once __DIR__ . '/../../src/inclues/header.php';
?>

    <h1>Comptes en attente de validation</h1>

<?php if ($message !== ''): ?>
    <div class="message message-<?= htmlspecialchars($messageType) ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<?php if ($erreurChargement): ?>

    <p class="message message-error">La liste des comptes est momentanément indisponible. Veuillez réessayer.</p>

<?php elseif (empty($comptesEnAttente)): ?>

    <p class="message-vide">Aucun compte en attente de validation.</p>

<?php else: ?>

    <table class="table-admin">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Demandé le</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($comptesEnAttente as $compte): ?>
            <tr>
                <td><?= htmlspecialchars($compte->getNom()) ?></td>
                <td><?= htmlspecialchars($compte->getPrenom()) ?></td>
                <td><?= htmlspecialchars($compte->getEmail()) ?></td>
                <td>
                    <span class="badge">
                        <?= htmlspecialchars($libellesRole[$compte->getRole()] ?? $compte->getRole()) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars(date('d/m/Y à H:i', strtotime($compte->getDateCreation()))) ?></td>
                <td class="actions">
                    <form method="post" action="/GHD/src/traitement/admin/traitementGestionUtilisateurs.php">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_gestion_utilisateurs']) ?>">
                        <input type="hidden" name="id_utilisateur" value="<?= (int) $compte->getIdUtilisateur() ?>">
                        <input type="hidden" name="action" value="valider">
                        <button type="submit" class="btn btn-valider">Valider</button>
                    </form>
                    <form method="post" action="/GHD/src/traitement/admin/traitementGestionUtilisateurs.php"
                          onsubmit="return confirm('Refuser et supprimer définitivement ce compte ?');">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_gestion_utilisateurs']) ?>">
                        <input type="hidden" name="id_utilisateur" value="<?= (int) $compte->getIdUtilisateur() ?>">
                        <input type="hidden" name="action" value="refuser">
                        <button type="submit" class="btn btn-refuser">Refuser</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>

<?php require_once __DIR__ . '/../../src/inclues/footer.php'; ?>
