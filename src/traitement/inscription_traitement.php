<?php

require_once '../Utilisateur.php'; // adapter le chemin selon ton projet

session_start();

/*
|--------------------------------------------------------------------------
| Connexion BDD
|--------------------------------------------------------------------------
*/
try {
    $database = new PDO(
        'mysql:host=localhost;dbname=gdh_hsp;charset=utf8mb4',
        'root',
        ''
    );

    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $exception) {
    die('Erreur de connexion à la base de données.');
}

$utilisateur = new Utilisateur($database);

/*
|--------------------------------------------------------------------------
| Traitement du formulaire
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';
    $confirmation = $_POST['confirmation'] ?? '';
    $role = $_POST['role'] ?? '';

    // Champs obligatoires
    if ($nom === '' || $prenom === '' || $email === '' ||
        $motDePasse === '' || $confirmation === '' || $role === '') {

        $_SESSION['message'] = 'Veuillez remplir tous les champs.';
        $_SESSION['messageType'] = 'error';
        header('Location: ../inscription.php');
        exit;
    }

    // Email valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = 'Adresse email invalide.';
        $_SESSION['messageType'] = 'error';
        header('Location: ../inscription.php');
        exit;
    }

    // Mot de passe
    if (strlen($motDePasse) < 8) {
        $_SESSION['message'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        $_SESSION['messageType'] = 'error';
        header('Location: ../inscription.php');
        exit;
    }

    if ($motDePasse !== $confirmation) {
        $_SESSION['message'] = 'Les mots de passe ne correspondent pas.';
        $_SESSION['messageType'] = 'error';
        header('Location: ../inscription.php');
        exit;
    }

    // Rôle valide
    if (!in_array($role, ['etudiant', 'medecin', 'partenaire'], true)) {
        $_SESSION['message'] = 'Rôle invalide.';
        $_SESSION['messageType'] = 'error';
        header('Location: ../inscription.php');
        exit;
    }

    // Email déjà utilisé
    if ($utilisateur->emailExiste($email)) {
        $_SESSION['message'] = 'Cette adresse email est déjà utilisée.';
        $_SESSION['messageType'] = 'error';
        header('Location: ../inscription.php');
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Création de l'utilisateur
    |--------------------------------------------------------------------------
    */
    try {
        // Hash du mot de passe
        $motDePasseHash = password_hash($motDePasse, PASSWORD_DEFAULT);

        // Insertion utilisateur
        $query = "
            INSERT INTO Utilisateur (nom, prenom, email, mot_de_passe, role, est_valide)
            VALUES (:nom, :prenom, :email, :mot_de_passe, :role, 0)
        ";

        $statement = $database->prepare($query);
        $statement->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'mot_de_passe' => $motDePasseHash,
            'role' => $role
        ]);

        $idUtilisateur = $database->lastInsertId();

        /*
        |--------------------------------------------------------------------------
        | Création de la fiche selon le rôle
        |--------------------------------------------------------------------------
        */
        switch ($role) {
            case 'etudiant':
                $database->prepare("INSERT INTO Etudiant (id_utilisateur) VALUES (:id)")
                    ->execute(['id' => $idUtilisateur]);
                break;

            case 'medecin':
                $database->prepare("INSERT INTO Medecin (id_utilisateur) VALUES (:id)")
                    ->execute(['id' => $idUtilisateur]);
                break;

            case 'partenaire':
                $database->prepare("INSERT INTO Partenaire (id_utilisateur) VALUES (:id)")
                    ->execute(['id' => $idUtilisateur]);
                break;
        }

        $_SESSION['message'] = 'Votre compte a été créé avec succès.';
        $_SESSION['messageType'] = 'success';

    } catch (PDOException $exception) {
        $_SESSION['message'] = 'Erreur lors de la création du compte.';
        $_SESSION['messageType'] = 'error';
    }

    header('Location: ../inscription.php');
    exit;
}
