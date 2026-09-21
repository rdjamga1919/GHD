<?php

require_once 'Connexion.php';
require_once 'Entreprise.php';

class EntrepriseRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connexion::getPdo();
    }

    /**
     * Récupérer toutes les entreprises
     *
     * @return entreprise[]
     */
    public function findAll()
    {
        $sql = "SELECT * FROM entreprise";
        $stmt = $this->pdo->query($sql);

        $entreprises = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $entreprises[] = new entreprise(
                $row['id_entreprise'],
                $row['nom'],
                $row['adresse'],
                $row['site_web']
            );
        }

        return $entreprises;
    }

    /**
     * Récupérer une entreprise par son ID
     *
     * @param $id
     * @return entreprise|null
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM entreprise WHERE id_entreprise = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new entreprise(
                $row['id_entreprise'],
                $row['nom'],
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
        $sql = "INSERT INTO entreprise
                (nom, adresse, site_web)
                VALUES (:nom, :adresse, :site_web)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'nom' => $entreprise->getNom(),
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
        $sql = "UPDATE entreprise
                SET nom = :nom,
                    adresse = :adresse,
                    site_web = :site_web
                WHERE id_entreprise = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $entreprise->getIdEntreprise(),
            'nom' => $entreprise->getNom(),
            'adresse' => $entreprise->getAdresse(),
            'site_web' => $entreprise->getSiteWeb()
        ]);
    }

    /**
     * Supprimer une entreprise
     *
     * @param $id
     */
    public function delete($id)
    {
        $sql = "DELETE FROM entreprise WHERE id_entreprise = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}