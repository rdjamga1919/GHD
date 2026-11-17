<?php
// Variables attendues : $evenement (tableau|null), $organisateurs (tableau), $erreur (string|null)
if (!function_exists('echapper')) {
    function echapper($valeur): string
    {
        return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
    }
}
$libellesRoles = [
    'etudiant' => 'Étudiant',
    'medecin' => 'Médecin',
    'partenaire' => 'Partenaire',
    'gestionnaire' => 'Gestionnaire',
];
?>

<section class="page-evenement">
    <p><a class="lien-retour" href="evenements.php">← Retour aux événements</a></p>

    <?php if ($erreur !== null): ?>
        <p class="alert alert-error"><?= echapper($erreur) ?></p>

    <?php elseif ($evenement === null): ?>
        <h1>Événement introuvable</h1>
        <p class="message">Cet événement n'existe pas ou n'est plus disponible.</p>
        <a class="btn" href="evenements.php">Voir tous les événements</a>

    <?php else: ?>
        <?php
        $timestamp = strtotime((string) $evenement['date_heure_evenement']);
        $capacite = (int) $evenement['nombre_place'];
        $placesRestantes = max(0, $capacite - (int) $evenement['nb_inscrits']);
        ?>
        <h1 class="page-title"><?= echapper($evenement['titre']) ?></h1>
        <article class="carte carte-evenement-detail">
            <p class="tag"><?= echapper($evenement['type_evenement']) ?></p>

            <ul class="infos">
                <li><strong>Date :</strong> <?= $timestamp ? echapper(date('d/m/Y à H\hi', $timestamp)) : 'à confirmer' ?></li>
                <li><strong>Lieu :</strong> <?= echapper($evenement['adresse_lieu']) ?></li>
                <li><strong>Capacité :</strong> <?= $capacite ?> places</li>
                <li><strong>Places restantes :</strong> <?= $placesRestantes > 0 ? $placesRestantes : 'Complet' ?></li>
                <?php if (!empty($evenement['element_requis'])): ?>
                    <li><strong>À prévoir :</strong> <?= echapper($evenement['element_requis']) ?></li>
                <?php endif; ?>
            </ul>

            <h2>Description</h2>
            <p><?= nl2br(echapper($evenement['description'])) ?></p>

            <h2>Organisateur<?= count($organisateurs) > 1 ? 's' : '' ?></h2>
            <?php if (empty($organisateurs)): ?>
                <p>Organisateur non renseigné.</p>
            <?php else: ?>
                <ul class="organisateurs">
                    <?php foreach ($organisateurs as $org): ?>
                        <li>
                            <strong><?= echapper(trim(($org['prenom'] ?? '') . ' ' . ($org['nom'] ?? ''))) ?></strong>
                            <?php if (!empty($org['role'])): ?>
                                · <?= echapper($libellesRoles[$org['role']] ?? $org['role']) ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (empty($_SESSION['utilisateur_id'])): ?>
                <p class="meta">Connectez-vous pour vous inscrire à un événement.</p>
                <a class="btn" href="connexion.php">Se connecter</a>
            <?php endif; ?>
        </article>
    <?php endif; ?>
</section>
