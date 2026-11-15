<?php
/**
 * Variables définies par public/admin/gestion_specialites.php avant l'inclusion de cette vue.
 *
 * @var string       $pageTitle
 * @var Specialite[] $specialites
 * @var array        $nbMedecins        [id_specialite => nombre de rattachements]
 * @var array|null   $flash             ['type' => 'succes'|'erreur', 'message' => string]
 * @var string|null  $erreurChargement
 */
require __DIR__ . '/../../header.php'; ?>
    <h1>Spécialités médicales</h1>

<?php if (!empty($flash)): ?>
    <p role="alert" class="message message-<?= specHtml($flash['type']) ?>">
        <?= specHtml($flash['message']) ?>
    </p>
<?php endif; ?>

<?php if (!empty($erreurChargement)): ?>
    <p role="alert" class="message message-erreur"><?= specHtml($erreurChargement) ?></p>
<?php endif; ?>

    <p><a href="gestion_specialites.php?action=creer">Ajouter une spécialité</a></p>

<?php if (empty($specialites)): ?>
    <p>Aucune spécialité n'est enregistrée pour le moment.</p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Libellé</th>
            <th scope="col">Médecins rattachés</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($specialites as $specialite): ?>
            <?php $idLigne = $specialite->getIdSpecialite(); ?>
            <tr>
                <td><?= specHtml($idLigne) ?></td>
                <td><?= specHtml($specialite->getLibelle()) ?></td>
                <td><?= specHtml($nbMedecins[$idLigne] ?? 0) ?></td>
                <td>
                    <a href="gestion_specialites.php?action=voir&amp;id=<?= urlencode((string) $idLigne) ?>">Consulter</a>
                    <a href="gestion_specialites.php?action=modifier&amp;id=<?= urlencode((string) $idLigne) ?>">Modifier</a>

                    <form method="post" action="gestion_specialites.php" style="display:inline"
                          onsubmit="return confirm(<?= specHtml(json_encode('Supprimer la spécialité « ' . $specialite->getLibelle() . ' » ? Cette action est définitive.', JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>);">
                        <input type="hidden" name="csrf_token" value="<?= specHtml(specJetonCsrf()) ?>">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id_specialite" value="<?= specHtml($idLigne) ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../../footer.php'; ?>