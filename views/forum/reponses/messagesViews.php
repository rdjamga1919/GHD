<?php
/*
 * Vue : messages d'erreur et de confirmation.
 * Variables attendues : $erreurs et $succes (tableaux de textes, peuvent être vides)
 */
?>
<?php if (!empty($erreurs)): ?>
    <div class="alert alert-error">
        <?php foreach ($erreurs as $message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($succes)): ?>
    <div class="alert alert-success">
        <?php foreach ($succes as $message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>