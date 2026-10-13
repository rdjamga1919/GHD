# GDH

Projet réalisé dans le cadre du BTS SIO SLAM.

## Présentation

Application web développée en PHP orienté objet permettant de gérer les différents acteurs et fonctionnalités de la plateforme GDH.

## Technologies

- PHP
- MySQL
- HTML / CSS
- JavaScript
- Git / GitHub

## Architecture

- `public/` : pages accessibles depuis le site
- `src/` : classes PHP et logique applicative
- `traitements/` : traitement des formulaires et actions
- `database/` : scripts SQL
- `assets/` : CSS, JavaScript et images
- `uploads/` : fichiers envoyés par les utilisateurs

## Configuration MySQL locale

1. Copier `src/bdd/Bdd.example.php` vers `src/bdd/Bdd.php`, sans écraser une configuration locale existante.
2. Dans `Bdd.php`, remplacer les valeurs d'exemple par le nom de la base, l'hôte MySQL, l'utilisateur et le mot de passe de votre installation MAMP, WAMP ou XAMPP. Si nécessaire, préciser le port dans le DSN avec `;port=VOTRE_PORT_MYSQL`. Utiliser le nom réel de la base importée depuis le script de `database/`.
3. Ne pas versionner `Bdd.php` : il est exclu par `.gitignore`. Seul `Bdd.example.php`, sans identifiants réels, doit être partagé.

L'application charge uniquement `Bdd.php` ; ne pas inclure simultanément le fichier d'exemple, qui déclare lui aussi la classe `Bdd`.
