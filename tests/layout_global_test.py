"""python3 tests/layout_global_test.py — vérifications de présentation sans MySQL."""
from pathlib import Path
from html.parser import HTMLParser
import base64
import json
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
checks = 0


def check(ok, message):
    global checks
    checks += 1
    if not ok:
        raise AssertionError(message)


def render(config):
    result = subprocess.run(['php', '-d', 'display_errors=stderr', '-d', 'log_errors=0',
        str(ROOT / 'tests/layout_global_render.php'), base64.b64encode(json.dumps(config).encode()).decode()],
        cwd=ROOT, capture_output=True, text=True)
    check(result.returncode == 0 and not result.stderr, str(config) + '\n' + result.stderr)
    return result.stdout


class Structure(HTMLParser):
    def __init__(self):
        super().__init__()
        self.stack = []
        self.errors = []
        self.links = []
        self.in_breadcrumb = False
        self.crumbs = []

    def handle_starttag(self, tag, attributes):
        attrs = dict(attributes)
        if tag == 'nav' and attrs.get('aria-label') == 'Fil d’Ariane':
            self.in_breadcrumb = True
        if tag == 'a':
            self.links.append(attrs.get('href', ''))
        if tag not in {'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'}:
            self.stack.append(tag)

    def handle_data(self, text):
        if self.in_breadcrumb and text.strip():
            self.crumbs.append(text.strip())

    def handle_endtag(self, tag):
        if tag == 'nav':
            self.in_breadcrumb = False
        if not self.stack or self.stack[-1] != tag:
            self.errors.append((tag, self.stack[-4:]))
        else:
            self.stack.pop()


def validate(config):
    html = render(config)
    parser = Structure()
    parser.feed(html)
    check(not parser.errors and not parser.stack, 'HTML équilibré : ' + str(config) + str(parser.errors))
    for tag in ['html', 'head', 'body', 'main']:
        check(len(re.findall('<' + tag + r'(?:\s|>)', html, re.I)) == 1, 'Un seul ' + tag)
    check(html.count('class="site-header"') == 1, 'Un seul header partagé')
    check(html.count('class="site-footer"') == 1, 'Un seul footer partagé')
    for asset in ['variables.css', 'style.css', 'components.css', 'layout.css', 'pages.css', 'main.js']:
        check(len(re.findall('/' + re.escape(asset) + r'(?:\?[^"]*)?"', html)) == 1, 'Une seule inclusion ' + asset)
    for link in parser.links:
        if link.startswith('/GHD/'):
            target = ROOT / link.removeprefix('/GHD/').split('?')[0].split('#')[0]
            check(target.is_file(), 'Destination locale réelle : ' + link)
    if config['page'] != 'index.php':
        check(len(parser.crumbs) >= 2, 'Fil d’Ariane présent')
    else:
        check(not parser.crumbs, 'Pas de fil sur l’accueil')
    return html, parser


views = [
    ('profil.php', 'views/profils/etudiantView.php'), ('profil.php', 'views/profils/medecinView.php'),
    ('profil.php', 'views/profils/partenaireView.php'),
    ('modifier_profil.php', 'views/profils/modification/etudiantViewModif.php'),
    ('modifier_profil.php', 'views/profils/modification/medecinViewModif.php'),
    ('modifier_profil.php', 'views/profils/modification/partenaireViewModif.php'),
    ('offres.php', 'views/offres/liste.php'), ('offre_detail.php', 'views/offres/detail.php'),
    ('creer_offre.php', 'views/offres/formulaire.php'), ('modifier_offre.php', 'views/offres/formulaire.php'),
    ('evenement_detail.php', 'views/evenements/detail.php'),
    ('evenement_creer.php', 'views/evenements/evenement_creation_view.php'),
    ('evenement_modifier.php', 'views/evenements/evenement_modification_view.php'),
    ('evenement_supprimer.php', 'views/evenements/evenement_supprimer_view.php'),
    ('evenements.php', 'views/evenements/listeView.php'), ('evenement.php', 'views/evenements/detailView.php'),
]
for page, view in views:
    validate({'page': page, 'view': view, 'evenement_tableau': view.endswith('detailView.php')})
for page in ['connexion.php', 'inscription.php', 'annuaire.php', 'mes_candidatures.php',
             'candidatures_recues.php', 'postuler.php', 'entreprise.php', 'etablissement.php', 'profil.php']:
    validate({'page': page, 'public_body': True})
for page in ['index.php', 'a_propos.php', 'design_demo.php', 'admin/test_page_admin.php']:
    validate({'page': page, 'public_full': True, 'admin': page.startswith('admin/')})
