<?php
// Vue de modification du profil partenaire - GHD-HSP
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mon profil partenaire – GHD-HSP</title>
    <link rel="stylesheet" href="variables.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
</head>
<body>

<div class="container" style="max-width: 600px;">
    <div class="page-header">
        <h1 class="page-title">Modifier mon profil partenaire</h1>
    </div>

    <?php if ($flash !== null): ?>
        <div class="alert alert-<?= ($flash[0] ?? '') === 'succes' ? 'success' : 'error' ?>">
            <div class="alert-body">
                <span class="alert-text"><?= htmlspecialchars($flash[1]) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <form action="modifier_profil.php" method="POST" style="display: flex; flex-direction: column; gap: var(--space-md);">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

            <div class="form-group">
                <label for="poste">Poste occupé :</label>
                <input type="text"
                       id="poste"
                       name="poste"
                       class="form-control"
                       value="<?= htmlspecialchars($partenaire->getPoste()) ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="entreprise">Entreprise :</label>
                <select name="ref_entreprise" id="entreprise" class="form-control" required>
                    <?php foreach ($entreprises as $entreprise): ?>
                        <option value="<?= (int) $entreprise->getIdEntreprise() ?>"
                                <?= $entreprise->getIdEntreprise() == $partenaire->getRefEntreprise() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($entreprise->getNom()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-block" style="margin-top: 8px;">Enregistrer les modifications</button>
        </form>
    </div>

    <div>
        <a href="profil.php" class="btn btn-secondary">← Retour à mon profil</a>
    </div>
</div>

</body>
</html>