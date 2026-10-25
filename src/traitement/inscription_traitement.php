<?php

session_start();
require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../repository/UtilisateurRepository.php';
require_once __DIR__ . '/../repository/EtudiantRepository.php';
require_once __DIR__ . '/../repository/MedecinRepository.php';
require_once __DIR__ . '/../repository/PartenaireRepository.php';

function retourInscription(string $message, string $type = 'error'): void
{
    $_SESSION['inscription_message'] = $message;
    $_SESSION['inscription_message_type'] = $type;
    header('Location: /GHD/public/inscription.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /GHD/public/inscription.php');
    exit;
}

$csrfToken = $_POST['csrf_token'] ?? null;
$csrfSession = $_SESSION['csrf_inscription'] ?? null;
if (!is_string($csrfToken) || !is_string($csrfSession) || $csrfSession === '' ||
    !hash_equals($csrfSession, $csrfToken)) {
    retourInscription('Votre formulaire a expiré. Veuillez recharger la page.');
}

// Ne conserver que les champs non sensibles, jamais les mots de passe.
$valeurs = [];
foreach (['nom', 'prenom', 'email', 'role', 'formation', 'ref_etablissement', 'numero_rpps', 'poste', 'ref_entreprise'] as $champ) {
    if (isset($_POST[$champ]) && !is_string($_POST[$champ])) {
        retourInscription('Les données du formulaire sont invalides.');
    }
    $valeurs[$champ] = trim($_POST[$champ] ?? '');
}
$_SESSION['inscription_valeurs'] = $valeurs;
$nom = $valeurs['nom'];
$prenom = $valeurs['prenom'];
$email = $valeurs['email'];
$role = $valeurs['role'];
$motDePasse = $_POST['mot_de_passe'] ?? '';
$confirmation = $_POST['confirmation'] ?? '';
if (!is_string($motDePasse) || !is_string($confirmation)) {
    retourInscription('Les données du formulaire sont invalides.');
}
if ($nom === '' || $prenom === '' || $email === '' || $motDePasse === '' || $confirmation === '') {
    retourInscription('Veuillez remplir tous les champs obligatoires.');
}
if (!in_array($role, ['etudiant', 'medecin', 'partenaire'], true)) {
    retourInscription('Le rôle sélectionné est invalide.');
}

// Contrôler les limites SQL en caractères UTF-8, pas seulement dans le navigateur.
foreach (['nom' => 100, 'prenom' => 100, 'email' => 150, 'formation' => 150, 'numero_rpps' => 20, 'poste' => 100] as $champ => $maximum) {
    $longueur = preg_match_all('/./us', $valeurs[$champ]);
    if ($longueur === false || $longueur > $maximum) {
        retourInscription('Un champ est invalide ou dépasse la longueur autorisée.');
    }
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    retourInscription('Adresse email invalide.');
}
$longueurMotDePasse = preg_match_all('/./us', $motDePasse);
if ($longueurMotDePasse === false || $longueurMotDePasse < 8) {
    retourInscription('Le mot de passe doit contenir au moins 8 caractères.');
}
// PASSWORD_DEFAULT utilise bcrypt sur les versions PHP de MAMP utilisées ici.
if (strlen($motDePasse) > 72 || strpos($motDePasse, "\0") !== false) {
    retourInscription('Le mot de passe doit contenir au maximum 72 octets et aucun caractère nul.');
}
if ($motDePasse !== $confirmation) {
    retourInscription('Les mots de passe ne correspondent pas.');
}

$refEtablissement = filter_var($valeurs['ref_etablissement'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$refEntreprise = filter_var($valeurs['ref_entreprise'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($role === 'etudiant' && ($valeurs['formation'] === '' || $refEtablissement === false)) {
    retourInscription('Veuillez renseigner votre formation et choisir un établissement.');
}
if ($role === 'medecin' && $valeurs['numero_rpps'] === '') {
    retourInscription('Veuillez renseigner votre numéro RPPS.');
}
if ($role === 'partenaire' && ($valeurs['poste'] === '' || $refEntreprise === false)) {
    retourInscription('Veuillez renseigner votre poste et choisir une entreprise.');
}

$pdo = null;
try {
    $pdo = (new Bdd())->getConnexionBdd();
    $utilisateurRepository = new UtilisateurRepository($pdo);
    if ($utilisateurRepository->findByEmail($email) !== null) {
        retourInscription('Cette adresse email est déjà utilisée.');
    }

    if ($role === 'etudiant') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM Etablissement WHERE id_etablissement = :id');
        $stmt->execute(['id' => $refEtablissement]);
        if ((int) $stmt->fetchColumn() === 0) {
            retourInscription('Veuillez choisir un établissement existant.');
        }
        $utilisateur = new Etudiant(null, $nom, $prenom, $email, $motDePasse, $valeurs['formation'], $refEtablissement);
        $repository = new EtudiantRepository($pdo);
    } elseif ($role === 'medecin') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM Medecin WHERE numero_rpps = :rpps');
        $stmt->execute(['rpps' => $valeurs['numero_rpps']]);
        if ((int) $stmt->fetchColumn() > 0) {
            retourInscription('Ce numéro RPPS est déjà utilisé.');
        }
        $utilisateur = new Medecin(null, $nom, $prenom, $email, $motDePasse, $valeurs['numero_rpps']);
        $repository = new MedecinRepository($pdo);
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM Entreprise WHERE id_entreprise = :id');
        $stmt->execute(['id' => $refEntreprise]);
        if ((int) $stmt->fetchColumn() === 0) {
            retourInscription('Veuillez choisir une entreprise existante.');
        }
        $utilisateur = new Partenaire(null, $nom, $prenom, $email, $motDePasse, $valeurs['poste'], $refEntreprise);
        $repository = new PartenaireRepository($pdo);
    }

    $pdo->beginTransaction();
    // insert() spécialisé crée également le parent et le hash une seule fois.
    // Les modèles spécialisés conservent est_valide = false par défaut.
    $repository->insert($utilisateur);
    $pdo->commit();
    unset($_SESSION['inscription_valeurs']);
    retourInscription('Votre compte a été créé. Il doit être validé par un gestionnaire avant votre connexion.', 'success');
} catch (Throwable $exception) {
    error_log('[GDH][inscription] Echec de la creation du compte.');
    try {
        if ($pdo !== null && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
    } catch (Throwable $erreurRollback) {
        // Signaler cet échec distinct sans masquer la gestion de l'erreur initiale.
        error_log('[GDH][inscription] Echec du rollback apres erreur de creation.');
    }
    // Les contraintes uniques/FK restent la protection finale en cas de concurrence.
    retourInscription('Impossible de créer votre compte. Vérifiez vos informations et réessayez.');
}
