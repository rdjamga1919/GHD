

<section class="container">

    <?php if ($flash): ?>
        <div class="alert <?= $flash[0] === 'succes' ? 'alert-success' : 'alert-error' ?>"><?= htmlspecialchars($flash[1]) ?></div>
    <?php endif; ?>

    <?php if ($evenement === null): ?>

        <h1 class="page-title">Événement introuvable</h1>
        <div class="alert alert-error">Cet événement n'existe pas ou n'est plus disponible.</div>

    <?php else: ?>

        <h1 class="page-title"><?= htmlspecialchars($evenement->getTitre()) ?></h1>

        <div class="card">
            <p><span class="tag"><?= htmlspecialchars(ucfirst($evenement->getTypeEvenement())) ?></span></p>
            <p>Date : <?= htmlspecialchars(date('d/m/Y à H:i', strtotime($evenement->getDateHeureEvenement()))) ?></p>
            <p>Lieu : <?= htmlspecialchars($evenement->getAdresseLieu()) ?></p>
            <?php if (!empty($evenement->getElementRequis())): ?>
                <p>À prévoir : <?= htmlspecialchars($evenement->getElementRequis()) ?></p>
            <?php endif; ?>
            <h2>Description</h2>
            <p><?= nl2br(htmlspecialchars($evenement->getDescription())) ?></p>
        </div>

        <?php require __DIR__ . '/bloc_inscription.php'; ?>

    <?php endif; ?>

    <p><a class="btn btn-secondary" href="evenements.php">Retour aux événements</a></p>

</section>
