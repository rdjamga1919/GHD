<?php
/*
 * Vue RÉUTILISABLE : liste des réponses d'une publication.
 * Elle peut être incluse dans la page de détail d'une publication.
 *
 * Variable attendue : $reponses (tableau d'objets ReponseForum, voir findByPublication)
 * Il faut aussi avoir chargé : authentification.php et permissionsForum.php
 * et la feuille de style forum_reponses.css (la vue doit être dans un bloc .forum-reponses)
 */
?>
<section>
    <?php if (empty($reponses)): ?>

        <p class="reponses-vide">Aucune réponse pour le moment.</p>

    <?php else: ?>

        <?php foreach ($reponses as $reponse): ?>
            <article class="reponse">

                <div class="reponse-entete">
                    <strong><?= htmlspecialchars($reponse->getPrenomAuteur() . ' ' . $reponse->getNomAuteur()) ?></strong>
                    <span class="reponse-date">
                        le <?= htmlspecialchars(date('d/m/Y à H:i', strtotime($reponse->getDateHeureReponse()))) ?>
                    </span>
                </div>

                <p><?= nl2br(htmlspecialchars($reponse->getContenu())) ?></p>

                <?php if (peutModifierReponse($reponse) || peutSupprimerReponse($reponse)): ?>
                    <div class="reponse-actions">

                        <?php if (peutModifierReponse($reponse)): ?>
                            <a class="btn" href="<?= URL_BASE ?>/public/modifier_reponse.php?id_reponse=<?= (int) $reponse->getIdReponse() ?>">Modifier</a>
                        <?php endif; ?>

                        <?php if (peutSupprimerReponse($reponse)): ?>
                            <form method="post"
                                  action="<?= URL_BASE ?>/src/traitement/forum/traitement_reponse.php"
                                  onsubmit="return confirm('Voulez-vous vraiment supprimer cette réponse ?');">
                                <?= champCsrf() ?>
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id_reponse" value="<?= (int) $reponse->getIdReponse() ?>">
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

            </article>
        <?php endforeach; ?>

    <?php endif; ?>
</section>