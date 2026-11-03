<?php
// Variables attendues : $evenements (tableau), $erreur (string|null)
if (!function_exists('echapper')) {
    function echapper($valeur): string
    {
        return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
    }
}
?>
<link rel="stylesheet" href="../ressources/css/evenements.css">
<section class="page-evenements">
    <h1>Événements</h1>

    <?php if ($erreur !== null): ?>
        <p class="message message-erreur"><?= echapper($erreur) ?></p>
    <?php elseif (empty($evenements)): ?>
        <p class="message">Aucun événement n'est disponible pour le moment.</p>
    <?php else: ?>
        <div class="grille-evenements">
            <?php foreach ($evenements as $ev): ?>
                <?php
                $timestamp = strtotime((string) $ev['date_heure_evenement']);
                $passe = $timestamp !== false && $timestamp < time();
                $description = mb_strimwidth((string) $ev['description'], 0, 160, '…', 'UTF-8');
                ?>
                <article class="carte carte-evenement">
                    <p class="etiquette"><?= echapper($ev['type_evenement']) ?><?= $passe ? ' · Terminé' : '' ?></p>
                    <h2><?= echapper($ev['titre']) ?></h2>
                    <p class="meta">
                        Date : <?= $timestamp ? echapper(date('d/m/Y à H\hi', $timestamp)) : 'à confirmer' ?><br>
                        Lieu : <?= echapper($ev['adresse_lieu']) ?>
                    </p>
                    <p><?= echapper($description) ?></p>
                    <p class="meta">
                        Organisé par :
                        <?= !empty($ev['organisateurs']) ? echapper($ev['organisateurs']) : 'non renseigné' ?>
                    </p>
                    <a class="bouton" href="evenement.php?id=<?= (int) $ev['id_evenement'] ?>">Voir les détails</a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
