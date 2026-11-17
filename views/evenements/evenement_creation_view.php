<?php
/**
 * Formulaire de création d'événement (HTML uniquement, aucune logique métier).
 * Variables attendues : $valeurs (array), $erreurs (array), $flash (?array), $csrfToken (string)
 */

/** @var array<string,string> $valeurs */
/** @var array<string,string> $erreurs */
/** @var array{type:string, message:string}|null $flash */
/** @var string $csrfToken */

$valeurs = array_merge([
        'titre' => '',
        'description' => '',
        'type_evenement' => '',
        'adresse_lieu' => '',
        'element_requis' => '',
        'nombre_place' => '',
        'date_heure_evenement' => '',
], $valeurs ?? []);

/** @noinspection PhpUnhandledExceptionInspection */
$h =static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$minDate = (new DateTimeImmutable('now', new DateTimeZone('Europe/Paris')))->format('Y-m-d\TH:i');
?>


<section>
    <h1>Créer un événement</h1>

    <?php if (!empty($flash)): ?>
        <p class="message message-<?= ($flash[0] ?? '') === 'succes' ? 'succes' : 'erreur' ?>"
           role="alert">
            <?= $h($flash[1] ?? '') ?>
        </p>
    <?php endif; ?>

    <?php if (isset($erreurs['general'])): ?>
        <p class="alert alert-error" role="alert"><?= $h($erreurs['general']) ?></p>
    <?php endif; ?>

    <form method="post" action="/GHD/public/evenement_creer.php" novalidate>
        <input type="hidden" name="csrf" value="<?= $h($csrf) ?>">
        <p>
            <label for="titre">Titre *</label><br>
            <input type="text" id="titre" name="titre" maxlength="150" required value="<?= $h($valeurs['titre']) ?>" class="form-control">
            <?php if (isset($erreurs['titre'])): ?><span class="alert alert-error" role="alert"><?= $h($erreurs['titre']) ?></span><?php endif; ?>
        </p>

        <p>
            <label for="type_evenement">Type d'événement *</label><br>
            <input type="text" id="type_evenement" name="type_evenement" maxlength="100" required value="<?= $h($valeurs['type_evenement']) ?>" class="form-control">
            <?php if (isset($erreurs['type_evenement'])): ?><span class="alert alert-error" role="alert"><?= $h($erreurs['type_evenement']) ?></span><?php endif; ?>
        </p>

        <p>
            <label for="description">Description *</label><br>
            <textarea id="description" name="description" rows="6" maxlength="5000" required class="form-control"><?= $h($valeurs['description']) ?></textarea>
            <?php if (isset($erreurs['description'])): ?><span class="alert alert-error" role="alert"><?= $h($erreurs['description']) ?></span><?php endif; ?>
        </p>

        <p>
            <label for="adresse_lieu">Lieu (adresse) *</label><br>
            <input type="text" id="adresse_lieu" name="adresse_lieu" maxlength="255" required value="<?= $h($valeurs['adresse_lieu']) ?>" class="form-control">
            <?php if (isset($erreurs['adresse_lieu'])): ?><span class="alert alert-error" role="alert"><?= $h($erreurs['adresse_lieu']) ?></span><?php endif; ?>
        </p>

        <p>
            <label for="date_heure_evenement">Date et heure *</label><br>
            <input type="datetime-local" id="date_heure_evenement" name="date_heure_evenement" min="<?= $h($minDate) ?>" required
                   value="<?= $h($valeurs['date_heure_evenement']) ?>" class="form-control">
            <?php if (isset($erreurs['date_heure_evenement'])): ?><span class="alert alert-error" role="alert"><?= $h($erreurs['date_heure_evenement']) ?></span><?php endif; ?>
        </p>

        <p>
            <label for="nombre_place">Nombre de places *</label><br>
            <input type="number" id="nombre_place" name="nombre_place" min="1" step="1" required value="<?= $h($valeurs['nombre_place']) ?>" class="form-control">
            <?php if (isset($erreurs['nombre_place'])): ?><span class="alert alert-error" role="alert"><?= $h($erreurs['nombre_place']) ?></span><?php endif; ?>
        </p>

        <p>
            <label for="element_requis">Élément requis (facultatif)</label><br>
            <input type="text" id="element_requis" name="element_requis" maxlength="255" value="<?= $h($valeurs['element_requis']) ?>" class="form-control">
            <?php if (isset($erreurs['element_requis'])): ?><span class="alert alert-error" role="alert"><?= $h($erreurs['element_requis']) ?></span><?php endif; ?>
        </p>

        <div class="cluster">
            <button type="submit" class="btn">Créer l'événement</button>
            <a class="btn btn-secondary" href="/GHD/public/evenements.php">Annuler</a>
        </div>
    </form>
</section>
