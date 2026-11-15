<?php
require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/ReponseForum.php';

class ReponseForumRepository
{
    private $connexionBdd;

    // Requête de base : on joint Utilisateur pour récupérer le nom de l'auteur
    private const SELECT = "SELECT r.*, u.nom AS nom_auteur, u.prenom AS prenom_auteur
                            FROM Reponse_forum r
                            JOIN Utilisateur u ON u.id_utilisateur = r.ref_utilisateur";

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    // Transforme une ligne de la BDD en objet ReponseForum
    private function creerReponse(array $ligne): ReponseForum
    {
        return new ReponseForum(
            $ligne['id_reponse'],
            $ligne['ref_post'],
            $ligne['contenu'],
            $ligne['date_heure_reponse'],
            $ligne['ref_utilisateur'],
            $ligne['nom_auteur'],
            $ligne['prenom_auteur']
        );
    }

    public function getNbReponseForum(): int
    {
        $sql = "SELECT COUNT(*) FROM Reponse_forum";
        $req = $this->connexionBdd->query($sql);
        return $req->fetchColumn();
    }

    /**
     * Toutes les réponses, de la plus ancienne à la plus récente
     *
     * @return ReponseForum[]
     */
    public function findAll(): array
    {
        $sql = self::SELECT . " ORDER BY r.date_heure_reponse ASC, r.id_reponse ASC";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();

        $reponses = [];
        foreach ($req->fetchAll(PDO::FETCH_ASSOC) as $ligne) {
            $reponses[] = $this->creerReponse($ligne);
        }
        return $reponses;
    }

    /**
     * Une réponse grâce à son id (null si elle n'existe pas)
     */
    public function findById(int $idReponse): ?ReponseForum
    {
        $sql = self::SELECT . " WHERE r.id_reponse = :id_reponse";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_reponse', $idReponse, PDO::PARAM_INT);
        $req->execute();

        $ligne = $req->fetch(PDO::FETCH_ASSOC);
        if (!$ligne) {
            return null;
        }
        return $this->creerReponse($ligne);
    }

    /**
     * Les réponses d'une publication, de la plus ancienne à la plus récente
     *
     * @return ReponseForum[]
     */
    public function findByPublication(int $idPost): array
    {
        $sql = self::SELECT . " WHERE r.ref_post = :id_post
                                ORDER BY r.date_heure_reponse ASC, r.id_reponse ASC";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_post', $idPost, PDO::PARAM_INT);
        $req->execute();

        $reponses = [];
        foreach ($req->fetchAll(PDO::FETCH_ASSOC) as $ligne) {
            $reponses[] = $this->creerReponse($ligne);
        }
        return $reponses;
    }

    /**
     * Lecture seule d'une publication (titre + accès de son canal).
     * Sert à vérifier qu'elle existe, sans dépendre du module Publications.
     * Retourne un tableau (id_post, titre, ref_canal, acces) ou null.
     */
    public function findPublication(int $idPost): ?array
    {
        $sql = "SELECT p.id_post, p.titre, p.ref_canal, c.acces
                FROM Post_forum p
                JOIN Canal_forum c ON c.id_canal = p.ref_canal
                WHERE p.id_post = :id_post";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_post', $idPost, PDO::PARAM_INT);
        $req->execute();

        $ligne = $req->fetch(PDO::FETCH_ASSOC);
        return $ligne ?: null;
    }

    /**
     * Enregistre une nouvelle réponse (la date est mise par la BDD)
     * et retourne l'id créé.
     */
    public function insert(ReponseForum $reponse): int
    {
        $sql = "INSERT INTO Reponse_forum (contenu, ref_post, ref_utilisateur)
                VALUES (:contenu, :ref_post, :ref_utilisateur)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':contenu', $reponse->getContenu());
        $req->bindValue(':ref_post', $reponse->getRefPost(), PDO::PARAM_INT);
        $req->bindValue(':ref_utilisateur', $reponse->getRefUtilisateur(), PDO::PARAM_INT);
        $req->execute();

        return (int) $this->connexionBdd->lastInsertId();
    }

    /**
     * Modifie le contenu d'une réponse (l'auteur, la publication et la date ne changent pas)
     */
    public function update(ReponseForum $reponse): void
    {
        $sql = "UPDATE Reponse_forum
                SET contenu = :contenu
                WHERE id_reponse = :id_reponse";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':contenu', $reponse->getContenu());
        $req->bindValue(':id_reponse', $reponse->getIdReponse(), PDO::PARAM_INT);
        $req->execute();
    }

    public function delete(int $idReponse): void
    {
        $sql = "DELETE FROM Reponse_forum WHERE id_reponse = :id_reponse";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_reponse', $idReponse, PDO::PARAM_INT);
        $req->execute();
    }
}