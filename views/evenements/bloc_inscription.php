<div class="card">
    <h2>Inscription</h2>

    <p>
        Places restantes : <?= (int) $etat['places_restantes'] ?> / <?= (int) $etat['nombre_place'] ?>
    </p>

    <?php if (!$connecte): ?>

        <p>Vous devez être connecté pour vous inscrire à cet événement.</p>
        <a class="btn" href="connexion.php">Se connecter</a>

    <?php elseif (!$roleAutorise): ?>

        <p>Votre profil ne permet pas de s'inscrire à cet événement.</p>

    <?php elseif ($etat['passe']): ?>

        <p>
            <?php if ($estInscrit): ?>
                <span class="tag">Vous êtes inscrit</span>
            <?php endif; ?>
            <span class="tag">Événement terminé</span>
        </p>

    <?php elseif ($estInscrit): ?>

        <p><span class="tag">Vous êtes inscrit</span></p>

        <form method="post" action="../src/traitement/traitement_inscription_evenement.php">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="desinscrire">
            <input type="hidden" name="id_evenement" value="<?= (int) $evenement->getIdEvenement() ?>">
            <button type="submit" class="btn btn-secondary">Se désinscrire</button>
        </form>

    <?php elseif ($etat['places_restantes'] <= 0): ?>

        <p><span class="tag">Complet</span></p>

    <?php else: ?>

        <form method="post" action="../src/traitement/traitement_inscription_evenement.php">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="inscrire">
            <input type="hidden" name="id_evenement" value="<?= (int) $evenement->getIdEvenement() ?>">
            <button type="submit" class="btn">S'inscrire</button>
        </form>

    <?php endif; ?>
</div>