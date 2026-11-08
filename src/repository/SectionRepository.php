<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Section.php';

class SectionRepository
{
    private $connexionBdd;

    // À ADAPTER après vérification du SQL : nom de la table et des colonnes.
    private const SELECT = 'SELECT id_section, nom, description FROM Section';

    public function __construct(?PDO $connexionBdd = null)
    {
        $this->connexionBdd = $connexionBdd ?? (new Bdd())->getConnexionBdd();
    }

    private function hydrate(array $row): Section
    {
        return new Section($row['nom'], $row['description'], (int) $row['id_section']);
    }

    public function findAll(): array
    {
        return array_map(
            [$this, 'hydrate'],
            $this->connexionBdd->query(self::SELECT . ' ORDER BY nom')->fetchAll()
        );
    }

    public function findById(int $idSection): ?Section
    {
        $stmt = $this->connexionBdd->prepare(self::SELECT . ' WHERE id_section = :id');
        $stmt->execute(['id' => $idSection]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function insert(Section $s): int
    {
        $stmt = $this->connexionBdd->prepare(
            'INSERT INTO Section (nom, description) VALUES (:nom, :description)'
        );
        $stmt->execute([
            'nom'         => $s->getNom(),
            'description' => $s->getDescription(),
        ]);
        return (int) $this->connexionBdd->lastInsertId();
    }

    public function update(Section $s): void
    {
        $stmt = $this->connexionBdd->prepare(
            'UPDATE Section SET nom = :nom, description = :description WHERE id_section = :id'
        );
        $stmt->execute([
            'nom'         => $s->getNom(),
            'description' => $s->getDescription(),
            'id'          => $s->getIdSection(),
        ]);
    }

    /**
     * Nombre de publications rattachées à la section.
     * Renvoie 0 si la table des publications n'existe pas encore (module Rosie non intégré).
     * À ADAPTER : nom de la table Publication et de la colonne ref_section.
     */
    public function countPublications(int $idSection): int
    {
        try {
            $stmt = $this->connexionBdd->prepare(
                'SELECT COUNT(*) FROM Publication WHERE ref_section = :id'
            );
            $stmt->execute(['id' => $idSection]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $ex) {
            if ($ex->getCode() === '42S02') { // table inexistante
                return 0;
            }
            throw $ex;
        }
    }

    /**
     * Refuse la suppression si des publications sont rattachées (aucune règle de suppression définie).
     * @throws RuntimeException si la section contient des publications
     */
    public function delete(int $idSection): void
    {
        if ($this->countPublications($idSection) > 0) {
            throw new RuntimeException('Impossible de supprimer une section contenant des publications.');
        }
        $this->connexionBdd->prepare('DELETE FROM Section WHERE id_section = :id')
            ->execute(['id' => $idSection]);
    }
}