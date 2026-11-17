<?php
/** Métadonnées de présentation uniquement : aucune requête ni permission ici. */
$layoutFile = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
$layoutPages = [
    'index.php' => ['Accueil', 'accueil'], 'connexion.php' => ['Connexion', 'connexion'],
    'inscription.php' => ['Inscription', 'inscription'], 'profil.php' => ['Mon profil', 'profil'],
    'modifier_profil.php' => ['Modifier mon profil', 'profil'], 'annuaire.php' => ['Annuaire', 'annuaire'],
    'entreprise.php' => ['Entreprises partenaires', 'annuaire'], 'etablissement.php' => ['Établissements', 'annuaire'],
    'offres.php' => ['Offres', 'offres'], 'offre_detail.php' => ['Détail de l’offre', 'offres'],
    'creer_offre.php' => ['Créer une offre', 'offres'], 'modifier_offre.php' => ['Modifier une offre', 'offres'],
    'postuler.php' => ['Postuler', 'offres'], 'mes_candidatures.php' => ['Mes candidatures', 'candidatures'],
    'candidatures_recues.php' => ['Candidatures reçues', 'offres'],
    'evenements.php' => ['Événements', 'evenements'], 'evenement.php' => ['Détail de l’événement', 'evenements'],
    'evenement_detail.php' => ['Détail de l’événement', 'evenements'],
    'evenement_creer.php' => ['Créer un événement', 'evenements'],
    'evenement_modifier.php' => ['Modifier un événement', 'evenements'],
    'evenement_supprimer.php' => ['Supprimer un événement', 'evenements'],
    'forum.php' => ['Forum', 'forum'], 'section.php' => ['Section', 'forum'],
    'creer_section.php' => ['Créer une section', 'forum'], 'modifier_section.php' => ['Modifier une section', 'forum'],
    'publications.php' => ['Publications', 'forum'], 'publication.php' => ['Publication', 'forum'],
    'creer_publication.php' => ['Créer une publication', 'forum'], 'modifier_publication.php' => ['Modifier', 'forum'],
    'a_propos.php' => ['À propos', ''], 'design_demo.php' => ['Composants graphiques', ''],
    'GestionUtilisateurs.php' => ['Gestion des utilisateurs', 'admin'], 'test_page_admin.php' => ['Test des permissions', 'admin'],
];
$layoutMeta = $layoutPages[$layoutFile] ?? ['GHD-HSP', ''];
$pageTitle = $pageTitle ?? $layoutMeta[0];
$activePage = $activePage ?? $layoutMeta[1];
$layoutNarrow = in_array($layoutFile, ['connexion.php', 'inscription.php', 'modifier_profil.php',
    'creer_offre.php', 'modifier_offre.php', 'postuler.php', 'evenement_creer.php', 'evenement_modifier.php',
    'evenement_supprimer.php', 'creer_section.php', 'modifier_section.php', 'creer_publication.php', 'modifier_publication.php'], true);
$layoutVariant = $layoutNarrow ? 'form' : match ($layoutFile) {
    'offre_detail.php', 'evenement.php', 'evenement_detail.php', 'publication.php', 'section.php', 'profil.php' => 'detail',
    'a_propos.php' => 'text',
    default => '',
};
$layoutBodyClass = 'page-' . strtolower(pathinfo($layoutFile, PATHINFO_FILENAME));

