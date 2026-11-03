<?php
// public/index.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$routes = require __DIR__ . '/../config/routes.php';

$estConnecte = isset($_SESSION['utilisateur_id']);
$role = $_SESSION['role'] ?? null;

$pageTitle = 'Accueil';
$activePage = 'accueil';

require __DIR__ . '/../views/layouts/header.php';
?>

<div class="container">

    <!-- PRÉSENTATION -->
    <section class="accueil-hero fade-in">

        <div class="accueil-hero-content">

            <span class="eyebrow">
                Bienvenue sur GHD-HSP
            </span>

            <h1 class="page-title">
                Ensemble, construisons le réseau
                hospitalo-universitaire.
            </h1>

            <p class="lead">
                GHD-HSP est une plateforme de mise en relation
                entre étudiants, médecins et partenaires.
                Découvrez les professionnels de santé,
                les établissements et les opportunités
                professionnelles.
            </p>

            <div class="cluster accueil-actions">

                <?php if (!$estConnecte): ?>

                    <?php if (!empty($routes['inscription'])): ?>
                        <a href="<?= htmlspecialchars($routes['inscription']) ?>"
                           class="btn">
                            Créer un compte
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($routes['connexion'])): ?>
                        <a href="<?= htmlspecialchars($routes['connexion']) ?>"
                           class="btn btn-secondary">
                            Se connecter
                        </a>
                    <?php endif; ?>

                <?php else: ?>

                    <?php if (!empty($routes['annuaire'])): ?>
                        <a href="<?= htmlspecialchars($routes['annuaire']) ?>"
                           class="btn">
                            Consulter l'annuaire
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($routes['profil'])): ?>
                        <a href="<?= htmlspecialchars($routes['profil']) ?>"
                           class="btn btn-secondary">
                            Mon profil
                        </a>
                    <?php endif; ?>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- LES PROFILS -->
    <section class="accueil-section">

        <div class="page-header">
            <span class="eyebrow">Notre communauté</span>

            <h2 class="page-title">
                Une plateforme pour chaque acteur
            </h2>

            <p class="lead">
                Chaque utilisateur dispose d'un espace
                adapté à son rôle dans le réseau.
            </p>
        </div>

        <div class="grid-2">

            <article class="card fade-in">
                <span class="tag tag--etudiant accueil-tag">
                    Étudiant
                </span>

                <h3 class="card-title">
                    Développer son parcours
                </h3>

                <p>
                    Consulter l'annuaire des médecins,
                    découvrir des offres de stage
                    et suivre ses candidatures.
                </p>
            </article>

            <article class="card fade-in">
                <span class="tag tag--medecin accueil-tag">
                    Médecin
                </span>

                <h3 class="card-title">
                    Valoriser son expertise
                </h3>

                <p>
                    Présenter ses spécialités,
                    ses hôpitaux et ses établissements
                    d'enseignement.
                </p>
            </article>

            <article class="card fade-in">
                <span class="tag tag--partenaire accueil-tag">
                    Partenaire
                </span>

                <h3 class="card-title">
                    Recruter et collaborer
                </h3>

                <p>
                    Présenter son entreprise,
                    publier des offres et consulter
                    les candidatures reçues.
                </p>
            </article>

            <article class="card fade-in">
                <span class="tag accueil-tag">
                    Gestionnaire
                </span>

                <h3 class="card-title">
                    Gérer la plateforme
                </h3>

                <p>
                    Administrer les utilisateurs
                    et accompagner le fonctionnement
                    du réseau GHD-HSP.
                </p>
            </article>

        </div>

    </section>


    <!-- ACCÈS RAPIDES -->
    <?php if ($estConnecte): ?>

        <section class="accueil-section">

            <div class="page-header">
                <span class="eyebrow">
                    Votre espace
                </span>

                <h2 class="page-title">
                    Accès rapides
                </h2>

                <p class="lead">
                    Retrouvez facilement les principales
                    fonctionnalités de la plateforme.
                </p>
            </div>

            <div class="grid-2">

                <?php
                $accesRapides = [
                        'annuaire' => 'Annuaire des médecins',
                        'offres' => 'Offres de stage et d’emploi',
                        'profil' => 'Mon profil'
                ];
                ?>

                <?php foreach ($accesRapides as $cle => $libelle): ?>

                    <?php if (!empty($routes[$cle])): ?>

                        <a class="card accueil-card-link"
                           href="<?= htmlspecialchars($routes[$cle]) ?>">

                            <h3 class="card-title">
                                <?= htmlspecialchars($libelle) ?>
                            </h3>

                            <span class="accueil-link">
                                Accéder à la page →
                            </span>

                        </a>

                    <?php endif; ?>

                <?php endforeach; ?>

                <?php if ($role === 'etudiant'
                        && !empty($routes['candidatures'])): ?>

                    <a class="card accueil-card-link"
                       href="<?= htmlspecialchars($routes['candidatures']) ?>">

                        <h3 class="card-title">
                            Mes candidatures
                        </h3>

                        <span class="accueil-link">
                            Suivre mes candidatures →
                        </span>

                    </a>

                <?php endif; ?>

            </div>

        </section>

    <?php endif; ?>

</div>

<?php require __DIR__ . '/../views/layouts/footer.php'; ?>
