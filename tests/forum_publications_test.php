<?php
/**
 * php tests/forum_publications_test.php
 * SQLite en mémoire uniquement : aucune connexion à la base GHD.
 * FOR UPDATE est retiré par l'adaptateur de test ; la concurrence InnoDB
 * doit être vérifiée sur MAMP. Les points d'entrée sont exécutés en processus
 * isolés, avec sessions en mémoire et injection des repositories de test.
 */
error_reporting(E_ALL);
set_error_handler(static function (int $niveau, string $message, string $fichier, int $ligne): never {
    throw new ErrorException($message, 0, $niveau, $fichier, $ligne);
});

session_set_save_handler(new class implements SessionHandlerInterface {
    public function open(string $path, string $name): bool { return true; }
    public function close(): bool { return true; }
    public function read(string $id): string { return ''; }
    public function write(string $id, string $data): bool { return true; }
    public function destroy(string $id): bool { return true; }
    public function gc(int $max_lifetime): int|false { return 0; }
});
session_start();
require_once __DIR__ . '/../src/traitement/forum/traitement_publication.php';

class ConnexionPublicationsTest extends PDO
{
    public array $requetes = [];

    public function __construct()
    {
        parent::__construct('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $this->exec('PRAGMA foreign_keys = ON');
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $this->requetes[] = $query;
        return parent::prepare(str_replace(' FOR UPDATE', '', $query), $options);
    }
}

function fixturePublications(): array
{
    $db = new ConnexionPublicationsTest();
    $db->exec("CREATE TABLE Utilisateur (id_utilisateur INTEGER PRIMARY KEY, nom TEXT, prenom TEXT, role TEXT);
        CREATE TABLE Canal_forum (id_canal INTEGER PRIMARY KEY, nom_canal TEXT, acces TEXT, description TEXT);
        CREATE TABLE Post_forum (id_post INTEGER PRIMARY KEY AUTOINCREMENT, titre TEXT, contenu TEXT,
            date_heure_creation TEXT, ref_canal INTEGER REFERENCES Canal_forum(id_canal) ON DELETE CASCADE,
            ref_utilisateur INTEGER REFERENCES Utilisateur(id_utilisateur));
        CREATE TABLE Reponse_forum (id_reponse INTEGER PRIMARY KEY, ref_post INTEGER REFERENCES Post_forum(id_post) ON DELETE CASCADE);
        INSERT INTO Utilisateur VALUES (1, 'Auteur', 'Alice', 'etudiant'), (2, 'Autre', 'Bob', 'etudiant'),
            (3, 'Admin', 'Claire', 'gestionnaire'), (4, 'Docteur', 'David', 'medecin'), (5, 'Partenaire', 'Eva', 'partenaire');
        INSERT INTO Canal_forum VALUES (1, 'Général', 'generale', 'Description'),
            (2, 'Étudiants', 'etudiants', 'Description'), (3, 'Médecins', 'medecins', 'Description');
        INSERT INTO Post_forum VALUES (1, 'Publication générale', 'Contenu général', '2026-11-01 12:00:00', 1, 1),
            (2, 'Publication étudiants', 'Contenu étudiants', '2026-11-02 12:00:00', 2, 1),
            (3, 'Publication médecins', 'Contenu médecins', '2026-11-03 12:00:00', 3, 4),
            (4, 'Autre publication', 'Autre contenu', '2026-11-04 12:00:00', 1, 2);
        INSERT INTO Reponse_forum VALUES (1, 2);");
    $postReflection = new ReflectionClass(PostForumRepository::class);
    $posts = $postReflection->newInstanceWithoutConstructor();
    $postReflection->getProperty('connexionBdd')->setValue($posts, $db);
    $canalReflection = new ReflectionClass(CanalForumRepository::class);
    $canaux = $canalReflection->newInstanceWithoutConstructor();
    $canalReflection->getProperty('connexionBdd')->setValue($canaux, $db);
    return [$db, $posts, $canaux];
}

if (($argv[1] ?? '') === '--scenario') {
    $config = json_decode(base64_decode($argv[2]), true, flags: JSON_THROW_ON_ERROR);
    [$testDb, $testPosts, $testCanaux] = fixturePublications();
    if (!empty($config['canal_vide'])) {
        $testDb->exec("INSERT INTO Canal_forum VALUES (4, 'Canal vide', 'generale', 'Description')");
    }
    $_SESSION = empty($config['visiteur']) ? ['utilisateur_id' => $config['user'] ?? 1,
        'role' => $config['role'] ?? 'etudiant', 'csrf_forum' => 'csrf-fictif-test'] : [];
    $_GET = $config['get'] ?? [];
    $_POST = $config['post'] ?? [];
    $_SERVER['REQUEST_METHOD'] = $config['method'] ?? 'GET';
    $_SERVER['SCRIPT_NAME'] = $config['script_name'] ?? '/GHD/public/' . $config['page'];
    http_response_code(200);
    ob_start();
    register_shutdown_function(static function () use ($testDb): void {
        $body = ob_get_clean();
        $error = error_get_last();
        echo json_encode(['status' => http_response_code(), 'body' => $body,
            'flash' => $_SESSION['flash_forum'] ?? null,
            'posts' => $testDb->query('SELECT * FROM Post_forum ORDER BY id_post')->fetchAll(PDO::FETCH_ASSOC),
            'reponses' => (int) $testDb->query('SELECT COUNT(*) FROM Reponse_forum')->fetchColumn(),
            'lectures_publications' => count(array_filter($testDb->requetes,
                static fn(string $sql): bool => str_contains($sql, 'INNER JOIN Canal_forum'))),
            'fatal' => $error && in_array($error['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR], true) ? $error['message'] : null],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    });
    $path = __DIR__ . '/../public/' . $config['page'];
    $source = file_get_contents($path);
    // Injection en mémoire uniquement, sans modifier les points d'entrée.
    $source = str_replace(['new PostForumRepository()', 'new CanalForumRepository()', '__DIR__'],
        ['$GLOBALS["testPosts"]', '$GLOBALS["testCanaux"]', var_export(dirname($path), true)], $source);
    eval('?>' . $source);
    exit;
}

$controles = 0;
function verifierPublication(bool $ok, string $message): void
{
    global $controles;
    $controles++;
    if (!$ok) {
        throw new RuntimeException('ÉCHEC : ' . $message);
    }
}

function scenarioPublication(string $page, array $config = []): array
{
    $config['page'] = $page;
    $process = proc_open([PHP_BINARY, '-d', 'display_errors=stderr', '-d', 'log_errors=0', __FILE__, '--scenario',
        base64_encode(json_encode($config, JSON_THROW_ON_ERROR))],
        [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $status = proc_close($process);
    verifierPublication($status === 0 && $stderr === '', 'Pas d’erreur PHP pour ' . $page . ': ' . $stderr);
    $resultat = json_decode($stdout, true, flags: JSON_THROW_ON_ERROR);
    verifierPublication($resultat['fatal'] === null, 'Pas d’erreur fatale');
    return $resultat;
}

[$db, $posts, $canaux] = fixturePublications();
$attendus = ['gestionnaire' => [4, 3, 2, 1], 'medecin' => [4, 3, 1], 'etudiant' => [4, 2, 1], 'partenaire' => [4, 1]];
foreach ($attendus as $role => $ids) {
    verifierPublication(array_map('intval', array_column($posts->getPublicationsVisibles($role), 'id_post')) === $ids, 'Filtrage SQL + tri : ' . $role);
    $_SESSION = ['utilisateur_id' => 1, 'role' => $role];
    foreach ([1, 2, 3] as $canalId) {
        $accessible = peutConsulterCanalForum($canaux->getCanalForum($canalId));
        $liste = $posts->getPublicationsVisibles($role, $canalId);
        verifierPublication(($liste !== []) === $accessible, 'Matrice SQL identique aux permissions du forum');
    }
}
verifierPublication($posts->getPublicationVisible(3, 'etudiant') === null, 'Détail SQL interdit masqué');
verifierPublication($posts->getPublicationVisible(999, 'etudiant') === null, 'Détail inexistant');
verifierPublication($posts->getPublicationVisible(1, 'etudiant')['auteur_prenom'] === 'Alice', 'Informations auteur jointes');
$post = new PostForum(null, 1, 1, '2026-11-10 12:00:00', 'Texte', 'Nouveau');
$posts->ajouterPostForum($post);
verifierPublication((int) $post->getIdPost() === 5, 'Identifiant généré réaffecté au modèle');
$post->setRefCanal(3);
$post->setDateHeureCreation('2099-01-01 00:00:00');
$post->setTitre('Modifié');
$posts->modifierPostForum($post);
$lu = $posts->getPostForum(5);
verifierPublication($lu->getTitre() === 'Modifié' && (int) $lu->getRefCanal() === 1
    && $lu->getDateHeureCreation() === '2026-11-10 12:00:00', 'Modification limitée au titre/contenu');
$post->setRefUtilisateur(2);
$post->setTitre('Interdit');
$posts->modifierPostForum($post);
verifierPublication($posts->getPostForum(5)->getTitre() === 'Modifié', 'SQL modification avec auteur incorrect sans effet');
foreach ([[2, 1], [1, 2]] as [$id, $user]) {
    $refus = false;
    try { $posts->supprimerPostForumAuteur($id, $user); } catch (DomainException $e) { $refus = true; }
    verifierPublication($refus && $posts->getPostForum($id) !== null && !$db->inTransaction(), 'Suppression protégée + rollback');
}
verifierPublication((int) $db->query('SELECT COUNT(*) FROM Reponse_forum')->fetchColumn() === 1, 'Réponses préservées');
$posts->supprimerPostForumAuteur(5, 1);
verifierPublication($posts->getPostForum(5) === null, 'Suppression par auteur sans réponses');
$refus = false;
try { $posts->supprimerPostForum(2); } catch (DomainException $e) { $refus = true; }
verifierPublication($refus, 'Ancienne méthode de suppression également non destructive');

$vide = validerPublication([], true);
verifierPublication(count($vide['erreurs']) === 3, 'Champs obligatoires');
verifierPublication(isset(validerPublication(['titre' => str_repeat('é', 151), 'contenu' => 'texte'], false)['erreurs']['titre']), '150 caractères, pas 150 octets');
verifierPublication(isset(validerPublication(['titre' => 'Titre', 'contenu' => str_repeat('é', 32768)], false)['erreurs']['contenu']), 'Limite TEXT en octets');
verifierPublication(isset(validerPublication(['titre' => [], 'contenu' => []], false)['erreurs']['titre']), 'Champs tableaux refusés sans warning');

foreach (array_keys($attendus) as $role) {
    $r = scenarioPublication('publications.php', ['role' => $role]);
    verifierPublication($r['status'] === 200 && str_contains($r['body'], 'Publication générale'), 'Liste accessible');
    verifierPublication(str_contains($r['body'], 'Publication médecins') === in_array(3, $attendus[$role], true), 'Liste HTML sans fuite canal médecins');
    verifierPublication(str_contains($r['body'], 'Publication étudiants') === in_array(2, $attendus[$role], true), 'Liste HTML sans fuite canal étudiants');
}
foreach (['publications.php', 'publication.php', 'creer_publication.php', 'modifier_publication.php'] as $page) {
    $r = scenarioPublication($page, ['visiteur' => true]);
    verifierPublication($r['status'] === 302 && count($r['posts']) === 4, 'Visiteur refusé : ' . $page);
}
$r = scenarioPublication('publications.php', ['get' => ['id_canal' => 1]]);
verifierPublication($r['status'] === 200 && !str_contains($r['body'], 'Publication étudiants'), 'Filtre par canal');
$r = scenarioPublication('publications.php', ['get' => ['id_canal' => 3]]);
verifierPublication($r['status'] === 403 && !str_contains($r['body'], 'Publication médecins'), 'Filtre interdit');
$r = scenarioPublication('publications.php', ['get' => ['id_canal' => 999]]);
verifierPublication($r['status'] === 404, 'Canal inexistant');
$r = scenarioPublication('publication.php', ['get' => ['id' => 1]]);
verifierPublication($r['status'] === 200 && str_contains($r['body'], 'confirmer_suppression'), 'Détail auteur + actions');
foreach ([3, 999, ['1']] as $id) {
    $r = scenarioPublication('publication.php', ['get' => ['id' => $id]]);
    verifierPublication($r['status'] === 404, 'Détail interdit/inexistant/ID invalide');
}
$r = scenarioPublication('publication.php', ['user' => 3, 'role' => 'gestionnaire', 'get' => ['id' => 1]]);
verifierPublication($r['status'] === 200 && !str_contains($r['body'], 'confirmer_suppression'), 'Gestionnaire sans actions sur autrui');
$r = scenarioPublication('creer_publication.php', ['get' => ['id_canal' => 1]]);
verifierPublication($r['status'] === 200 && str_contains($r['body'], 'csrf_forum'), 'Formulaire initial sans warnings');
$valide = ['titre' => 'Création test', 'contenu' => 'Contenu test', 'id_canal' => '1', 'csrf_forum' => 'csrf-fictif-test', 'ref_utilisateur' => '999'];
$r = scenarioPublication('creer_publication.php', ['method' => 'POST', 'post' => $valide]);
verifierPublication($r['status'] === 303 && count($r['posts']) === 5 && (int) $r['posts'][4]['ref_utilisateur'] === 1
    && $r['posts'][4]['date_heure_creation'] !== '' && $r['flash']['type'] === 'succes', 'Création avec auteur session, date et confirmation');
$r = scenarioPublication('creer_publication.php', ['method' => 'POST', 'post' => array_replace($valide, ['titre' => '', 'contenu' => '<script>test</script>'])]);
verifierPublication(count($r['posts']) === 4 && str_contains($r['body'], '&lt;script&gt;test&lt;/script&gt;')
    && !str_contains($r['body'], '<script>test</script>'), 'Validation, conservation et échappement après erreur');
foreach (['csrf_forum' => 'faux', 'id_canal' => '3'] as $champ => $valeur) {
    $r = scenarioPublication('creer_publication.php', ['method' => 'POST', 'post' => array_replace($valide, [$champ => $valeur])]);
    verifierPublication(count($r['posts']) === 4, 'Création refusée : ' . $champ);
}
$r = scenarioPublication('modifier_publication.php', ['get' => ['id' => 1]]);
verifierPublication($r['status'] === 200 && str_contains($r['body'], 'value="Publication générale"'), 'Modification préremplie');
$r = scenarioPublication('modifier_publication.php', ['method' => 'POST', 'get' => ['id' => 1],
    'post' => array_replace($valide, ['titre' => 'Titre modifié', 'id_canal' => '3', 'date_heure_creation' => '2099-01-01'])]);
verifierPublication($r['status'] === 303 && $r['posts'][0]['titre'] === 'Titre modifié'
    && (int) $r['posts'][0]['ref_utilisateur'] === 1 && (int) $r['posts'][0]['ref_canal'] === 1
    && $r['posts'][0]['date_heure_creation'] === '2026-11-01 12:00:00', 'Auteur/canal/date immuables');
foreach ([[2, 'etudiant'], [3, 'gestionnaire']] as [$user, $role]) {
    foreach (['modifier_publication.php', 'publication.php'] as $page) {
        $r = scenarioPublication($page, ['user' => $user, 'role' => $role, 'method' => 'POST', 'get' => ['id' => 1],
            'post' => array_replace($valide, ['action' => 'supprimer', 'confirmer_suppression' => '1'])]);
        verifierPublication($r['status'] === 403 && count($r['posts']) === 4 && $r['posts'][0]['titre'] === 'Publication générale', 'Autrui refusé : ' . $role . ' ' . $page);
    }
}
$r = scenarioPublication('modifier_publication.php', ['method' => 'POST', 'get' => ['id' => 1], 'post' => array_replace($valide, ['csrf_forum' => 'faux'])]);
verifierPublication($r['status'] === 403 && $r['posts'][0]['titre'] === 'Publication générale', 'Modification CSRF invalide refusée');
$suppression = ['action' => 'supprimer', 'confirmer_suppression' => '1', 'csrf_forum' => 'csrf-fictif-test'];
$r = scenarioPublication('publication.php', ['method' => 'POST', 'get' => ['id' => 1], 'post' => $suppression]);
verifierPublication($r['status'] === 303 && count($r['posts']) === 3 && $r['reponses'] === 1, 'Suppression auteur sans cascade de réponses');
$r = scenarioPublication('publication.php', ['method' => 'POST', 'get' => ['id' => 2], 'post' => $suppression]);
verifierPublication($r['status'] === 200 && count($r['posts']) === 4 && $r['reponses'] === 1 && str_contains($r['body'], 'contient des réponses'), 'Suppression contenant réponses refusée');
foreach ([['csrf_forum' => 'faux'], ['confirmer_suppression' => '']] as $patch) {
    $r = scenarioPublication('publication.php', ['method' => 'POST', 'get' => ['id' => 1], 'post' => array_replace($suppression, $patch)]);
    verifierPublication(count($r['posts']) === 4, 'Suppression exige CSRF et confirmation');
}
// Raccordement sections/publications : mêmes accès que les pages du module,
// une seule lecture SQL filtrée, aucun chargement de publications si refus.
$accesSections = ['gestionnaire' => [1, 2, 3], 'medecin' => [1, 3], 'etudiant' => [1, 2], 'partenaire' => [1]];
foreach ($accesSections as $role => $idsCanaux) {
    foreach ([1, 2, 3] as $idCanal) {
        $r = scenarioPublication('section.php', ['role' => $role, 'get' => ['id' => $idCanal]]);
        $autorise = in_array($idCanal, $idsCanaux, true);
        verifierPublication($r['status'] === ($autorise ? 200 : 403), 'Accès direct section : ' . $role . ' / ' . $idCanal);
        verifierPublication($r['lectures_publications'] === ($autorise ? 1 : 0), 'Une seule requête publications après autorisation');
        if ($autorise) {
            verifierPublication(str_contains($r['body'], '/GHD/public/creer_publication.php?id_canal=' . $idCanal), 'Lien création avec le vrai paramètre GET');
            verifierPublication(str_contains($r['body'], '/GHD/public/publication.php?id=' . $idCanal), 'Lien détail avec le vrai paramètre GET');
            verifierPublication(str_contains($r['body'], $idCanal === 3 ? 'David Docteur' : 'Alice Auteur'), 'Auteur affiché');
            verifierPublication(str_contains($r['body'], '2026-11-0' . $idCanal . ' 12:00:00'), 'Date affichée');
            verifierPublication(!str_contains($r['body'], "après l'intégration du module"), 'Texte provisoire retiré');
            verifierPublication(str_contains($r['body'], 'Modifier / supprimer') === ($role === 'gestionnaire'), 'Actions de section inchangées');
            if ($idCanal === 1) {
                verifierPublication(!str_contains($r['body'], 'Publication étudiants') && !str_contains($r['body'], 'Publication médecins'), 'Pas de publication d’un autre canal');
            }
        }
    }
}
$r = scenarioPublication('section.php', ['get' => ['id' => 4], 'canal_vide' => true]);
verifierPublication($r['status'] === 200 && str_contains($r['body'], 'Aucune publication dans cette section pour le moment.'), 'État vide de la section');
foreach ([[], ['id' => 999], ['id' => ['1']]] as $get) {
    $r = scenarioPublication('section.php', ['get' => $get]);
    verifierPublication($r['status'] === 404 && $r['lectures_publications'] === 0, 'Section absente/invalide sans requête publications');
}
$r = scenarioPublication('section.php', ['visiteur' => true, 'get' => ['id' => 1]]);
verifierPublication($r['status'] === 302 && $r['lectures_publications'] === 0, 'Visiteur redirigé avant consultation de la section');
$r = scenarioPublication('creer_publication.php', ['get' => ['id_canal' => 3]]);
verifierPublication($r['status'] === 403, 'Création directe dans un canal interdit refusée');
// Le layout partagé doit apparaître une seule fois sur les huit pages du forum.
foreach (['forum.php', 'section.php', 'creer_section.php', 'modifier_section.php',
    'publications.php', 'publication.php', 'creer_publication.php', 'modifier_publication.php'] as $page) {
    $admin = in_array($page, ['creer_section.php', 'modifier_section.php'], true);
    $r = scenarioPublication($page, ['user' => $admin ? 3 : 1, 'role' => $admin ? 'gestionnaire' : 'etudiant', 'get' => ['id' => 1]]);
    verifierPublication($r['status'] === 200, 'Page forum affichée : ' . $page);
    foreach (['html', 'head', 'body', 'main', 'header', 'footer'] as $balise) {
        verifierPublication(preg_match_all('/<' . $balise . '(?:\s|>)/i', $r['body']) === 1, 'Balise unique ' . $balise . ' : ' . $page);
        verifierPublication(substr_count($r['body'], '</' . $balise . '>') === 1, 'Fermeture unique ' . $balise . ' : ' . $page);
    }
    verifierPublication((bool) preg_match('/<body[^>]*class="[^"]*\bforum-layout\b/', $r['body']), 'Classe forum conservée dans le layout global');
    verifierPublication((bool) preg_match('~href="/GHD/public/forum.php"\s+class="header-link is-active"\s+aria-current="page"~', $r['body']), 'Forum actif dans le header');
    foreach (['variables.css', 'style.css', 'components.css', 'layout.css', 'main.js'] as $asset) {
        verifierPublication(preg_match_all('~/' . preg_quote($asset, '~') . '(?:\?[^"]*)?"~', $r['body']) === 1, 'Ressource chargée une seule fois : ' . $asset);
    }
    verifierPublication(!str_contains($r['body'], 'provisoire'), 'Aucun composant provisoire');
}
foreach (array_keys($attendus) as $role) {
    $r = scenarioPublication('forum.php', ['role' => $role]);
    foreach (['forum.php', 'evenements.php'] as $lien) {
        verifierPublication(str_contains($r['body'], 'href="/GHD/public/' . $lien . '"'), 'Navigation connectée : ' . $role . ' / ' . $lien);
    }
    verifierPublication(str_contains($r['body'], '/GHD/public/mes_candidatures.php') === ($role === 'etudiant'), 'Navigation candidatures selon rôle');
}
$r = scenarioPublication('../views/includes/header.php', ['visiteur' => true]);
verifierPublication(!str_contains($r['body'], 'href="/GHD/public/forum.php"') && !str_contains($r['body'], 'href="/GHD/public/evenements.php"'), 'Forum/Événements cachés aux visiteurs');
verifierPublication(str_contains($r['body'], '/GHD/public/connexion.php') && str_contains($r['body'], '/GHD/public/inscription.php'), 'Navigation visiteurs conservée');
$r = scenarioPublication('../views/includes/header.php', ['script_name' => '/GHD/public/evenements.php']);
verifierPublication((bool) preg_match('~href="/GHD/public/evenements.php"\s+class="header-link is-active"\s+aria-current="page"~', $r['body']), 'Navigation événements active');
echo $controles . " contrôles réussis (SQLite et sessions en mémoire ; sans test de concurrence InnoDB).\n";
