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

    public function findAll(): array
    {
        $sql = "SELECT id_hopital, nom_hopital, commune FROM Hopital ORDER BY nom_hopital";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll(PDO::FETCH_ASSOC);
        $tabHopital = [];
        foreach ($results as $result) {
            $tabHopital[] = new Hopital($result["id_hopital"], $result["nom_hopital"], $result["commune"]);
        }
        return $tabHopital;
    }
}