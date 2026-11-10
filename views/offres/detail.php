<?php
// Vue de détail d'une offre - GHD-HSP
?>

<?php if ($flash !== null): ?>
    <div class="message message-<?= $flash[0] === 'succes' ? 'succes' : 'erreur' ?>">
        <?= htmlspecialchars($flash[1]) ?>
    </div>
<?php endif; ?>

<?php if ($offre === null): ?>

        <h1 class="page-title">Offre introuvable</h1>
        <div class="message message-erreur content-flush">
            Cette offre n'existe pas ou n'est plus disponible.
        </div>

    <?php else: ?>
        <?php $ouverte = ($offre['etat'] === 'ouverte'); ?>

        <div class="forum-toolbar">
            <div>
                <h1 class="page-title"><?= htmlspecialchars($offre['titre']) ?></h1>
                <p class="space-after-xs">
                    <strong>Entreprise :</strong> <?= htmlspecialchars($offre['nom_entreprise'] ?? 'Non renseignée') ?>
                </p>
            </div>
            <div>
                <span class="tag <?= $ouverte ? 'tag--success' : 'tag--error' ?>">
                    <?= $ouverte ? 'Offre ouverte' : 'Offre fermée' ?>
                </span>
            </div>
        </div>

        <div class="card detail-card">
        <div class="meta-grid space-before-md">
            <div class="meta-item">
                <span>Type d'offre</span>
                <strong><?= htmlspecialchars(ucfirst($offre['type_d_offre'])) ?></strong>
            </div>
            <div class="meta-item">
                <span>Date de publication</span>
                <strong><?= htmlspecialchars(date('d/m/Y', strtotime($offre['date_publication']))) ?></strong>
            </div>
            <?php if ($offre['salaire'] !== null): ?>
                <div class="meta-item">
                    <span>Rémunération</span>
                    <strong><?= htmlspecialchars(number_format((float) $offre['salaire'], 2, ',', ' ')) ?> €</strong>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($offre['adresse']) || !empty($offre['site_web'])): ?>
            <div class="space-after-md">
                <?php if (!empty($offre['adresse'])): ?>
                    <p class="space-after-xs"><strong>Adresse :</strong> <?= htmlspecialchars($offre['adresse']) ?></p>
                <?php endif; ?>
                <?php if (!empty($offre['site_web'])): ?>
                    <p class="space-after-xs">
                        <strong>Site web :</strong>
                        <a href="<?= htmlspecialchars($offre['site_web']) ?>" target="_blank" rel="noopener noreferrer" class="text-primary">
                            <?= htmlspecialchars($offre['site_web']) ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div>
            <h3>Description</h3>
            <p><?= nl2br(htmlspecialchars($offre['description'])) ?></p>

            <h3>Missions</h3>
            <p><?= nl2br(htmlspecialchars($offre['missions'])) ?></p>
        </div>

        <?php if ($estProprietaire): ?>
            <div class="actions-group">
                <?php if ($ouverte): ?>
                    <a href="modifier_offre.php?id=<?= (int) $offre['id_offre'] ?>" class="btn btn-ghost">Modifier l'offre</a>
                    <form method="post" action="../src/traitement/traitement_offre.php" class="content-flush">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                        <input type="hidden" name="action" value="fermer">
                        <input type="hidden" name="id_offre" value="<?= (int) $offre['id_offre'] ?>">
                        <button type="submit" class="btn btn-danger">Fermer l'offre</button>
                    </form>
                <?php endif; ?>
                <a href="candidatures_recues.php?id=<?= (int) $offre['id_offre'] ?>" class="btn">
                    Voir les candidatures reçues
                </a>
            </div>
        <?php endif; ?>

        </div>
<?php endif; ?>

<p>
    <a href="offres.php" class="btn btn-ghost">← Retour aux offres</a>
</p>
