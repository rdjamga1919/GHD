<?php
/**
 * Traitement des formulaires du module sections : création, modification, suppression.
 * Chaque fonction vérifie elle-même les permissions et le jeton CSRF (défense côté serveur).
 */

require_once __DIR__ . '/outils_forum.php';
require_once __DIR__ . '/../../repository/SectionRepository.php';

// Aligné sur Canal_forum.nom_canal VARCHAR(100).
if (!defined('SECTION_NOM_MAX')) define('SECTION_NOM_MAX', 100);
// Aligné sur Canal_forum.description VARCHAR(500) (cf. sql/migration_canal_forum_description.sql).
if (!defined('SECTION_DESCRIPTION_MAX')) define('SECTION_DESCRIPTION_MAX', 500);

/**
 * Nettoie et valide la saisie : nom obligatoire, description facultative, accès parmi les valeurs de l'ENUM.
 * @return array{nom:string, description:string, acces:string, erreurs:array<string,string>}
 */
function validerSaisieSection(array $post): array
{
    $nom = (isset($post['nom']) && is_string($post['nom'])) ? trim($post['nom']) : '';
    $description = (isset($post['description']) && is_string($post['description'])) ? trim($post['description']) : '';
    $acces = (isset($post['acces']) && is_string($post['acces'])) ? $post['acces'] : '';
    $erreurs = [];

    if ($nom === '') {
        $erreurs['nom'] = 'Le nom de la section est obligatoire.';
    } elseif (mb_strlen($nom) > SECTION_NOM_MAX) {
        $erreurs['nom'] = 'Le nom ne doit pas dépasser ' . SECTION_NOM_MAX . ' caractères.';
    }
    if (mb_strlen($description) > SECTION_DESCRIPTION_MAX) {
        $erreurs['description'] = 'La description ne doit pas dépasser ' . SECTION_DESCRIPTION_MAX . ' caractères.';
    }
    if (!array_key_exists($acces, Section::ACCES)) {
        $erreurs['acces'] = 'Veuillez choisir un accès valide.';
    }

    return ['nom' => $nom, 'description' => $description, 'acces' => $acces, 'erreurs' => $erreurs];
}

/**
 * Création. Sur succès : message flash + redirection vers la liste (la fonction ne retourne pas).
 * Sinon retourne ['valeurs' => ..., 'erreurs' => ...] pour réafficher le formulaire.
 */
function traiterCreationSection(SectionRepository $repo): array
{
    exigerAdminForum();

    $resultat = ['valeurs' => ['nom' => '', 'description' => '', 'acces' => 'generale'], 'erreurs' => []];
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $resultat;
    }

    $saisie = validerSaisieSection($_POST);
    $resultat['valeurs'] = ['nom' => $saisie['nom'], 'description' => $saisie['description'], 'acces' => $saisie['acces']];

    if (!verifierCsrfForum()) {
        $resultat['erreurs']['general'] = 'Session expirée ou requête invalide. Veuillez réessayer.';
        return $resultat;
    }
    if ($saisie['erreurs'] !== []) {
        $resultat['erreurs'] = $saisie['erreurs'];
        return $resultat;
    }

    try {
        $repo->insert(new Section(
            $saisie['nom'],
            $saisie['acces'],
            $saisie['description'] !== '' ? $saisie['description'] : null
        ));
    } catch (PDOException $ex) {
        error_log('[GHD][forum] Echec creation section : ' . $ex->getMessage());
        $resultat['erreurs']['general'] = 'Impossible d\'enregistrer la section. Veuillez réessayer.';
        return $resultat;
    }

    redirigerAvecMessageForum(URL_LISTE_SECTIONS, 'succes', 'La section a bien été créée.');
    exit;
}

/** Modification d'une section existante (même contrat de retour que la création). */
function traiterModificationSection(SectionRepository $repo, Section $section): array
{
    exigerAdminForum();

    $resultat = [
        'valeurs' => [
            'nom'         => $section->getNom(),
            'description' => (string) $section->getDescription(),
            'acces'       => $section->getAcces(),
        ],
        'erreurs' => [],
    ];
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $resultat;
    }

    $saisie = validerSaisieSection($_POST);
    $resultat['valeurs'] = ['nom' => $saisie['nom'], 'description' => $saisie['description'], 'acces' => $saisie['acces']];

    if (!verifierCsrfForum()) {
        $resultat['erreurs']['general'] = 'Session expirée ou requête invalide. Veuillez réessayer.';
        return $resultat;
    }
    if ($saisie['erreurs'] !== []) {
        $resultat['erreurs'] = $saisie['erreurs'];
        return $resultat;
    }

    $section->setNom($saisie['nom']);
    $section->setAcces($saisie['acces']);
    $section->setDescription($saisie['description'] !== '' ? $saisie['description'] : null);

    try {
        $repo->update($section);
    } catch (PDOException $ex) {
        error_log('[GHD][forum] Echec modification section : ' . $ex->getMessage());
        $resultat['erreurs']['general'] = 'Impossible d\'enregistrer les modifications. Veuillez réessayer.';
        return $resultat;
    }

    redirigerAvecMessageForum(URL_LISTE_SECTIONS, 'succes', 'La section a bien été modifiée.');
    exit;
}

/**
 * Suppression : POST uniquement, jeton CSRF obligatoire.
 * Retourne un message d'erreur à afficher, ou ne retourne pas (redirection) en cas de succès.
 */
function traiterSuppressionSection(SectionRepository $repo, Section $section): ?string
{
    exigerAdminForum();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return null; // GET : on affiche seulement la page de confirmation, aucune suppression.
    }
    if (!verifierCsrfForum()) {
        return 'Session expirée ou requête invalide. Veuillez réessayer.';
    }

    try {
        $repo->delete((int) $section->getIdSection());
    } catch (RuntimeException $ex) {
        return $ex->getMessage(); // section contenant des publications
    } catch (PDOException $ex) {
        error_log('[GHD][forum] Echec suppression section : ' . $ex->getMessage());
        return 'Suppression impossible : la base est momentanément indisponible.';
    }

    redirigerAvecMessageForum(URL_LISTE_SECTIONS, 'succes', 'La section a bien été supprimée.');
    exit;
}