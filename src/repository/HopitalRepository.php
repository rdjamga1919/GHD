<?php

require_once 'Connexion.php';
require_once 'Hopital.php';

class HopitalRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connexion::getPdo();
    }

    /**
     * Récupérer tous les hôpitaux
     *
     * @return hopital[]
     */
    public function findAll()
    {
        $sql = "SELECT * FROM hopital";
        $stmt = $this->pdo->query($sql);

        $hopitaux = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hopitaux[] = new hopital(
                $row['id_hopital'],
                $row['nom'],
                $row['localisation']
            );
        }

        return $hopitaux;
    }

    /**
     * Récupérer un hôpital par son ID
     *
     * @param $id
     * @return hopital|null
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM hopital
                WHERE id_hopital = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new hopital(
                $row['id_hopital'],
                $row['nom'],
                $row['localisation']
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
        $sql = "INSERT INTO hopital
                (nom, localisation)
                VALUES (:nom, :localisation)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'nom' => $hopital->getNom(),
            'localisation' => $hopital->getLocalisation()
        ]);
    }

    /**
     * Modifier un hôpital
     *
     * @param hopital $hopital
     */
    public function update($hopital)
    {
        $sql = "UPDATE hopital
                SET nom = :nom,
                    localisation = :localisation
                WHERE id_hopital = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $hopital->getIdHopital(),
            'nom' => $hopital->getNom(),
            'localisation' => $hopital->getLocalisation()
        ]);
    }

    /**
     * Supprimer un hôpital
     *
     * @param $id
     */
    public function delete($id)
    {
        $sql = "DELETE FROM hopital
                WHERE id_hopital = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}