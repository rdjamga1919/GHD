<?php
class Candidature
{
    private $id_candidature;
    private $motivation;
    private $date_candidature;
    private $statut;
    private $ref_utilisateur;
    private $ref_offre;

    public function __construct($id_candidature, $motivation, $date_candidature, $statut, $ref_utilisateur, $ref_offre)
    {
        $this->id_candidature = $id_candidature;
        $this->motivation = $motivation;
        $this->date_candidature = $date_candidature;
        $this->statut = $statut;
        $this->ref_utilisateur = $ref_utilisateur;
        $this->ref_offre = $ref_offre;
    }

    /**
     * @return mixed
     */
    public function getIdCandidature()
    {
        return $this->id_candidature;
    }

    /**
     * @param mixed $id_candidature
     */
    public function setIdCandidature($id_candidature): void
    {
        $this->id_candidature = $id_candidature;
    }

    /**
     * @return mixed
     */
    public function getMotivation()
    {
        return $this->motivation;
    }

    /**
     * @param mixed $motivation
     */
    public function setMotivation($motivation): void
    {
        $this->motivation = $motivation;
    }

    /**
     * @return mixed
     */
    public function getDateCandidature()
    {
        return $this->date_candidature;
    }

    /**
     * @param mixed $date_candidature
     */
    public function setDateCandidature($date_candidature): void
    {
        $this->date_candidature = $date_candidature;
    }

    /**
     * @return mixed
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param mixed $statut
     */
    public function setStatut($statut): void
    {
        $this->statut = $statut;
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
    public function getRefOffre()
    {
        return $this->ref_offre;
    }

    /**
     * @param mixed $ref_offre
     */
    public function setRefOffre($ref_offre): void
    {
        $this->ref_offre = $ref_offre;
    }

}
