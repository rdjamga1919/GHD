<?php

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Service centralisé d'envoi d'e-mails pour GHD-HSP.
 *
 * Il s'appuie sur PHPMailer (installé avec Composer) et sur la configuration
 * locale src/config/Mail.php (non versionnée, modèle : Mail.example.php).
 *
 * Il est destiné à être réutilisé par les futures fonctionnalités (mot de passe
 * oublié, confirmation de compte, notifications, prise de contact) :
 *
 *     $mailService = new MailService();
 *     $ok = $mailService->envoyer($email, 'Sujet', '<p>Bonjour</p>');
 *
 * Aucune information de connexion SMTP (serveur, port, identifiants) n'est
 * jamais renvoyée à l'appelant : les détails techniques vont uniquement dans
 * le journal d'erreurs PHP.
 */
class MailService
{
    /** Message générique renvoyé à l'appelant quand l'envoi échoue. */
    public const MESSAGE_ERREUR = "L'e-mail n'a pas pu être envoyé. Veuillez réessayer plus tard.";

    /** @var array<string, mixed> Configuration SMTP (voir src/config/Mail.example.php). */
    private array $config;

    /** Dernier message d'erreur, sans information SMTP (null si le dernier envoi a réussi). */
    private ?string $derniereErreur = null;

    /**
     * @param array<string, mixed>|null $config Configuration à utiliser ; si null, charge src/config/Mail.php.
     *
     * @throws RuntimeException si Composer n'est pas installé ou si la configuration est absente ou incomplète
     *                          (erreur de mise en place du projet, pas une erreur d'envoi).
     */
    public function __construct(?array $config = null)
    {
        $autoload = __DIR__ . '/../../vendor/autoload.php';
        if (!is_file($autoload)) {
            throw new RuntimeException("Dépendances manquantes : lancer « composer install » à la racine du projet.");
        }
        require_once $autoload;

        if ($config === null) {
            $fichier = __DIR__ . '/../config/Mail.php';
            if (!is_file($fichier)) {
                throw new RuntimeException("Configuration e-mail absente : copier src/config/Mail.example.php vers src/config/Mail.php.");
            }
            $config = require $fichier;
        }

        foreach (['host', 'port', 'from_address'] as $cle) {
            if (!is_array($config) || empty($config[$cle])) {
                throw new RuntimeException("Configuration e-mail incomplète : la clé « $cle » est obligatoire.");
            }
        }

        $this->config = $config;
    }

    /**
     * Envoie un e-mail HTML accompagné d'une version texte.
     *
     * Cette méthode ne lève jamais d'exception : en cas d'échec elle renvoie false
     * et le message à afficher à l'utilisateur est disponible via getDerniereErreur().
     *
     * @param string      $destinataire Adresse e-mail du destinataire.
     * @param string      $sujet        Sujet du message (UTF-8).
     * @param string      $contenuHtml  Corps du message en HTML (UTF-8). Le contenu doit déjà être échappé par l'appelant.
     * @param string|null $contenuTexte Version texte ; si null, elle est générée à partir du HTML.
     *
     * @return bool true si le serveur SMTP a accepté le message, false sinon.
     */
    public function envoyer(string $destinataire, string $sujet, string $contenuHtml, ?string $contenuTexte = null): bool
    {
        $this->derniereErreur = null;

        $destinataire = trim($destinataire);
        if (!filter_var($destinataire, FILTER_VALIDATE_EMAIL)) {
            $this->derniereErreur = "L'adresse e-mail du destinataire n'est pas valide.";
            return false;
        }

        // Pas de retour à la ligne dans le sujet (protection contre l'injection d'en-têtes).
        $sujet = trim(str_replace(["\r", "\n"], ' ', $sujet));
        if ($sujet === '') {
            $this->derniereErreur = "Le sujet de l'e-mail est vide.";
            return false;
        }

        if (trim($contenuHtml) === '') {
            $this->derniereErreur = "Le contenu de l'e-mail est vide.";
            return false;
        }

        try {
            $mail = $this->creerMailer();
            $mail->addAddress($destinataire);
            $mail->Subject = $sujet;
            $mail->isHTML(true);
            $mail->Body = $contenuHtml;
            $mail->AltBody = $contenuTexte ?? $this->htmlVersTexte($contenuHtml);
            $mail->send();

            return true;
        } catch (PHPMailerException $e) {
            // Détail technique : journal du serveur uniquement, jamais affiché.
            error_log('[MailService] Échec d\'envoi : ' . $e->getMessage());
        } catch (Throwable $e) {
            error_log('[MailService] Erreur inattendue : ' . $e->getMessage());
        }

        $this->derniereErreur = self::MESSAGE_ERREUR;
        return false;
    }

    /**
     * Message d'erreur du dernier envoi, sans information SMTP.
     *
     * @return string|null null si le dernier envoi a réussi (ou si aucun envoi n'a eu lieu).
     */
    public function getDerniereErreur(): ?string
    {
        return $this->derniereErreur;
    }

    /**
     * Crée et configure un objet PHPMailer prêt à l'emploi (SMTP, UTF-8, expéditeur).
     *
     * @throws PHPMailerException si l'adresse d'expédition configurée est invalide.
     */
    private function creerMailer(): PHPMailer
    {
        $mail = new PHPMailer(true); // true : PHPMailer lève des exceptions

        $mail->isSMTP();
        $mail->Host = (string) $this->config['host'];
        $mail->Port = (int) $this->config['port'];
        $mail->Timeout = (int) ($this->config['timeout'] ?? 10);

        // Aucune sortie de débogage : elle afficherait les échanges avec le serveur SMTP.
        $mail->SMTPDebug = 0;

        $chiffrement = strtolower((string) ($this->config['encryption'] ?? ''));
        if ($chiffrement === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($chiffrement === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false; // serveur de test local sans chiffrement
        }

        $mail->SMTPAuth = !empty($this->config['auth']);
        if ($mail->SMTPAuth) {
            $mail->Username = (string) ($this->config['username'] ?? '');
            $mail->Password = (string) ($this->config['password'] ?? '');
        }

        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->Encoding = PHPMailer::ENCODING_BASE64;

        $mail->setFrom(
            (string) $this->config['from_address'],
            (string) ($this->config['from_name'] ?? '')
        );

        return $mail;
    }

    /**
     * Génère une version texte lisible à partir d'un contenu HTML.
     */
    private function htmlVersTexte(string $html): string
    {
        // On supprime d'abord les blocs qui ne doivent pas apparaître en texte.
        $texte = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $html) ?? $html;
        // Les balises de bloc deviennent des retours à la ligne.
        $texte = preg_replace('#<br\s*/?>|</(p|div|h[1-6]|li|tr)>#i', "\n", $texte) ?? $texte;
        $texte = strip_tags($texte);
        $texte = html_entity_decode($texte, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Espaces superflus et lignes vides en trop.
        $texte = preg_replace("/[ \t]+/", ' ', $texte) ?? $texte;
        $texte = preg_replace("/\n{3,}/", "\n\n", $texte) ?? $texte;

        return trim($texte);
    }
}
