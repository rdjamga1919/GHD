<?php if ($flash !== null): ?>
    <p style="color: <?= $flash[0] === 'succes' ? 'green' : 'red' ?>;">
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
                    <form method="post" style="display:inline;">
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
                    <form method="post" style="display:inline;">
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

    <p><a href="profil.php">Retour à mon profil</a></p>

<?php endif; ?>