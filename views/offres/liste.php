<?php
// Vue de la liste des offres - GHD-HSP
?>

<div class="header-flex">
    <h1 class="page-title">Offres d'emploi et stages</h1>
    <?php if ($estPartenaire): ?>
        <a href="creer_offre.php" class="btn">+ Créer une offre</a>
    <?php endif; ?>
</div>

<?php if ($flash !== null): ?>
    <div class="message message-<?= $flash[0] === 'succes' ? 'succes' : 'erreur' ?>">
        <?= htmlspecialchars($flash[1]) ?>
    </div>
<?php endif; ?>

<?php if (empty($offres)): ?>
    <div class="etat-vide">
        Aucune offre n'est disponible pour le moment.
    </div>
<?php else: ?>
    <ul class="liste-offres">
        <?php foreach ($offres as $o): ?>
            <?php $ouverte = ($o['etat'] === 'ouverte'); ?>
            <li class="card">
                <div class="offre-titre-wrapper">
                    <h3>
                        <a href="offre_detail.php?id=<?= (int) $o['id_offre'] ?>">
                            <?= htmlspecialchars($o['titre']) ?>
                        </a>
                    </h3>
                    <span class="tag <?= $ouverte ? 'tag--success' : 'tag--error' ?>">
                        <?= $ouverte ? 'Ouverte' : 'Fermée' ?>
                    </span>
                </div>
                <p class="content-strong">
                    <?= htmlspecialchars($o['nom_entreprise'] ?? 'Entreprise non renseignée') ?>
                    · <span class="text-capitalize"><?= htmlspecialchars($o['type_d_offre']) ?></span>
                    · <span class="caption">Publiée le <?= htmlspecialchars(date('d/m/Y', strtotime($o['date_publication']))) ?></span>
                </p>
                <p><?= htmlspecialchars(mb_strimwidth($o['description'], 0, 150, '…')) ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
