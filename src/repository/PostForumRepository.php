<?php
class PostForumRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getNbPostForum(): int
    {
        $sql = "SELECT COUNT(*) FROM Post_forum";
        $req = $this->connexionBdd->query($sql);
        return $req->fetchColumn();
    }

    public function getPostForum($idPost)
    {
        $sql = "SELECT * FROM Post_forum WHERE id_post = :idPost";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idPost', $idPost, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();
        if (!$result) {
            return null;
        }
        return new PostForum($result["id_post"], $result["titre"], $result["contenu"], $result["date_heure_creation"], $result["ref_canal"], $result["ref_utilisateur"]);
    }

    public function getAllPostForum(): array
    {
        $sql = "SELECT * FROM Post_forum";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabPostForum = [];
        foreach ($results as $result) {
            $tabPostForum[] = new PostForum($result["id_post"], $result["titre"], $result["contenu"], $result["date_heure_creation"], $result["ref_canal"], $result["ref_utilisateur"]);
        }
        return $tabPostForum;
    }

    public function ajouterPostForum(PostForum $postForum): void
    {
        $sql = "INSERT INTO Post_forum (titre, contenu, date_heure_creation, ref_canal, ref_utilisateur)
                VALUES (:titre, :contenu, :date_heure_creation, :ref_canal, :ref_utilisateur)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':titre', $postForum->getTitre());
        $req->bindValue(':contenu', $postForum->getContenu());
        $req->bindValue(':date_heure_creation', $postForum->getDateHeureCreation());
        $req->bindValue(':ref_canal', $postForum->getRefCanal());
        $req->bindValue(':ref_utilisateur', $postForum->getRefUtilisateur());
        $req->execute();
    }

    public function modifierPostForum(PostForum $postForum): void
    {
        $sql = "UPDATE Post_forum
                SET titre = :titre, contenu = :contenu, date_heure_creation = :date_heure_creation, ref_canal = :ref_canal, ref_utilisateur = :ref_utilisateur
                WHERE id_post = :id_post";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_post', $postForum->getIdPost(), PDO::PARAM_INT);
        $req->bindValue(':titre', $postForum->getTitre());
        $req->bindValue(':contenu', $postForum->getContenu());
        $req->bindValue(':date_heure_creation', $postForum->getDateHeureCreation());
        $req->bindValue(':ref_canal', $postForum->getRefCanal());
        $req->bindValue(':ref_utilisateur', $postForum->getRefUtilisateur());
        $req->execute();
    }

    public function supprimerPostForum($idPost): void
    {
        $sql = "DELETE FROM Post_forum WHERE id_post = :idPost";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idPost', $idPost, PDO::PARAM_INT);
        $req->execute();
    }
}

