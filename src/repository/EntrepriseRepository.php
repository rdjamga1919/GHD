<?php

require_once __DIR__ . '/../bdd/Bdd.new.php';
require_once __DIR__ . '/../modele/Entreprise.php';

class EntrepriseRepository
{
    private const COL_NOM = 'nom_entreprise
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new Bdd())->getConnexionBdd();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id_entreprise, ' . self::COL_NOM . ' AS nom, adresse, site_web
             FROM entreprise
             ORDER BY ' . self::COL_NOM
        );

        $entreprises = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $entreprises[] = $this->hydrate($row);
        }
        return $entreprises;
    }

    public function findById(int $id): ?Entreprise
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_entreprise, ' . self::COL_NOM . ' AS nom, adresse, site_web
             FROM entreprise
             WHERE id_entreprise = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }

    public function create(Entreprise $entreprise): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO entreprise (' . self::COL_NOM . ', adresse, site_web)
             VALUES (:nom, :adresse, :site_web)'
        );
        $stmt->execute([
            'nom'      => $entreprise->getNom(),
            'adresse'  => $entreprise->getAdresse(),
            'site_web' => $entreprise->getSiteWeb(),
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(Entreprise $entreprise): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE entreprise
             SET ' . self::COL_NOM . ' = :nom,
                 adresse = :adresse,
                 site_web = :site_web
             WHERE id_entreprise = :id'
        );
        $stmt->execute([
            'id'       => $entreprise->getIdEntreprise(),
            'nom'      => $entreprise->getNom(),
            'adresse'  => $entreprise->getAdresse(),
            'site_web' => $entreprise->getSiteWeb(),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM entreprise WHERE id_entreprise = :id');
        $stmt->execute(['id' => $id]);
    }

    private function hydrate(array $row): Entreprise
    {
        return new Entreprise(
            $row['id_entreprise'],
            $row['nom'],
            $row['adresse'],
            $row['site_web']
        );
    }
}