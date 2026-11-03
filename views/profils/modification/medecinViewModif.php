<?php
// Vue de modification du profil médecin - GHD-HSP
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mon profil médecin – GHD-HSP</title>
    <link rel="stylesheet" href="variables.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
</head>
<body>

<div class="container">
    <?php if ($flash !== null): ?>
        <div class="alert alert-<?= $flash[0] === 'succes' ? 'success' : 'error' ?>">
            <div class="alert-body">
                <span class="alert-text"><?= htmlspecialchars($flash[1]) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($medecin !== null): ?>

        <div class="page-header">
            <h2 class="page-title">Modifier mon profil</h2>
        </div>

        <!-- Spécialités -->
        <div class="card">
            <h3 class="card-title">Mes spécialités</h3>
            <?php if (empty($specialites)): ?>
                <p class="lead">Aucune spécialité renseignée.</p>
            <?php else: ?>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                    <?php foreach ($specialites as $s): ?>
                        <li class="split" style="padding: 10px 14px; background: var(--surface-soft); border: 1px solid var(--border); border-radius: var(--radius-md);">
                            <span class="split-text"><?= htmlspecialchars($s['libelle_specialite']) ?></span>
                            <form method="post" style="margin: 0;">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                <input type="hidden" name="action" value="retirer_specialite">
                                <input type="hidden" name="id" value="<?= (int) $s['id_specialite'] ?>">
                                <button type="submit" class="btn" style="background: var(--danger); border-color: var(--danger); min-height: 32px; padding: 4px 12px; font-size: 12px;">Retirer</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (!empty($specialitesDispo)): ?>
                <form method="post" style="display: flex; gap: var(--space-md); align-items: flex-end; margin-top: 10px;">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                    <input type="hidden" name="action" value="ajouter_specialite">
                    <div class="form-group" style="flex: 1;">
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
            <h3 class="card-title">Mes hôpitaux d'exercice</h3>
            <?php if (empty($hopitaux)): ?>
                <p class="lead">Aucun hôpital renseigné.</p>
            <?php else: ?>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                    <?php foreach ($hopitaux as $h): ?>
                        <li class="split" style="padding: 10px 14px; background: var(--surface-soft); border: 1px solid var(--border); border-radius: var(--radius-md);">
                            <span class="split-text"><strong><?= htmlspecialchars($h['nom_hopital']) ?></strong> (<?= htmlspecialchars($h['commune']) ?>)</span>
                            <form method="post" style="margin: 0;">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                <input type="hidden" name="action" value="retirer_hopital">
                                <input type="hidden" name="id" value="<?= (int) $h['id_hopital'] ?>">
                                <button type="submit" class="btn" style="background: var(--danger); border-color: var(--danger); min-height: 32px; padding: 4px 12px; font-size: 12px;">Retirer</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (!empty($hopitauxDispo)): ?>
                <form method="post" style="display: flex; gap: var(--space-md); align-items: flex-end; margin-top: 10px;">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                    <input type="hidden" name="action" value="ajouter_hopital">
                    <div class="form-group" style="flex: 1;">
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
            <h3 class="card-title">Établissements d'enseignement</h3>
            <?php if (empty($etablissements)): ?>
                <p class="lead">Vous n'êtes rattaché à aucun établissement d'enseignement.</p>
            <?php else: ?>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                    <?php foreach ($etablissements as $e): ?>
                        <li class="split" style="padding: 10px 14px; background: var(--surface-soft); border: 1px solid var(--border); border-radius: var(--radius-md);">
                            <span class="split-text">
                                <strong><?= htmlspecialchars($e['nom_etablissement']) ?></strong>
                                <?php if (!empty($e['adresse'])): ?>
                                    <span class="caption">(<?= htmlspecialchars($e['adresse']) ?>)</span>
                                <?php endif; ?>
                            </span>
                            <form method="post" action="../src/traitement/traitement_enseigner.php" style="margin: 0;">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id_etablissement" value="<?= (int) $e['id_etablissement'] ?>">
                                <button type="submit" class="btn" style="background: var(--danger); border-color: var(--danger); min-height: 32px; padding: 4px 12px; font-size: 12px;">Retirer</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div style="margin-top: 10px;">
                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 8px;">Ajouter un établissement</h4>
                <form method="get" action="modifier_profil.php" style="display: flex; gap: var(--space-md); align-items: flex-end;">
                    <div class="form-group" style="flex: 1;">
                        <input type="text" name="q" class="form-control" value="<?= htmlspecialchars($recherche ?? '') ?>" placeholder="Nom de l'établissement">
                    </div>
                    <button type="submit" class="btn">Rechercher</button>
                </form>
            </div>

            <?php if (($recherche ?? '') !== ''): ?>
                <div style="margin-top: 10px;">
                    <?php if (empty($resultats)): ?>
                        <?php if ($dejaAssocie ?? false): ?>
                            <p class="lead">Les établissements correspondant à cette recherche sont déjà associés à votre profil.</p>
                        <?php else: ?>
                            <p class="lead">Aucun établissement trouvé pour « <?= htmlspecialchars($recherche) ?> ».</p>
                        <?php endif; ?>
                        <p class="caption">
                            Votre établissement n'apparaît pas dans la liste ? Contactez un gestionnaire pour demander son ajout.
                        </p>
                    <?php else: ?>
                        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                            <?php foreach ($resultats as $e): ?>
                                <li class="split" style="padding: 10px 14px; background: var(--surface-soft); border: 1px solid var(--border); border-radius: var(--radius-md);">
                                    <span class="split-text"><?= htmlspecialchars($e->getNom()) ?></span>
                                    <form method="post" action="../src/traitement/traitement_enseigner.php" style="margin: 0;">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                        <input type="hidden" name="action" value="ajouter">
                                        <input type="hidden" name="id_etablissement" value="<?= (int) $e->getIdEtablissement() ?>">
                                        <button type="submit" class="btn" style="min-height: 32px; padding: 4px 12px; font-size: 12px;">Ajouter</button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <a href="profil.php" class="btn btn-secondary">← Retour à mon profil</a>
        </div>

    <?php endif; ?>
</div>

</body>
</html>