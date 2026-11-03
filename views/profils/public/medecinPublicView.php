<?php
// Vue publique d'un profil médecin - GHD-HSP
// Variables attendues : $utilisateur, $medecin, $specialites, $hopitaux, $etablissements
// Données volontairement non affichées : email, n° RPPS, mot de passe.
?>
<div class="page-header">
    <h1><?= htmlspecialchars($medecin->getPrenom() . ' ' . $medecin->getNom(), ENT_QUOTES, 'UTF-8') ?></h1>
    <span class="tag tag--medecin">Médecin</span>
</div>

<div class="card">
    <h2>Spécialités</h2>
    <?php if (empty($specialites)): ?>
        <p>Aucune spécialité renseignée.</p>
    <?php else: ?>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <?php foreach ($specialites as $s): ?>
                <span class="tag tag--medecin"><?= htmlspecialchars($s['libelle_specialite'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Hôpitaux d'exercice</h2>
    <?php if (empty($hopitaux)): ?>
        <p>Aucun hôpital renseigné.</p>
    <?php else: ?>
        <ul class="item-list">
            <?php foreach ($hopitaux as $h): ?>
                <li class="item-row">
                    <strong><?= htmlspecialchars($h['nom_hopital'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <small><?= htmlspecialchars($h['commune'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Établissements d'enseignement</h2>
    <?php if (empty($etablissements)): ?>
        <p>Aucun établissement d'enseignement renseigné.</p>
    <?php else: ?>
        <ul class="item-list">
            <?php foreach ($etablissements as $e): ?>
                <li class="item-row">
                    <strong><?= htmlspecialchars($e['nom_etablissement'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <?php if (!empty($e['adresse'])): ?>
                        <small><?= htmlspecialchars($e['adresse'], ENT_QUOTES, 'UTF-8') ?></small>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>