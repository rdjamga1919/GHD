<?php
// src/traitement/admin/traitementGestionSpecialites.php
// Traitements du module d'administration des spécialités (ADMIN-02).
// Inclus par public/admin/gestion_specialites.php, après authentification.

require_once __DIR__ . '/../../repository/SpecialiteRepository.php';

const SPEC_PAGE = 'gestion_specialites.php';

/* ---------- Utilitaires (préfixe "spec" pour éviter les collisions avec d'autres modules) ---------- */

function specHtml($valeur): string
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}

function specRediriger(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function specAjouterFlash(string $type, string $message): void
{
    $_SESSION['flash_specialite'] = ['type' => $type, 'message' => $message];
}

function specLireFlash(): ?array
{
    $flash = $_SESSION['flash_specialite'] ?? null;
    unset($_SESSION['flash_specialite']);
    return $flash;
}

/** @return int|null entier >= 1, sinon null */
function specIdValide($valeur): ?int
{
    $id = filter_var($valeur, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? null : $id;
}

/*
 * CSRF : aucun outil existant n'a été fourni pour ce module (src/traitement/securite.php
 * non transmis). Ces deux fonctions sont à remplacer par celles du projet si elles existent.
 */
function specJetonCsrf(): string
{
    if (empty($_SESSION['csrf_token_specialites'])) {
        $_SESSION['csrf_token_specialites'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token_specialites'];
}

function specVerifierCsrf($jeton): bool
{
    return is_string($jeton)
        && !empty($_SESSION['csrf_token_specialites'])
        && hash_equals($_SESSION['csrf_token_specialites'], $jeton);
}

/* ---------- Traitements (écritures en POST uniquement) ---------- */

const SPEC_MESSAGE_CSRF = "Formulaire invalide ou expiré. Veuillez réessayer.";

/**
 * Création. Redirige vers la liste en cas de succès ;
 * sinon renvoie ['erreur' => ..., 'libelle' => ...] pour réafficher le formulaire.
 */
function specTraiterCreation(): array
{
    $libelle = trim((string) ($_POST['libelle'] ?? ''));

    if (!specVerifierCsrf($_POST['csrf_token'] ?? null)) {
        return ['erreur' => SPEC_MESSAGE_CSRF, 'libelle' => $libelle];
    }

    try {
        (new SpecialiteRepository())->create(new Specialite(null, $libelle));
    } catch (SpecialiteException $e) {
        return ['erreur' => $e->getMessage(), 'libelle' => $libelle];
    }

    specAjouterFlash('succes', "La spécialité a été créée.");
    specRediriger(SPEC_PAGE);
}

function specTraiterModification(int $id): array
{
    $libelle = trim((string) ($_POST['libelle'] ?? ''));

    if (!specVerifierCsrf($_POST['csrf_token'] ?? null)) {
        return ['erreur' => SPEC_MESSAGE_CSRF, 'libelle' => $libelle];
    }

    try {
        (new SpecialiteRepository())->update(new Specialite($id, $libelle));
    } catch (SpecialiteException $e) {
        return ['erreur' => $e->getMessage(), 'libelle' => $libelle];
    }

    specAjouterFlash('succes', "La spécialité a été modifiée.");
    specRediriger(SPEC_PAGE);
}

/**
 * Suppression : POST + CSRF + contrôle des rattachements (dans le repository).
 * Redirige toujours vers la liste avec un message.
 */
function specTraiterSuppression(): void
{
    if (!specVerifierCsrf($_POST['csrf_token'] ?? null)) {
        specAjouterFlash('erreur', SPEC_MESSAGE_CSRF);
        specRediriger(SPEC_PAGE);
    }

    $id = specIdValide($_POST['id_specialite'] ?? null);
    if ($id === null) {
        specAjouterFlash('erreur', "Identifiant de spécialité invalide.");
        specRediriger(SPEC_PAGE);
    }

    try {
        (new SpecialiteRepository())->delete($id);
        specAjouterFlash('succes', "La spécialité a été supprimée.");
    } catch (SpecialiteException $e) {
        specAjouterFlash('erreur', $e->getMessage());
    }

    specRediriger(SPEC_PAGE);
}