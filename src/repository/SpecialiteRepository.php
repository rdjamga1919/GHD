<?php

require_once 'Connexion.php';
require_once 'Specialite.php';

class SpecialiteRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connexion::getPdo();
    }

    /**
     * Récupérer toutes les spécialités
     *
     * @return specialite[]
     */
    public function findAll()
    {
        $sql = "SELECT * FROM specialite";
        $stmt = $this->pdo->query($sql);

        $specialites = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $specialites[] = new specialite(
                $row['id_specialite'],
                $row['nom']
            );
        }

        return $specialites;
    }

    /**
     * Récupérer une spécialité par son ID
     *
     * @param $id
     * @return specialite|null
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM specialite
                WHERE id_specialite = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new specialite(
                $row['id_specialite'],
                $row['nom']
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
        $sql = "INSERT INTO specialite
                (nom)
                VALUES (:nom)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'nom' => $specialite->getNom()
        ]);
    }

    /**
     * Modifier une spécialité
     *
     * @param specialite $specialite
     */
    public function update($specialite)
    {
        $sql = "UPDATE specialite
                SET nom = :nom
                WHERE id_specialite = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $specialite->getIdSpecialite(),
            'nom' => $specialite->getNom()
        ]);
    }

    /**
     * Supprimer une spécialité
     *
     * @param $id
     */
    public function delete($id)
    {
        $sql = "DELETE FROM specialite
                WHERE id_specialite = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}