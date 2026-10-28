<?php
// views/layouts/navbar.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$routes = require __DIR__ . '/../../config/routes.php';

$user        = $_SESSION['user'] ?? null;
$estConnecte = $user !== null;
$estValide   = $estConnecte && !empty($user['est_valide']);
$role        = $user['role'] ?? null;

// [clé de route, libellé, visibilité]
// 'tous' | 'visiteur' | 'session' (connecté, même non validé) | 'connecte' (connecté et validé) | liste de rôles
$liens = [
    ['accueil',      'Accueil',          'tous'],
    ['annuaire',     'Annuaire',         'connecte'],
    ['offres',       'Offres',           'connecte'],
    ['evenements',   'Événements',       'connecte'],
    ['forum',        'Forum',            'connecte'],
    ['candidatures', 'Mes candidatures', ['etudiant']],
    ['mes_offres',   'Mes offres',       ['partenaire']],
    ['admin',        'Administration',   ['gestionnaire']],
    ['profil',       'Mon profil',       'session'],
    ['connexion',    'Connexion',        'visiteur'],
    ['inscription',  'Inscription',      'visiteur'],
    ['deconnexion',  'Déconnexion',      'session'],
];

$estVisible = function ($v) use ($estConnecte, $estValide, $role): bool {
    if ($v === 'tous')     return true;
    if ($v === 'visiteur') return !$estConnecte;
    if ($v === 'session')  return $estConnecte;
    if ($v === 'connecte') return $estValide;
    return $estValide && in_array($role, (array) $v, true);
};
?>
<nav aria-label="Navigation principale">
    <details>
        <summary>Menu</summary>
        <ul>
            <?php foreach ($liens as [$cle, $libelle, $visibilite]): ?>
                <?php if (!empty($routes[$cle]) && $estVisible($visibilite)): ?>
                    <li><a href="<?= htmlspecialchars($routes[$cle]) ?>"><?= htmlspecialchars($libelle) ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </details>
    <?php if ($estConnecte): ?>
        <p>Connecté : <?= htmlspecialchars($user['prenom'] ?? '') ?> (<?= htmlspecialchars((string) $role) ?>)</p>
    <?php endif; ?>
</nav>