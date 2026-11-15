<?php
/*
 * Vue : formulaire pour répondre à une publication.
 * Variables attendues : $idPost (id de la publication) et $contenu (texte déjà saisi, peut être vide)
 */
?>
<form class="card" method="post" action="<?= URL_BASE ?>/src/traitement/forum/traitement_reponse.php">
    <?= champCsrf() ?>
    <input type="hidden" name="action" value="creer">
    <input type="hidden" name="id_post" value="<?= (int) $idPost ?>">

    <label for="contenu">Votre réponse</label>
    <textarea id="contenu" name="contenu" rows="6" maxlength="2000" required><?= htmlspecialchars($contenu) ?></textarea>

    <button type="submit" class="btn">Publier la réponse</button>
</form>