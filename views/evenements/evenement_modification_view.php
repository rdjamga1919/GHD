<?php
// Valeurs soumises en POST ou valeurs actuelles de l'événement
$valeursFormulaire = $valeurs ?? [];

function valeurChampEvenement(
        string $champ,
               $valeurActuelle,
        array $valeursFormulaire
): string {
    return htmlspecialchars(
            (string) ($valeursFormulaire[$champ] ?? $valeurActuelle ?? ''),
            ENT_QUOTES,
            'UTF-8'
    );
}
?>

<h1>Modifier l'événement</h1>

<?php if (!empty($flash)): ?>
    <p role="alert">
        <?= htmlspecialchars($flash[1], ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

<form method="POST" action="evenement_modifier.php">

    <input
            type="hidden"
            name="id_evenement"
            value="<?= (int) $evenement->getIdEvenement() ?>"
    >

    <input
            type="hidden"
            name="csrf"
            value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"
    >

    <label for="titre">Titre de l'événement</label>
    <input
            type="text"
            id="titre"
            name="titre"
            maxlength="150"
            value="<?= valeurChampEvenement('titre', $evenement->getTitre(), $valeursFormulaire) ?>"
            required
    >

    <label for="description">Description</label>
    <textarea
            id="description"
            name="description"
            maxlength="2000"
    ><?= valeurChampEvenement('description', $evenement->getDescription(), $valeursFormulaire) ?></textarea>

    <label for="type_evenement">Type d'événement</label>
    <input
            type="text"
            id="type_evenement"
            name="type_evenement"
            maxlength="50"
            value="<?= valeurChampEvenement('type_evenement', $evenement->getTypeEvenement(), $valeursFormulaire) ?>"
            required
    >

    <label for="adresse_lieu">Adresse</label>
    <input
            type="text"
            id="adresse_lieu"
            name="adresse_lieu"
            maxlength="255"
            value="<?= valeurChampEvenement('adresse_lieu', $evenement->getAdresseLieu(), $valeursFormulaire) ?>"
            required
    >

    <label for="element_requis">Éléments requis</label>
    <input
            type="text"
            id="element_requis"
            name="element_requis"
            maxlength="255"
            value="<?= valeurChampEvenement('element_requis', $evenement->getElementRequis(), $valeursFormulaire) ?>"
    >

    <label for="nombre_place">Nombre de places</label>
    <input
            type="number"
            id="nombre_place"
            name="nombre_place"
            min="1"
            max="1000"
            value="<?= valeurChampEvenement('nombre_place', $evenement->getNombrePlace(), $valeursFormulaire) ?>"
            required
    >

    <label for="date_heure_evenement">Date et heure</label>
    <input
            type="datetime-local"
            id="date_heure_evenement"
            name="date_heure_evenement"
            value="<?= valeurChampEvenement(
                    'date_heure_evenement',
                    date('Y-m-d\TH:i', strtotime($evenement->getDateHeureEvenement())),
                    $valeursFormulaire
            ) ?>"
            required
    >

    <button type="submit" name="action" value="modifier">
        Enregistrer les modifications
    </button>

    <a href="evenement_detail.php?id=<?= (int) $evenement->getIdEvenement() ?>">
        Annuler
    </a>

</form>
