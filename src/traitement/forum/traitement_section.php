<?php
/**
 * Traitement des formulaires du module sections : création, modification, suppression.
 * Chaque fonction vérifie elle-même les permissions et le jeton CSRF (défense côté serveur).
 */

require_once __DIR__ . '/outils_forum.php';
require_once __DIR__ . '/../../repository/SectionRepository.php';

// À ADAPTER aux tailles réelles des colonnes dans le SQL.
if (!defined('SECTION_NOM_MAX'))         define('SECTION_NOM_MAX', 100);
if (!defined('SECTION_DESCRIPTION_MAX')) define('SECTION_DESCRIPTION_MAX', 1000);

function validerSaisieSection(array $post): array
{
    $nom = (isset($post['nom']) && is_string($post['nom'])) ? trim($post['nom']) : '';
    $description = (isset($post['description']) && is_string($post['description'])) ? trim($post['description']) : '';
    $erreurs = [];

    if ($nom === '') {
        $erreurs['nom'] = 'Le nom de la section est obligatoire.';
    } elseif (mb_strlen($nom) > SECTION_NOM_MAX) {
        $erreurs['nom'] = 'Le nom ne doit pas dépasser ' . SECTION_NOM_MAX . ' caractères.';
    }
    if (mb_strlen($description) > SECTION_DESCRIPTION_MAX) {
        $erreurs['description'] = 'La description ne doit pas dépasser ' . SECTION_DESCRIPTION_MAX . ' caractères.';
    }

    return ['nom' => $nom, 'description' => $description, 'erreurs' => $erreurs];
}

function traiterCreationSection(SectionRepository $repo): array
{
    exigerAdminForum();

    $resultat = ['valeurs' => ['nom' => '', 'description' => ''], 'erreurs' => []];
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $resultat;
    }

    $saisie = validerSaisieSection($_POST);
    $resultat['valeurs'] = ['nom' => $saisie['nom'], 'description' => $saisie['description']];

    if (!verifierCsrfForum()) {
        $resultat['erreurs']['general'] = 'Session expirée ou requête invalide. Veuillez réessayer.';
        return $resultat;
    }
    if ($saisie['erreurs'] !== []) {
        $resultat['erreurs'] = $saisie['erreurs'];
        return $resultat;
    }

    try {
        $repo->insert(new Section($saisie['nom'], $saisie['description'] !== '' ? $saisie['description'] : null));
    } catch (PDOException $ex) {
        error_log('[GHD][forum] Echec creation section : ' . $ex->getMessage());
        $resultat['erreurs']['general'] = 'Impossible d\'enregistrer la section. Veuillez réessayer.';
        return $resultat;
    }

    redirigerAvecMessageForum(URL_LISTE_SECTIONS, 'succes', 'La section a bien été créée.');
    exit;
}

function traiterModificationSection(SectionRepository $repo, Section $section): array
{
    exigerAdminForum();

    $resultat = [
        'valeurs' => ['nom' => $section->getNom(), 'description' => (string) $section->getDescription()],
        'erreurs' => [],
    ];
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $resultat;
    }

    $saisie = validerSaisieSection($_POST);
    $resultat['valeurs'] = ['nom' => $saisie['nom'], 'description' => $saisie['description']];

    if (!verifierCsrfForum()) {
        $resultat['erreurs']['general'] = 'Session expirée ou requête invalide. Veuillez réessayer.';
        return $resultat;
    }
    if ($saisie['erreurs'] !== []) {
        $resultat['erreurs'] = $saisie['erreurs'];
        return $resultat;
    }

    $section->setNom($saisie['nom']);
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

function traiterSuppressionSection(SectionRepository $repo, Section $section): ?string
{
    exigerAdminForum();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return null; // GET : page de confirmation seulement, aucune suppression.
    }
    if (!verifierCsrfForum()) {
        return 'Session expirée ou requête invalide. Veuillez réessayer.';
    }

    try {
        $repo->delete((int) $section->getIdSection());
    } catch (RuntimeException $ex) {
        return $ex->getMessage(); // section liée à des publications
    } catch (PDOException $ex) {
        error_log('[GHD][forum] Echec suppression section : ' . $ex->getMessage());
        return 'Suppression impossible : la section est encore utilisée ou la base est indisponible.';
    }

    redirigerAvecMessageForum(URL_LISTE_SECTIONS, 'succes', 'La section a bien été supprimée.');
    exit;
}