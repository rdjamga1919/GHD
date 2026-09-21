<?php
class hopital
{
    private $id_hopital;
    private $nom;
    private $localisation;

    /**
     * @param $id_hopital
     * @param $nom
     * @param $localisation
     */
    public function __construct($id_hopital, $nom, $localisation)
    {
        $this->id_hopital = $id_hopital;
        $this->nom = $nom;
        $this->localisation = $localisation;
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
        return $this->nom;
    }

    /**
     * @return mixed
     */
    public function getLocalisation()
    {
        return $this->localisation;
    }

    /**
     * @param mixed $id_hopital
     */
    public function setIdHopital($id_hopital)
    {
        $this->id_hopital = $id_hopital;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @param mixed $localisation
     */
    public function setLocalisation($localisation)
    {
        $this->localisation = $localisation;
    }
}