<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Section.php';

class SectionRepository
{
    private $connexionBdd;

    // La colonne `description` est ajoutée par sql/migration_canal_forum_description.sql
    private const SELECT = 'SELECT id_canal, nom_canal, description, acces FROM Canal_forum';

    public function __construct(?PDO $connexionBdd = null)
    {
        $this->connexionBdd = $connexionBdd ?? (new Bdd())->getConnexionBdd();
    }

    private function hydrate(array $row): Section
    {
        return new Section($row['nom_canal'], $row['acces'], $row['description'], (int) $row['id_canal']);
    }

    public function findAll(): array
    {
        return array_map(
            [$this, 'hydrate'],
            $this->connexionBdd->query(self::SELECT . ' ORDER BY id_canal')->fetchAll()
        );
    }

    public function findById(int $idSection): ?Section
    {
        $stmt = $this->connexionBdd->prepare(self::SELECT . ' WHERE id_canal = :id');
        $stmt->execute(['id' => $idSection]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function insert(Section $s): int
    {
        $stmt = $this->connexionBdd->prepare(
            'INSERT INTO Canal_forum (nom_canal, description, acces) VALUES (:nom, :description, :acces)'
        );
        $stmt->execute([
            'nom'         => $s->getNom(),
            'description' => $s->getDescription(),
            'acces'       => $s->getAcces(),
        ]);
        return (int) $this->connexionBdd->lastInsertId();
    }

    public function update(Section $s): void
    {
        $stmt = $this->connexionBdd->prepare(
            'UPDATE Canal_forum SET nom_canal = :nom, description = :description, acces = :acces WHERE id_canal = :id'
        );
        $stmt->execute([
            'nom'         => $s->getNom(),
            'description' => $s->getDescription(),
            'acces'       => $s->getAcces(),
            'id'          => $s->getIdSection(),
        ]);
    }

    /** Nombre de posts (Post_forum) rattachés à la section. */
    public function countPublications(int $idSection): int
    {
        $stmt = $this->connexionBdd->prepare('SELECT COUNT(*) FROM Post_forum WHERE ref_canal = :id');
        $stmt->execute(['id' => $idSection]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Refuse la suppression si la section contient des posts : la clé étrangère
     * Post_forum.ref_canal est en ON DELETE CASCADE, donc un DELETE les effacerait tous.
     * @throws RuntimeException si la section contient des publications
     */
    public function delete(int $idSection): void
    {
        if ($this->countPublications($idSection) > 0) {
            throw new RuntimeException('Impossible de supprimer une section contenant des publications.');
        }
        $this->connexionBdd->prepare('DELETE FROM Canal_forum WHERE id_canal = :id')
            ->execute(['id' => $idSection]);
    }
}