<?php
// Variables attendues : $statistiques (array|null), $erreur (string|null), $accesRapides (array)
if (!function_exists('echapper')) {
    function echapper($valeur): string
    {
        return htmlspecialchars((string) $valeur, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$cartes = [
    'utilisateurs'  => 'Utilisateurs',
    'etudiants'     => 'Étudiants',
    'medecins'      => 'Médecins',
    'partenaires'   => 'Partenaires',
    'gestionnaires' => 'Gestionnaires',
    'a_valider'     => 'Comptes à valider',
    'evenements'    => 'Événements',
    'publications'  => 'Publications du forum',
    'hopitaux'      => 'Hôpitaux',
    'specialites'   => 'Spécialités',
];
?>
<section class="admin-dashboard">
    <h1 class="page-title">Tableau de bord</h1>

    <?php if ($erreur !== null || $statistiques === null): ?>
        <div class="card" role="alert">
            <p><?= echapper($erreur ?? 'Les statistiques sont momentanément indisponibles.') ?></p>
        </div>
    <?php else: ?>
        <div class="stats-grid">
            <?php foreach ($cartes as $cle => $libelle): ?>
                <div class="stat-card">
                    <span class="stat-label"><?= echapper($libelle) ?></span>
                    <span class="stat-value"><?= (int) ($statistiques[$cle] ?? 0) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h2 class="card-title">Accès rapides</h2>
    <?php if (empty($accesRapides)): ?>
        <div class="card">
            <p>Aucun module d'administration n'est encore disponible.</p>
        </div>
    <?php else: ?>
        <div class="card">
            <?php foreach ($accesRapides as $acces): ?>
                <a class="btn btn-secondary" href="<?= echapper($acces['url']) ?>"><?= echapper($acces['libelle']) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
