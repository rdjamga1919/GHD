<?php
class Offre
{
    private $id_offre;
    private $titre;
    private $description;
    private $missions;
    private $salaire;
    private $type_d_offre;
    private $etat;
    private $date_publication;
    private $ref_utilisateur;

    /**
     * @param $id_offre
     * @param $ref_utilisateur
     * @param $date_publication
     * @param $etat
     * @param $type_d_offre
     * @param $missions
     * @param $description
     * @param $titre
     * @param $salaire
     */
    public function __construct($id_offre, $ref_utilisateur, $date_publication, $etat, $type_d_offre, $missions, $description, $titre, $salaire)
    {
        $this->id_offre = $id_offre;
        $this->ref_utilisateur = $ref_utilisateur;
        $this->date_publication = $date_publication;
        $this->etat = $etat;
        $this->type_d_offre = $type_d_offre;
        $this->missions = $missions;
        $this->description = $description;
        $this->titre = $titre;
        $this->salaire = $salaire;
    }

    /**
     * @return mixed
     */
    public function getRefUtilisateur()
    {
        return $this->ref_utilisateur;
    }

    /**
     * @param mixed $ref_utilisateur
     */
    public function setRefUtilisateur($ref_utilisateur): void
    {
        $this->ref_utilisateur = $ref_utilisateur;
    }

    /**
     * @return mixed
     */
    public function getDatePublication()
    {
        return $this->date_publication;
    }

    /**
     * @param mixed $date_publication
     */
    public function setDatePublication($date_publication): void
    {
        $this->date_publication = $date_publication;
    }

    /**
     * @return mixed
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $etat
     */
    public function setEtat($etat): void
    {
        $this->etat = $etat;
    }

    /**
     * @return mixed
     */
    public function getTypeDOffre()
    {
        return $this->type_d_offre;
    }

    /**
     * @param mixed $type_d_offre
     */
    public function setTypeDOffre($type_d_offre): void
    {
        $this->type_d_offre = $type_d_offre;
    }

    /**
     * @return mixed
     */
    public function getSalaire()
    {
        return $this->salaire;
    }

    /**
     * @param mixed $salaire
     */
    public function setSalaire($salaire): void
    {
        $this->salaire = $salaire;
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
    public function getMissions()
    {
        return $this->missions;
    }

    /**
     * @param mixed $missions
     */
    public function setMissions($missions): void
    {
        $this->missions = $missions;
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
    public function getIdOffre()
    {
        return $this->id_offre;
    }

    /**
     * @param mixed $id_offre
     */
    public function setIdOffre($id_offre): void
    {
        $this->id_offre = $id_offre;
    }



}