<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Entreprise.php';

class EntrepriseRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    /**
     * Récupérer toutes les entreprises
     *
     * @return entreprise[]
     */
    public function findAll()
    {
        $sql = "SELECT * FROM Entreprise";
        $stmt = $this->connexionBdd->query($sql);

        $entreprises = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $entreprises[] = new entreprise(
                $row['id_entreprise'],
                $row['nom_entreprise'],
                $row['adresse'],
                $row['site_web']
            );
        }

        return $entreprises;
    }

    /**
     * Récupérer une entreprise par son ID
     *
     * @param $id_entreprise
     * @return entreprise|null
     */
    public function findById($id_entreprise)
    {
        $sql = "SELECT * FROM Entreprise WHERE id_entreprise = :id_entreprise";

        $stmt = $this->connexionBdd->prepare($sql);
        $stmt->execute(['id_entreprise' => $id_entreprise]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new entreprise(
                $row['id_entreprise'],
                $row['nom_entreprise'],
                $row['adresse'],
                $row['site_web']
            );
        }

        return null;
    }

    /**
     * Ajouter une entreprise
     *
     * @param entreprise $entreprise
     */
    public function create($entreprise)
    {
        $sql = "INSERT INTO Entreprise
                (nom_entreprise, adresse, site_web)
                VALUES (:nom_entreprise, :adresse, :site_web)";

        $stmt = $this->connexionBdd->prepare($sql);

        $stmt->execute([
            'nom_entreprise' => $entreprise->getNom(),
            'adresse' => $entreprise->getAdresse(),
            'site_web' => $entreprise->getSiteWeb()
        ]);
    }

    /**
     * Modifier une entreprise
     *
     * @param entreprise $entreprise
     */
    public function update($entreprise)
    {
        $sql = "UPDATE Entreprise
                SET nom_entreprise = :nom_entreprise,
                    adresse = :adresse,
                    site_web = :site_web
                WHERE id_entreprise = :id_entreprise";

        $stmt = $this->connexionBdd->prepare($sql);

        $stmt->execute([
            'id_entreprise' => $entreprise->getIdEntreprise(),
            'nom_entreprise' => $entreprise->getNom(),
            'adresse' => $entreprise->getAdresse(),
            'site_web' => $entreprise->getSiteWeb()
        ]);
    }

    /**
     * Supprimer une entreprise
     *
     * @param $id_entreprise
     */
    public function delete($id_entreprise)
    {
        $sql = "DELETE FROM Entreprise WHERE id_entreprise = :id_entreprise";

        $stmt = $this->connexionBdd->prepare($sql);
        $stmt->execute(['id_entreprise' => $id_entreprise]);
    }
}