<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Offre.php';

class OffreRepository{

    private $connexionBdd;

    public function __construct(){
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getNbOffre(): int
    {
        $sql = "SELECT COUNT(*) FROM Offre";
        $req = $this->connexionBdd->query($sql);
        return $req->fetchColumn();
    }
    public function getOffre($idOffre)
    {
        $sql = "SELECT * FROM Offre WHERE id_offre = :idOffre";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idOffre', $idOffre, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();
        if (!$result) {
            return null;
        }
        return new Offre($result["id_offre"], $result["ref_utilisateur"], $result["date_publication"], $result["etat"], $result["type_d_offre"], $result["missions"], $result["description"], $result["titre"], $result["salaire"]);
    }

    public function getAllOffre(): array
    {
        $sql = "SELECT * FROM Offre";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabOffre = [];
        foreach ($results as $result) {
            $tabOffre[] = new Offre($result["id_offre"], $result["ref_utilisateur"], $result["date_publication"], $result["etat"], $result["type_d_offre"], $result["missions"], $result["description"], $result["titre"], $result["salaire"]);
        }
        return $tabOffre;
    }

    public function ajouterOffre(Offre $offre): void
    {
        $sql = "INSERT INTO Offre (titre, description, missions, salaire, type_d_offre, etat, date_publication, ref_utilisateur)
                VALUES (:titre, :description, :missions, :salaire, :type_d_offre, :etat, :date_publication, :ref_utilisateur)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':titre', $offre->getTitre());
        $req->bindValue(':description', $offre->getDescription());
        $req->bindValue(':missions', $offre->getMissions());
        $req->bindValue(':salaire', $offre->getSalaire());
        $req->bindValue(':type_d_offre', $offre->getTypeDOffre());
        $req->bindValue(':etat', $offre->getEtat());
        $req->bindValue(':date_publication', $offre->getDatePublication());
        $req->bindValue(':ref_utilisateur', $offre->getRefUtilisateur());
        $req->execute();
    }

    public function modifierOffre(Offre $offre): void
    {
        $sql = "UPDATE Offre
                SET titre = :titre, description = :description, missions = :missions, salaire = :salaire, type_d_offre = :type_d_offre, etat = :etat, date_publication = :date_publication, ref_utilisateur = :ref_utilisateur
                WHERE id_offre = :id_offre";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_offre', $offre->getIdOffre(), PDO::PARAM_INT);
        $req->bindValue(':titre', $offre->getTitre());
        $req->bindValue(':description', $offre->getDescription());
        $req->bindValue(':missions', $offre->getMissions());
        $req->bindValue(':salaire', $offre->getSalaire());
        $req->bindValue(':type_d_offre', $offre->getTypeDOffre());
        $req->bindValue(':etat', $offre->getEtat());
        $req->bindValue(':date_publication', $offre->getDatePublication());
        $req->bindValue(':ref_utilisateur', $offre->getRefUtilisateur());
        $req->execute();
    }

    public function supprimerOffre($idOffre): void
    {
        $sql = "DELETE FROM Offre WHERE id_offre = :idOffre";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idOffre', $idOffre, PDO::PARAM_INT);
        $req->execute();
    }

    /**
     * Liste des offres avec le nom de l'entreprise (offres ouvertes en premier)
     */
    public function getOffresAvecEntreprise(): array
    {
        $sql = "SELECT o.id_offre, o.titre, o.description, o.type_d_offre, o.etat,
                       o.salaire, o.date_publication,
                       e.id_entreprise, e.nom_entreprise
                FROM offre o
                LEFT JOIN partenaire p ON p.ref_utilisateur = o.ref_utilisateur
                LEFT JOIN entreprise e ON e.id_entreprise = p.ref_entreprise
                ORDER BY (o.etat = 'ouverte') DESC, o.date_publication DESC";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Détail d'une offre avec son entreprise, ou null si elle n'existe pas
     */
    public function getOffreAvecEntreprise(int $idOffre): ?array
    {
        $sql = "SELECT o.id_offre, o.titre, o.description, o.missions, o.type_d_offre,
                       o.etat, o.salaire, o.date_publication,
                       e.id_entreprise, e.nom_entreprise, e.adresse, e.site_web
                FROM offre o
                LEFT JOIN partenaire p ON p.ref_utilisateur = o.ref_utilisateur
                LEFT JOIN entreprise e ON e.id_entreprise = p.ref_entreprise
                WHERE o.id_offre = :id";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id', $idOffre, PDO::PARAM_INT);
        $req->execute();
        $row = $req->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}