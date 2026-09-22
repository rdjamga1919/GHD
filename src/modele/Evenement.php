<?php
class Evenement
{
    private $id_evenement;
    private $titre;
    private $description;
    private $type_evenement;
    private $adresse_lieu;
    private $element_requis;
    private $nombre_place;
    private $date_heure_evenement;

    /**
     * @param $id_evenement
     * @param $date_heure_evenement
     * @param $element_requis
     * @param $nombre_place
     * @param $type_evenement
     * @param $adresse_lieu
     * @param $description
     * @param $titre
     */
    public function __construct($id_evenement, $date_heure_evenement, $element_requis, $nombre_place, $type_evenement, $adresse_lieu, $description, $titre)
    {
        $this->id_evenement = $id_evenement;
        $this->date_heure_evenement = $date_heure_evenement;
        $this->element_requis = $element_requis;
        $this->nombre_place = $nombre_place;
        $this->type_evenement = $type_evenement;
        $this->adresse_lieu = $adresse_lieu;
        $this->description = $description;
        $this->titre = $titre;
    }

    /**
     * @return mixed
     */
    public function getIdEvenement()
    {
        return $this->id_evenement;
    }

    /**
     * @param mixed $id_evenement
     */
    public function setIdEvenement($id_evenement): void
    {
        $this->id_evenement = $id_evenement;
    }

    /**
     * @return mixed
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * @param mixed $titre
     */
    public function setTitre($titre): void
    {
        $this->titre = $titre;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getTypeEvenement()
    {
        return $this->type_evenement;
    }

    /**
     * @param mixed $type_evenement
     */
    public function setTypeEvenement($type_evenement): void
    {
        $this->type_evenement = $type_evenement;
    }

    /**
     * @return mixed
     */
    public function getAdresseLieu()
    {
        return $this->adresse_lieu;
    }

    /**
     * @param mixed $adresse_lieu
     */
    public function setAdresseLieu($adresse_lieu): void
    {
        $this->adresse_lieu = $adresse_lieu;
    }

    /**
     * @return mixed
     */
    public function getElementRequis()
    {
        return $this->element_requis;
    }

    /**
     * @param mixed $element_requis
     */
    public function setElementRequis($element_requis): void
    {
        $this->element_requis = $element_requis;
    }

    /**
     * @return mixed
     */
    public function getNombrePlace()
    {
        return $this->nombre_place;
    }

    /**
     * @param mixed $nombre_place
     */
    public function setNombrePlace($nombre_place): void
    {
        $this->nombre_place = $nombre_place;
    }

    /**
     * @return mixed
     */
    public function getDateHeureEvenement()
    {
        return $this->date_heure_evenement;
    }

    /**
     * @param mixed $date_heure_evenement
     */
    public function setDateHeureEvenement($date_heure_evenement): void
    {
        $this->date_heure_evenement = $date_heure_evenement;
    }




}