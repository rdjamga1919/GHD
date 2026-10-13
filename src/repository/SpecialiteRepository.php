<?php

require_once __DIR__ . '/../bdd/Bdd.new.php';
require_once __DIR__ . '/../modele/Specialite.php';

class SpecialiteRepository
{
    private const COL_LIBELLE = 'libelle_specialite';

    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new Bdd())->getConnexionBdd();
    }

    /**
     * @return Specialite[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id_specialite, ' . self::COL_LIBELLE . ' AS libelle
             FROM specialite
             ORDER BY ' . self::COL_LIBELLE
        );

        $specialites = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $specialites[] = $this->hydrate($row);
        }
        return $specialites;
    }

    public function findById(int $id): ?Specialite
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_specialite, ' . self::COL_LIBELLE . ' AS libelle
             FROM specialite
             WHERE id_specialite = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }

    public function create(Specialite $specialite): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO specialite (' . self::COL_LIBELLE . ') VALUES (:libelle)'
        );
        $stmt->execute(['libelle' => $specialite->getLibelle()]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(Specialite $specialite): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE specialite
             SET ' . self::COL_LIBELLE . ' = :libelle
             WHERE id_specialite = :id'
        );
        $stmt->execute([
            'id'      => $specialite->getIdSpecialite(),
            'libelle' => $specialite->getLibelle(),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM specialite WHERE id_specialite = :id');
        $stmt->execute(['id' => $id]);
    }

    private function hydrate(array $row): Specialite
    {
        return new Specialite(
            $row['id_specialite'],
            $row['libelle']
        );
    }
}