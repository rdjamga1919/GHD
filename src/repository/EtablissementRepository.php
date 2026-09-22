<?php

require_once 'Connexion.php';
require_once 'Etablissement.php';

class EtablissementRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connexion::getPdo();
    }

    /**
     * Récupérer tous les établissements
     *
     * @return etablissement[]
     */
    public function findAll()
    {
        $sql = "SELECT * FROM etablissement";
        $stmt = $this->pdo->query($sql);

        $etablissements = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $etablissements[] = new etablissement(
                $row['id_etablissement'],
                $row['nom'],
                $row['adresse'],
                $row['site_web']
            );
        }

        return $etablissements;
    }

    /**
     * Récupérer un établissement par son ID
     *
     * @param $id
     * @return etablissement|null
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM etablissement
                WHERE id_etablissement = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new etablissement(
                $row['id_etablissement'],
                $row['nom'],
                $row['adresse'],
                $row['site_web']
            );
        }

        return null;
    }

    /**
     * Ajouter un établissement
     *
     * @param etablissement $etablissement
     */
    public function create($etablissement)
    {
        $sql = "INSERT INTO etablissement
                (nom_etablissement, adresse, site_web)
                VALUES (:nom, :adresse, :site_web)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'nom' => $etablissement->getNom(),
            'adresse' => $etablissement->getAdresse(),
            'site_web' => $etablissement->getSiteWeb()
        ]);
    }

    /**
     * Modifier un établissement
     *
     * @param etablissement $etablissement
     */
    public function update($etablissement)
    {
        $sql = "UPDATE etablissement
                SET nom_etablissement = :nom,
                    adresse = :adresse,
                    site_web = :site_web
                WHERE id_etablissement = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $etablissement->getIdEtablissement(),
            'nom' => $etablissement->getNom(),
            'adresse' => $etablissement->getAdresse(),
            'site_web' => $etablissement->getSiteWeb()
        ]);
    }

    /**
     * Supprimer un établissement
     *
     * @param $id
     */
    public function delete($id)
    {
        $sql = "DELETE FROM etablissement
                WHERE id_etablissement = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}