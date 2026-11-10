<?php
// Le repository commun des canaux contient une sortie blanche avant <?php.
// Isoler le chargement des dépendances pour préserver les en-têtes HTTP.
ob_start();
try {
    require_once __DIR__ . '/outils_forum.php';
    require_once __DIR__ . '/../../repository/PostForumRepository.php';
    require_once __DIR__ . '/../../repository/CanalForumRepository.php';
} finally {
    ob_end_clean();
}

const URL_LISTE_PUBLICATIONS = '/GHD/public/publications.php';
const URL_DETAIL_PUBLICATION = '/GHD/public/publication.php';
const URL_CREER_PUBLICATION = '/GHD/public/creer_publication.php';
const URL_MODIFIER_PUBLICATION = '/GHD/public/modifier_publication.php';
const PUBLICATION_TITRE_MAX = 150;
// TEXT MySQL : capacité en octets, y compris pour les caractères UTF-8.
const PUBLICATION_CONTENU_MAX_OCTETS = 65535;

function estAuteurPublication(array $publication): bool
{
    return isLoggedIn()
        && (int) $publication['ref_utilisateur'] === (int) $_SESSION['utilisateur_id'];
}

function exigerAuteurPublication(array $publication): void
{
    requireLogin();
    if (!estAuteurPublication($publication)) {
        http_response_code(403);
        exit('Seul l’auteur peut modifier ou supprimer cette publication.');
    }
    $canal = new CanalForum($publication['ref_canal'], $publication['nom_canal'], $publication['acces']);
    if (!peutConsulterCanalForum($canal)) {
        http_response_code(403);
        exit('Accès refusé à ce canal.');
    }
}

function chargerCanalPublication(CanalForumRepository $repo, ?int $id): CanalForum
{
    $canal = $id === null ? null : $repo->getCanalForum($id);
    if ($canal === null) {
        http_response_code(404);
        exit('Canal introuvable.');
    }
    if (!peutConsulterCanalForum($canal)) {
        http_response_code(403);
        exit('Accès refusé à ce canal.');
    }
    return $canal;
}

function chargerPublicationForum(PostForumRepository $repo, CanalForumRepository $canaux, ?int $id): array
{
    // Un identifiant interdit n'expose ni titre, ni contenu, ni auteur.
    $publication = $id === null ? null : $repo->getPublicationVisible($id, (string) ($_SESSION['role'] ?? ''));
    if ($publication === null) {
        http_response_code(404);
        exit('Publication introuvable ou inaccessible.');
    }
    chargerCanalPublication($canaux, (int) $publication['ref_canal']);
    return $publication;
}

function validerPublication(array $post, bool $creation): array
{
    $valeurs = [];
    $erreurs = [];
    foreach (['titre', 'contenu'] as $champ) {
        $brut = $post[$champ] ?? '';
        $valeurs[$champ] = is_string($brut) ? trim($brut) : '';
        if ($valeurs[$champ] === '') {
            $erreurs[$champ] = $champ === 'titre' ? 'Le titre est obligatoire.' : 'Le contenu est obligatoire.';
        } elseif (!mb_check_encoding($valeurs[$champ], 'UTF-8') || str_contains($valeurs[$champ], "\0")) {
            $erreurs[$champ] = 'Le texte doit être un texte UTF-8 valide sans caractère nul.';
        }
    }
    if (!isset($erreurs['titre']) && mb_strlen($valeurs['titre'], 'UTF-8') > PUBLICATION_TITRE_MAX) {
        $erreurs['titre'] = 'Le titre ne doit pas dépasser 150 caractères.';
    }
    if (!isset($erreurs['contenu']) && strlen($valeurs['contenu']) > PUBLICATION_CONTENU_MAX_OCTETS) {
        $erreurs['contenu'] = 'Le contenu dépasse la capacité de 65 535 octets.';
    }
    if ($creation) {
        $valeurs['id_canal'] = is_string($post['id_canal'] ?? null) ? $post['id_canal'] : '';
        if (lireIdSection($valeurs['id_canal']) === null) {
            $erreurs['id_canal'] = 'Choisis un canal valide.';
        }
    }
    return ['valeurs' => $valeurs, 'erreurs' => $erreurs];
}

