<?php
class ReponseForum
{
    private $id_reponse;
    private $contenu;
    private $date_heure_reponse;
    private $ref_post;
    private $ref_utilisateur;

    /**
     * @param $id_reponse
     * @param $ref_post
     * @param $contenu
     * @param $date_heure_reponse
     * @param $ref_utilisateur
     */
    public function __construct($id_reponse, $ref_post, $contenu, $date_heure_reponse, $ref_utilisateur)
    {
        $this->id_reponse = $id_reponse;
        $this->ref_post = $ref_post;
        $this->contenu = $contenu;
        $this->date_heure_reponse = $date_heure_reponse;
        $this->ref_utilisateur = $ref_utilisateur;
    }

    /**
     * @return mixed
     */
    public function getIdReponse()
    {
        return $this->id_reponse;
    }

    /**
     * @param mixed $id_reponse
     */
    public function setIdReponse($id_reponse): void
    {
        $this->id_reponse = $id_reponse;
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
    public function getRefPost()
    {
        return $this->ref_post;
    }

    /**
     * @param mixed $ref_post
     */
    public function setRefPost($ref_post): void
    {
        $this->ref_post = $ref_post;
    }

    /**
     * @return mixed
     */
    public function getDateHeureReponse()
    {
        return $this->date_heure_reponse;
    }

    /**
     * @param mixed $date_heure_reponse
     */
    public function setDateHeureReponse($date_heure_reponse): void
    {
        $this->date_heure_reponse = $date_heure_reponse;
    }

    /**
     * @return mixed
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * @param mixed $contenu
     */
    public function setContenu($contenu): void
    {
        $this->contenu = $contenu;
    }


}
