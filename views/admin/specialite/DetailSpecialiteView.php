<?php
/**
 * Variables définies par public/admin/gestion_specialites.php avant l'inclusion de cette vue.
 *
 * @var string     $pageTitle
 * @var Specialite $specialite
 * @var int        $nbMedecins  nombre de rattachements existants
 */
require __DIR__ . '/../../header.php'; ?>
    <h1><?= specHtml($specialite->getLibelle()) ?></h1>

    <dl>
        <dt>Identifiant</dt>
        <dd><?= specHtml($specialite->getIdSpecialite()) ?></dd>

        <dt>Libellé</dt>
        <dd><?= specHtml($specialite->getLibelle()) ?></dd>

        <dt>Médecins rattachés</dt>
        <dd><?= specHtml($nbMedecins) ?></dd>
    </dl>

<?php if ($nbMedecins > 0): ?>
    <p>Cette spécialité ne peut pas être supprimée tant que des médecins y sont rattachés.</p>
<?php endif; ?>

    <p>
        <a href="gestion_specialites.php?action=modifier&amp;id=<?= urlencode((string) $specialite->getIdSpecialite()) ?>">Modifier</a>
        <a href="gestion_specialites.php">Retour à la liste</a>
    </p>

<?php require __DIR__ . '/../../footer.php'; ?>