if (!isset($breadcrumbs)) {
    $breadcrumbs = $layoutFile === 'index.php' ? [] : [['label' => 'Accueil', 'url' => '/GHD/public/index.php']];
    $layoutAdd = static function (string $label, ?string $url = null) use (&$breadcrumbs): void {
        $breadcrumbs[] = ['label' => $label, 'url' => $url];
    };
    $layoutCanalId = null;
    $layoutCanalNom = null;
    if (isset($publication) && is_array($publication) && isset($publication['ref_canal'], $publication['nom_canal'])) {
        $layoutCanalId = (int) $publication['ref_canal'];
        $layoutCanalNom = (string) $publication['nom_canal'];
    } elseif (isset($section) && is_object($section) && method_exists($section, 'getIdCanal')) {
        $layoutCanalId = (int) $section->getIdCanal();
        $layoutCanalNom = (string) $section->getNomCanal();
    } elseif (isset($canal) && is_object($canal) && method_exists($canal, 'getIdCanal')) {
        $layoutCanalId = (int) $canal->getIdCanal();
        $layoutCanalNom = (string) $canal->getNomCanal();
    } elseif (isset($canaux) && is_array($canaux)) {
        foreach ($canaux as $layoutOption) {
            if ((string) $layoutOption->getIdCanal() === (string) ($valeurs['id_canal'] ?? $idCanal ?? '')) {
                $layoutCanalId = (int) $layoutOption->getIdCanal();
                $layoutCanalNom = (string) $layoutOption->getNomCanal();
                break;
            }
        }
    }
    if ($layoutMeta[1] === 'forum') {
        if ($layoutFile !== 'forum.php') $layoutAdd('Forum', '/GHD/public/forum.php');
        if ($layoutCanalId !== null && $layoutCanalNom !== null) {
            $layoutAdd($layoutCanalNom, $layoutFile === 'section.php' ? null : '/GHD/public/section.php?id=' . $layoutCanalId);
        }
        if ($layoutFile === 'publication.php') {
            $layoutAdd((string) ($publication['titre'] ?? 'Publication'));
        } elseif ($layoutFile === 'modifier_publication.php') {
            $layoutAdd((string) $publication['titre'], '/GHD/public/publication.php?id=' . (int) $publication['id_post']);
            $layoutAdd('Modifier');
        } elseif ($layoutFile !== 'section.php' || $layoutCanalId === null) {
            $layoutAdd($layoutMeta[0]);
        }
    } elseif ($layoutMeta[1] === 'evenements') {
        if ($layoutFile !== 'evenements.php') $layoutAdd('Événements', '/GHD/public/evenements.php');
        $layoutTitreEvenement = isset($evenement) && is_object($evenement) && method_exists($evenement, 'getTitre')
            ? (string) $evenement->getTitre() : (is_array($evenement ?? null) ? (string) ($evenement['titre'] ?? '') : '');
        $layoutIdEvenement = isset($evenement) && is_object($evenement) && method_exists($evenement, 'getIdEvenement')
            ? (int) $evenement->getIdEvenement() : (int) ($evenement['id_evenement'] ?? 0);
        if (in_array($layoutFile, ['evenement_modifier.php', 'evenement_supprimer.php'], true) && $layoutIdEvenement > 0) {
            $layoutAdd($layoutTitreEvenement, '/GHD/public/evenement_detail.php?id=' . $layoutIdEvenement);
        }
        $layoutAdd(in_array($layoutFile, ['evenement.php', 'evenement_detail.php'], true) && $layoutTitreEvenement !== '' ? $layoutTitreEvenement : $layoutMeta[0]);
    } elseif ($layoutMeta[1] === 'offres') {
        if ($layoutFile !== 'offres.php') $layoutAdd('Offres', '/GHD/public/offres.php');
        if (in_array($layoutFile, ['modifier_offre.php', 'postuler.php', 'candidatures_recues.php'], true) && isset($offre['id_offre'])) {
            $layoutAdd((string) $offre['titre'], '/GHD/public/offre_detail.php?id=' . (int) $offre['id_offre']);
        }
        $layoutAdd($layoutFile === 'offre_detail.php' ? (string) ($offre['titre'] ?? $layoutMeta[0]) : $layoutMeta[0]);
    } else {
        if ($layoutFile === 'modifier_profil.php') $layoutAdd('Mon profil', '/GHD/public/profil.php');
        if (in_array($layoutFile, ['entreprise.php', 'etablissement.php'], true) && isset($_SESSION['utilisateur_id'])) {
            $layoutAdd('Annuaire', '/GHD/public/annuaire.php');
        }
        if ($layoutFile !== 'index.php') $layoutAdd($layoutMeta[0]);
    }
}
