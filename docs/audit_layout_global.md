# Audit initial du layout global GHD-HSP

Référence vérifiée : `test` distant et `feature/layout-global` à `64b9b66` (récupération de `origin/test` avant modification). Répertoire initial propre. Aucun commit, merge ou push.

Les statuts ci-dessous sont relevés **avant les corrections**. Les contrôleurs déléguant à une vue sont analysés avec cette vue. « Actions » signifie présence de liens/boutons, sans garantir leur validité. Le fil d’Ariane était absent partout.

| Page | Header initial | Footer initial | CSS initial | Conteneur | Actions | Fil pertinent | Statut initial |
|---|---|---|---|---|---|---|---|
| public/a_propos.php | Codé en dur | Codé en dur | CSS locaux | Oui | Oui | Oui | À harmoniser |
| public/admin/GestionUtilisateurs.php | Absent | Absent | Absents | Non | Oui | Oui | À harmoniser |
| public/admin/dashboard.php | Absent | Absent | Absents | Non | Non | Non | Hors périmètre : fichier vide |
| public/admin/gestion_evenements.php | Absent | Absent | Absents | Non | Non | Non | Hors périmètre : fichier vide |
| public/admin/gestion_forum.php | Absent | Absent | Absents | Non | Non | Non | Hors périmètre : fichier vide |
| public/admin/gestion_hopitaux.php | Absent | Absent | Absents | Non | Non | Non | Hors périmètre : fichier vide |
| public/admin/gestion_offres.php | Absent | Absent | Absents | Non | Non | Non | Hors périmètre : fichier vide |
| public/admin/gestion_specialites.php | Absent | Absent | Absents | Non | Non | Non | Hors périmètre : fichier vide |
| public/admin/test_page_admin.php | Absent | Absent | Absents | Non | Oui | Oui | À harmoniser ; inclusion auth inexistante |
| public/annuaire.php | Absent | Absent | CSS inline | Non | Oui | Oui | À harmoniser |
| public/candidatures_recues.php | Absent | Absent | CSS inline | Non | Oui | Oui | À harmoniser |
| public/connexion.php | Absent | Absent | CSS inline | Oui | Oui | Oui | À harmoniser |
| public/contact.php | Absent | Absent | Absents | Non | Non | Non | Hors périmètre : fichier vide |
| public/creer_offre.php | Absent | Absent | Absents | Non | Non | Oui | À harmoniser |
| public/creer_publication.php | Partagé | Partagé | CSS partagés | Non | Non | Oui | Layout partagé, fil manquant |
| public/creer_section.php | Partagé | Partagé | CSS partagés | Non | Non | Oui | Layout partagé, fil manquant |
| public/design_demo.php | Codé en dur | Codé en dur | CSS locaux | Oui | Oui | Oui | À harmoniser |
| public/entreprise.php | Absent | Absent | CSS inline | Oui | Oui | Oui | À harmoniser |
| public/etablissement.php | Absent | Absent | Absents | Non | Oui | Oui | À harmoniser |
| public/evenement.php | Partagé | Partagé | CSS partagés | Non | Non | Oui | Layout partagé, fil manquant |
| public/evenement_creer.php | Absent | Absent | Absents | Non | Non | Oui | À harmoniser |
| public/evenement_detail.php | Absent | Absent | Absents | Non | Non | Oui | À harmoniser |
| public/evenement_modifier.php | Absent | Absent | Absents | Non | Non | Oui | À harmoniser |
| public/evenement_supprimer.php | Absent | Absent | Absents | Non | Non | Oui | À harmoniser |
| public/evenements.php | Partagé | Partagé | CSS partagés | Non | Non | Oui | Layout partagé, fil manquant |
| public/forum.php | Partagé | Partagé | CSS partagés | Non | Non | Oui | Layout partagé, fil manquant |
| public/index.php | Partagé | Partagé | CSS partagés | Oui | Oui | Non | Layout partagé, fil manquant |
| public/inscription.php | Absent | Absent | CSS inline | Oui | Oui | Oui | À harmoniser |
| public/mes_candidatures.php | Absent | Absent | CSS inline | Oui | Oui | Oui | À harmoniser |
| public/modifier_offre.php | Absent | Absent | Absents | Non | Non | Oui | À harmoniser |
| public/modifier_profil.php | Absent | Absent | Absents | Non | Non | Oui | À harmoniser |
| public/modifier_publication.php | Partagé | Partagé | CSS partagés | Non | Non | Oui | Layout partagé, fil manquant |
| public/modifier_section.php | Partagé | Partagé | CSS partagés | Non | Non | Oui | Layout partagé, fil manquant |
| public/offre_detail.php | Absent | Absent | Absents | Non | Non | Oui | À harmoniser |
| public/offres.php | Absent | Absent | Absents | Non | Non | Oui | À harmoniser |
| public/postuler.php | Absent | Absent | CSS inline | Non | Oui | Oui | À harmoniser |
| public/profil.php | Absent | Absent | Absents | Non | Oui | Oui | À harmoniser |
| public/publication.php | Partagé | Partagé | CSS partagés | Non | Non | Oui | Layout partagé, fil manquant |
| public/publications.php | Partagé | Partagé | CSS partagés | Non | Non | Oui | Layout partagé, fil manquant |
| public/section.php | Partagé | Partagé | CSS partagés | Non | Non | Oui | Layout partagé, fil manquant |

