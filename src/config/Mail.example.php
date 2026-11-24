<?php

/**
 * Configuration de l'envoi d'e-mails (modèle).
 *
 * Utilisation :
 *   1. Copier ce fichier vers src/config/Mail.php (fichier local, exclu de Git).
 *   2. Remplacer les valeurs ci-dessous par celles de votre serveur SMTP.
 *   3. Ne jamais versionner Mail.php : il contient des identifiants.
 *
 * Valeurs par défaut : serveur de test local (Mailpit / MailHog) sur le port 1025,
 * sans authentification ni chiffrement. Les e-mails ne quittent pas votre machine.
 */
return [
    // Serveur SMTP et port.
    'host'         => 'localhost',
    'port'         => 1025,

    // Chiffrement : 'tls' (STARTTLS, port 587), 'ssl' (SMTPS, port 465) ou '' (aucun, serveur de test local).
    'encryption'   => '',

    // Authentification : true si le serveur demande un identifiant et un mot de passe.
    'auth'         => false,
    'username'     => '',
    'password'     => '',

    // Expéditeur affiché dans les e-mails envoyés.
    'from_address' => 'no-reply@ghd-hsp.local',
    'from_name'    => 'GHD-HSP',

    // Délai maximal (en secondes) pour joindre le serveur SMTP.
    'timeout'      => 10,
];
