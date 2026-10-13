<?php

require_once __DIR__ . '/../bdd/Bdd.new.php';
require_once __DIR__ . '/../modele/Etablissement.php';

class EtablissementRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new Bdd())->getConnexionBdd();
    }

    /**
     * @return Etablissement[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id_etablissement, nom_etablissement, adresse, site_web
             FROM etablissement
             ORDER BY nom_etablissement'
        );

        $etablissements = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $etablissements[] = $this->hydrate($row);
        }
        return $etablissements;
    }

    public function findById(int $id): ?Etablissement
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_etablissement, nom_etablissement, adresse, site_web
             FROM etablissement
             WHERE id_etablissement = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }

    /**
     * Recherche par nom (pour la section « Je suis professeur dans… »)
     *
     * @return Etablissement[]
     */
    public function rechercherParNom(string $terme): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_etablissement, nom_etablissement, adresse, site_web
             FROM etablissement
             WHERE nom_etablissement LIKE :terme
             ORDER BY nom_etablissement
             LIMIT 20'
        );
        $stmt->execute(['terme' => '%' . $terme . '%']);

        $etablissements = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $etablissements[] = $this->hydrate($row);
        }
        return $etablissements;
    }

    public function create(Etablissement $etablissement): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO etablissement (nom_etablissement, adresse, site_web)
             VALUES (:nom, :adresse, :site_web)'
        );
        $stmt->execute([
            'nom'      => $etablissement->getNom(),
            'adresse'  => $etablissement->getAdresse(),
            'site_web' => $etablissement->getSiteWeb(),
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(Etablissement $etablissement): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE etablissement
             SET nom_etablissement = :nom,
                 adresse = :adresse,
                 site_web = :site_web
             WHERE id_etablissement = :id'
        );
        $stmt->execute([
            'id'       => $etablissement->getIdEtablissement(),
            'nom'      => $etablissement->getNom(),
            'adresse'  => $etablissement->getAdresse(),
            'site_web' => $etablissement->getSiteWeb(),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM etablissement WHERE id_etablissement = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    private function hydrate(array $row): Etablissement
    {
        return new Etablissement(
            $row['id_etablissement'],
            $row['nom_etablissement'],
            $row['adresse'],
            $row['site_web']
        );
    }
}