
<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/CanalForum.php';

class CanalForumRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getNbCanalForum(): int
    {
        $sql = "SELECT COUNT(*) FROM Canal_forum";
        $req = $this->connexionBdd->query($sql);

        return (int) $req->fetchColumn();
    }

    public function getCanalForum($idCanal): ?CanalForum
    {
        $sql = "SELECT id_canal, nom_canal, acces, description
                FROM Canal_forum
                WHERE id_canal = :idCanal";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idCanal', $idCanal, PDO::PARAM_INT);
        $req->execute();

        $result = $req->fetch();

        if (!$result) {
            return null;
        }

        return new CanalForum(
            $result['id_canal'],
            $result['nom_canal'],
            $result['acces'],
            $result['description']
        );
    }

    public function getAllCanalForum(): array
    {
        $sql = "SELECT id_canal, nom_canal, acces, description
                FROM Canal_forum
                ORDER BY id_canal";

        $req = $this->connexionBdd->prepare($sql);
        $req->execute();

        $results = $req->fetchAll();
        $tabCanalForum = [];

        foreach ($results as $result) {
            $tabCanalForum[] = new CanalForum(
                $result['id_canal'],
                $result['nom_canal'],
                $result['acces'],
                $result['description']
            );
        }

        return $tabCanalForum;
    }

    public function ajouterCanalForum(CanalForum $canalForum): void
    {
        $sql = "INSERT INTO Canal_forum
                    (nom_canal, acces, description)
                VALUES
                    (:nom_canal, :acces, :description)";

        $req = $this->connexionBdd->prepare($sql);

        $req->bindValue(':nom_canal', $canalForum->getNomCanal());
        $req->bindValue(':acces', $canalForum->getAcces());
        $req->bindValue(
            ':description',
            $canalForum->getDescription(),
            $canalForum->getDescription() === null
                ? PDO::PARAM_NULL
                : PDO::PARAM_STR
        );

        $req->execute();
    }

    public function modifierCanalForum(CanalForum $canalForum): void
    {
        $sql = "UPDATE Canal_forum
                SET nom_canal = :nom_canal,
                    acces = :acces,
                    description = :description
                WHERE id_canal = :id_canal";

        $req = $this->connexionBdd->prepare($sql);

        $req->bindValue(
            ':id_canal',
            $canalForum->getIdCanal(),
            PDO::PARAM_INT
        );
        $req->bindValue(':nom_canal', $canalForum->getNomCanal());
        $req->bindValue(':acces', $canalForum->getAcces());
        $req->bindValue(
            ':description',
            $canalForum->getDescription(),
            $canalForum->getDescription() === null
                ? PDO::PARAM_NULL
                : PDO::PARAM_STR
        );

        $req->execute();
    }

    public function getNbPublicationsCanal(int $idCanal): int
    {
        $sql = "SELECT COUNT(*)
                FROM Post_forum
                WHERE ref_canal = :idCanal";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idCanal', $idCanal, PDO::PARAM_INT);
        $req->execute();

        return (int) $req->fetchColumn();
    }

    /**
     * Supprime un canal uniquement s'il ne contient
     * aucune publication.
     *
     * Une transaction protège les opérations de
     * vérification et de suppression.
     */
    public function supprimerCanalForum($idCanal): void
    {
        $idCanal = filter_var(
            $idCanal,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($idCanal === false) {
            throw new InvalidArgumentException(
                'Identifiant de canal invalide.'
            );
        }

        $this->connexionBdd->beginTransaction();

        try {
            // Verrouiller le canal avant la suppression.
            $sql = "SELECT id_canal
                    FROM Canal_forum
                    WHERE id_canal = :idCanal
                    FOR UPDATE";

            $req = $this->connexionBdd->prepare($sql);
            $req->bindValue(':idCanal', $idCanal, PDO::PARAM_INT);
            $req->execute();

            if (!$req->fetchColumn()) {
                throw new RuntimeException('Section introuvable.');
            }

            // Refuser la suppression si des publications existent.
            if ($this->getNbPublicationsCanal((int) $idCanal) > 0) {
                throw new RuntimeException(
                    'Impossible de supprimer une section contenant des publications.'
                );
            }

            // Supprimer uniquement le canal vide.
            $sql = "DELETE FROM Canal_forum
                    WHERE id_canal = :idCanal";

            $req = $this->connexionBdd->prepare($sql);
            $req->bindValue(':idCanal', $idCanal, PDO::PARAM_INT);
            $req->execute();

            $this->connexionBdd->commit();

        } catch (Throwable $e) {
            if ($this->connexionBdd->inTransaction()) {
                $this->connexionBdd->rollBack();
            }

            throw $e;
        }
    }
}
