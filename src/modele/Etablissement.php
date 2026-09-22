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

    /**
     * @return mixed
     */
    public function getIdEtablissement()
    {
        return $this->id_etablissement;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom_etablissement;
    }

    /**
     * @return mixed
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @return mixed
     */
    public function getSiteWeb()
    {
        return $this->site_web;
    }

    /**
     * @param mixed $id_etablissement
     */
    public function setIdEtablissement($id_etablissement)
    {
        $this->id_etablissement = $id_etablissement;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @param mixed $adresse
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    /**
     * @param mixed $site_web
     */
    public function setSiteWeb($site_web)
    {
        $this->site_web = $site_web;
    }
}