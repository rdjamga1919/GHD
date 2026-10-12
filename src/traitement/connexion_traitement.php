<?php
session_start();
require_once __DIR__ . '/../bdd/Bdd.new.php';

function echec(string $message): void
{
    $_SESSION['erreur_connexion'] = $message;
    header('Location: ../../public/connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/connexion.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$motDePasse = $_POST['mot_de_passe'] ?? '';

if ($email === '' || $motDePasse === '') {
    echec('Veuillez remplir tous les champs.');
}

$bdd = new Bdd();
$pdo = $bdd->getConnexionBdd();

$stmt = $pdo->prepare(
    'SELECT id_utilisateur, email, mot_de_passe, role, est_valide
     FROM utilisateur WHERE email = :email'
);
$stmt->execute(['email' => $email]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$utilisateur || !password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
    echec('Email ou mot de passe incorrect.');
}

if ((int) $utilisateur['est_valide'] !== 1) {
    echec("Votre compte n'a pas encore été validé.");
}

session_regenerate_id(true);
$_SESSION['utilisateur_id'] = $utilisateur['id_utilisateur'];
$_SESSION['role'] = $utilisateur['role'];

// Redirection : pour l'instant tous les rôles vont sur profil.php
header('Location: ../../public/profil.php');
exit;