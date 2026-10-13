

<?php


/*
 * Traitement : src/traitement/traitement_etablissement.php
 * Rôle : recevoir le formulaire d'ajout d'un établissement, le vérifier,
 * puis l'enregistrer grâce à EtablissementRepository.
 *
 * On suppose que $_SESSION['id_utilisateur'] contient l'id du compte connecté.
 * Adapte ce nom si ton système de connexion en utilise un autre.
 */

session_start();

require_once __DIR__ . '/../repository/EtablissementRepository.php';

/* ---------- 1. Vérifications de sécurité ---------- */

// Seule la méthode POST est acceptée
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée.');
}

// L'utilisateur doit être connecté
$idUtilisateur = (int) ($_SESSION['id_utilisateur'] ?? 0);
if ($idUtilisateur <= 0) {
    http_response_code(401);
    exit('Vous devez être connecté pour effectuer cette action.');
}

// Le jeton CSRF du formulaire doit être le même que celui de la session
$jetonSession    = (string) ($_SESSION['csrf_token'] ?? '');
$jetonFormulaire = (string) ($_POST['csrf_token'] ?? '');
if ($jetonSession === '' || !hash_equals($jetonSession, $jetonFormulaire)) {
    http_response_code(403);
    exit('Requête refusée : jeton de sécurité invalide. Rechargez le formulaire.');
}

/* ---------- 2. Connexion à la base de données ---------- */

$host   = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'gdh_hsp';
$user   = getenv('DB_USER') ?: 'root';
$pass   = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // Le repository qui fait les requêtes sur la table Etablissement
    $repo = new EtablissementRepository($pdo);

    /* ---------- 3. Vérifier que l'utilisateur est un gestionnaire validé ---------- */

    // On relit le rôle dans la base : on ne fait pas confiance au navigateur
    $stmt = $pdo->prepare('SELECT role, est_valide FROM Utilisateur WHERE id_utilisateur = ?');
    $stmt->execute([$idUtilisateur]);
    $compte = $stmt->fetch();

    if (!$compte || $compte['role'] !== 'gestionnaire' || (int) $compte['est_valide'] !== 1) {
        http_response_code(403);
        exit('Vous n’êtes pas autorisé à créer un établissement.');
    }

    /* ---------- 4. Récupérer et vérifier les champs ---------- */

    $nom     = trim((string) ($_POST['nom_etablissement'] ?? ''));
    $adresse = trim((string) ($_POST['adresse'] ?? ''));
    $siteWeb = trim((string) ($_POST['site_web'] ?? ''));

    $erreurs = [];

    if ($nom === '' || mb_strlen($nom) > 150) {
        $erreurs[] = 'Le nom est obligatoire (150 caractères maximum).';
    }

    if ($adresse === '' || mb_strlen($adresse) > 255) {
        $erreurs[] = 'L’adresse est obligatoire (255 caractères maximum).';
    }

    // Le site doit être une URL en http ou https
    $schema = strtolower((string) parse_url($siteWeb, PHP_URL_SCHEME));
    if (
        $siteWeb === ''
        || mb_strlen($siteWeb) > 255
        || filter_var($siteWeb, FILTER_VALIDATE_URL) === false
        || !in_array($schema, ['http', 'https'], true)
    ) {
        $erreurs[] = 'Le site web doit être une URL valide, par exemple https://www.exemple.fr.';
    } elseif ($repo->siteExiste($siteWeb)) {
        // La colonne site_web est UNIQUE : on refuse les doublons
        $erreurs[] = 'Ce site web est déjà associé à un établissement.';
    }

    // S'il y a des erreurs : on les garde en session et on revient au formulaire
    if (!empty($erreurs)) {
        $_SESSION['erreurs_etablissement'] = $erreurs;
        $_SESSION['ancien_etablissement']  = [
            'nom_etablissement' => $nom,
            'adresse'           => $adresse,
            'site_web'          => $siteWeb,
        ];
        header('Location: ../../public/etablissement.php');
        exit;
    }

    /* ---------- 5. Enregistrer l'établissement ---------- */

    $nouvelId = $repo->ajouter($nom, $adresse, $siteWeb);

    // Nouveau jeton CSRF après une action réussie
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // Redirection vers la fiche du nouvel établissement
    header('Location: ../../public/etablissement.php?id=' . $nouvelId . '&ajout=ok');
    exit;
} catch (PDOException $e) {
    // Erreur inattendue (ou doublon créé au même moment par quelqu'un d'autre)
    if ($e->getCode() === '23000') {
        $_SESSION['erreurs_etablissement'] = ['Ce site web est déjà associé à un établissement.'];
        header('Location: ../../public/etablissement.php');
        exit;
    }

    error_log('Erreur traitement établissement : ' . $e->getMessage());
    http_response_code(500);
    exit('Une erreur interne est survenue. Veuillez réessayer plus tard.');
}

