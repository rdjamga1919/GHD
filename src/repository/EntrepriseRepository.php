<?php

require_once __DIR__ . '/../bdd/Bdd.new.php';
require_once __DIR__ . '/../modele/Entreprise.php';

class EntrepriseRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    private function hydrate($result)
    {
        return new Entreprise((int) $result["id_entreprise"], $result["nom_entreprise"], $result["adresse"], $result["site_web"]);
    }

    public function getAllEntreprises()
    {
        $sql = "SELECT * FROM entreprise ORDER BY nom_entreprise";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll(PDO::FETCH_ASSOC);
        $tabEntreprises = [];
        foreach ($results as $result) {
            $tabEntreprises[] = $this->hydrate($result);
        }
        return $tabEntreprises;
    }

    public function rechercherEntreprises($recherche)
    {
        $sql = "SELECT * FROM entreprise WHERE nom_entreprise LIKE :recherche ORDER BY nom_entreprise";
        $req = $this->connexionBdd->prepare($sql);
        // On échappe % et _ pour que la recherche soit littérale
        $req->bindValue(':recherche', '%' . addcslashes($recherche, '%_\\') . '%');
        $req->execute();
        $results = $req->fetchAll(PDO::FETCH_ASSOC);
        $tabEntreprises = [];
        foreach ($results as $result) {
            $tabEntreprises[] = $this->hydrate($result);
        }
        return $tabEntreprises;
    }

    public function getEntreprise($idEntreprise)
    {
        $sql = "SELECT * FROM entreprise WHERE id_entreprise = :id_entreprise";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_entreprise', $idEntreprise, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch(PDO::FETCH_ASSOC);
        return $result ? $this->hydrate($result) : null;
    }

    public function getEntrepriseBySiteWeb($siteWeb)
    {
        $sql = "SELECT * FROM entreprise WHERE site_web = :site_web";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':site_web', $siteWeb);
        $req->execute();
        $result = $req->fetch(PDO::FETCH_ASSOC);
        return $result ? $this->hydrate($result) : null;
    }

    public function entrepriseExiste($idEntreprise)
    {
        $sql = "SELECT COUNT(*) FROM entreprise WHERE id_entreprise = :id_entreprise";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_entreprise', $idEntreprise, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchColumn() > 0;
    }

    // Retourne l'identifiant de la nouvelle entreprise (utilisable pour Partenaire.ref_entreprise et Offre.ref_entreprise)
    public function ajouterEntreprise(Entreprise $entreprise)
    {
        $sql = "INSERT INTO entreprise (nom_entreprise, adresse, site_web)
                VALUES (:nom_entreprise, :adresse, :site_web)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom_entreprise', $entreprise->getNom());
        $req->bindValue(':adresse', $entreprise->getAdresse());
        $req->bindValue(':site_web', $entreprise->getSiteWeb());
        $req->execute();
        return (int) $this->connexionBdd->lastInsertId();
    }
}