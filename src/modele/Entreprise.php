<?php
class Entreprise
{
    private $id_entreprise;
    private $nom_entreprise;
    private $adresse;
    private $site_web;

    /**
     * @param $id_entreprise
     * @param $nom_entreprise
     * @param $adresse
     * @param $site_web
     */
    public function __construct($id_entreprise, $nom_entreprise, $adresse, $site_web)
    {
        $this->id_entreprise = $id_entreprise;
        $this->nom_entreprise = $nom_entreprise;
        $this->adresse = $adresse;
        $this->site_web = $site_web;
    }

    /**
     * @return mixed
     */
    public function getIdEntreprise()
    {
        return $this->id_entreprise;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom_entreprise;
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
     * @param mixed $id_entreprise
     */
    public function setIdEntreprise($id_entreprise)
    {
        $this->id_entreprise = $id_entreprise;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom_entreprise)
    {
        $this->nom_entreprise = $nom_entreprise;
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