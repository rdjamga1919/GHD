

Markdown collé.md
Document
Créer ou compléter la page public/etablissement.php
Afficher la liste des établissements enregistrés dans la base de données
Créer un formulaire de recherche par nom d'établissement
Permettre d'accéder à la fiche détaillée d'un établissement
Afficher le nom, l'adresse et le site web de l'établissement
Créer un formulaire permettant d'ajouter un établissement
Prévoir les champs : nom, adresse et site web  code les éléments suivants en te basant sur la base de donnée ci-dessous

Voici une version complète de public/etablissement.php, adaptée à la base de données gdh_hsp et à la table Etablissement que tu as fournie.
Markdown collé.md

Le fichier contient les fonctionnalités demandées :

Liste des établissements enregistrés dans la base de données.

Recherche par nom d'établissement.

Fiche détaillée accessible en cliquant sur un établissement.

Affichage du nom, de l'adresse et du site web.

Formulaire d'ajout avec les trois champs demandés.

Vérification des champs obligatoires et des URL.

Requêtes SQL préparées avec PDO pour sécuriser les interactions avec la base de données.

Télécharger le fichier PHP complet — etablissement.php

Installation dans ton projet
Place le fichier dans le dossier public/etablissement.php.

Vérifie que MySQL est démarré et que la base gdh_hsp existe.

Configure les identifiants de connexion à la base de données au début du fichier (DB_HOST, DB_NAME, DB_USER et DB_PASSWORD), ou modifie les valeurs par défaut.

Ouvre la page depuis ton serveur PHP, par exemple http://localhost/ton-projet/public/etablissement.php.

La page utilise les colonnes exactes de ta table :

Etablissement (
id_etablissement,
nom_etablissement,
adresse,
site_web
)

Important : la colonne site_web possède une contrainte UNIQUE dans ta base. Le code gère donc le cas où un site web est déjà enregistré.

J'ai également vérifié la syntaxe PHP du fichier : elle est valide. Il reste à tester la connexion avec ta configuration MySQL et à vérifier le fonctionnement dans ton projet.


etablissement.php
Code
Pour la création de page PHP et le site, voici une option de création web.



Pub
Les publicités n’influencent pas les réponses que vous donne ChatGPT. Vos chats restent privés.
En savoir plus sur les publicités
Créer le fichier src/traitement/traitement_etablissement.php
Vérifier les données du formulaire côté serveur
Vérifier les autorisations de création et protéger le traitement contre les requêtes non autorisées

J’ai créé le fichier src/traitement/traitement_etablissement.php en tenant compte de la structure de ta base gdh_hsp et des champs de la table Etablissement.

Le traitement comprend :

Vérification côté serveur du nom, de l’adresse et du site web.

Contrôle des autorisations : seul un gestionnaire connecté dont le compte est validé peut créer un établissement.

Protection CSRF contre les soumissions de formulaires non autorisées.

Requêtes PDO préparées pour sécuriser l’insertion en base de données.

Gestion des sites web en doublon, puisque la colonne site_web est unique.

Redirection après l’ajout ou en cas d’erreur.

Télécharger traitement_etablissement.php

1. Modifier le formulaire dans public/etablissement.php
Remplace l'ouverture du formulaire d'ajout par :

