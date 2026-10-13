<?php
class Etablissement
{
    private $id_etablissement;
    private $nom_etablissement;
    private $adresse;
    private $site_web;

    /**
     * @param $id_etablissement
     * @param $nom_etablissement
     * @param $adresse
     * @param $site_web
     */
    public function __construct($id_etablissement, $nom_etablissement, $adresse, $site_web)
    {
        $this->id_etablissement = $id_etablissement;
        $this->nom_etablissement = $nom_etablissement;
        $this->adresse = $adresse;
        $this->site_web = $site_web;
    }

    public function getIdEtablissement()
    {
        return $this->id_etablissement;
    }

    public function getNom()
    {
        return $this->nom_etablissement;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function getSiteWeb()
    {
        return $this->site_web;
    }

    public function setIdEtablissement($id_etablissement)
    {
        $this->id_etablissement = $id_etablissement;
    }

    public function setNom($nom)
    {
        $this->nom_etablissement = $nom;
    }

    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    public function setSiteWeb($site_web)
    {
        $this->site_web = $site_web;
    }
}