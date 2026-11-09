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
        value="<?= htmlspecialchars($evenement->getTitre(), ENT_QUOTES, 'UTF-8') ?>"
        required
    >

    <button type="submit" name="action" value="modifier">
        Enregistrer les modifications
    </button>

</form>