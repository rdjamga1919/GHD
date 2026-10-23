<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $offre !== null ? htmlspecialchars($offre['titre']) : 'Offre introuvable' ?></title>
</head>
<body>

<?php if ($flash): ?>
    <p class="message message-<?= $flash[0] === 'succes' ? 'succes' : 'erreur' ?>"><?= htmlspecialchars($flash[1]) ?></p>
<?php endif; ?>

<?php if ($offre === null): ?>

    <h2>Offre introuvable</h2>
    <p class="message message-erreur">Cette offre n'existe pas ou n'est plus disponible.</p>

<?php else: ?>
    <?php $ouverte = ($offre['etat'] === 'ouverte'); ?>

    <h2><?= htmlspecialchars($offre['titre']) ?></h2>
    <p class="offre <?= $ouverte ? 'offre-ouverte' : 'offre-fermee' ?>">
        <span class="badge-etat"><?= $ouverte ? 'Offre ouverte' : 'Offre fermée' ?></span>
    </p>

    <p>Entreprise : <?= htmlspecialchars($offre['nom_entreprise'] ?? 'Non renseignée') ?></p>
    <?php if (!empty($offre['adresse'])): ?>
        <p>Adresse : <?= htmlspecialchars($offre['adresse']) ?></p>
    <?php endif; ?>
    <?php if (!empty($offre['site_web'])): ?>
        <p>Site web : <?= htmlspecialchars($offre['site_web']) ?></p>
    <?php endif; ?>

    <p>Type : <?= htmlspecialchars(ucfirst($offre['type_d_offre'])) ?></p>
    <p>Publiée le <?= htmlspecialchars(date('d/m/Y', strtotime($offre['date_publication']))) ?></p>
    <?php if ($offre['salaire'] !== null): ?>
        <p>Rémunération : <?= htmlspecialchars(number_format((float) $offre['salaire'], 2, ',', ' ')) ?> €</p>
    <?php endif; ?>

    <h3>Description</h3>
    <p><?= nl2br(htmlspecialchars($offre['description'])) ?></p>

    <h3>Missions</h3>
    <p><?= nl2br(htmlspecialchars($offre['missions'])) ?></p>

    <?php if ($estProprietaire && $ouverte): ?>
        <p><a href="modifier_offre.php?id=<?= (int) $offre['id_offre'] ?>">Modifier l'offre</a></p>
        <form method="post" action="../src/traitement/traitement_offre.php">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="fermer">
            <input type="hidden" name="id_offre" value="<?= (int) $offre['id_offre'] ?>">
            <button type="submit">Fermer l'offre</button>
        </form>
    <?php endif; ?>

<?php endif; ?>

<p><a href="offres.php">Retour aux offres</a></p>

</body>
</html>