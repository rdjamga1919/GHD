<?php if ($flash !== null): ?>
    <p class="message message-<?= $flash[0] === 'succes' ? 'succes' : 'erreur' ?>">
        <?= htmlspecialchars($flash[1]) ?>
    </p>
<?php endif; ?>

<?php if ($medecin !== null): ?>

    <h2>Modifier mon profil</h2>

    <h3>Mes spécialités</h3>
    <?php if (empty($specialites)): ?>
        <p>Aucune spécialité renseignée.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($specialites as $s): ?>
                <li>
                    <?= htmlspecialchars($s['libelle_specialite']) ?>
                    <form method="post" class="form-retrait">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                        <input type="hidden" name="action" value="retirer_specialite">
                        <input type="hidden" name="id" value="<?= (int) $s['id_specialite'] ?>">
                        <button type="submit">Retirer</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (empty($specialitesDispo)): ?>
        <p>Aucune autre spécialité disponible.</p>
    <?php else: ?>
        <form method="post">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="ajouter_specialite">
            <select name="id" required>
                <option value="">-- Choisir une spécialité --</option>
                <?php foreach ($specialitesDispo as $s): ?>
                    <option value="<?= (int) $s['id_specialite'] ?>">
                        <?= htmlspecialchars($s['libelle_specialite']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Ajouter</button>
        </form>
    <?php endif; ?>

    <h3>Mes hôpitaux d'exercice</h3>
    <?php if (empty($hopitaux)): ?>
        <p>Aucun hôpital renseigné.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($hopitaux as $h): ?>
                <li>
                    <?= htmlspecialchars($h['nom_hopital']) ?> (<?= htmlspecialchars($h['commune']) ?>)
                    <form method="post" class="form-retrait">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                        <input type="hidden" name="action" value="retirer_hopital">
                        <input type="hidden" name="id" value="<?= (int) $h['id_hopital'] ?>">
                        <button type="submit">Retirer</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (empty($hopitauxDispo)): ?>
        <p>Aucun autre hôpital disponible.</p>
    <?php else: ?>
        <form method="post">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="ajouter_hopital">
            <select name="id" required>
                <option value="">-- Choisir un hôpital --</option>
                <?php foreach ($hopitauxDispo as $h): ?>
                    <option value="<?= (int) $h['id_hopital'] ?>">
                        <?= htmlspecialchars($h['nom_hopital']) ?> (<?= htmlspecialchars($h['commune']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Ajouter</button>
        </form>
    <?php endif; ?>

    <h3>Je suis professeur dans…</h3>
    <?php if (empty($etablissements)): ?>
        <p>Vous n'êtes rattaché à aucun établissement d'enseignement.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($etablissements as $e): ?>
                <li>
                    <?= htmlspecialchars($e['nom_etablissement']) ?>
                    <?php if (!empty($e['adresse'])): ?>
                        (<?= htmlspecialchars($e['adresse']) ?>)
                    <?php endif; ?>
                    <form method="post" action="../src/traitement/traitement_enseigner.php" class="form-retrait">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id_etablissement" value="<?= (int) $e['id_etablissement'] ?>">
                        <button type="submit">Retirer</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h4>Ajouter un établissement</h4>
    <form method="get" action="modifier_profil.php">
        <input type="text" name="q" value="<?= htmlspecialchars($recherche) ?>" placeholder="Nom de l'établissement">
        <button type="submit">Rechercher</button>
    </form>

    <?php if ($recherche !== ''): ?>
        <?php if (empty($resultats)): ?>
            <p>Aucun établissement trouvé pour « <?= htmlspecialchars($recherche) ?> ».</p>
            <!-- À brancher sur la page de création d'établissement de Serge (ne pas dupliquer son formulaire) -->
            <p><a href="etablissement.php">Votre établissement n'existe pas ? Le créer</a></p>
        <?php else: ?>
            <ul>
                <?php foreach ($resultats as $e): ?>
                    <li>
                        <?= htmlspecialchars($e->getNom()) ?>
                        <form method="post" action="../src/traitement/traitement_enseigner.php" class="form-retrait">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                            <input type="hidden" name="action" value="ajouter">
                            <input type="hidden" name="id_etablissement" value="<?= (int) $e->getIdEtablissement() ?>">
                            <button type="submit">Ajouter</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>

    <p><a href="profil.php">Retour à mon profil</a></p>

<?php endif; ?>