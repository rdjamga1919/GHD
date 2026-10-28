<?php
/**
 * Page « À propos » : présentation du groupe GHD et de l’hôpital HSP.
 * Sources : cahier des charges du projet (contexte, expression de besoin).
 */

$site_name    = 'GHD-HSP';
$page_title   = 'À propos';
$current_page = 'a_propos.php';

// Échappement systématique des sorties HTML
function e(string $texte): string
{
    return htmlspecialchars($texte, ENT_QUOTES, 'UTF-8');
}

// Navigation principale (à adapter aux noms réels de tes fichiers)
$navigation = [
    'index.php'       => 'Accueil',
    'a_propos.php'    => 'À propos',
    'annuaire.php'    => 'Annuaire',
    'offres.php'      => 'Offres',
    'evenements.php'  => 'Événements',
    'forum.php'       => 'Forum',
    'contact.php'     => 'Contact',
];

$poles = [
    'Chirurgie',
    'Médecine',
    'Cancérologie',
    'Maternité',
    'Imagerie médicale',
    'Urgences 24 h/24, 7 j/7',
];

$hopitaux = [
    ['nom' => 'Hôpital Sud Paris (HSP)',        'lieu' => 'Marreuil sur Seine',          'courant' => true],
    ['nom' => 'Hôpital Nord Paris',             'lieu' => 'près de Saint-Maximin',       'courant' => false],
    ['nom' => 'Hôpital Général de Lyon',        'lieu' => 'Limonest',                    'courant' => false],
    ['nom' => 'Hôpital Général de Marseille',   'lieu' => 'près de Peypin',              'courant' => false],
    ['nom' => 'Hôpital Général de Bordeaux',    'lieu' => 'près de Beychac-et-Cailleau', 'courant' => false],
    ['nom' => 'Hôpital Général de Nantes',      'lieu' => 'près de Mauves-sur-Loire',    'courant' => false],
    ['nom' => 'Hôpital Général de Toulouse',    'lieu' => 'Muret',                       'courant' => false],
    ['nom' => 'Hôpital Général de Strasbourg',  'lieu' => 'Vendenheim',                  'courant' => false],
    ['nom' => 'Hôpital Général de Rennes',      'lieu' => 'près de Pont-Péan',           'courant' => false],
];
// Bouton retour : renvoie vers la page précédente du même site, sinon vers l’accueil
$retour_url = 'index.php';
$referer    = $_SERVER['HTTP_REFERER'] ?? '';