## Écarts avant intervention

- Connexion/inscription, profils, annuaire, entreprises, offres et candidatures : documents autonomes et CSS locaux souvent dupliqués.
- Profil : le contrôleur et les vues de rôles ouvrent chacun un document HTML.
- Événements : liste/détail avec layout partagé, mais autres détails et formulaires autonomes ou sans enveloppe.
- Administration utilisateurs : inclusions des fichiers presque vides `src/inclues/header.php` et `footer.php`.
- Test administrateur : chemin d’authentification inexistant. La règle `requireRole('gestionnaire')` doit rester inchangée.
- À propos : navigation dupliquée, retour dépendant de l’historique et liens vers `contact.php`, qui est vide.
- Démonstrateur : document autonome ; ses exemples de composants ne sont pas des destinations fonctionnelles.
- Forum : composants communs déjà présents, mais pas de fil d’Ariane.
- Fichiers provisoires : les deux vues provisoires des événements existent encore, mais aucun contrôleur public ne les inclut.
- Les pages vides d’administration et de contact ne sont ni des interfaces opérationnelles ni des endpoints à convertir. Aucun écran métier n’est inventé pour elles.

## Composants retenus

`views/layouts/header.php` et `footer.php`, déléguant à `views/includes/header.php` et `footer.php`. `src/inclues/header.php`/`footer.php` ne sont pas des layouts utilisables. La navbar alternative n’est pas utilisée par les pages récentes.

## Résultat de l’harmonisation

- Toutes les pages HTML opérationnelles recensées utilisent désormais les composants partagés, directement ou autour de leur vue.
- Une seule enveloppe html/head/body/main ; vues de rôles et formulaires convertis en fragments.
- Fil d’Ariane partagé dans `views/includes/breadcrumbs.php`, métadonnées dans `page_context.php`. Les métadonnées consomment uniquement les données déjà chargées et peuvent être remplacées via `$breadcrumbs`.
- Liens locaux vérifiés : refus des schémas externes/javascript, des chemins hors public et des destinations inexistantes ; dernier niveau non cliquable ; libellés échappés.
- Variables internes du header renommées pour préserver les variables métier déjà préparées, notamment `$role` à l’inscription et au filtrage de l’annuaire.
- Navigation principale et liens de pied de page adaptés aux rôles ; pas de lien Mon profil pour un gestionnaire dont le contrôleur profil ne gère pas ce rôle. L’accès administrateur pointe sur la vraie page de gestion des utilisateurs.
- Styles locaux déplacés hors PHP, limités à la page dans pages.css. Pas de second jeu de variables : les tokens communs restent la référence. Les styles inline statiques ont été remplacés par des classes réutilisables.
- Layout global centré, largeur de formulaire distincte, footer en flux normal, tableaux dans une zone de défilement locale. Styles responsive réutilisés.
- Retour explicite sur À propos ; retrait des faux liens du démonstrateur ; Annuler ajouté à la création d’événement. Les confirmations déjà présentes et les actions POST sont conservées.
- Aucun fichier de src/ ou database/ modifié ; aucune connexion de test à la base MySQL ; aucun commit, push ou merge.

## Fichiers créés

- `views/includes/page_context.php`
- `views/includes/breadcrumbs.php`
- `public/assets/css/pages.css`
- `tests/layout_global_render.php`
- `tests/layout_global_test.py`
- `docs/audit_layout_global.md`

## Fichiers modifiés

