<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/ReponseForum.php';

class ReponseForumRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getNbReponseForum(): int
    {
        $sql = "SELECT COUNT(*) FROM Reponse_forum";
        $req = $this->connexionBdd->query($sql);
        return $req->fetchColumn();
    }

    public function getReponseForum($id_reponse)
    {
        $sql = "SELECT * FROM Reponse_forum WHERE id_reponse = :id_reponse";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_reponse', $id_reponse, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();
        if (!$result) {
            return null;
        }
        return new ReponseForum($result["id_reponse"], $result["ref_post"], $result["contenu"], $result["date_heure_reponse"], $result["ref_utilisateur"]);
    }

    public function getAllReponseForum(): array
    {
        $sql = "SELECT * FROM Reponse_forum";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabReponseForum = [];
        foreach ($results as $result) {
            $tabReponseForum[] = new ReponseForum($result["id_reponse"], $result["ref_post"], $result["contenu"], $result["date_heure_reponse"], $result["ref_utilisateur"]);
        }
        return $tabReponseForum;
    }

    public function ajouterReponseForum(ReponseForum $reponseForum): void
    {
        $sql = "INSERT INTO Reponse_forum (contenu, date_heure_reponse, ref_post, ref_utilisateur)
                VALUES (:contenu, :date_heure_reponse, :ref_post, :ref_utilisateur)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':contenu', $reponseForum->getContenu());
        $req->bindValue(':date_heure_reponse', $reponseForum->getDateHeureReponse());
        $req->bindValue(':ref_post', $reponseForum->getRefPost());
        $req->bindValue(':ref_utilisateur', $reponseForum->getRefUtilisateur());
        $req->execute();
    }

    public function modifierReponseForum(ReponseForum $reponseForum): void
    {
        $sql = "UPDATE Reponse_forum
                SET contenu = :contenu, date_heure_reponse = :date_heure_reponse, ref_post = :ref_post, ref_utilisateur = :ref_utilisateur
                WHERE id_reponse = :id_reponse";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_reponse', $reponseForum->getIdReponse(), PDO::PARAM_INT);
        $req->bindValue(':contenu', $reponseForum->getContenu());
        $req->bindValue(':date_heure_reponse', $reponseForum->getDateHeureReponse());
        $req->bindValue(':ref_post', $reponseForum->getRefPost());
        $req->bindValue(':ref_utilisateur', $reponseForum->getRefUtilisateur());
        $req->execute();
    }

    public function supprimerReponseForum($id_reponse): void
    {
        $sql = "DELETE FROM Reponse_forum WHERE id_reponse = :id_reponse";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_reponse', $id_reponse, PDO::PARAM_INT);
        $req->execute();
    }
}