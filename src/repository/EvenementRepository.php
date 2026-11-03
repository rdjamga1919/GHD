<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Evenement.php';

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
        return new Evenement($result["id_evenement"], $result["date_heure_evenement"], $result["element_requis"], $result["nombre_place"], $result["type_evenement"], $result["adresse_lieu"], $result["description"], $result["titre"]);
    }

    public function getAllEvenement(): array
    {
        $sql = "SELECT * FROM Evenement";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabEvenement = [];
        foreach ($results as $result) {
            $tabEvenement[] = new Evenement($result["id_evenement"], $result["date_heure_evenement"], $result["element_requis"], $result["nombre_place"], $result["type_evenement"], $result["adresse_lieu"], $result["description"], $result["titre"]);
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

    public function getEvenementsAvecDetails(): array
    {
        $sql = "SELECT e.id_evenement, e.titre, e.description, e.type_evenement,
                       e.adresse_lieu, e.nombre_place, e.date_heure_evenement,
                       (SELECT COUNT(*) FROM s_inscrire si
                         WHERE si.ref_evenement = e.id_evenement) AS nb_inscrits,
                       GROUP_CONCAT(DISTINCT CONCAT(u.prenom, ' ', u.nom) SEPARATOR ', ') AS organisateurs
                FROM evenement e
                LEFT JOIN organiser o ON o.ref_evenement = e.id_evenement
                LEFT JOIN utilisateur u ON u.id_utilisateur = o.ref_utilisateur
                GROUP BY e.id_evenement
                ORDER BY e.date_heure_evenement ASC";
        return $this->connexionBdd->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Détail d'un événement (null si inexistant) avec le nombre d'inscrits.
     */
    public function getEvenementDetail(int $idEvenement): ?array
    {
        $sql = "SELECT e.*,
                       (SELECT COUNT(*) FROM s_inscrire si
                         WHERE si.ref_evenement = e.id_evenement) AS nb_inscrits
                FROM evenement e
                WHERE e.id_evenement = :idEvenement";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Organisateurs d'un événement (tableau vide si aucun).
     */
    public function getOrganisateurs(int $idEvenement): array
    {
        $sql = "SELECT u.id_utilisateur, u.nom, u.prenom, u.role
                FROM organiser o
                INNER JOIN utilisateur u ON u.id_utilisateur = o.ref_utilisateur
                WHERE o.ref_evenement = :idEvenement
                ORDER BY u.nom, u.prenom";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

}