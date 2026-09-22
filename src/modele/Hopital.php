<?php
class Hopital
{
    private $id_hopital;
    private $nom_hopital;
    private $commune;

    /**
     * @param $id_hopital
     * @param $nom_hopital
     * @param $commune
     */
    public function __construct($id_hopital, $nom_hopital, $commune)
    {
        $this->id_hopital = $id_hopital;
        $this->nom_hopital = $nom_hopital;
        $this->commune = $commune;
    }

    /**
     * @return mixed
     */
    public function getIdHopital()
    {
        return $this->id_hopital;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom_hopital;
    }

    /**
     * @return mixed
     */
    public function getCommune()
    {
        return $this->commune;
    }

    /**
     * @param mixed $id_hopital
     */
    public function setIdHopital($id_hopital)
    {
        $this->id_hopital = $id_hopital;
    }

    /**
     * @param mixed $nom_hopital
     */
    public function setNom($nom_hopital)
    {
        $this->nom_hopital = $nom_hopital;
    }

    /**
     * @param mixed $commune
     */
    public function setCommune($commune)
    {
        $this->commune = $commune;
    }
}