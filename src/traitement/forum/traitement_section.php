<?php
/** Traitement métier des sections : admin et CSRF contrôlés côté serveur. */
require_once __DIR__ . '/outils_forum.php';
require_once __DIR__ . '/../../repository/CanalForumRepository.php';

const SECTION_NOM_MAX = 100;
const SECTION_DESCRIPTION_MAX = 1000;
const SECTION_ACCES = ['generale', 'medecins', 'etudiants'];

/** @return array{nom:string,description:string,acces:string,erreurs:array<string,string>} */
function validerSaisieSection(array $post): array
{
    $nom = is_string($post['nom'] ?? null) ? trim($post['nom']) : '';
    $description = is_string($post['description'] ?? null) ? trim($post['description']) : '';
    $acces = is_string($post['acces'] ?? null) ? trim($post['acces']) : '';
    $erreurs = [];
    if ($nom === '') {
        $erreurs['nom'] = 'Le nom est obligatoire.';
    } elseif (mb_strlen($nom) > SECTION_NOM_MAX) {
        $erreurs['nom'] = 'Le nom ne doit pas dépasser 100 caractères.';
    }
    if (mb_strlen($description) > SECTION_DESCRIPTION_MAX) {
        $erreurs['description'] = 'La description ne doit pas dépasser 1000 caractères.';
    }
    if (!in_array($acces, SECTION_ACCES, true)) {
        $erreurs['acces'] = 'Choisis un accès valide.';
    }
    return compact('nom', 'description', 'acces', 'erreurs');
}

function traiterCreationSection(CanalForumRepository $repo): array
{
    exigerAdminForum();
    $resultat =
        ['valeurs' => [
            'nom' => '',
            'description' => '',
            'acces' => 'generale'
        ],
            'erreurs' => []
        ];
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') return $resultat;
    $saisie = validerSaisieSection($_POST);
    $resultat['valeurs'] = array_intersect_key($saisie, $resultat['valeurs']);
    if (!verifierCsrfForum()) {
        $resultat['erreurs']['general'] = 'Requête invalide. Actualise la page et réessaie.';
    } else {
        $resultat['erreurs'] = $saisie['erreurs'];
    }
    if ($resultat['erreurs']) return $resultat;
    try {
        $repo->ajouterCanalForum(new CanalForum(null, $saisie['nom'], $saisie['acces'], $saisie['description'] ?: null));
    } catch (PDOException $e) {
        error_log('[GHD][forum] Création canal : ' . $e->getMessage());
        $resultat['erreurs']['general'] = 'Création impossible. Réessaie.';
        return $resultat;
    }
    redirigerAvecMessageForum(URL_LISTE_SECTIONS, 'succes', 'Section créée.');
}

function traiterModificationSection(CanalForumRepository $repo, CanalForum $canal): array
{
    exigerAdminForum();
    $resultat = ['valeurs' => [
        'nom' => $canal->getNomCanal(),
        'description' => (string) $canal->getDescription(),
        'acces' => $canal->getAcces(),
    ], 'erreurs' => []];
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') return $resultat;
    $saisie = validerSaisieSection($_POST);
    $resultat['valeurs'] = array_intersect_key($saisie, $resultat['valeurs']);
    if (!verifierCsrfForum()) {
        $resultat['erreurs']['general'] = 'Requête invalide. Actualise la page et réessaie.';
    } else {
        $resultat['erreurs'] = $saisie['erreurs'];
    }
    if ($resultat['erreurs']) return $resultat;
    $canal->setNomCanal($saisie['nom']);
    $canal->setAcces($saisie['acces']);
    $canal->setDescription($saisie['description'] ?: null);
    try {
        $repo->modifierCanalForum($canal);
    } catch (PDOException $e) {
        error_log('[GHD][forum] Modification canal : ' . $e->getMessage());
        $resultat['erreurs']['general'] = 'Modification impossible. Réessaie.';
        return $resultat;
    }
    redirigerAvecMessageForum(URL_LISTE_SECTIONS, 'succes', 'Section modifiée.');
}

/** Suppression uniquement en POST et après contrôle du nombre de publications. */
function traiterSuppressionSection(CanalForumRepository $repo, CanalForum $canal): ?string
{
    exigerAdminForum();
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') return 'Méthode non autorisée.';
    if (!verifierCsrfForum()) return 'Requête invalide. Actualise la page et réessaie.';
    try {
        $repo->supprimerCanalForum((int) $canal->getIdCanal());
    } catch (RuntimeException $e) {
        return $e->getMessage();
    } catch (PDOException $e) {
        error_log('[GHD][forum] Suppression canal : ' . $e->getMessage());
        return 'Suppression impossible. Vérifie les dépendances du canal.';
    }
    redirigerAvecMessageForum(URL_LISTE_SECTIONS, 'succes', 'Section supprimée.');
}
