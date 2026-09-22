<?php
class EvenementRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getNbEvenement(): int
    {
        $sql = "SELECT COUNT(*) FROM Evenement";
        $req = $this->connexionBdd->query($sql);
        return $req->fetchColumn();
    }

    public function getEvenement($idEvenement)
    {
        $sql = "SELECT * FROM Evenement WHERE id_evenement = :idEvenement";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();
        if (!$result) {
            return null;
        }
        return new Evenement($result["id_evenement"], $result["titre"], $result["description"], $result["type_evenement"], $result["adresse_lieu"], $result["element_requis"], $result["nombre_place"], $result["date_heure_evenement"]);
    }

    public function getAllEvenement(): array
    {
        $sql = "SELECT * FROM Evenement";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabEvenement = [];
        foreach ($results as $result) {
            $tabEvenement[] = new Evenement($result["id_evenement"], $result["titre"], $result["description"], $result["type_evenement"], $result["adresse_lieu"], $result["element_requis"], $result["nombre_place"], $result["date_heure_evenement"]);
        }
        return $tabEvenement;
    }

    public function ajouterEvenement(Evenement $evenement): void
    {
        $sql = "INSERT INTO Evenement (titre, description, type_evenement, adresse_lieu, element_requis, nombre_place, date_heure_evenement)
                VALUES (:titre, :description, :type_evenement, :adresse_lieu, :element_requis, :nombre_place, :date_heure_evenement)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':titre', $evenement->getTitre());
        $req->bindValue(':description', $evenement->getDescription());
        $req->bindValue(':type_evenement', $evenement->getTypeEvenement());
        $req->bindValue(':adresse_lieu', $evenement->getAdresseLieu());
        $req->bindValue(':element_requis', $evenement->getElementRequis());
        $req->bindValue(':nombre_place', $evenement->getNombrePlace());
        $req->bindValue(':date_heure_evenement', $evenement->getDateHeureEvenement());
        $req->execute();
    }

    public function modifierEvenement(Evenement $evenement): void
    {
        $sql = "UPDATE Evenement
                SET titre = :titre, description = :description, type_evenement = :type_evenement, adresse_lieu = :adresse_lieu, element_requis = :element_requis, nombre_place = :nombre_place, date_heure_evenement = :date_heure_evenement
                WHERE id_evenement = :id_evenement";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_evenement', $evenement->getIdEvenement(), PDO::PARAM_INT);
        $req->bindValue(':titre', $evenement->getTitre());
        $req->bindValue(':description', $evenement->getDescription());
        $req->bindValue(':type_evenement', $evenement->getTypeEvenement());
        $req->bindValue(':adresse_lieu', $evenement->getAdresseLieu());
        $req->bindValue(':element_requis', $evenement->getElementRequis());
        $req->bindValue(':nombre_place', $evenement->getNombrePlace());
        $req->bindValue(':date_heure_evenement', $evenement->getDateHeureEvenement());
        $req->execute();
    }

    public function supprimerEvenement($idEvenement): void
    {
        $sql = "DELETE FROM Evenement WHERE id_evenement = :idEvenement";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $req->execute();
    }
}