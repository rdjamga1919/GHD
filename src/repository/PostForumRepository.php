<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/PostForum.php';

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
        return new PostForum($result["id_post"], $result["ref_utilisateur"], $result["ref_canal"], $result["date_heure_creation"], $result["contenu"], $result["titre"]);
    }

    public function getAllPostForum(): array
    {
        $sql = "SELECT * FROM Post_forum";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabPostForum = [];
        foreach ($results as $result) {
            $tabPostForum[] = new PostForum($result["id_post"], $result["ref_utilisateur"], $result["ref_canal"], $result["date_heure_creation"], $result["contenu"], $result["titre"]);
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
        $postForum->setIdPost((int) $this->connexionBdd->lastInsertId());
    }

    public function modifierPostForum(PostForum $postForum): void
    {
        $sql = "UPDATE Post_forum
                SET titre = :titre, contenu = :contenu
                WHERE id_post = :id_post AND ref_utilisateur = :ref_utilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_post', $postForum->getIdPost(), PDO::PARAM_INT);
        $req->bindValue(':titre', $postForum->getTitre());
        $req->bindValue(':contenu', $postForum->getContenu());
        $req->bindValue(':ref_utilisateur', $postForum->getRefUtilisateur(), PDO::PARAM_INT);
        $req->execute();
    }

    public function supprimerPostForum($idPost): void
    {
        $this->supprimerPostForumControle((int) $idPost, null);
    }

    /** Suppression demandée par l'auteur, sans droit implicite du gestionnaire. */
    public function supprimerPostForumAuteur(int $idPost, int $idUtilisateur): void
    {
        $this->supprimerPostForumControle($idPost, $idUtilisateur);
    }

    private function supprimerPostForumControle(int $idPost, ?int $idUtilisateur): void
    {
        $this->connexionBdd->beginTransaction();
        try {
            // Le verrou parent empêche une nouvelle réponse pendant la vérification.
            $sql = "SELECT ref_utilisateur FROM Post_forum WHERE id_post = :idPost FOR UPDATE";
            $req = $this->connexionBdd->prepare($sql);
            $req->bindValue(':idPost', $idPost, PDO::PARAM_INT);
            $req->execute();
            $auteur = $req->fetchColumn();
            if ($auteur === false) {
                throw new DomainException('Publication introuvable.');
            }
            if ($idUtilisateur !== null && (int) $auteur !== $idUtilisateur) {
                throw new DomainException('Seul l’auteur peut supprimer cette publication.');
            }
            if ($this->getNbReponsesPostForum($idPost) > 0) {
                throw new DomainException('Suppression refusée : cette publication contient des réponses.');
            }
            $sql = "DELETE FROM Post_forum WHERE id_post = :idPost";
            $req = $this->connexionBdd->prepare($sql);
            $req->bindValue(':idPost', $idPost, PDO::PARAM_INT);
            $req->execute();
            $this->connexionBdd->commit();
        } catch (Throwable $e) {
            if ($this->connexionBdd->inTransaction()) {
                $this->connexionBdd->rollBack();
            }
            throw $e;
        }
    }

    public function getNbReponsesPostForum(int $idPost): int
    {
        $sql = "SELECT COUNT(*) FROM Reponse_forum WHERE ref_post = :idPost";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idPost', $idPost, PDO::PARAM_INT);
        $req->execute();
        return (int) $req->fetchColumn();
    }

    public function getPublicationsVisibles(string $role, ?int $idCanal = null): array
    {
        return $this->lirePublicationsVisibles($role, $idCanal, null);
    }

    public function getPublicationVisible(int $idPost, string $role): ?array
    {
        return $this->lirePublicationsVisibles($role, null, $idPost)[0] ?? null;
    }

    /** Le SQL exclut les canaux interdits avant toute restitution à la page. */
    private function lirePublicationsVisibles(string $role, ?int $idCanal, ?int $idPost): array
    {
        $acces = match ($role) {
            'gestionnaire' => ['generale', 'etudiants', 'medecins'],
            'etudiant' => ['generale', 'etudiants'],
            'medecin' => ['generale', 'medecins'],
            default => ['generale'],
        };
        $parametres = [];
        foreach ($acces as $index => $valeur) {
            $parametres[] = ':acces' . $index;
        }
        $sql = "SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom,
                       c.nom_canal, c.acces
                FROM Post_forum p
                INNER JOIN Canal_forum c ON c.id_canal = p.ref_canal
                INNER JOIN Utilisateur u ON u.id_utilisateur = p.ref_utilisateur
                WHERE c.acces IN (" . implode(', ', $parametres) . ")";
        if ($idCanal !== null) {
            $sql .= " AND p.ref_canal = :idCanal";
        }
        if ($idPost !== null) {
            $sql .= " AND p.id_post = :idPost";
        }
        $sql .= " ORDER BY p.date_heure_creation DESC, p.id_post DESC";
        $req = $this->connexionBdd->prepare($sql);
        foreach ($acces as $index => $valeur) {
            $req->bindValue(':acces' . $index, $valeur, PDO::PARAM_STR);
        }
        if ($idCanal !== null) {
            $req->bindValue(':idCanal', $idCanal, PDO::PARAM_INT);
        }
        if ($idPost !== null) {
            $req->bindValue(':idPost', $idPost, PDO::PARAM_INT);
        }
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}

