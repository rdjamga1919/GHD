




<?php
/**
 * Formulaire de création d'événement (HTML uniquement, aucune logique métier).
 * Variables attendues : $valeurs (array), $erreurs (array), $flash (?array), $csrfToken (string)
 */

/** @var array<string,string> $valeurs */
/** @var array<string,string> $erreurs */
/** @var array{type:string, message:string}|null $flash */
/** @var string $csrfToken */

/** @noinspection PhpUnhandledExceptionInspection */
$h =static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$minDate = (new DateTimeImmutable('now', new DateTimeZone('Europe/Paris')))->format('Y-m-d\TH:i');
?>
<?php /* TODO : inclure ici l'en-tête commun du site (ne pas rajouter de <html>/<head>/<body>). */ ?>

<main>
    <h1>Créer un événement</h1>

    <?php if ($flash !== null): ?>
        <p class="message message-<?= $flash['type'] === 'succes' ? 'succes' : 'erreur' ?>" role="status">
            <?= $h($flash['message']) ?>
        </p>
    <?php endif; ?>

    <?php if (isset($erreurs['general'])): ?>
        <p class="message message-erreur" role="alert"><?= $h($erreurs['general']) ?></p>
    <?php endif; ?>

    <form method="post" action="/GHD/public/evenement_creer.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $h($csrfToken) ?>">

        <p>
            <label for="titre">Titre *</label><br>
            <input type="text" id="titre" name="titre" maxlength="150" required value="<?= $h($valeurs['titre']) ?>">
            <?php if (isset($erreurs['titre'])): ?><span class="message message-erreur" role="alert"><?= $h($erreurs['titre']) ?></span><?php endif; ?>
        </p>

        <p>
            <label for="type_evenement">Type d'événement *</label><br>
            <input type="text" id="type_evenement" name="type_evenement" maxlength="100" required value="<?= $h($valeurs['type_evenement']) ?>">
            <?php if (isset($erreurs['type_evenement'])): ?><span class="message message-erreur" role="alert"><?= $h($erreurs['type_evenement']) ?></span><?php endif; ?>
        </p>

        <p>
            <label for="description">Description *</label><br>
            <textarea id="description" name="description" rows="6" maxlength="5000" required><?= $h($valeurs['description']) ?></textarea>
            <?php if (isset($erreurs['description'])): ?><span class="message message-erreur" role="alert"><?= $h($erreurs['description']) ?></span><?php endif; ?>
        </p>

        <p>
            <label for="adresse_lieu">Lieu (adresse) *</label><br>
            <input type="text" id="adresse_lieu" name="adresse_lieu" maxlength="255" required value="<?= $h($valeurs['adresse_lieu']) ?>">
            <?php if (isset($erreurs['adresse_lieu'])): ?><span class="message message-erreur" role="alert"><?= $h($erreurs['adresse_lieu']) ?></span><?php endif; ?>
        </p>

        <p>
            <label for="date_heure_evenement">Date et heure *</label><br>
            <input type="datetime-local" id="date_heure_evenement" name="date_heure_evenement" min="<?= $h($minDate) ?>" required
                   value="<?= $h($valeurs['date_heure_evenement']) ?>">
            <?php if (isset($erreurs['date_heure_evenement'])): ?><span class="message message-erreur" role="alert"><?= $h($erreurs['date_heure_evenement']) ?></span><?php endif; ?>
        </p>

        <p>
            <label for="nombre_place">Nombre de places *</label><br>
            <input type="number" id="nombre_place" name="nombre_place" min="1" step="1" required value="<?= $h($valeurs['nombre_place']) ?>">
            <?php if (isset($erreurs['nombre_place'])): ?><span class="message message-erreur" role="alert"><?= $h($erreurs['nombre_place']) ?></span><?php endif; ?>
        </p>

        <p>
            <label for="element_requis">Élément requis (facultatif)</label><br>
            <input type="text" id="element_requis" name="element_requis" maxlength="255" value="<?= $h($valeurs['element_requis']) ?>">
            <?php if (isset($erreurs['element_requis'])): ?><span class="message message-erreur" role="alert"><?= $h($erreurs['element_requis']) ?></span><?php endif; ?>
        </p>

        <p>
            <button type="submit">Créer l'événement</button>
        </p>
    </form>
</main>

<?php /* TODO : inclure ici le pied de page commun du site. */ ?>








