<?php
/**
 * Tests manuels du MailService (ligne de commande uniquement).
 *
 * Usage, à la racine du projet :
 *     php tests/mail_test.php adresse@exemple.com
 *
 * Prérequis : « composer install » exécuté et src/config/Mail.php créé
 * à partir de Mail.example.php, avec un serveur SMTP de test démarré.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Script réservé à la ligne de commande.');
}

require_once __DIR__ . '/../src/service/MailService.php';

$destinataire = $argv[1] ?? '';
if ($destinataire === '') {
    fwrite(STDERR, "Usage : php tests/mail_test.php adresse@exemple.com\n");
    exit(1);
}

$resultats = [];

function verifier(string $nom, bool $condition, ?string $detail = null): void
{
    global $resultats;
    $resultats[] = $condition;
    echo ($condition ? '[OK] ' : '[KO] ') . $nom . ($detail !== null ? ' -> ' . $detail : '') . "\n";
}

try {
    $service = new MailService();
} catch (RuntimeException $e) {
    fwrite(STDERR, 'Mise en place incomplète : ' . $e->getMessage() . "\n");
    exit(1);
}

// 1. Envoi normal, avec accents, caractères spéciaux et HTML.
$html = '<h1>Test d\'envoi GHD-HSP</h1>'
      . '<p>Accents : é è à ç ê ô œ — euro : 5 € — guillemets « test ».</p>'
      . '<p>Deuxième paragraphe<br>avec retour à la ligne.</p>';
$ok = $service->envoyer($destinataire, 'Test GHD-HSP : accents é à ç €', $html);
verifier('Envoi valide', $ok === true, $ok ? null : (string) $service->getDerniereErreur());

// 2. Adresse invalide : refus avant tout envoi.
$ok = $service->envoyer('pas-une-adresse', 'Test', '<p>Test</p>');
verifier('Adresse invalide refusée', $ok === false, (string) $service->getDerniereErreur());

// 3. Sujet vide et contenu vide.
verifier('Sujet vide refusé', $service->envoyer($destinataire, '   ', '<p>Test</p>') === false);
verifier('Contenu vide refusé', $service->envoyer($destinataire, 'Test', '  ') === false);

// 4. Configuration SMTP incorrecte : échec propre, sans fuite d'information.
$configMauvaise = require __DIR__ . '/../src/config/Mail.php';
$configMauvaise['host'] = '127.0.0.1';
$configMauvaise['port'] = 1; // aucun serveur n'écoute sur ce port
$configMauvaise['timeout'] = 3;
$serviceKo = new MailService($configMauvaise);
$ok = $serviceKo->envoyer($destinataire, 'Test', '<p>Test</p>');
$message = (string) $serviceKo->getDerniereErreur();
verifier('SMTP incorrect : échec géré', $ok === false, $message);
verifier(
    'SMTP incorrect : aucune information SMTP dans le message',
    $message === MailService::MESSAGE_ERREUR
        && strpos($message, '127.0.0.1') === false
        && strpos($message, 'SMTP') === false
);

echo "\n" . (in_array(false, $resultats, true) ? "Au moins un test a échoué.\n" : "Tous les tests sont passés.\n");
exit(in_array(false, $resultats, true) ? 1 : 0);