function traiterCreationPublication(PostForumRepository $repo, CanalForumRepository $canaux, ?int $idCanal = null): array
{
    requireLogin();
    $resultat = ['valeurs' => ['titre' => '', 'contenu' => '', 'id_canal' => (string) ($idCanal ?? '')], 'erreurs' => []];
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return $resultat;
    }
    $resultat = validerPublication($_POST, true);
    if (!verifierCsrfForum()) {
        http_response_code(403);
        $resultat['erreurs']['general'] = 'Requête invalide. Recharge le formulaire et réessaie.';
    }
    if ($resultat['erreurs']) {
        return $resultat;
    }
    try {
        $canal = $canaux->getCanalForum((int) $resultat['valeurs']['id_canal']);
        if ($canal === null || !peutConsulterCanalForum($canal)) {
            $resultat['erreurs']['id_canal'] = 'Canal inexistant ou inaccessible.';
            return $resultat;
        }
        $post = new PostForum(null, (int) $_SESSION['utilisateur_id'], (int) $canal->getIdCanal(),
            date('Y-m-d H:i:s'), $resultat['valeurs']['contenu'], $resultat['valeurs']['titre']);
        $repo->ajouterPostForum($post);
        redirigerAvecMessageForum(URL_DETAIL_PUBLICATION . '?id=' . (int) $post->getIdPost(), 'succes', 'Publication créée.');
    } catch (PDOException $e) {
        error_log('[GHD][publications] Création : ' . $e->getMessage());
        $resultat['erreurs']['general'] = 'Enregistrement impossible. Réessaie ultérieurement.';
        return $resultat;
    }
}

function traiterModificationPublication(PostForumRepository $repo, array $publication): array
{
    exigerAuteurPublication($publication);
    $resultat = ['valeurs' => ['titre' => $publication['titre'], 'contenu' => $publication['contenu']], 'erreurs' => []];
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return $resultat;
    }
    $resultat = validerPublication($_POST, false);
    if (!verifierCsrfForum()) {
        http_response_code(403);
        $resultat['erreurs']['general'] = 'Requête invalide. Recharge le formulaire et réessaie.';
    }
    if ($resultat['erreurs']) {
        return $resultat;
    }
    try {
        $post = new PostForum($publication['id_post'], (int) $_SESSION['utilisateur_id'], $publication['ref_canal'],
            $publication['date_heure_creation'], $resultat['valeurs']['contenu'], $resultat['valeurs']['titre']);
        // Le SQL ne modifie ni l'auteur, ni le canal, ni la date de création.
        $repo->modifierPostForum($post);
        redirigerAvecMessageForum(URL_DETAIL_PUBLICATION . '?id=' . (int) $post->getIdPost(), 'succes', 'Publication modifiée.');
    } catch (PDOException $e) {
        error_log('[GHD][publications] Modification : ' . $e->getMessage());
        $resultat['erreurs']['general'] = 'Modification impossible. Réessaie ultérieurement.';
        return $resultat;
    }
}

function traiterSuppressionPublication(PostForumRepository $repo, array $publication): ?string
{
    exigerAuteurPublication($publication);
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        http_response_code(405);
        return 'La suppression nécessite une requête POST.';
    }
    if (!verifierCsrfForum()) {
        http_response_code(403);
        return 'Requête invalide. Recharge la page et réessaie.';
    }
    if (($_POST['confirmer_suppression'] ?? '') !== '1') {
        return 'Coche la confirmation pour supprimer cette publication.';
    }
    try {
        $repo->supprimerPostForumAuteur((int) $publication['id_post'], (int) $_SESSION['utilisateur_id']);
    } catch (DomainException $e) {
        return $e->getMessage();
    } catch (PDOException $e) {
        error_log('[GHD][publications] Suppression : ' . $e->getMessage());
        return 'Suppression impossible : la publication peut être liée à d’autres données.';
    }
    redirigerAvecMessageForum(URL_LISTE_PUBLICATIONS . '?id_canal=' . (int) $publication['ref_canal'], 'succes', 'Publication supprimée.');
}

function erreurTechniquePublication(Throwable $e): void
{
    error_log('[GHD][publications] Chargement : ' . $e->getMessage());
    http_response_code(500);
    exit('Les publications sont momentanément indisponibles.');
}
