<?php

require_once __DIR__ . '/../bdd/Bdd.new.php';

class EnseignerRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new Bdd())->getConnexionBdd();
    }

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

    public function supprimer(int $idMedecin, int $idEtablissement): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM enseigner
             WHERE ref_medecin = :medecin AND ref_etablissement = :etablissement'
        );
        $stmt->execute(['medecin' => $idMedecin, 'etablissement' => $idEtablissement]);
        return $stmt->rowCount() === 1;
    }

    private function etablissementExiste(int $idEtablissement): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM etablissement WHERE id_etablissement = :id');
        $stmt->execute(['id' => $idEtablissement]);
        return (bool) $stmt->fetchColumn();
    }
}