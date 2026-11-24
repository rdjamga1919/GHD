<?php
// Boutons « Modifier » et « Supprimer » d'un événement.
// À inclure uniquement si peutGererEvenement() est vrai.
// Variable attendue : $idEvenementActions (int)
// « Supprimer » ouvre la page de confirmation (la suppression se fait ensuite par POST avec CSRF).
?>
<div class="actions-evenement" style="display: flex; gap: 12px; flex-wrap: wrap; margin: 20px 0;">
    <a class="btn" href="evenement_modifier.php?id=<?= (int) $idEvenementActions ?>">Modifier</a>
    <a class="btn btn-danger" href="evenement_supprimer.php?id=<?= (int) $idEvenementActions ?>">Supprimer</a>
</div>