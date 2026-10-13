<?php
// Modèle à copier vers Bdd.php, puis à adapter à votre environnement MySQL.
class Bdd
{
    private $connexionBdd;
    private $identifiant = "UTILISATEUR_MYSQL_A_RENSEIGNER";
    private $motDePasse = "MOT_DE_PASSE_MYSQL_A_RENSEIGNER";
    private $nomBdd = "BASE_MYSQL_A_RENSEIGNER";
    private $host = "HOTE_MYSQL_A_RENSEIGNER";

    public function __construct()
    {
        try {
            $this->connexionBdd = new PDO(
                "mysql:host=" . $this->host
                . ";dbname=" . $this->nomBdd
                . ";charset=utf8mb4",
                $this->identifiant,
                $this->motDePasse
            );

            $this->connexionBdd->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getConnexionBdd(): PDO
    {
        return $this->connexionBdd;
    }
}
