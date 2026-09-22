<?php
class Specialite
{
    private $id_specialite;
    private $libelle_specialite;

    /**
     * @param $id_specialite
     * @param $libelle_specialite
     */
    public function __construct($id_specialite, $libelle_specialite)
    {
        $this->id_specialite = $id_specialite;
        $this->libelle_specialite = $libelle_specialite;
    }

    /**
     * @return mixed
     */
    public function getIdSpecialite()
    {
        return $this->id_specialite;
    }

    /**
     * @return mixed
     */
    public function getLibelle()
    {
        return $this->libelle_specialite;
    }

    /**
     * @param mixed $id_specialite
     */
    public function setIdSpecialite($id_specialite)
    {
        $this->id_specialite = $id_specialite;
    }

    /**
     * @param mixed $nom
     */
    public function setLibelle($libelle_specialite)
    {
        $this->libelle_specialite = $libelle_specialite;
    }
}