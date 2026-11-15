<?php

class Specialite
{
    private $id_specialite;
    private $libelle_specialite;

    /**
     * @param int|null $id_specialite   null lors d'une création
     * @param string   $libelle_specialite
     */
    public function __construct($id_specialite, $libelle_specialite)
    {
        $this->id_specialite = $id_specialite;
        $this->libelle_specialite = $libelle_specialite;
    }

    public function getIdSpecialite()
    {
        return $this->id_specialite;
    }

    public function getLibelle()
    {
        return $this->libelle_specialite;
    }

    public function setIdSpecialite($id_specialite)
    {
        $this->id_specialite = $id_specialite;
    }

    public function setLibelle($libelle_specialite)
    {
        $this->libelle_specialite = $libelle_specialite;
    }
}