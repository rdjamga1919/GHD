<?php
session_start();
require_once __DIR__ . '/../../src/inclues/authentification.php';
if (!isLoggedIn()) {
    header('Location: /GHD/public/connexion.php');
    exit;
}
requireRole('gestionnaire');

require_once __DIR__ . '/../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../src/modele/Hopital.php';
require_once __DIR__ . '/../../src/repository/HopitalRepository.php';

$hopitaux = [];
$erreurChargement = false;
try {
    $hopitalRepository = new HopitalRepository();
    $hopitaux = $hopitalRepository->findAll();
} catch (PDOException $exception) {
    $erreurChargement = true;
    http_response_code(503);
    error_log('[GDH][gestion-hopitaux] Echec du chargement des hopitaux.');
}

require_once __DIR__ . '/../../src/inclues/header.php';
?>

    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/components.css">

    <main class="container">

        <h1 class="page-title">Hôpitaux</h1>

        <?php if ($erreurChargement): ?>

            <div class="alert alert-error">La liste des hôpitaux est momentanément indisponible. Veuillez réessayer.</div>

        <?php elseif (empty($hopitaux)): ?>

            <div class="card">
                <p>Aucun hôpital enregistré.</p>
            </div>

        <?php else: ?>

            <div class="card">
                <h2>Liste des hôpitaux (<?= count($hopitaux) ?>)</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Commune</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($hopitaux as $hopital): ?>
                        <tr>
                            <td><?= htmlspecialchars($hopital->getNom()) ?></td>
                            <td><?= htmlspecialchars($hopital->getCommune()) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

        <p><a class="btn btn-secondary" href="dashboard.php">Retour au tableau de bord</a></p>

    </main>

<?php require_once __DIR__ . '/../../src/inclues/footer.php'; ?>