<form action="../src/traitement/traitement_etablissement.php" method="post">

    Conserve le champ CSRF déjà présent :

    <input
        type="hidden"
        name="csrf_token"
        value="<?= e($_SESSION['csrf_token']) ?>"
    >

    Les champs doivent conserver ces noms : nom_etablissement, adresse et site_web.

    2. Vérifier l'authentification
    Le traitement suppose que ton système d'authentification stocke l'identifiant du compte connecté dans :

    $_SESSION['id_utilisateur']

    Il vérifie ensuite en base que le compte a le rôle gestionnaire et que est_valide = 1. Si ton projet utilise d'autres noms de variables de session, adapte-les au fichier de connexion existant.

    3. Arborescence attendue
    ton-projet/

    public/

    etablissement.php

    src/traitement/

    traitement_etablissement.php

    À noter : pour que les erreurs de validation s'affichent dans la page, il faudra également lire les messages stockés en session dans public/etablissement.php. Le fichier de traitement est prêt à les enregistrer, mais cette partie de l'affichage reste à intégrer.


    traitement_etablissement.php
    Code
    reecris etablissement.php avec les modifactions que tu voulias faire en fonctiàon de traitement etablissement

    J’ai réécrit etablissement.php pour qu’il fonctionne avec src/traitement/traitement_etablissement.php.

    Cette nouvelle version reprend les fonctionnalités précédentes et ajoute les modifications nécessaires :

    Le formulaire envoie les données au fichier de traitement avec method="post".

    Le jeton CSRF est transmis pour protéger le formulaire.

    Les erreurs de validation enregistrées en session sont affichées sur la page.

    Les valeurs saisies sont conservées après une erreur de validation.

    Un message de confirmation apparaît après l'ajout.

    La liste, la recherche et la fiche détaillée restent disponibles.

    Le formulaire précise que la création est réservée aux gestionnaires autorisés.

    Télécharger le fichier etablissement.php mis à jour

    Installation
    Remplace ton ancien fichier public/etablissement.php par celui-ci.

    Conserve src/traitement/traitement_etablissement.php dans son dossier.

    Vérifie que les deux fichiers utilisent la même session PHP et le même nom de jeton CSRF : csrf_token.

    Connecte-toi avec un compte ayant le rôle gestionnaire et est_valide = 1 pour tester l'ajout.

    Vérification : la syntaxe PHP du nouveau fichier a été contrôlée avec succès. Le fonctionnement complet doit encore être testé avec ta base de données et ton système de connexion.


    etablissement.php
    Code
    Vous n’avez plus de d'analyse de données pour le moment. Lancez un essai gratuit de l'abonnement Plus pour en obtenir plus, ou réessayez demain après 09:20.



<?php

/**
 * Gestion des établissements — GDH
 * Fichier : public/etablissement.php
 *
 * Base attendue : gdh_hsp
 * Table : Etablissement(id_etablissement, nom_etablissement, adresse, site_web)
 */

session_start();

$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'gdh_hsp';
$dbuser = getenv('DB_USER') ?: 'root';
$dbpass = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $dbuser,
        $dbpass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    error_log('Connexion établissement impossible : ' . $e->getMessage());
    http_response_code(500);
    exit('Impossible de se connecter à la base de données.');
}

/* Échappement systématique des valeurs affichées dans le HTML. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* Le jeton doit être partagé avec src/traitement/traitement_etablissement.php. */
if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* Récupérer les messages d'erreur et les anciennes valeurs après redirection. */
$erreurs = $_SESSION['erreurs_etablissement'] ?? [];
unset($_SESSION['erreurs_etablissement']);

$ancien = $_SESSION['ancien_etablissement'] ?? [];
unset($_SESSION['ancien_etablissement']);

$ancienNom = is_array($ancien) ? (string)($ancien['nom_etablissement'] ?? '') : '';
$ancienneAdresse = is_array($ancien) ? (string)($ancien['adresse'] ?? '') : '';
$ancienSite = is_array($ancien) ? (string)($ancien['site_web'] ?? '') : '';

$succes = (isset($_GET['ajout']) && $_GET['ajout'] === 'ok')
    ? 'L’établissement a bien été ajouté.'
    : '';

$recherche = trim((string)($_GET['q'] ?? ''));
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$etablissementDetail = null;
$etablissements = [];

try {
    /* Fiche détaillée si un identifiant est présent. */
    if ($id !== false && $id !== null && $id > 0) {
        $stmt = $pdo->prepare(
            'SELECT id_etablissement, nom_etablissement, adresse, site_web
             FROM Etablissement
             WHERE id_etablissement = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $etablissementDetail = $stmt->fetch() ?: null;
    }

    /* Sinon, afficher la liste, éventuellement filtrée par nom. */
    if ($etablissementDetail === null) {
        $sql = 'SELECT id_etablissement, nom_etablissement, adresse, site_web
                FROM Etablissement';
        $params = [];

        if ($recherche !== '') {
            $sql .= ' WHERE nom_etablissement LIKE :recherche';
            $params[':recherche'] = '%' . $recherche . '%';
        }

        $sql .= ' ORDER BY nom_etablissement ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $etablissements = $stmt->fetchAll();
    }
} catch (PDOException $e) {
}