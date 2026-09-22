<?php

class Bdd
{
    private $connexionBdd;
    private $identifiant = "root";
    private $motDePasse = "root";
    private $nomBdd = "gdh_hsp";
    private $host = "localhost";// ajoutez :VOTRE_PORT si besoin (ex: MAMP = souvent 8889)

    public function __construct()
    {
        try {
            $this->connexionBdd = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->nomBdd . ";charset=utf8mb4",
                $this->identifiant,
                $this->motDePasse
            );
            $this->connexionBdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    /**
     * @return PDO
     */
    public function getConnexionBdd(): PDO
    {
        return $this->connexionBdd;
    }
}