validate({'page': 'admin/GestionUtilisateurs.php', 'public_body': True, 'admin': True})

for page, expected, extra in [
    ('modifier_profil.php', ['Accueil', 'Mon profil', 'Modifier mon profil'], {}),
    ('publication.php', ['Accueil', 'Forum', 'Général', 'Publication fictive'], {'publication_fixture': True}),
    ('modifier_publication.php', ['Accueil', 'Forum', 'Général', 'Publication fictive', 'Modifier'], {'publication_fixture': True}),
    ('creer_publication.php', ['Accueil', 'Forum', 'Général', 'Créer une publication'], {'canal_fixture': True}),
    ('evenement_detail.php', ['Accueil', 'Événements', 'Événement fictif'], {}),
]:
    _, parser = validate({'page': page, **extra})
    check(parser.crumbs == expected, 'Hiérarchie exacte : ' + page + repr(parser.crumbs))

# Classification des largeurs : pas de mesure ni de test visuel navigateur.
for page, variant in [('offre_detail.php', 'detail'), ('evenement_detail.php', 'detail'),
                      ('publication.php', 'detail'), ('profil.php', 'detail'),
                      ('creer_publication.php', 'form'), ('inscription.php', 'form'),
                      ('modifier_profil.php', 'form'), ('a_propos.php', 'text')]:
    html = render({'page': page})
    check('layout-content--' + variant in html, 'Variante du conteneur : ' + page)
css = (ROOT / 'public/assets/css/layout.css').read_text()
check('position: sticky;' in css and 'top: 0;' in css, 'Header sticky déclaré')
check('.site-layout .breadcrumb > ol' in css and 'list-style: none;' in css, 'Fil horizontal dédié sans numérotation')
check('width: min(calc(100% - 2 * var(--page-gutter)), var(--layout-max))' in css, 'Gouttières et largeur bornée déclarées')

for url in ['javascript:alert(1)', 'https://example.invalid', '//example.invalid', '/GHD/public/../../secret.php',
            '/GHD/public/%2e%2e/secret.php', '/GHD/public/inexistant.php', '/GHD/public/index.php\\evil',
            '/GHD/public/index.php\nX-Test: injection']:
    html = render({'page': 'connexion.php', 'breadcrumbs': [{'label': '<script>test</script>', 'url': url}, {'label': 'Courante', 'url': '/GHD/public/index.php'}]})
    check('href=' not in html, 'URL refusée et dernier élément non cliquable : ' + url)
    check('<script>test</script>' not in html and '&lt;script&gt;' in html, 'Libellé échappé')
html = render({'page': 'connexion.php', 'breadcrumbs': [{'label': 'Accueil', 'url': '/GHD/public/index.php'}, {'label': 'Connexion'}]})
check('href="/GHD/public/index.php"' in html and 'aria-current="page"' in html, 'URL interne valide et page courante')

# Contrats des formulaires : comparer aux fichiers de la référence initiale,
# en ignorant uniquement les classes et styles de présentation.
def signatures(source):
    php_blocks = []
    def save(match):
        php_blocks.append(match[0])
        return f'PHPBLOCK{len(php_blocks) - 1}END'
    html = re.sub(r'<\?(?:php|=).*?\?>', save, source, flags=re.S)
    class Forms(HTMLParser):
        def __init__(self): super().__init__(); self.tags = []
        def handle_starttag(self, tag, attrs):
            if tag not in ['form', 'input', 'textarea', 'select', 'button']: return
            values = {}
            for key, value in attrs:
                if key in ['class', 'style']: continue
                if value:
                    value = re.sub(r'PHPBLOCK(\d+)END', lambda match: php_blocks[int(match[1])], value)
                    value = re.sub(r'\s+', ' ', value).strip()
                values[key] = value
            self.tags.append((tag, values))
    parser = Forms(); parser.feed(html)
    return parser.tags

for file in subprocess.check_output(['git', 'diff', '--name-only', '64b9b66'], cwd=ROOT, text=True).splitlines():
    if not file.endswith('.php') or file.startswith('tests/'): continue
    before = subprocess.run(['git', 'show', '64b9b66:' + file], cwd=ROOT, capture_output=True, text=True)
    if before.returncode: continue
    check(signatures(before.stdout) == signatures((ROOT / file).read_text()), 'Champs/actions POST inchangés : ' + file)
protected = subprocess.check_output(['git', 'diff', '--name-only', '--', 'src/', 'database/'], cwd=ROOT, text=True)
check(not protected.strip(), 'Aucun modèle, repository, traitement ou schéma modifié')
print(f'{checks} vérifications de layout réussies ; rendu avec données fictives, sans test visuel navigateur ni MySQL.')
