<?php
// public/index.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$routes      = require __DIR__ . '/../config/routes.php';
$user        = $_SESSION['user'] ?? null;
$estConnecte = $user !== null;
$estValide   = $estConnecte && !empty($user['est_valide']);

$pageTitle = 'Accueil';
require __DIR__ . '/../views/layouts/header.php';
?>

    <section>
        <h1>Bienvenue sur GHD-HSP</h1>
        <p>
            GHD-HSP est la plateforme du groupe hospitalier qui met en relation
            étudiants, médecins, partenaires et gestionnaires : annuaire des médecins,
            offres de stage et d'emploi, événements et forum d'échange.
        </p>

        <?php if (!$estConnecte): ?>
            <p>
                <?php if (!empty($routes['connexion'])): ?>
                    <a href="<?= htmlspecialchars($routes['connexion']) ?>">Se connecter</a>
                <?php endif; ?>
                <?php if (!empty($routes['connexion']) && !empty($routes['inscription'])): ?> ou <?php endif; ?>
                <?php if (!empty($routes['inscription'])): ?>
                    <a href="<?= htmlspecialchars($routes['inscription']) ?>">créer un compte</a>
                <?php endif; ?>
            </p>
        <?php elseif (!$estValide): ?>
            <p><strong>Votre compte est en attente de validation par un gestionnaire.</strong></p>
        <?php else: ?>
            <p>Bonjour <?= htmlspecialchars($user['prenom'] ?? '') ?>.</p>
        <?php endif; ?>
    </section>

    <section>
        <h2>Les quatre rôles</h2>
        <ul>
            <li>
                <h3>Étudiant</h3>
                <p>Consulte l'annuaire des médecins, postule aux offres de stage et d'alternance, participe aux événements.</p>
            </li>
            <li>
                <h3>Médecin</h3>
                <p>Renseigne ses spécialités, ses hôpitaux et ses établissements d'enseignement, échange sur le forum.</p>
            </li>
            <li>
                <h3>Partenaire</h3>
                <p>Représente une entreprise : publie des offres et reçoit des candidatures.</p>
            </li>
            <li>
                <h3>Gestionnaire</h3>
                <p>Valide les comptes, modère les contenus et gère les événements.</p>
            </li>
        </ul>
    </section>

<?php if ($estValide): ?>
    <section>
        <h2>Accès rapide</h2>
        <ul>
            <?php foreach (['annuaire' => 'Annuaire des médecins', 'offres' => 'Offres', 'profil' => 'Mon profil'] as $cle => $libelle): ?>
                <?php if (!empty($routes[$cle])): ?>
                    <li><a href="<?= htmlspecialchars($routes[$cle]) ?>"><?= htmlspecialchars($libelle) ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/../views/layouts/footer.php'; ?>