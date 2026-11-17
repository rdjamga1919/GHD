<?php
function echapperSuppression($valeur): string
{
    return htmlspecialchars(
        (string) $valeur,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<section class="container">
    <h1>Supprimer un événement</h1>

    <?php if ($erreur !== null): ?>
        <p role="alert">
            <?= echapperSuppression($erreur) ?>
        </p>
    <?php endif; ?>

    <p>
        Souhaites-tu vraiment supprimer cet événement ?
        Cette action est irréversible.
    </p>

    <h2><?= echapperSuppression($evenement->getTitre()) ?></h2>

    <p>
        <strong>Date :</strong>
        <?= echapperSuppression($evenement->getDateHeureEvenement()) ?>
    </p>

    <p>
        <strong>Lieu :</strong>
        <?= echapperSuppression($evenement->getAdresseLieu()) ?>
    </p>

    <p>
        <strong>Participants inscrits :</strong>
        <?= (int) $nbInscrits ?>
    </p>

    <?php if ($nbInscrits > 0): ?>
        <p role="alert">
            Cet événement possède des inscriptions.
            Sa suppression est actuellement interdite.
        </p>
    <?php else: ?>
        <form method="POST" action="evenement_supprimer.php">

            <input
                type="hidden"
                name="id_evenement"
                value="<?= (int) $evenement->getIdEvenement() ?>"
            >

            <input
                type="hidden"
                name="csrf"
                value="<?= echapperSuppression($csrf) ?>"
            >

            <button
                type="submit"
                name="action"
                value="supprimer"
             class="btn btn-danger">
                Confirmer la suppression
            </button>

        </form>
    <?php endif; ?>

    <a href="evenement_detail.php?id=<?= (int) $evenement->getIdEvenement() ?>">
        Annuler
    </a>
</section>
