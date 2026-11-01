<?php

require_once __DIR__ . '/securite.php';
require_once __DIR__ . '/../modele/Evenement.php';
require_once __DIR__ . '/../repository/EvenementRepository.php';

const NB_PLACES_MAX = 100000;

// ------------------------- Utilitaires -------------------------

function lireIdentifiant(array $source): ?int
{
    $id = filter_var($source['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? null : $id;
}

function champTexte(array $source, string $cle): string
{
    $valeur = $source[$cle] ?? '';
    return is_string($valeur) ? trim($valeur) : '';
}

/**
 * Analyse une date saisie (champ datetime-local ou format SQL).
 * Retourne null si le format est faux ou si la date n'existe pas (ex. 31/02).
 */
function analyserDate(string $texte): ?DateTime
{
    foreach (['Y-m-d\TH:i', 'Y-m-d\TH:i:s', 'Y-m-d H:i:s', 'Y-m-d H:i'] as $format) {
        $date = DateTime::createFromFormat('!' . $format, $texte);
        $erreurs = DateTime::getLastErrors();
        $propre = ($erreurs === false)
            || ($erreurs['warning_count'] === 0 && $erreurs['error_count'] === 0);
        if ($date !== false && $propre) {
            return $date;
        }
    }
    return null;
}

function valeursVidesEvenement(): array
{
    return [
        'titre' => '', 'description' => '', 'type_evenement' => '',
        'adresse_lieu' => '', 'element_requis' => '',
        'nombre_place' => '', 'date_heure_evenement' => '',
    ];
}

/** Valeurs de formulaire à partir d'un événement existant (préremplissage). */
function valeursDepuisEvenement(Evenement $evenement): array
{
    return [
        'titre'                => $evenement->getTitre(),
        'description'          => $evenement->getDescription(),
        'type_evenement'       => $evenement->getTypeEvenement(),
        'adresse_lieu'         => $evenement->getAdresseLieu(),
        'element_requis'       => (string) $evenement->getElementRequis(),
        'nombre_place'         => (string) $evenement->getNombrePlace(),
        // 'Y-m-d H:i:s' -> 'Y-m-d\TH:i' attendu par <input type="datetime-local">
        'date_heure_evenement' => substr(str_replace(' ', 'T', $evenement->getDateHeureEvenement()), 0, 16),
    ];
}

function construireEvenement(array $valeurs, ?int $idEvenement): Evenement
{
    $date = analyserDate($valeurs['date_heure_evenement']);

    return new Evenement(
        $idEvenement,
        $date->format('Y-m-d H:i:s'),
        $valeurs['element_requis'] === '' ? null : $valeurs['element_requis'],
        (int) $valeurs['nombre_place'],
        $valeurs['type_evenement'],
        $valeurs['adresse_lieu'],
        $valeurs['description'],
        $valeurs['titre']
    );
}

// ------------------------- Validation -------------------------

/**
 * Valide les données du formulaire.
 *
 * @param bool $creation    true : la date doit être dans le futur
 * @param int  $nbInscrits  nombre d'inscrits actuel (la capacité ne peut pas descendre dessous)
 * @return array [valeurs nettoyées, erreurs par champ]
 */
function validerEvenement(array $post, bool $creation, int $nbInscrits = 0): array
{
    $valeurs = [
        'titre'                => champTexte($post, 'titre'),
        'description'          => champTexte($post, 'description'),
        'type_evenement'       => champTexte($post, 'type_evenement'),
        'adresse_lieu'         => champTexte($post, 'adresse_lieu'),
        'element_requis'       => champTexte($post, 'element_requis'),
        'nombre_place'         => champTexte($post, 'nombre_place'),
        'date_heure_evenement' => champTexte($post, 'date_heure_evenement'),
    ];
    $erreurs = [];

    // Champs obligatoires
    $obligatoires = [
        'titre'                => 'Le titre',
        'description'          => 'La description',
        'type_evenement'       => "Le type d'événement",
        'adresse_lieu'         => "L'adresse du lieu",
        'nombre_place'         => 'Le nombre de places',
        'date_heure_evenement' => "La date et l'heure",
    ];
    foreach ($obligatoires as $champ => $libelle) {
        if ($valeurs[$champ] === '') {
            $erreurs[$champ] = $libelle . ' est obligatoire.';
        }
    }

    // Longueurs maximales (colonnes VARCHAR)
    $longueursMax = ['titre' => 150, 'type_evenement' => 100, 'adresse_lieu' => 255, 'element_requis' => 255];
    foreach ($longueursMax as $champ => $max) {
        if (!isset($erreurs[$champ]) && mb_strlen($valeurs[$champ]) > $max) {
            $erreurs[$champ] = 'Ce champ ne doit pas dépasser ' . $max . ' caractères.';
        }
    }

    // Capacité : entier positif
    if (!isset($erreurs['nombre_place'])) {
        $nb = filter_var($valeurs['nombre_place'], FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => NB_PLACES_MAX],
        ]);
        if ($nb === false) {
            $erreurs['nombre_place'] = 'Le nombre de places doit être un entier positif (1 à ' . NB_PLACES_MAX . ').';
        } elseif (!$creation && $nb < $nbInscrits) {
            $erreurs['nombre_place'] = 'Impossible de passer sous le nombre de personnes déjà inscrites (' . $nbInscrits . ').';
        } else {
            $valeurs['nombre_place'] = (string) $nb;
        }
    }

    // Date valide (et dans le futur à la création)
    if (!isset($erreurs['date_heure_evenement'])) {
        $date = analyserDate($valeurs['date_heure_evenement']);
        if ($date === null) {
            $erreurs['date_heure_evenement'] = "La date et l'heure ne sont pas valides.";
        } elseif ($creation && $date <= new DateTime()) {
            $erreurs['date_heure_evenement'] = "La date de l'événement doit être dans le futur.";
        }
    }

    return [$valeurs, $erreurs];
}

