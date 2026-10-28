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
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>

<body>

<main class="container">

    <h1 class="page-title">Annuaire GHD</h1>

    <p>Retrouvez les membres du réseau hospitalo-universitaire.</p>

    <form class="card" method="GET" action="annuaire.php">

        <div class="form-group">
            <label for="recherche">Nom ou prénom</label>
            <input
                    type="text"
                    id="recherche"
                    name="recherche"
                    class="form-control"
                    placeholder="Rechercher un nom ou prénom"
                    value="<?= htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <div class="form-group">
            <label for="role">Profil</label>
            <select id="role" name="role" class="form-control">
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
        </div>

        <button type="submit" class="btn">Rechercher</button>

        <a class="btn btn-secondary" href="annuaire.php">Réinitialiser</a>

    </form>

    <p><?= count($resultats) ?> résultat(s)</p>

    <div class="card">

        <table class="table">

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
                    <td><span class="tag"><?= htmlspecialchars(ucfirst($utilisateur->getRole()), ENT_QUOTES, 'UTF-8') ?></span></td>
                </tr>

            <?php endforeach; ?>

            <?php if (empty($resultats)): ?>

                <tr>
                    <td colspan="3">Aucun utilisateur trouvé.</td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

    <p>
        <a class="btn btn-secondary" href="profil.php">Retour à mon profil</a>
    </p>

</main>

</body>
</html>