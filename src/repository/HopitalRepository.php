<?php

require_once __DIR__ . '/../bdd/Bdd.new.php';
require_once __DIR__ . '/../modele/Hopital.php';

class HopitalRepository
{
    private const COL_NOM = 'nom_hopital';
    private const COL_COMMUNE = 'commune';

    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new Bdd())->getConnexionBdd();
    }

    /**
     * @return Hopital[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id_hopital, ' . self::COL_NOM . ' AS nom, ' . self::COL_COMMUNE . ' AS localisation
             FROM hopital
             ORDER BY ' . self::COL_NOM
        );

        $hopitaux = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hopitaux[] = $this->hydrate($row);
        }
        return $hopitaux;
    }

    public function findById(int $id): ?Hopital
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_hopital, ' . self::COL_NOM . ' AS nom, ' . self::COL_COMMUNE . ' AS localisation
             FROM hopital
             WHERE id_hopital = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }

    public function create(Hopital $hopital): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO hopital (' . self::COL_NOM . ', ' . self::COL_COMMUNE . ')
             VALUES (:nom, :localisation)'
        );
        $stmt->execute([
            'nom'          => $hopital->getNom(),
            'localisation' => $hopital->getLocalisation(),
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(Hopital $hopital): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE hopital
             SET ' . self::COL_NOM . ' = :nom,
                 ' . self::COL_COMMUNE . ' = :localisation
             WHERE id_hopital = :id'
        );
        $stmt->execute([
            'id'           => $hopital->getIdHopital(),
            'nom'          => $hopital->getNom(),
            'localisation' => $hopital->getLocalisation(),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM hopital WHERE id_hopital = :id');
        $stmt->execute(['id' => $id]);
    }

    private function hydrate(array $row): Hopital
    {
        return new Hopital(
            $row['id_hopital'],
            $row['nom'],
            $row['localisation']
        );
    }
}