<?php
/** Rendu de vues avec données fictives uniquement, sans connexion SQL. */
error_reporting(E_ALL);
set_error_handler(static function (int $level, string $message, string $file, int $line): never {
    throw new ErrorException($message, 0, $level, $file, $line);
});
session_set_save_handler(new class implements SessionHandlerInterface {
    private string $data = '';
    public function open(string $path, string $name): bool { return true; }
    public function close(): bool { return true; }
    public function read(string $id): string { return $this->data; }
    public function write(string $id, string $data): bool { $this->data = $data; return true; }
    public function destroy(string $id): bool { return true; }
    public function gc(int $max_lifetime): int|false { return 0; }
});
session_start();
$root = dirname(__DIR__);
require $root . '/src/traitement/forum/outils_forum.php';
require $root . '/src/modele/Evenement.php';
class DonneeLayoutTest {
    public function __call(string $name, array $args) {
        if (str_starts_with($name, 'getId') || str_starts_with($name, 'getRef')) return 1;
        if (str_starts_with($name, 'getDate')) return '2026-11-10 12:00:00';
        return match ($name) {
            'getRole' => 'etudiant', 'getEmail' => 'fixture@example.invalid',
            'getSiteWeb' => 'https://example.invalid', 'getNom' => 'Nom fictif',
            default => 'Valeur fictive',
        };
    }
}
$config = json_decode(base64_decode($argv[1]), true, flags: JSON_THROW_ON_ERROR);
$_SERVER['SCRIPT_NAME'] = '/GHD/public/' . $config['page'];
$_SERVER['REQUEST_METHOD'] = 'GET';
if (isset($config['breadcrumbs'])) {
    $breadcrumbs = $config['breadcrumbs'];
    require $root . '/views/includes/breadcrumbs.php';
    exit;
}
$_GET = [];
$_SESSION = ['utilisateur_id' => 1, 'role' => 'etudiant', 'csrf' => 'fixture', 'csrf_inscription' => 'fixture', 'csrf_gestion_utilisateurs' => 'fixture'];
$dummy = new DonneeLayoutTest();
$etudiant = $medecin = $partenaire = $etablissement = $entreprise = $dummy;
$etablissements = $entreprises = [$dummy];
$specialites = $hopitaux = $specialitesDispo = $hopitauxDispo = $resultats = [];
$recherche = $role = $nom = $prenom = $email = $message = $messageType = $motivation = '';
$nomSaisi = $adresseSaisie = $siteWebSaisi = '';
$flash = $erreur = $erreurChargement = $existante = null;
$erreurs = $ancien = $old = $valeurs = $candidatures = $comptesEnAttente = [];
$succes = '';
$estPartenaire = $estProprietaire = $connecte = $roleAutorise = $peutCreer = true;
$estInscrit = $dejaPostule = $estRattache = $dejaAssocie = false;
$monPartenaire = $dummy;
$idOffre = 1; $aDejaPostule = false; $partenaires = [];
$libellesRole = [];
if (!empty($config['admin'])) $_SESSION['role'] = 'gestionnaire';
$monEntrepriseId = $idEntreprise = null;
$action = 'creer'; $titrePage = 'Titre fictif'; $csrf = 'fixture'; $nbInscrits = 0;
$offre = ['id_offre'=>1,'titre'=>'Offre fictive','etat'=>'ouverte','ref_utilisateur'=>1,'description'=>'Description',
    'missions'=>'Missions','salaire'=>null,'type_d_offre'=>'stage','date_publication'=>'2026-11-10','adresse'=>'Adresse', 'site_web'=>''];
$offres = [$offre];
$evenement = new Evenement(1, '2026-12-01 12:00:00', '', 10, 'Conférence', 'Lieu', 'Description', 'Événement fictif');
$etat = ['places_restantes'=>10,'nombre_place'=>10,'passe'=>false];
$organisateurs = [];
$evenements = [['id_evenement'=>1,'titre'=>'Événement fictif','description'=>'Description','type_evenement'=>'Conférence',
    'adresse_lieu'=>'Lieu','nombre_place'=>10,'date_heure_evenement'=>'2026-12-01 12:00:00','nb_inscrits'=>0,'organisateurs'=>'Auteur fictif']];
if (!empty($config['evenement_tableau'])) $evenement = $evenements[0];
if (!empty($config['publication_fixture'])) {
    $publication = ['id_post'=>1, 'titre'=>'Publication fictive', 'ref_canal'=>1, 'nom_canal'=>'Général'];
}
if (!empty($config['canal_fixture'])) {
    require_once $root . '/src/modele/CanalForum.php';
    $canal = new CanalForum(1, 'Général', 'generale');
}

if (str_contains($config['view'] ?? '', 'medecin')) $etablissements = [];
ob_start();
if (!empty($config['public_full'])) {
    if ($config['page'] === 'admin/test_page_admin.php') session_write_close();
    require $root . '/public/' . $config['page'];
} elseif (isset($config['public_body'])) {
    $path = $root . '/public/' . $config['page'];
    $source = file_get_contents($path);
    if (!preg_match("~(?:require|require_once) __DIR__ \\. '[^']*views/layouts/header.php';~", $source, $match, PREG_OFFSET_CAPTURE)) {
        throw new RuntimeException('Point de rendu absent : ' . $path);
    }
    $start = $match[0][1];
    $fragment = '<?php ' . substr($source, $start);
    eval('?>' . str_replace('__DIR__', var_export(dirname($path), true), $fragment));
} else {
    require $root . '/views/layouts/header.php';
    if (!empty($config['view'])) require $root . '/' . $config['view'];
    require $root . '/views/layouts/footer.php';
}
$html = ob_get_clean();
echo $html;
