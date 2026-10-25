<?php
session_start();
require_once __DIR__ . '/../bdd/Bdd.php';

function echec(string $message): void
{
    $_SESSION['erreur_connexion'] = $message;
    header('Location: /GHD/public/connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /GHD/public/connexion.php');
    exit;
}

$email = $_POST['email'] ?? '';
$motDePasse = $_POST['mot_de_passe'] ?? '';

if (!is_string($email) || !is_string($motDePasse)) {
    echec('Email ou mot de passe incorrect.');
}
$email = trim($email);

if ($email === '' || $motDePasse === '') {
    echec('Veuillez remplir tous les champs.');
}

try {
    $bdd = new Bdd();
    $pdo = $bdd->getConnexionBdd();

    $stmt = $pdo->prepare(
        'SELECT id_utilisateur, email, mot_de_passe, role, est_valide
         FROM Utilisateur WHERE email = :email'
    );
    $stmt->execute(['email' => $email]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    error_log('[GDH][connexion] Echec de connexion ou de lecture BDD.');
    echec('Connexion momentanément indisponible. Veuillez réessayer.');
}

if (!$utilisateur || !password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
    echec('Email ou mot de passe incorrect.');
}

if ((int) $utilisateur['est_valide'] !== 1) {
    echec("Votre compte n'a pas encore été validé.");
}

session_regenerate_id(true);
$_SESSION['utilisateur_id'] = (int) $utilisateur['id_utilisateur'];
$_SESSION['role'] = $utilisateur['role'];

// Les espaces des autres rôles restent provisoirement sur la page existante.
$destination = $utilisateur['role'] === 'gestionnaire'
    ? '/GHD/public/admin/GestionUtilisateurs.php'
    : '/GHD/public/profil.php';
header('Location: ' . $destination);
exit;
