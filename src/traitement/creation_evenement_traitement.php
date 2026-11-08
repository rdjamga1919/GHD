<?php
/**
 * Traitement de la création d'un événement.
 * Inclus par public/evenement_creer.php (session démarrée, utilisateur connecté et autorisé).
 * Fournit à la vue : $valeurs, $erreurs, $flash, $csrfToken.
 */

// À ADAPTER : page affichée après une création réussie.
const URL_APRES_CREATION_EVENEMENT = '/GHD/public/evenements.php';
// À ADAPTER : limite de places définie par le projet.
const NOMBRE_PLACE_MAX = 500;

function lireTexteEvenement(string $cle): string
{
    $valeur = $_POST[$cle] ?? '';
    return is_string($valeur) ? trim($valeur) : '';
}

// Message flash éventuel (affiché une seule fois).
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Jeton CSRF : créé une fois par session.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$valeurs = [
    'titre'                => '',
    'description'          => '',
    'type_evenement'       => '',
    'adresse_lieu'         => '',
    'element_requis'       => '',
    'nombre_place'         => '',
    'date_heure_evenement' => '',
];
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($valeurs as $cle => $_) {
        $valeurs[$cle] = lireTexteEvenement($cle);
    }

    $jeton = $_POST['csrf_token'] ?? '';
    if (!is_string($jeton) || !hash_equals($_SESSION['csrf_token'], $jeton)) {
        $erreurs['general'] = 'Session expirée ou requête invalide. Veuillez réessayer.';
    } else {
        // Champs texte (limites alignées sur le SQL).
        if ($valeurs['titre'] === '') {
            $erreurs['titre'] = 'Le titre est obligatoire.';
        } elseif (mb_strlen($valeurs['titre']) > 150) {
            $erreurs['titre'] = 'Le titre ne doit pas dépasser 150 caractères.';
        }

        if ($valeurs['description'] === '') {
            $erreurs['description'] = 'La description est obligatoire.';
        } elseif (mb_strlen($valeurs['description']) > 5000) {
            $erreurs['description'] = 'La description ne doit pas dépasser 5000 caractères.';
        }

        if ($valeurs['type_evenement'] === '') {
            $erreurs['type_evenement'] = 'Le type d\'événement est obligatoire.';
        } elseif (mb_strlen($valeurs['type_evenement']) > 100) {
            $erreurs['type_evenement'] = 'Le type ne doit pas dépasser 100 caractères.';
        }

        if ($valeurs['adresse_lieu'] === '') {
            $erreurs['adresse_lieu'] = 'Le lieu est obligatoire.';
        } elseif (mb_strlen($valeurs['adresse_lieu']) > 255) {
            $erreurs['adresse_lieu'] = 'Le lieu ne doit pas dépasser 255 caractères.';
        }

        if (mb_strlen($valeurs['element_requis']) > 255) {
            $erreurs['element_requis'] = 'Ce champ ne doit pas dépasser 255 caractères.';
        }

        // Capacité : entier strictement positif (rejette décimaux, négatifs, texte).
        $nombrePlace = 0;
        if (!preg_match('/^\d{1,6}$/', $valeurs['nombre_place'])) {
            $erreurs['nombre_place'] = 'Le nombre de places doit être un entier positif.';
        } else {
            $nombrePlace = (int) $valeurs['nombre_place'];
            if ($nombrePlace < 1 || $nombrePlace > NOMBRE_PLACE_MAX) {
                $erreurs['nombre_place'] = 'Le nombre de places doit être compris entre 1 et ' . NOMBRE_PLACE_MAX . '.';
            }
        }

        // Date et heure : format valide, date réelle, dans le futur.
        $dateSql = '';
        $fuseau = new DateTimeZone('Europe/Paris');
        $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $valeurs['date_heure_evenement'], $fuseau);
        $problemes = DateTimeImmutable::getLastErrors();
        if ($date === false || ($problemes && ($problemes['warning_count'] > 0 || $problemes['error_count'] > 0))) {
            $erreurs['date_heure_evenement'] = 'La date et l\'heure sont invalides.';
        } elseif ($date <= new DateTimeImmutable('now', $fuseau)) {
            $erreurs['date_heure_evenement'] = 'L\'événement doit avoir lieu dans le futur.';
        } else {
            $dateSql = $date->format('Y-m-d H:i:s');
        }

        if ($erreurs === []) {
            try {
                $evenement = new Evenement(
                    $valeurs['titre'],
                    $valeurs['description'],
                    $valeurs['type_evenement'],
                    $valeurs['adresse_lieu'],
                    $valeurs['element_requis'] === '' ? null : $valeurs['element_requis'],
                    $nombrePlace,
                    $dateSql
                );
                // L'organisateur vient de la session, jamais du formulaire.
                (new EvenementRepository())->insertAvecOrganisateur($evenement, (int) $_SESSION['utilisateur_id']);

                unset($_SESSION['csrf_token']);
                $_SESSION['flash'] = ['type' => 'succes', 'message' => 'L\'événement a bien été créé.'];
                header('Location: ' . URL_APRES_CREATION_EVENEMENT);
                exit;
            } catch (Throwable $ex) {
                error_log('[GHD][evenement] Echec creation : ' . $ex->getMessage());
                $erreurs['general'] = 'Impossible d\'enregistrer l\'événement. Veuillez réessayer.';
            }
        }
    }
}

$csrfToken = $_SESSION['csrf_token'];