if ($referer !== '') {
    $ref  = parse_url($referer);
    $hote = parse_url('//' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST);

    if (
        isset($ref['host'], $ref['path'], $hote)
        && strcasecmp($ref['host'], $hote) === 0
        && basename($ref['path']) !== basename($_SERVER['SCRIPT_NAME'])
    ) {
        // On garde uniquement le chemin : pas de redirection vers un site externe
        $retour_url = $ref['path'] . (isset($ref['query']) ? '?' . $ref['query'] : '');
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Découvrez le groupe GHD, l’hôpital Sud Paris et la plateforme qui relie médecins, étudiants en médecine et entreprises partenaires.">

    <title><?= e($page_title) ?> · <?= e($site_name) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">

    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>

<body>

<a class="skip-link" href="#contenu">Aller au contenu</a>

<!-- En-tête -->
<header class="site-header">
    <div class="site-header-inner">
        <a class="brand" href="index.php"><?= e($site_name) ?></a>

        <nav class="site-nav" aria-label="Navigation principale">
            <?php foreach ($navigation as $fichier => $libelle): ?>
                <a href="<?= e($fichier) ?>"<?= $fichier === $current_page ? ' aria-current="page"' : '' ?>>
                    <?= e($libelle) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="site-actions">
            <a class="btn btn-secondary" href="connexion.php">Connexion</a>
            <a class="btn" href="inscription.php">Inscription</a>
        </div>
    </div>
</header>

<main id="contenu" class="container">

    <!-- Bouton retour -->
    <div>
        <a class="back-link" href="<?= e($retour_url) ?>" data-retour>
            <span aria-hidden="true">←</span> Retour
        </a>
    </div>

    <!-- Héro -->
    <section class="hero fade-in">
        <p class="eyebrow">À PROPOS</p>
        <h1 class="page-title">Un réseau pour relier médecins, étudiants et partenaires.</h1>
        <p class="lead">
            Cette plateforme réunit les médecins du groupe GHD, les étudiants en médecine
            en quête d’un réseau et d’un premier emploi, et les entreprises partenaires.
        </p>
        <div class="cluster">
            <a class="btn btn-accent" href="inscription.php">Rejoindre le réseau</a>
            <a class="btn btn-secondary" href="contact.php">Nous contacter</a>
        </div>
    </section>

    <!-- Le groupe -->
    <section class="section" aria-labelledby="titre-groupe">
        <h2 class="section-title" id="titre-groupe">Le groupe GHD</h2>

        <div class="grid-2">
            <div class="prose">
                <p>
                    Créée en <strong>1954</strong> et dont le siège est situé à Paris, la
                    <strong>Générale des hôpitaux</strong> est le premier groupe de cliniques et
                    d’hôpitaux privés en France.
                </p>
                <p>
                    Forte de 70 années d’expérience, elle a su tirer profit des dernières
                    technologies médicales pour offrir aux patients des prestations couvrant
                    une large variété de spécialités.
                </p>
            </div>

            <div class="stats-grid">
                <article class="stat-card">
                    <p class="stat-label">Création du groupe</p>
                    <p class="stat-value">1954</p>
                </article>
                <article class="stat-card">
                    <p class="stat-label">Hôpitaux en France</p>
                    <p class="stat-value"><?= count($hopitaux) ?></p>
                </article>
            </div>
        </div>
    </section>

    <!-- HSP -->
    <section class="section" aria-labelledby="titre-hsp">
        <h2 class="section-title" id="titre-hsp">L’hôpital Sud Paris (HSP)</h2>

        <div class="card">
            <div class="prose">
                <p>
                    Inauguré en 1995, l’hôpital privé HSP est le fruit du rattachement de plusieurs
                    établissements indépendants au pôle territorial Paris Sud du groupe. Il est situé
                    à <strong>Marreuil sur Seine</strong>, à 25 km de Paris.
                </p>
            </div>

            <div class="stats-grid">
                <article class="stat-card">
                    <p class="stat-label">Praticiens</p>
                    <p class="stat-value">90</p>
                </article>
                <article class="stat-card">
                    <p class="stat-label">Lits</p>
                    <p class="stat-value">289</p>
                </article>
            </div>

            <div>
                <p class="user-name">Des compétences diverses et complémentaires</p>
                <div class="cluster" style="margin-top: var(--space-sm)">
                    <?php foreach ($poles as $pole): ?>
                        <span class="tag"><?= e($pole) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission et vision -->
    <section class="section" aria-labelledby="titre-mission">
        <h2 class="section-title" id="titre-mission">Notre mission</h2>

        <div class="grid-3">
            <article class="card">
                <h3 class="card-title">Renforcer le réseau</h3>
                <p class="prose">
                    Garder un contact régulier avec les médecins du groupe, les informer des
                    actualités et des événements, et faciliter les relations entre les hôpitaux.
                </p>
            </article>
            <article class="card">
                <h3 class="card-title">Accompagner les étudiants</h3>
                <p class="prose">
                    Offrir aux élèves en médecine des opportunités de mise en relation et de
                    développement de carrière, et les aider à trouver stages, alternances et emplois.
                </p>
            </article>
            <article class="card">
                <h3 class="card-title">Ouvrir aux partenaires</h3>
                <p class="prose">
                    Faciliter les partenariats pour des événements, et permettre aux entreprises
                    de publier des offres et de rechercher des talents.
                </p>
            </article>
        </div>
    </section>

    <!-- Pour qui -->
    <section class="section" aria-labelledby="titre-public">
        <h2 class="section-title" id="titre-public">À qui s’adresse la plateforme ?</h2>

        <div class="grid-3">
            <article class="card">
                <span class="tag tag--etudiant">Étudiants</span>
                <ul class="check-list">
                    <li>Offres de stage, d’alternance et d’emploi</li>
                    <li>Annuaire des médecins du groupe</li>
                    <li>Événements du groupe et des partenaires</li>
                    <li>Forum d’échange</li>
                </ul>
            </article>

            <article class="card">
                <span class="tag tag--medecin">Médecins</span>
                <ul class="check-list">
                    <li>Accès aux profils des étudiants</li>
                    <li>Publication d’événements et de projets</li>
                    <li>Échanges entre les hôpitaux du groupe</li>
                    <li>Forum dédié aux médecins</li>
                </ul>
            </article>

            <article class="card">
                <span class="tag tag--partenaire">Entreprises partenaires</span>
                <ul class="check-list">
                    <li>Fiche entreprise partagée par plusieurs personnes</li>
                    <li>Publication d’offres de stage et d’emploi</li>
                    <li>Organisation d’événements avec les hôpitaux</li>
                    <li>Recherche de talents</li>
                </ul>
            </article>
        </div>
    </section>

    <!-- Fonctionnement -->
    <section class="section" aria-labelledby="titre-fonctionnement">
        <h2 class="section-title" id="titre-fonctionnement">Comment ça marche ?</h2>

        <ol class="steps">
            <li>
                <div class="card">
                    <h3 class="card-title">Créez votre profil</h3>
                    <p class="prose">Inscrivez-vous en tant qu’étudiant, médecin ou partenaire, puis rattachez-vous à votre établissement ou à votre entreprise.</p>
                </div>
            </li>
            <li>
                <div class="card">
                    <h3 class="card-title">Attendez la validation</h3>
                    <p class="prose">Un gestionnaire vérifie la pertinence de chaque inscription avant d’ouvrir l’accès à la plateforme.</p>
                </div>
            </li>
            <li>
                <div class="card">
                    <h3 class="card-title">Rejoignez le réseau</h3>
                    <p class="prose">Une fois validé, connectez-vous pour échanger, postuler, publier et vous inscrire aux événements.</p>
                </div>
            </li>
        </ol>

        <div class="alert alert-info" role="note">
            <div class="alert-body">
                <p class="alert-title">Un accès vérifié</p>
                <p class="alert-text">Chaque compte est validé par un gestionnaire pour garantir un réseau fiable et pertinent.</p>
            </div>
        </div>
    </section>

    <!-- Hôpitaux -->
    <section class="section" aria-labelledby="titre-hopitaux">
        <h2 class="section-title" id="titre-hopitaux">Les hôpitaux du groupe</h2>

        <ul class="hospital-grid">
            <?php foreach ($hopitaux as $hopital): ?>
                <li<?= $hopital['courant'] ? ' class="is-current"' : '' ?>>
                    <span class="user-name"><?= e($hopital['nom']) ?></span>
                    <span class="caption"><?= e($hopital['lieu']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <!-- Contact -->
    <section class="card">
        <h2 class="card-title">Une question sur la plateforme ?</h2>
        <p class="prose">Les gestionnaires vous répondent via le formulaire de contact.</p>
        <div>
            <a class="btn" href="contact.php">Accéder au formulaire de contact</a>
        </div>
    </section>

</main>

<!-- Pied de page -->
<footer class="site-footer">
    <div class="site-footer-inner">
        <p>© <?= date('Y') ?> <?= e($site_name) ?> · Plateforme de mise en relation du groupe</p>
        <p><a href="contact.php">Aide et contact</a></p>
    </div>
</footer>

<script>
    // Retour : utilise l’historique du navigateur si la page précédente est sur ce site,
    // sinon le lien (href) sert de solution de secours.
    document.querySelectorAll('[data-retour]').forEach(function (lien) {
        lien.addEventListener('click', function (event) {
            var memeSite = document.referrer &&
                new URL(document.referrer).origin === window.location.origin;

            if (memeSite && window.history.length > 1) {
                event.preventDefault();
                window.history.back();
            }
        });
    });
</script>

</body>
</html>