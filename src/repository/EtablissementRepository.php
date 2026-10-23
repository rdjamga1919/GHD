<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Etablissement.php';

class EtablissementRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    /**
     * Récupérer tous les établissements
     *
     * @return etablissement[]
     */
    public function findAll()
    {
        $sql = "SELECT * FROM Etablissement";
        $stmt = $this->connexionBdd->query($sql);

        $etablissements = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $etablissements[] = new etablissement(
                $row['id_etablissement'],
                $row['nom_etablissement'],
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
        $sql = "SELECT * FROM Etablissement
                WHERE id_etablissement = :id";

        $stmt = $this->connexionBdd->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new etablissement(
                $row['id_etablissement'],
                $row['nom_etablissement'],
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
        $sql = "INSERT INTO Etablissement
                (nom_etablissement, adresse, site_web)
                VALUES (:nom, :adresse, :site_web)";

        $stmt = $this->connexionBdd->prepare($sql);

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
        $sql = "UPDATE Etablissement
                SET nom_etablissement = :nom,
                    adresse = :adresse,
                    site_web = :site_web
                WHERE id_etablissement = :id";

        $stmt = $this->connexionBdd->prepare($sql);

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
        $sql = "DELETE FROM Etablissement
                WHERE id_etablissement = :id";

        $stmt = $this->connexionBdd->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
    /**
     * Rechercher des établissements dont le nom contient le texte saisi
     *
     * @param string $nom
     * @return etablissement[]
     */
    public function rechercherParNom($nom)
    {
        $sql = "SELECT * FROM Etablissement
                WHERE nom_etablissement LIKE :nom
                ORDER BY nom_etablissement
                LIMIT 20";

        $stmt = $this->connexionBdd->prepare($sql);
        $stmt->execute(['nom' => '%' . addcslashes($nom, '%_\\') . '%']);

        $etablissements = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $etablissements[] = new etablissement(
                $row['id_etablissement'],
                $row['nom_etablissement'],
                $row['adresse'],
                $row['site_web']
            );
        }

        return $etablissements;
    }
}