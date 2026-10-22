
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

    private function hydrate($row)
    {
        return new Entreprise(
            (int) $row['id_entreprise'],
            $row['nom_entreprise'],
            $row['adresse'],
            $row['site_web']
        );
    }

    // Récupérer toutes les entreprises
    public function findAll()
    {
        $sql = "SELECT * FROM Entreprise ORDER BY nom_entreprise";
        $stmt = $this->connexionBdd->query($sql);

        $entreprises = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $entreprises[] = $this->hydrate($row);
        }

        return $entreprises;
    }

    // Méthode utilisée par Onésime
    public function getAllEntreprises()
    {
        return $this->findAll();
    }

    // Récupérer une entreprise par son ID
    public function findById($id_entreprise)
    {
        $sql = "SELECT * FROM Entreprise WHERE id_entreprise = :id_entreprise";
        $stmt = $this->connexionBdd->prepare($sql);
        $stmt->execute(['id_entreprise' => $id_entreprise]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }

    // Méthode utilisée par Onésime
    public function getEntreprise($idEntreprise)
    {
        return $this->findById($idEntreprise);
    }

    // Rechercher une entreprise par son nom
    public function rechercherEntreprises($recherche)
    {
        $sql = "SELECT * FROM Entreprise
                WHERE nom_entreprise LIKE :recherche
                ORDER BY nom_entreprise";

        $stmt = $this->connexionBdd->prepare($sql);
        $stmt->execute([
            'recherche' => '%' . addcslashes($recherche, '%_\\') . '%'
        ]);

        $entreprises = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $entreprises[] = $this->hydrate($row);
        }

        return $entreprises;
    }

    // Rechercher une entreprise par son site web
    public function getEntrepriseBySiteWeb($siteWeb)
    {
        $sql = "SELECT * FROM Entreprise WHERE site_web = :site_web";
        $stmt = $this->connexionBdd->prepare($sql);
        $stmt->execute(['site_web' => $siteWeb]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }

    // Vérifier qu'une entreprise existe
    public function entrepriseExiste($idEntreprise)
    {
        $sql = "SELECT COUNT(*) FROM Entreprise
                WHERE id_entreprise = :id_entreprise";

        $stmt = $this->connexionBdd->prepare($sql);
        $stmt->execute(['id_entreprise' => $idEntreprise]);

        return $stmt->fetchColumn() > 0;
    }

    // Ajouter une entreprise
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

        return (int) $this->connexionBdd->lastInsertId();
    }

    // Méthode utilisée par Onésime
    public function ajouterEntreprise(Entreprise $entreprise)
    {
        return $this->create($entreprise);
    }

    // Modifier une entreprise
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

    // Supprimer une entreprise
    public function delete($id_entreprise)
    {
        $sql = "DELETE FROM Entreprise WHERE id_entreprise = :id_entreprise";

        $stmt = $this->connexionBdd->prepare($sql);
        $stmt->execute(['id_entreprise' => $id_entreprise]);
    }
}
