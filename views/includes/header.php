<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/page_context.php';

$navigationRoutes = require __DIR__ . '/../../config/routes.php';

$navigationConnecte = isset($_SESSION['utilisateur_id']);
$navigationRole = $_SESSION['role'] ?? null;

$navigationLiens = [
        ['accueil', 'Accueil', 'tous'],
        ['annuaire', 'Annuaire', 'connecte'],
        ['evenements', 'Événements', 'connecte'],
        ['forum', 'Forum', 'connecte'],
        ['offres', 'Offres', 'connecte'],
        ['candidatures', 'Mes candidatures', ['etudiant']],
        ['profil', 'Mon profil', ['etudiant', 'medecin', 'partenaire']],
        ['admin', 'Administration', ['gestionnaire']],
        ['connexion', 'Connexion', 'visiteur'],
        ['inscription', 'Inscription', 'visiteur'],
        ['deconnexion', 'Déconnexion', 'connecte'],
];

$navigationVisible = function ($navigationVisibilite) use ($navigationConnecte, $navigationRole) {
    if ($navigationVisibilite === 'tous') return true;
    if ($navigationVisibilite === 'visiteur') return !$navigationConnecte;
    if ($navigationVisibilite === 'connecte') return $navigationConnecte;

    return $navigationConnecte && in_array($navigationRole, (array) $navigationVisibilite, true);
};

// Le navigateur recharge les styles lorsqu'un fichier est modifié sur MAMP.
$layoutAssetUrl = static fn(string $file): string => '/GHD/public/assets/' . $file
    . '?v=' . filemtime(__DIR__ . '/../../public/assets/' . $file);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - GHD-HSP</title>

    <link rel="stylesheet" href="<?= htmlspecialchars($layoutAssetUrl('css/variables.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($layoutAssetUrl('css/style.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($layoutAssetUrl('css/components.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($layoutAssetUrl('css/layout.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($layoutAssetUrl('css/pages.css'), ENT_QUOTES, 'UTF-8') ?>">
    <script src="<?= htmlspecialchars($layoutAssetUrl('js/main.js'), ENT_QUOTES, 'UTF-8') ?>" defer></script>
</head>
<body class="site-layout <?= htmlspecialchars($layoutBodyClass . ' ' . ($bodyClass ?? ''), ENT_QUOTES, 'UTF-8') ?>">

<header class="site-header">
    <div class="header-container">

        <a href="<?= htmlspecialchars($navigationRoutes['accueil']) ?>" class="site-logo">
            <span class="logo-icon">+</span>
            <span>GHD-HSP</span>
        </a>

        <nav class="header-nav" aria-label="Navigation principale">
            <?php foreach ($navigationLiens as [$navigationCle, $navigationLibelle, $navigationVisibilite]): ?>
                <?php if (!empty($navigationRoutes[$navigationCle]) && $navigationVisible($navigationVisibilite)): ?>
                    <a href="<?= htmlspecialchars($navigationRoutes[$navigationCle]) ?>"
                       class="header-link <?= $activePage === $navigationCle ? 'is-active' : '' ?>"
                       <?= $activePage === $navigationCle ? 'aria-current="page"' : '' ?>>
                        <?= htmlspecialchars($navigationLibelle) ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>

    </div>
</header>

<main id="main-content">
<div class="layout-content <?= $layoutVariant !== '' ? 'layout-content--' . $layoutVariant : '' ?>">
<?php require __DIR__ . '/breadcrumbs.php'; ?>
