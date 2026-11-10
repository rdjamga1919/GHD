<?php
// Vue de modification du profil médecin - GHD-HSP
?>

<?php if ($flash !== null): ?>
    <div class="message message-<?= $flash[0] === 'succes' ? 'succes' : 'erreur' ?>">
        <?= htmlspecialchars($flash[1]) ?>
    </div>
<?php endif; ?>

<?php if ($medecin !== null): ?>

    <h1 class="page-title">Modifier mon profil</h1>

    <!-- Spécialités -->
    <div class="card">
        <h3>Mes spécialités</h3>
        <?php if (empty($specialites)): ?>
            <p>Aucune spécialité renseignée.</p>
        <?php else: ?>
            <ul class="item-list">
                <?php foreach ($specialites as $s): ?>
                    <li class="item-row">
                        <span><?= htmlspecialchars($s['libelle_specialite']) ?></span>
                        <form method="post" class="form-retrait">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                            <input type="hidden" name="action" value="retirer_specialite">
                            <input type="hidden" name="id" value="<?= (int) $s['id_specialite'] ?>">
                            <button type="submit" class="btn btn-danger btn-small">Retirer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($specialitesDispo)): ?>
            <form method="post" class="form-row space-before-md">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                <input type="hidden" name="action" value="ajouter_specialite">
                <div class="form-group">
                    <select name="id" class="form-control" required>
                        <option value="">-- Choisir une spécialité --</option>
                        <?php foreach ($specialitesDispo as $s): ?>
                            <option value="<?= (int) $s['id_specialite'] ?>">
                                <?= htmlspecialchars($s['libelle_specialite']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Ajouter</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Hôpitaux d'exercice -->
    <div class="card">
        <h3>Mes hôpitaux d'exercice</h3>
        <?php if (empty($hopitaux)): ?>
            <p>Aucun hôpital renseigné.</p>
        <?php else: ?>
            <ul class="item-list">
                <?php foreach ($hopitaux as $h): ?>
                    <li class="item-row">
                        <span><strong><?= htmlspecialchars($h['nom_hopital']) ?></strong> (<?= htmlspecialchars($h['commune']) ?>)</span>
                        <form method="post" class="form-retrait">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                            <input type="hidden" name="action" value="retirer_hopital">
                            <input type="hidden" name="id" value="<?= (int) $h['id_hopital'] ?>">
                            <button type="submit" class="btn btn-danger btn-small">Retirer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($hopitauxDispo)): ?>
            <form method="post" class="form-row space-before-md">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                <input type="hidden" name="action" value="ajouter_hopital">
                <div class="form-group">
                    <select name="id" class="form-control" required>
                        <option value="">-- Choisir un hôpital --</option>
                        <?php foreach ($hopitauxDispo as $h): ?>
                            <option value="<?= (int) $h['id_hopital'] ?>">
                                <?= htmlspecialchars($h['nom_hopital']) ?> (<?= htmlspecialchars($h['commune']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Ajouter</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Établissements d'enseignement -->
    <div class="card">
        <h3>Établissements d'enseignement</h3>
        <?php if (empty($etablissements)): ?>
            <p>Vous n'êtes rattaché à aucun établissement d'enseignement.</p>
        <?php else: ?>
            <ul class="item-list">
                <?php foreach ($etablissements as $e): ?>
                    <li class="item-row">
                        <span>
                            <strong><?= htmlspecialchars($e['nom_etablissement']) ?></strong>
                            <?php if (!empty($e['adresse'])): ?>
                                <span class="caption">(<?= htmlspecialchars($e['adresse']) ?>)</span>
                            <?php endif; ?>
                        </span>
                        <form method="post" action="../src/traitement/traitement_enseigner.php" class="form-retrait">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="id_etablissement" value="<?= (int) $e['id_etablissement'] ?>">
                            <button type="submit" class="btn btn-danger btn-small">Retirer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="space-before-md">
            <h4>Ajouter un établissement</h4>
            <form method="get" action="modifier_profil.php" class="form-row">
                <div class="form-group">
                    <input type="text" name="q" class="form-control" value="<?= htmlspecialchars($recherche ?? '') ?>" placeholder="Nom de l'établissement">
                </div>
                <button type="submit" class="btn">Rechercher</button>
            </form>
        </div>

        <?php if (($recherche ?? '') !== ''): ?>
            <div class="space-before-md">
                <?php if (empty($resultats)): ?>
                    <?php if ($dejaAssocie ?? false): ?>
                        <p>Les établissements correspondant à cette recherche sont déjà associés à votre profil.</p>
                    <?php else: ?>
                        <p>Aucun établissement trouvé pour « <?= htmlspecialchars($recherche) ?> ».</p>
                    <?php endif; ?>
                    <p class="caption">
                        Votre établissement n'apparaît pas dans la liste ? Contactez un gestionnaire pour demander son ajout.
                    </p>
                <?php else: ?>
                    <ul class="item-list">
                        <?php foreach ($resultats as $e): ?>
                            <li class="item-row">
                                <span><?= htmlspecialchars($e->getNom()) ?></span>
                                <form method="post" action="../src/traitement/traitement_enseigner.php" class="form-retrait">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                    <input type="hidden" name="action" value="ajouter">
                                    <input type="hidden" name="id_etablissement" value="<?= (int) $e->getIdEtablissement() ?>">
                                    <button type="submit" class="btn btn-small">Ajouter</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <p class="space-before-lg">
        <a href="profil.php" class="btn btn-ghost">← Retour à mon profil</a>
    </p>

<?php endif; ?>
