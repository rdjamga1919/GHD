<?php

require_once __DIR__ . '/../bdd/Bdd.php';

/**
 * Gère la table de jonction enseigner (ref_medecin, ref_etablissement).
 * La clé primaire est composée des deux colonnes : un médecin ne peut pas
 * être associé deux fois au même établissement.
 */
class EnseignerRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new Bdd())->getConnexionBdd();
    }

    /** Tous les établissements dans lesquels un médecin enseigne. */
    public function getEtablissementsDuMedecin(int $idMedecin): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT e.id_etablissement, e.nom_etablissement, e.adresse, e.site_web
             FROM etablissement e
             JOIN enseigner en ON en.ref_etablissement = e.id_etablissement
             WHERE en.ref_medecin = :medecin
             ORDER BY e.nom_etablissement'
        );
        $stmt->execute(['medecin' => $idMedecin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Vérifie si la relation médecin / établissement existe déjà. */
    public function existe(int $idMedecin, int $idEtablissement): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM enseigner
             WHERE ref_medecin = :medecin AND ref_etablissement = :etablissement
             LIMIT 1'
        );
        $stmt->execute(['medecin' => $idMedecin, 'etablissement' => $idEtablissement]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Ajoute la relation. Retourne false si l'établissement n'existe pas
     * ou si la relation existe déjà (aucune ligne n'est alors créée).
     */
    public function ajouter(int $idMedecin, int $idEtablissement): bool
    {
        if (!$this->etablissementExiste($idEtablissement) || $this->existe($idMedecin, $idEtablissement)) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'INSERT IGNORE INTO enseigner (ref_medecin, ref_etablissement)
             VALUES (:medecin, :etablissement)'
        );
        $stmt->execute(['medecin' => $idMedecin, 'etablissement' => $idEtablissement]);
        return $stmt->rowCount() === 1;
    }

    /**
     * Supprime UNIQUEMENT la relation entre ce médecin et cet établissement
     * (les autres associations du médecin ne sont pas touchées).
     * Retourne false si la relation n'existait pas.
     */
    public function supprimer(int $idMedecin, int $idEtablissement): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM enseigner
             WHERE ref_medecin = :medecin AND ref_etablissement = :etablissement'
        );
        $stmt->execute(['medecin' => $idMedecin, 'etablissement' => $idEtablissement]);
        return $stmt->rowCount() === 1;
    }

    /**
     * Tous les établissements existants (pour la liste déroulante).
     * À remplacer par EtablissementRepository::findAll() quand ce repository
     * sera corrigé (il utilise encore une classe Connexion qui n'existe pas).
     */
    public function getTousLesEtablissements(): array
    {
        return $this->pdo
            ->query('SELECT id_etablissement, nom_etablissement, adresse, site_web
                     FROM etablissement ORDER BY nom_etablissement')
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    private function etablissementExiste(int $idEtablissement): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM etablissement WHERE id_etablissement = :id');
        $stmt->execute(['id' => $idEtablissement]);
        return (bool) $stmt->fetchColumn();
    }
}