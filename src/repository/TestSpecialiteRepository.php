<?php
/**
 * Tests fonctionnels du repository (point 6 du ticket), en ligne de commande :
 *     php tests/test_specialite_repository.php
 * Utilise la base configurée dans Bdd.php. Les données créées sont supprimées à la fin.
 * A lancer sur une base de TEST, pas sur la production.
 */

if (PHP_SAPI !== 'cli') {
    exit("Script réservé à la ligne de commande.\n");
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../src/repository/SpecialiteRepository.php';

$repo = new SpecialiteRepository();
$echecs = 0;

function verifier($nom, $condition)
{
    global $echecs;
    echo ($condition ? "[OK]   " : "[ECHEC] ") . $nom . "\n";
    if (!$condition) {
        $echecs++;
    }
}

function attendreException(callable $fn)
{
    try {
        $fn();
    } catch (SpecialiteException $e) {
        return $e->getMessage();
    }
    return null;
}

$suffixe = uniqid();
$libelle = "TEST_SPEC_" . $suffixe;

// Afficher les spécialités existantes
verifier("findAll() retourne un tableau", is_array($repo->findAll()));

// Créer une spécialité valide
$s = new Specialite(null, $libelle);
$repo->create($s);
verifier("create() : ID généré", !empty($s->getIdSpecialite()));
$id = $s->getIdSpecialite();
$lue = $repo->findById($id);
verifier("Vérification en BDD après création", $lue !== null && $lue->getLibelle() === $libelle);

// Refuser un nom vide
verifier("Nom vide refusé (création)", attendreException(function () use ($repo) {
        $repo->create(new Specialite(null, "   "));
    }) !== null);

// Doublon
verifier("Doublon refusé (création)", attendreException(function () use ($repo, $libelle) {
        $repo->create(new Specialite(null, $libelle));
    }) !== null);

// Modifier une spécialité
$nouveau = $libelle . "_MODIF";
$repo->update(new Specialite($id, $nouveau));
verifier("Vérification en BDD après modification", $repo->findById($id)->getLibelle() === $nouveau);

verifier("Modification vers un nom vide refusée", attendreException(function () use ($repo, $id) {
        $repo->update(new Specialite($id, ""));
    }) !== null);

// Identifiant inexistant
verifier("findById(id inexistant) = null", $repo->findById(999999999) === null);
verifier("update(id inexistant) refusé", attendreException(function () use ($repo) {
        $repo->update(new Specialite(999999999, "X"));
    }) !== null);
verifier("delete(id inexistant) refusé", attendreException(function () use ($repo) {
        $repo->delete(999999999);
    }) !== null);

// Supprimer une spécialité non utilisée
verifier("Spécialité neuve non utilisée", $repo->estUtilisee($id) === false);
$repo->delete($id);
verifier("Suppression effective en BDD", $repo->findById($id) === null);

// Tenter de supprimer une spécialité liée à un médecin
// Données temporaires : Utilisateur(role medecin) -> Medecin -> Posseder -> Specialite
$pdo = (new Bdd())->getConnexionBdd();
$liee = new Specialite(null, "TEST_LIEE_" . $suffixe);
$repo->create($liee);
$idLiee = $liee->getIdSpecialite();

$pdo->prepare(
    "INSERT INTO Utilisateur (nom, prenom, email, mot_de_passe, role, est_valide)
     VALUES ('Test', 'Medecin', :email, 'x', 'medecin', 1)"
)->execute(['email' => "test_" . $suffixe . "@example.test"]);
$idUtilisateur = $pdo->lastInsertId();

$pdo->prepare("INSERT INTO Medecin (numero_rpps, ref_utilisateur) VALUES (:rpps, :u)")
    ->execute(['rpps' => substr($suffixe, 0, 20), 'u' => $idUtilisateur]);
$idMedecin = $pdo->lastInsertId();

$pdo->prepare("INSERT INTO Posseder (ref_medecin, ref_specialite) VALUES (:m, :s)")
    ->execute(['m' => $idMedecin, 's' => $idLiee]);

verifier("estUtilisee() détecte le médecin rattaché", $repo->estUtilisee($idLiee) === true);

$message = attendreException(function () use ($repo, $idLiee) {
    $repo->delete($idLiee);
});
verifier("Suppression d'une spécialité liée refusée", $message !== null);
verifier("La spécialité liée existe toujours", $repo->findById($idLiee) !== null);

$stmt = $pdo->prepare("SELECT COUNT(*) FROM Posseder WHERE ref_medecin = :m AND ref_specialite = :s");
$stmt->execute(['m' => $idMedecin, 's' => $idLiee]);
verifier("L'association du médecin n'a pas été supprimée (CASCADE évité)", (int) $stmt->fetchColumn() === 1);

// Nettoyage : supprimer l'utilisateur supprime Medecin et Posseder (CASCADE), puis la spécialité
$pdo->prepare("DELETE FROM Utilisateur WHERE id_utilisateur = :u")->execute(['u' => $idUtilisateur]);
$repo->delete($idLiee);
verifier("Spécialité de test supprimée après détachement", $repo->findById($idLiee) === null);

// Collation utf8mb4_unicode_ci : un doublon ne tient ni à la casse ni aux accents
verifier("Doublon insensible à la casse (ex. « cardiologue »)", attendreException(function () use ($repo) {
        $repo->create(new Specialite(null, "cardiologue"));
    }) !== null);

verifier("Libellé de plus de 100 caractères refusé", attendreException(function () use ($repo) {
        $repo->create(new Specialite(null, str_repeat("a", 101)));
    }) !== null);

echo "\n" . ($echecs === 0 ? "Tous les tests sont passés.\n" : "$echecs test(s) en échec.\n");
exit($echecs === 0 ? 0 : 1);