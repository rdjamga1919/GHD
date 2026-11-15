<?php
/**
 * Variables définies par public/admin/gestion_specialites.php avant l'inclusion de cette vue.
 *
 * @var string      $pageTitle
 * @var string      $titre
 * @var string      $action       URL de soumission du formulaire
 * @var string      $champAction  'creer' ou 'modifier' (champ caché)
 * @var string      $libelle      valeur préremplie
 * @var string|null $erreur
 * @var string      $texteBouton
 */
require __DIR__ . '/../../header.php'; ?>
    <h1><?= specHtml($titre) ?></h1>

<?php if (!empty($erreur)): ?>
    <p role="alert" class="message message-erreur"><?= specHtml($erreur) ?></p>
<?php endif; ?>

    <form method="post" action="<?= specHtml($action) ?>">
        <input type="hidden" name="csrf_token" value="<?= specHtml(specJetonCsrf()) ?>">
        <input type="hidden" name="action" value="<?= specHtml($champAction) ?>">

        <div>
            <label for="libelle">Libellé de la spécialité</label>
            <input type="text" id="libelle" name="libelle" required maxlength="100"
                   value="<?= specHtml($libelle) ?>">
        </div>

        <button type="submit"><?= specHtml($texteBouton) ?></button>
        <a href="gestion_specialites.php">Retour à la liste</a>
    </form>

<?php require __DIR__ . '/../../footer.php'; ?>