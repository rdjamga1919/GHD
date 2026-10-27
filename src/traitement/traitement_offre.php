<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../inclues/authentification.php';
require_once __DIR__ . '/../repository/OffreRepository.php';

// Non connecté : redirection vers la connexion. Autre rôle : 403.
requireRole('partenaire');

$publicDir = '../../public/';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $publicDir . 'offres.php');
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = [$type, $message];
}

if (!hash_equals((string) ($_SESSION['csrf'] ?? ''), (string) ($_POST['csrf'] ?? ''))) {
    flash('erreur', 'Requête invalide, veuillez réessayer.');
    header('Location: ' . $publicDir . 'offres.php');
    exit;
}

$idUtilisateur = (int) $_SESSION['utilisateur_id'];
$repo = new OffreRepository();
$action = $_POST['action'] ?? '';

/** Valide le formulaire : retourne [données, erreurs] */
function validerOffre(array $post): array
{
    $types = ['stage', 'alternance', 'cdd', 'cdi'];
    $erreurs = [];

    $d = [
        'titre'        => trim((string) ($post['titre'] ?? '')),
        'description'  => trim((string) ($post['description'] ?? '')),
        'missions'     => trim((string) ($post['missions'] ?? '')),
        'type_d_offre' => (string) ($post['type_d_offre'] ?? ''),
        'salaire'      => null,
    ];

    if ($d['titre'] === '' || mb_strlen($d['titre']) > 150) {
        $erreurs[] = 'Le titre est obligatoire (150 caractères maximum).';
    }
    if ($d['description'] === '') {
        $erreurs[] = 'La description est obligatoire.';
    }
    if ($d['missions'] === '') {
        $erreurs[] = 'Les missions sont obligatoires.';
    }
    if (!in_array($d['type_d_offre'], $types, true)) {
        $erreurs[] = "Le type d'offre est invalide.";
    }

    $salaire = str_replace(',', '.', trim((string) ($post['salaire'] ?? '')));
    if ($salaire !== '') {
        if (!is_numeric($salaire) || (float) $salaire < 0 || (float) $salaire > 99999999.99) {
            $erreurs[] = 'La rémunération doit être un nombre positif.';
        } else {
            $d['salaire'] = number_format((float) $salaire, 2, '.', '');
        }
    }

    return [$d, $erreurs];
}

try {
    if ($action === 'creer') {
        [$donnees, $erreurs] = validerOffre($_POST);

        if (!$repo->partenaireExiste($idUtilisateur)) {
            $erreurs[] = "Votre compte n'est rattaché à aucune entreprise.";
        }

        if ($erreurs) {
            flash('erreur', implode(' ', $erreurs));
            $_SESSION['old'] = $_POST;
            header('Location: ' . $publicDir . 'creer_offre.php');
            exit;
        }

        $id = $repo->insert($donnees, $idUtilisateur);
        flash('succes', 'Offre créée.');
        header('Location: ' . $publicDir . 'offre_detail.php?id=' . $id);
        exit;
    }

    $id = filter_input(INPUT_POST, 'id_offre', FILTER_VALIDATE_INT);
    if ($id === false || $id === null || $id <= 0) {
        flash('erreur', 'Offre invalide.');
        header('Location: ' . $publicDir . 'offres.php');
        exit;
    }

    if ($action === 'modifier') {
        [$donnees, $erreurs] = validerOffre($_POST);

        if ($erreurs) {
            flash('erreur', implode(' ', $erreurs));
            $_SESSION['old'] = $_POST;
            header('Location: ' . $publicDir . 'modifier_offre.php?id=' . $id);
            exit;
        }

        if ($repo->update($id, $donnees, $idUtilisateur)) {
            flash('succes', 'Offre modifiée.');
        } else {
            flash('erreur', "Modification impossible : offre introuvable, fermée ou qui ne vous appartient pas.");
        }
        header('Location: ' . $publicDir . 'offre_detail.php?id=' . $id);
        exit;
    }

    if ($action === 'fermer') {
        if ($repo->fermerOffre($id, $idUtilisateur)) {
            flash('succes', 'Offre fermée : elle n\'accepte plus de candidatures.');
        } else {
            flash('erreur', "Fermeture impossible : offre introuvable, déjà fermée ou qui ne vous appartient pas.");
        }
        header('Location: ' . $publicDir . 'offre_detail.php?id=' . $id);
        exit;
    }

    flash('erreur', 'Action invalide.');

} catch (Throwable $e) {
    flash('erreur', 'DEBUG : ' . $e->getMessage());
}

header('Location: ' . $publicDir . 'offres.php');
exit;