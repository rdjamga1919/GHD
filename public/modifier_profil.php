<?php
// public/modifier_profil.php
require_once __DIR__ . '/../src/traitement/traitement_verif_session.php';

$message = $_GET['message'] ?? '';
$erreur = $_GET['erreur'] ?? '';

// src/traitement/traitement_modifier_profil.php
require_once __DIR__ . '/../bdd/Bdd.new.php'; // Ajustez selon votre chemin de connexion BDD
require_once __DIR__ . '/../repository/PartenaireRepository.php';

session_start();

// Vérification de la méthode POST et de la session utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveauPoste = trim($_POST['poste'] ?? '');
    $partenaireId = $_SESSION['utilisateur_id'] ?? $_SESSION['partenaire_id'] ?? null;

    if ($partenaireId && !empty($nouveauPoste)) {
        try {
            $partenaireRepo = new PartenaireRepository();
            // Appel de la méthode de mise à jour (adaptez le nom de la méthode selon votre Repository existant)
            $success = $partenaireRepo->mettreAJourPoste($partenaireId, $nouveauPoste);

            if ($success) {
                header('Location: ../../public/modifier_profil.php?message=Poste modifié avec succès.');
                exit();
            } else {
                header('Location: ../../public/modifier_profil.php?erreur=Erreur lors de l\'enregistrement en base de données.');
                exit();
            }
        } catch (Exception $e) {
            header('Location: ../../public/modifier_profil.php?erreur=Erreur technique.');
            exit();
        }
    } else {
        header('Location: ../../public/modifier_profil.php?erreur=Veuillez remplir tous les champs obligatoires.');
        exit();
    }
} else {
    header('Location: ../../public/modifier_profil.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier mon profil - GHD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Modifier mon poste</h1>

    <?php if (!empty($message)): ?>
        <div class="alert success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!empty($erreur)): ?>
        <div class="alert error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form action="../src/traitement/traitement_modifier_profil.php" method="POST">
        <div class="form-group">
            <label for="poste">Nouveau poste :</label>
            <input type="text" id="poste" name="poste" required>
        </div>
        <button type="submit" class="btn">Enregistrer les modifications</button>
    </form>

    <a href="profil.php">Retour au profil</a>
</div>
</body>
</html>