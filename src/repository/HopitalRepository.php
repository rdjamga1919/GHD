<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Hopital.php';

class HopitalRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    /**
     * Récupérer tous les hôpitaux
     *
     * @return hopital[]
     */
    public function findAll()
    {
        $sql = "SELECT * FROM Hopital";
        $stmt = $this->connexionBdd->query($sql);

        $hopitaux = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hopitaux[] = new hopital(
                $row['id_hopital'],
                $row['nom_hopital'],
                $row['commune']
            );
        }

        return $hopitaux;
    }

    /**
     * Récupérer un hôpital par son ID
     *
     * @param $id_hopital
     * @return hopital|null
     */
    public function findById($id_hopital)
    {
        $sql = "SELECT * FROM Hopital
                WHERE id_hopital = :id_hopital";

        $stmt = $this->connexionBdd->prepare($sql);
        $stmt->execute(['id_hopital' => $id_hopital]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new hopital(
                $row['id_hopital'],
                $row['nom_hopital'],
                $row['commune']
            );
        }

        return null;
    }

    /**
     * Ajouter un hôpital
     *
     * @param hopital $hopital
     */
    public function create($hopital)
    {
        $sql = "INSERT INTO Hopital
                (nom_hopital, commune)
                VALUES (:nom_hopital, :commune)";

        $stmt = $this->connexionBdd->prepare($sql);

        $stmt->execute([
            'nom_hopital' => $hopital->getNom(),
            'commune' => $hopital->getCommune()
        ]);
    }

    /**
     * Modifier un hôpital
     *
     * @param hopital $hopital
     */
    public function update($hopital)
    {
        $sql = "UPDATE Hopital
                SET nom_hopital = :nom_hopital,
                    commune = :commune
                WHERE id_hopital = :id_hopital";

        $stmt = $this->connexionBdd->prepare($sql);

        $stmt->execute([
            'id_hopital' => $hopital->getIdHopital(),
            'nom_hopital' => $hopital->getNom(),
            'commune' => $hopital->getCommune()
        ]);
    }

    /**
     * Supprimer un hôpital
     *
     * @param $id_hopital
     */
    public function delete($id_hopital)
    {
        $sql = "DELETE FROM Hopital
                WHERE id_hopital = :id_hopital";

        $stmt = $this->connexionBdd->prepare($sql);
        $stmt->execute(['id_hopital' => $id_hopital]);
    }
}