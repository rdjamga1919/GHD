<?php
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
        return $req->fetchColumn();
    }

    public function getCanalForum($idCanal)
    {
        $sql = "SELECT * FROM Canal_forum WHERE id_canal = :idCanal";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idCanal', $idCanal, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();
        if (!$result) {
            return null;
        }
        return new CanalForum($result["id_canal"], $result["nom_canal"], $result["acces"]);
    }

    public function getAllCanalForum(): array
    {
        $sql = "SELECT * FROM Canal_forum";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabCanalForum = [];
        foreach ($results as $result) {
            $tabCanalForum[] = new CanalForum($result["id_canal"], $result["nom_canal"], $result["acces"]);
        }
        return $tabCanalForum;
    }

    public function ajouterCanalForum(CanalForum $canalForum): void
    {
        $sql = "INSERT INTO Canal_forum (nom_canal, acces)
                VALUES (:nom_canal, :acces)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom_canal', $canalForum->getNomCanal());
        $req->bindValue(':acces', $canalForum->getAcces());
        $req->execute();
    }

    public function modifierCanalForum(CanalForum $canalForum): void
    {
        $sql = "UPDATE Canal_forum
                SET nom_canal = :nom_canal, acces = :acces
                WHERE id_canal = :id_canal";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_canal', $canalForum->getIdCanal(), PDO::PARAM_INT);
        $req->bindValue(':nom_canal', $canalForum->getNomCanal());
        $req->bindValue(':acces', $canalForum->getAcces());
        $req->execute();
    }

    public function supprimerCanalForum($idCanal): void
    {
        $sql = "DELETE FROM Canal_forum WHERE id_canal = :idCanal";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idCanal', $idCanal, PDO::PARAM_INT);
        $req->execute();
    }
}
