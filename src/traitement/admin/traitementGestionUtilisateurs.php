<?php

session_start();

if (
    !isset($_SESSION['id_utilisateur']) ||
    ($_SESSION['role'] ?? '') !== 'gestionnaire'
) {
    header('Location: ../../public/connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/admin/GestionUtilisateurs.php');
    exit;
}

$idUtilisateur = isset($_POST['id_utilisateur']) ? (int) $_POST['id_utilisateur'] : 0;
$action = $_POST['action'] ?? '';

if ($idUtilisateur <= 0 || !in_array($action, ['valider', 'refuser'], true)) {
    $_SESSION['message'] = 'Requête invalide.';
    $_SESSION['messageType'] = 'error';
    header('Location: ../../public/admin/GestionUtilisateurs.php');
    exit;
}

try {
    $database = new PDO(
        'mysql:host=localhost;dbname=gdh_hsp;charset=utf8mb4',
        'root',
        ''
    );
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    $_SESSION['message'] = 'Impossible de se connecter à la base de données.';
    $_SESSION['messageType'] = 'error';
    header('Location: ../../public/admin/GestionUtilisateurs.php');
    exit;
}

$statement = $database->prepare("
    SELECT nom, prenom
    FROM Utilisateur
    WHERE id_utilisateur = :id AND est_valide = 0
");
$statement->execute(['id' => $idUtilisateur]);
$compte = $statement->fetch();

if ($compte === false) {
    $_SESSION['message'] = "Ce compte n'existe plus ou a déjà été traité.";
    $_SESSION['messageType'] = 'error';
    header('Location: ../../public/admin/GestionUtilisateurs.php');
    exit;
}

$nomComplet = $compte['prenom'] . ' ' . $compte['nom'];

try {
    if ($action === 'valider') {
        $statement = $database->prepare("
            UPDATE Utilisateur SET est_valide = 1 WHERE id_utilisateur = :id
        ");
        $statement->execute(['id' => $idUtilisateur]);

        $_SESSION['message'] = "Le compte de {$nomComplet} a été validé.";
        $_SESSION['messageType'] = 'success';
    } else {
        $statement = $database->prepare("
            DELETE FROM Utilisateur WHERE id_utilisateur = :id
        ");
        $statement->execute(['id' => $idUtilisateur]);

        $_SESSION['message'] = "Le compte de {$nomComplet} a été refusé.";
        $_SESSION['messageType'] = 'success';
    }
} catch (PDOException $exception) {
    $_SESSION['message'] = "Une erreur est survenue lors du traitement du compte.";
    $_SESSION['messageType'] = 'error';
}

header('Location: ../../public/admin/GestionUtilisateurs.php');
exit;