// ------------------------- Permissions -------------------------

/**
 * Un gestionnaire peut tout gérer ; sinon il faut être organisateur de l'événement.
 */
function peutGererEvenement(EvenementRepository $repo, int $idEvenement): bool
{
    $idUtilisateur = utilisateurId();
    if ($idUtilisateur === null) {
        return false;
    }
    if (utilisateurRole() === ROLE_GESTIONNAIRE) {
        return true;
    }
    return $repo->estOrganisateur($idEvenement, $idUtilisateur);
}

// ------------------------- Actions -------------------------

/**
 * @return array [succès (bool), valeurs, erreurs]
 */
function traiterCreation(EvenementRepository $repo, array $post, int $idUtilisateur): array
{
    [$valeurs, $erreurs] = validerEvenement($post, true);
    if (!empty($erreurs)) {
        return [false, $valeurs, $erreurs];
    }

    try {
        $repo->insertAvecOrganisateur(construireEvenement($valeurs, null), $idUtilisateur);
    } catch (PDOException $ex) {
        error_log('Création événement : ' . $ex->getMessage());
        return [false, $valeurs, ['general' => "Une erreur est survenue lors de l'enregistrement. Veuillez réessayer."]];
    }

    return [true, $valeurs, []];
}

/**
 * @return array [succès (bool), valeurs, erreurs]
 */
function traiterModification(EvenementRepository $repo, int $idEvenement, array $post): array
{
    $nbInscrits = $repo->compterInscrits($idEvenement);

    [$valeurs, $erreurs] = validerEvenement($post, false, $nbInscrits);
    if (!empty($erreurs)) {
        return [false, $valeurs, $erreurs];
    }

    try {
        // Revérifie la capacité dans une transaction (protège des inscriptions simultanées)
        $repo->updateAvecControleCapacite(construireEvenement($valeurs, $idEvenement));
    } catch (DomainException $ex) {
        return [false, $valeurs, ['nombre_place' => $ex->getMessage()]];
    } catch (Throwable $ex) {
        error_log('Modification événement : ' . $ex->getMessage());
        return [false, $valeurs, ['general' => "Une erreur est survenue lors de l'enregistrement. Veuillez réessayer."]];
    }

    return [true, $valeurs, []];
}

/**
 * Règles de suppression :
 *  - sans inscription : suppression autorisée (utilisateur autorisé) ;
 *  - avec inscriptions : réservé au gestionnaire, avec case de confirmation.
 *
 * @return array [succès (bool), message]
 */
function traiterSuppression(EvenementRepository $repo, int $idEvenement, array $post): array
{
    $nbInscrits = $repo->compterInscrits($idEvenement);

    if ($nbInscrits > 0) {
        if (utilisateurRole() !== ROLE_GESTIONNAIRE) {
            return [false, "Cet événement compte $nbInscrits inscription(s) : seul un gestionnaire peut le supprimer."];
        }
        if (($post['confirmer_inscriptions'] ?? '') !== '1') {
            return [false, "Veuillez cocher la case de confirmation : $nbInscrits inscription(s) seront supprimées."];
        }
    }

    try {
        $supprime = $repo->delete($idEvenement);
    } catch (PDOException $ex) {
        // Ex. contrainte de clé étrangère (RESTRICT) sur une table liée
        error_log('Suppression événement : ' . $ex->getMessage());
        return [false, "Suppression impossible : l'événement est encore lié à d'autres données."];
    }

    return $supprime
        ? [true, "L'événement a bien été supprimé."]
        : [false, "Cet événement n'existe plus."];
}