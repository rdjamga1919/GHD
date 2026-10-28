
<?php

session_start();

require_once __DIR__ . '/../src/inclues/authentification.php';
require_once __DIR__ . '/../src/repository/UtilisateurRepository.php';

requireLogin();

// Récupérer tous les utilisateurs
$repo = new UtilisateurRepository();
$utilisateurs = $repo->findAll();

// Récupérer les critères de recherche
$recherche = trim($_GET['recherche'] ?? '');
$role = $_GET['role'] ?? '';

// Filtrer les utilisateurs
$resultats = [];

foreach ($utilisateurs as $utilisateur) {

    // Ne pas afficher les comptes non validés
    if (!$utilisateur->estValide()) {
        continue;
    }

    // Afficher uniquement les trois rôles de l'annuaire
    if (!in_array($utilisateur->getRole(), ['etudiant', 'medecin', 'partenaire'], true)) {
        continue;
    }

    // Filtrer par rôle
    if ($role !== '' && $utilisateur->getRole() !== $role) {
        continue;
    }

    // Rechercher dans le nom et le prénom
    $nomComplet = $utilisateur->getNom() . ' ' . $utilisateur->getPrenom();

    if ($recherche !== '' && stripos($nomComplet, $recherche) === false) {
        continue;
    }

    $resultats[] = $utilisateur;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annuaire - GHD</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
            background-color: #f8f9fa;
        }

        h1 {
            color: #263b58;
        }

        form {
            display: flex;
            gap: 10px;
            margin: 25px 0;
            flex-wrap: wrap;
        }

        input, select, button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #263b58;
            color: white;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #263b58;
            color: white;
        }

        tr:hover {
            background-color: #f1f4f8;
        }
    </style>
</head>

<body>

<h1>Annuaire GHD</h1>

<p>Retrouvez les membres du réseau hospitalo-universitaire.</p>

<form method="GET" action="annuaire.php">

    <input
        type="text"
        name="recherche"
        placeholder="Rechercher un nom ou prénom"
        value="<?= htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8') ?>"
    >

    <select name="role">
        <option value="">Tous les profils</option>

        <option value="etudiant" <?= $role === 'etudiant' ? 'selected' : '' ?>>
            Étudiants
        </option>

        <option value="medecin" <?= $role === 'medecin' ? 'selected' : '' ?>>
            Médecins
        </option>

        <option value="partenaire" <?= $role === 'partenaire' ? 'selected' : '' ?>>
            Partenaires
        </option>
    </select>

    <button type="submit">Rechercher</button>

    <a href="annuaire.php">Réinitialiser</a>

</form>

<p><?= count($resultats) ?> résultat(s)</p>

<table>

    <thead>
    <tr>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Rôle</th>
    </tr>
    </thead>

    <tbody>

    <?php foreach ($resultats as $utilisateur): ?>

        <tr>
            <td><?= htmlspecialchars($utilisateur->getNom(), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($utilisateur->getPrenom(), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars(ucfirst($utilisateur->getRole()), ENT_QUOTES, 'UTF-8') ?></td>
        </tr>

    <?php endforeach; ?>

    <?php if (empty($resultats)): ?>

        <tr>
            <td colspan="3">Aucun utilisateur trouvé.</td>
        </tr>

    <?php endif; ?>

    </tbody>

</table>

<p>
    <a href="profil.php">Retour à mon profil</a>
</p>

</body>
</html>
