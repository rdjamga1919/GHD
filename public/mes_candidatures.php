<?php
ob_start();
session_start();

require_once __DIR__ . '/../src/bdd/Bdd.php';
require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/CandidatureRepository.php';
require_once __DIR__ . '/../src/repository/EtudiantRepository.php';

// Visiteur : redirigé vers la connexion. Autre rôle qu'étudiant : 403.
requireRole('etudiant');

// L'étudiant est celui de la session, confirmé en BDD.
$idUtilisateur = (int) $_SESSION['utilisateur_id'];
$etudiant = (new EtudiantRepository())->findById($idUtilisateur);
if ($etudiant === null || !$etudiant->estValide()) {
    http_response_code(403);
    exit('Accès refusé.');
}

$erreurs = [];
$candidatures = [];
try {
    $candidatures = (new CandidatureRepository())->findByEtudiant($idUtilisateur);
} catch (PDOException $e) {
    error_log("Erreur mes_candidatures : " . $e->getMessage());
    $erreurs[] = "Impossible d'afficher vos candidatures pour le moment.";
}

// Statuts de la BDD (ENUM) -> libellé et classe CSS GHD-HSP
$statuts = [
        'en_attente' => ['En attente', 'tag tag--warning'],
        'accepte'    => ['Acceptée', 'tag tag--success'],
        'bloque'     => ['Refusée', 'tag tag--error'],
];
?>
<?php require __DIR__ . '/../views/layouts/header.php'; ?>

<section class="content">

    <div class="page-header">
        <h1 class="page-title">Mes candidatures</h1>
    </div>

    <?php if (!empty($erreurs)): ?>
        <div class="alert alert-error">
            <?php foreach ($erreurs as $msg): ?>
                <p><?= htmlspecialchars($msg) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">Mes candidatures (<?= count($candidatures) ?>)</div>

        <div class="table-wrap">
            <div class="table-wrap"><table>
                <thead>
                <tr>
                    <th>Offre</th>
                    <th>Entreprise</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Détail</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($candidatures)): ?>
                    <?php foreach ($candidatures as $c): ?>
                        <?php
                        $statut = $statuts[$c['statut']] ?? [$c['statut'], 'tag tag--inconnu'];
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($c['titre_offre']) ?></strong></td>
                            <td><?= $c['nom_entreprise'] !== null ? htmlspecialchars($c['nom_entreprise']) : 'Non renseignée' ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($c['date_candidature']))) ?></td>
                            <td><span class="<?= htmlspecialchars($statut[1]) ?>"><?= htmlspecialchars($statut[0]) ?></span></td>
                            <td>
                                <a class="btn btn-ghost" href="offre_detail.php?id=<?= (int) $c['id_offre'] ?>">Voir l'offre</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty-msg">
                            Vous n'avez encore postulé à aucune offre.<br><br>
                            <a href="offres.php" class="btn">Consulter les offres</a>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table></div>
        </div>
    </div>

</section>

<?php require __DIR__ . '/../views/layouts/footer.php'; ?>
