<?php
/*
 * Vue : formulaire pour modifier une réponse.
 * Variables attendues : $idReponse (id de la réponse) et $contenu (texte à afficher dans le champ)
 */
?>
<form class="card" method="post" action="<?= URL_BASE ?>/src/traitement/forum/traitement_reponse.php">
    <?= champCsrf() ?>
    <input type="hidden" name="action" value="modifier">
    <input type="hidden" name="id_reponse" value="<?= (int) $idReponse ?>">

    <label for="contenu">Votre réponse</label>
    <textarea id="contenu" name="contenu" rows="6" maxlength="2000" required><?= htmlspecialchars($contenu) ?></textarea>

    <button type="submit" class="btn">Enregistrer les modifications</button>
</form>