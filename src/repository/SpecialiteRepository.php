<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Specialite.php';

class SpecialiteRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    /**
     * Récupérer toutes les spécialités
     *
     * @return specialite[]
     */
    public function findAll()
    {
        $sql = "SELECT * FROM Specialite";
        $stmt = $this->connexionBdd->query($sql);

        $specialites = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $specialites[] = new specialite(
                $row['id_specialite'],
                $row['libelle_specialite']
            );
        }

        return $specialites;
    }

    /**
     * Récupérer une spécialité par son ID
     *
     * @param $id_specialite
     * @return specialite|null
     */
    public function findById($id_specialite)
    {
        $sql = "SELECT * FROM Specialite
                WHERE id_specialite = :id_specialite";

        $stmt = $this->connexionBdd->prepare($sql);
        $stmt->execute(['id_specialite' => $id_specialite]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new specialite(
                $row['id_specialite'],
                $row['libelle_specialite']
            );
        }

        return null;
    }

    /**
     * Ajouter une spécialité
     *
     * @param specialite $specialite
     */
    public function create($specialite)
    {
        $sql = "INSERT INTO Specialite
                (libelle_specialite)
                VALUES (:libelle_specialite)";

        $stmt = $this->connexionBdd->prepare($sql);

        $stmt->execute([
            'libelle_specialite' => $specialite->getLibelle()
        ]);
    }

    /**
     * Modifier une spécialité
     *
     * @param specialite $specialite
     */
    public function update($specialite)
    {
        $sql = "UPDATE Specialite
                SET libelle_specialite = :libelle_specialite
                WHERE id_specialite = :id_specialite";

        $stmt = $this->connexionBdd->prepare($sql);

        $stmt->execute([
            'id_specialite' => $specialite->getIdSpecialite(),
            'libelle_specialite' => $specialite->getLibelle()
        ]);
    }

    /**
     * Supprimer une spécialité
     *
     * @param $id_specialite
     */
    public function delete($id_specialite)
    {
        $sql = "DELETE FROM Specialite
                WHERE id_specialite = :id_specialite";

        $stmt = $this->connexionBdd->prepare($sql);
        $stmt->execute(['id_specialite' => $id_specialite]);
    }
}