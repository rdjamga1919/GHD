<?php
class specialite
{
    private $id_specialite;
    private $nom;

    /**
     * @param $id_specialite
     * @param $nom
     */
    public function __construct($id_specialite, $nom)
    {
        $this->id_specialite = $id_specialite;
        $this->nom = $nom;
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
    public function getNom()
    {
        return $this->nom;
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
    public function setNom($nom)
    {
        $this->nom = $nom;
    }
}