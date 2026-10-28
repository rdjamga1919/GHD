
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#102f40">
    <meta name="description" content="Bibliothèque de composants et identité visuelle GHD-HSP.">

    <title>Design System — GHD-HSP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">

    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>

<body>

<main class="container">

    <header class="page-header">
        <p class="eyebrow">GHD-HSP / DESIGN SYSTEM</p>
        <h1 class="page-title">Une interface pensée pour le réseau.</h1>
        <p class="lead">
            Les composants visuels de GHD-HSP réunis dans une bibliothèque
            cohérente, accessible et conçue pour les usages hospitalo-universitaires.
        </p>

        <div class="cluster" style="margin-top: 8px;">
            <span class="tag">Version 1.0</span>
            <span class="caption">Bibliothèque de composants réutilisables</span>
        </div>
    </header>

    <!-- Boutons -->
    <section class="card fade-in">
        <h2 class="card-title">01. Boutons et actions</h2>
        <p class="caption">
            Des actions claires, une hiérarchie visible et des états cohérents.
        </p>

        <div class="cluster">
            <button class="btn" type="button">Bouton principal</button>
            <button class="btn btn-secondary" type="button">
                Bouton secondaire
            </button>
            <button class="btn" type="button" disabled>
                Bouton désactivé
            </button>
        </div>
    </section>

    <!-- Profil -->
    <section class="card">
        <h2 class="card-title">02. Profil utilisateur</h2>
        <p class="caption">
            Une présentation compacte pour identifier rapidement un membre.
        </p>

        <div class="user">
            <div class="avatar" aria-hidden="true">MD</div>
            <div class="list-item-body">
                <span class="user-name">Marie Dupont</span>
                <span class="caption">Université Val-de-Rhône</span>
            </div>
        </div>

        <div class="cluster">
            <span class="caption">Statut du compte</span>
            <span class="tag tag--success">Compte validé</span>
        </div>
    </section>

    <!-- Rôles -->
    <section class="card">
        <h2 class="card-title">03. Rôles et identification</h2>
        <p class="caption">
            Une couleur dédiée à chaque catégorie de membre.
        </p>

        <div class="cluster">
            <span class="tag tag--etudiant">Étudiant</span>
            <span class="tag tag--medecin">Médecin</span>
            <span class="tag tag--partenaire">Partenaire</span>
        </div>

        <div class="cluster">
            <div class="avatar" aria-label="Étudiant">NB</div>
            <div class="avatar avatar--medecin" aria-label="Médecin">TP</div>
            <div class="avatar avatar--partenaire" aria-label="Partenaire">HC</div>
        </div>
    </section>

    <!-- Formulaires -->
    <section class="card">
        <h2 class="card-title">04. Formulaires</h2>
        <p class="caption">
            Des champs lisibles, des libellés explicites et une recherche identifiable.
        </p>

        <div class="grid-2">
            <div class="form-group">
                <label for="nom">Nom complet</label>
                <input
                        type="text"
                        id="nom"
                        class="form-control"
                        placeholder="Ex. Marie Dupont"
                >
            </div>

            <div class="form-group">
                <label for="role">Type de membre</label>
                <select id="role" class="form-control">
                    <option value="">Sélectionner un rôle</option>
                    <option value="etudiant">Étudiant</option>
                    <option value="medecin">Médecin</option>
                    <option value="partenaire">Partenaire</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="recherche">Rechercher un utilisateur</label>
            <div class="input-icon">
                <span class="icon icon--lg">
                    <img src="img/vector-5.svg" alt="">
                </span>
                <input
                        type="search"
                        id="recherche"
                        class="form-control"
                        placeholder="Nom ou adresse e-mail"
                >
            </div>
        </div>

        <div class="cluster">
            <button class="btn" type="button">Enregistrer</button>
            <button class="btn btn-secondary" type="button">Annuler</button>
        </div>
    </section>

    <!-- Messages -->
    <section class="card">
        <h2 class="card-title">05. Messages système</h2>
        <p class="caption">
            Quatre niveaux de notification pour informer sans surcharger l'interface.
        </p>

        <div class="alert alert-info" role="status">
            <div class="alert-body">
                <p class="alert-title">Information</p>
                <p class="alert-text">12 comptes attendent une validation.</p>
            </div>
        </div>

        <div class="alert alert-success" role="status">
            <div class="alert-body">
                <p class="alert-title">Opération réussie</p>
                <p class="alert-text">La modification a été enregistrée.</p>
            </div>
        </div>

        <div class="alert alert-warning" role="status">
            <div class="alert-body">
                <p class="alert-title">Attention</p>
                <p class="alert-text">Certaines informations sont à vérifier.</p>
            </div>
        </div>

        <div class="alert alert-error" role="alert">
            <div class="alert-body">
                <p class="alert-title">Une erreur est survenue</p>
                <p class="alert-text">Vérifiez les informations puis réessayez.</p>
            </div>
        </div>
    </section>

    <!-- Tableau -->
    <section class="card">
        <h2 class="card-title">06. Tableau des membres</h2>
        <p class="caption">
            Des colonnes structurées et des statuts immédiatement reconnaissables.
        </p>

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Membre</th>
                    <th scope="col">Rôle</th>
                    <th scope="col">Établissement</th>
                    <th scope="col">Statut</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <div class="user">
                            <div class="avatar">MD</div>
                            <span class="user-name">Marie Dupont</span>
                        </div>
                    </td>
                    <td><span class="tag tag--etudiant">Étudiant</span></td>
                    <td>Université Val-de-Rhône</td>
                    <td><span class="tag tag--success">Validé</span></td>
                </tr>
                <tr>
                    <td>
                        <div class="user">
                            <div class="avatar avatar--medecin">JM</div>
                            <span class="user-name">Jean Martin</span>
                        </div>
                    </td>
                    <td><span class="tag tag--medecin">Médecin</span></td>
                    <td>Centre hospitalier</td>
                    <td><span class="tag tag--warning">En attente</span></td>
                </tr>
                <tr>
                    <td>
                        <div class="user">
                            <div class="avatar avatar--partenaire">SL</div>
                            <span class="user-name">Sophie Laurent</span>
                        </div>
                    </td>
                    <td><span class="tag tag--partenaire">Partenaire</span></td>
                    <td>Réseau universitaire</td>
                    <td><span class="tag tag--success">Validé</span></td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Indicateurs -->
    <section class="card">
        <h2 class="card-title">07. Indicateurs d'activité</h2>
        <p class="caption">
            Des données importantes mises en valeur sans surcharger la page.
        </p>

        <div class="stats-grid">
            <article class="stat-card">
                <p class="stat-label">Membres actifs</p>
                <p class="stat-value">1 240</p>
                <p class="stat-trend">+24 ce mois-ci</p>
            </article>

            <article class="stat-card">
                <p class="stat-label">Comptes à valider</p>
                <p class="stat-value">12</p>
                <p class="stat-trend">3 depuis hier</p>
            </article>
        </div>
    </section>

    <!-- Navigation -->
    <section class="card">
        <h2 class="card-title">08. Navigation gestionnaire</h2>
        <p class="caption">
            Une navigation sombre qui distingue clairement l'espace de gestion.
        </p>

        <aside class="sidebar" style="position: static;">
            <p class="sidebar-label">Espace gestionnaire</p>

            <nav class="nav" aria-label="Exemple de navigation">
                <a class="nav-link is-active" href="#"
                   aria-current="page">Tableau de bord</a>
                <a class="nav-link" href="#">Utilisateurs</a>
                <a class="nav-link" href="#">Contenus</a>
            </nav>

            <p class="sidebar-note">
                Marie Laurent<br>
                Gestionnaire du réseau
            </p>
        </aside>
    </section>

    <!-- Graphique -->
    <section class="card">
        <h2 class="card-title">09. Graphique d'activité</h2>
        <p class="caption">
            Une visualisation légère qui reprend les couleurs de la marque.
        </p>

        <div class="chart-legend">
            <span>Validations hebdomadaires</span>
            <span>Cette semaine</span>
        </div>

        <div class="chart" role="img"
             aria-label="Exemple : 9, 14, 28 et 18 validations">
            <div class="chart-col">
                <span class="chart-value">9</span>
                <div class="bar" style="--h:45px"></div>
                <span class="chart-day">Lun.</span>
            </div>
            <div class="chart-col">
                <span class="chart-value">14</span>
                <div class="bar" style="--h:72px"></div>
                <span class="chart-day">Mar.</span>
            </div>
            <div class="chart-col">
                <span class="chart-value">28</span>
                <div class="bar is-peak" style="--h:138px"></div>
                <span class="chart-day">Mer.</span>
            </div>
            <div class="chart-col">
                <span class="chart-value">18</span>
                <div class="bar" style="--h:88px"></div>
                <span class="chart-day">Jeu.</span>
            </div>
        </div>
    </section>

    <footer class="footer-note">
        GHD-HSP · Design System V1 · Bibliothèque de composants visuels
    </footer>

</main>

</body>
</html>
