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
    <title>Annuaire – GHD-HSP</title>
    <link rel="stylesheet" href="variables.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
</head>

<body>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Annuaire GHD</h1>
        <p class="lead">Retrouvez les membres du réseau hospitalo-universitaire.</p>
    </div>

    <div class="card">
        <form method="GET" action="annuaire.php" style="display: flex; gap: var(--space-md); flex-wrap: wrap; align-items: center;">
            <input
                    type="text"
                    name="recherche"
                    class="form-control"
                    placeholder="Rechercher un nom ou prénom"
                    value="<?= htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8') ?>"
                    style="flex: 1; min-width: 220px;"
            >

            <select name="role" class="form-control" style="flex: 1; min-width: 220px;">
                <option value="">Tous les profils</option>
                <option value="etudiant" <?= $role === 'etudiant' ? 'selected' : '' ?>>Étudiants</option>
                <option value="medecin" <?= $role === 'medecin' ? 'selected' : '' ?>>Médecins</option>
                <option value="partenaire" <?= $role === 'partenaire' ? 'selected' : '' ?>>Partenaires</option>
            </select>

            <button type="submit" class="btn">Rechercher</button>
            <a href="annuaire.php" class="btn btn-secondary">Réinitialiser</a>
        </form>
    </div>

    <p style="margin: 0; font-weight: 600; color: var(--navy);"><?= count($resultats) ?> résultat(s)</p>

    <div class="table-wrap">
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
                <?php
                $roleSlug = strtolower($utilisateur->getRole());
                ?>
                <tr>
                    <td><?= htmlspecialchars($utilisateur->getNom(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($utilisateur->getPrenom(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="tag tag--<?= $roleSlug ?>">
                            <?= htmlspecialchars(ucfirst($utilisateur->getRole()), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($resultats)): ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 30px;">Aucun utilisateur trouvé.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 10px;">
        <a href="profil.php" class="btn btn-secondary">Retour à mon profil</a>
    </div>
</div>

</body>
</html>