- `public/a_propos.php`
- `public/admin/GestionUtilisateurs.php`
- `public/admin/test_page_admin.php`
- `public/annuaire.php`
- `public/assets/css/components.css`
- `public/assets/css/layout.css`
- `public/candidatures_recues.php`
- `public/connexion.php`
- `public/creer_offre.php`
- `public/design_demo.php`
- `public/entreprise.php`
- `public/etablissement.php`
- `public/evenement_creer.php`
- `public/evenement_detail.php`
- `public/evenement_modifier.php`
- `public/evenement_supprimer.php`
- `public/inscription.php`
- `public/mes_candidatures.php`
- `public/modifier_offre.php`
- `public/modifier_profil.php`
- `public/offre_detail.php`
- `public/offres.php`
- `public/postuler.php`
- `public/profil.php`
- `tests/forum_publications_test.php`
- `views/evenements/detail.php`
- `views/evenements/detailView.php`
- `views/evenements/evenement_creation_view.php`
- `views/evenements/evenement_modification_view.php`
- `views/evenements/evenement_supprimer_view.php`
- `views/evenements/listeView.php`
- `views/includes/footer.php`
- `views/includes/header.php`
- `views/offres/detail.php`
- `views/offres/formulaire.php`
- `views/offres/liste.php`
- `views/profils/etudiantView.php`
- `views/profils/medecinView.php`
- `views/profils/modification/etudiantViewModif.php`
- `views/profils/modification/medecinViewModif.php`
- `views/profils/modification/partenaireViewModif.php`
- `views/profils/partenaireView.php`

## Vérifications exécutées

- `php tests/forum_publications_test.php` : **464 contrôles réussis** sur SQLite en mémoire et sessions en mémoire.
- `python3 tests/layout_global_test.py` : **1 121 vérifications réussies**. Rendus de vues avec données fictives et quelques pages publiques sans SQL ; structure HTML, unicité des composants, ressources, liens locaux, hiérarchies et sécurité des URL du fil d’Ariane.
- Comparaison des attributs fonctionnels form/input/select/textarea/button avec la référence initiale 64b9b66 : **39 fichiers identiques**, en ignorant seulement les classes/styles.
- Contrôle syntaxique de **75 fichiers PHP** de public/, views/ et tests/ : réussi.
- `git diff --check` : réussi.
- Recherche des dépendances provisoires : aucune inclusion active dans les contrôleurs. Les deux anciens fichiers restent inutilisés et ne sont pas supprimés.

Les essais ne constituent pas des tests visuels : aucun navigateur ni contrôle de pixels n’a été utilisé. Les rendus préparés n’exécutent pas les requêtes des contrôleurs SQL, sauf les scénarios du forum qui utilisent SQLite de test.

## Vérifications manuelles restantes sur MAMP

1. Vue ordinateur et mobile : header, fil long, footer court/long, tableaux larges et clavier.
2. Connexion/inscription réelles, notamment changement de rôle et conservation des saisies ; aucun attribut ni script fonctionnel du formulaire n’a été supprimé.
3. Profils réels des trois rôles, offres/candidatures, événements et administration avec MySQL.
4. Vérifier les scénarios vides et remplis avec les données d’équipe, ainsi que les réponses 403/404 existantes.
5. Valider les choix visuels : les styles spécifiques historiques ont été conservés dans un périmètre isolé pour limiter les régressions.

## Limites préexistantes conservées

Les fichiers ci-dessous sont vides : ils n’ont pas été transformés en interfaces ni présentés comme des pages opérationnelles.

- `public/admin/dashboard.php`
- `public/admin/gestion_evenements.php`
- `public/admin/gestion_forum.php`
- `public/admin/gestion_hopitaux.php`
- `public/admin/gestion_offres.php`
- `public/admin/gestion_specialites.php`
- `public/contact.php`


## Ajustement de centralisation, fil horizontal et header sticky

Les modifications précédentes sont conservées. Le conteneur `layout-content` porte maintenant seul les marges symétriques : largeur standard 1 152 px, détail 1 000 px, formulaire 800 px, texte 840 px. Les gouttières vont de 16 à 32 px. Le header/footer ont un fond pleine largeur et un contenu interne borné à 1 152 px. Les conteneurs de contenu hérités ne répètent plus padding, marge ou largeur étroite.

La mise en page du fil d’Ariane est explicitement horizontale, repliable et sans marqueur. Le composant PHP et sa sécurité sont conservés. Les ressources CSS/JS ont une version liée à leur date de modification afin de ne pas conserver d’anciennes règles dans le cache du navigateur.

Le header utilise `position: sticky; top: 0` et un fond opaque. `main.js` mesure sa hauteur via ResizeObserver pour le décalage des ancres, avec repli CSS et resize. Aucun événement de navigation ni traitement de formulaire n’est remplacé.

Les titres de détail d’offre et d’événement ont été sortis de la carte pour partager l’axe du fil d’Ariane et des retours. Les actions du formulaire de création d’événement sont regroupées sans changement d’attribut POST.

À la demande de l’utilisatrice, **aucun navigateur n’est lancé et aucun test visuel automatisé n’est effectué**. Les tailles et le comportement sticky doivent être confirmés manuellement dans Safari à 1 440 et 390 px, via MAMP. Les essais locaux ne constituent pas une validation visuelle.

Vérifications locales après cet ajustement : 1 139 contrôles de layout, 464 contrôles du forum, syntaxe PHP des 75 fichiers, `node --check` et `git diff --check`. Aucun test visuel réalisé.
