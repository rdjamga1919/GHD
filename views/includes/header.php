<?php
$pageTitle = $pageTitle ?? 'GHD-HSP';
$activePage = $activePage ?? match (basename($_SERVER['SCRIPT_NAME'] ?? '')) {
    'evenements.php', 'evenement.php', 'evenement_detail.php', 'evenement_creer.php',
    'evenement_modifier.php', 'evenement_supprimer.php' => 'evenements',
    default => '',
};

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$routes = require __DIR__ . '/../../config/routes.php';

$connecte = isset($_SESSION['utilisateur_id']);
$role = $_SESSION['role'] ?? null;

$liens = [
        ['accueil', 'Accueil', 'tous'],
        ['annuaire', 'Annuaire', 'connecte'],
        ['evenements', 'Événements', 'connecte'],
        ['forum', 'Forum', 'connecte'],
        ['offres', 'Offres', 'connecte'],
        ['candidatures', 'Mes candidatures', ['etudiant']],
        ['profil', 'Mon profil', 'connecte'],
        ['connexion', 'Connexion', 'visiteur'],
        ['inscription', 'Inscription', 'visiteur'],
        ['deconnexion', 'Déconnexion', 'connecte'],
];

$estVisible = function ($visibilite) use ($connecte, $role) {
    if ($visibilite === 'tous') return true;
    if ($visibilite === 'visiteur') return !$connecte;
    if ($visibilite === 'connecte') return $connecte;

    return $connecte && in_array($role, (array) $visibilite, true);
};
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - GHD-HSP</title>

    <link rel="stylesheet" href="/GHD/public/assets/css/variables.css">
    <link rel="stylesheet" href="/GHD/public/assets/css/style.css">
    <link rel="stylesheet" href="/GHD/public/assets/css/components.css">
    <link rel="stylesheet" href="/GHD/public/assets/css/layout.css">
    <script src="/GHD/public/assets/js/main.js" defer></script>
</head>
<body class="<?= htmlspecialchars($bodyClass ?? '', ENT_QUOTES, 'UTF-8') ?>">

<header class="site-header">
    <div class="header-container">

        <a href="<?= htmlspecialchars($routes['accueil']) ?>" class="site-logo">
            <span class="logo-icon">+</span>
            <span>GHD-HSP</span>
        </a>

        <nav class="header-nav" aria-label="Navigation principale">
            <?php foreach ($liens as [$cle, $libelle, $visibilite]): ?>
                <?php if (!empty($routes[$cle]) && $estVisible($visibilite)): ?>
                    <a href="<?= htmlspecialchars($routes[$cle]) ?>"
                       class="header-link <?= $activePage === $cle ? 'is-active' : '' ?>"
                       <?= $activePage === $cle ? 'aria-current="page"' : '' ?>>
                        <?= htmlspecialchars($libelle) ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>

    </div>
</header>

<main id="main-content">