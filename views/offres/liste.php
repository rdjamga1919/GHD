<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Offres</title>
</head>
<body>

<h2>Offres</h2>

<?php if (empty($offres)): ?>
    <p class="etat-vide">Aucune offre n'est disponible pour le moment.</p>
<?php else: ?>
    <ul class="liste-offres">
        <?php foreach ($offres as $o): ?>
            <?php $ouverte = ($o['etat'] === 'ouverte'); ?>
            <li class="offre <?= $ouverte ? 'offre-ouverte' : 'offre-fermee' ?>">
                <h3>
                    <a href="offre_detail.php?id=<?= (int) $o['id_offre'] ?>">
                        <?= htmlspecialchars($o['titre']) ?>
                    </a>
                    <span class="badge-etat">
                        <?= $ouverte ? 'Ouverte' : 'Fermée' ?>
                    </span>
                </h3>
                <p>
                    <?= htmlspecialchars($o['nom_entreprise'] ?? 'Entreprise non renseignée') ?>
                    · <?= htmlspecialchars(ucfirst($o['type_d_offre'])) ?>
                    · Publiée le <?= htmlspecialchars(date('d/m/Y', strtotime($o['date_publication']))) ?>
                </p>
                <p><?= htmlspecialchars(mb_strimwidth($o['description'], 0, 150, '…')) ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

</body>
</html>