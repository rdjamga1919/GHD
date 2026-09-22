<?php
class PostForum
{
    private $id_post;
    private $titre;
    private $contenu;
    private $date_heure_creation;
    private $ref_canal;
    private $ref_utilisateur;

    /**
     * @param $id_post
     * @param $ref_utilisateur
     * @param $ref_canal
     * @param $date_heure_creation
     * @param $contenu
     * @param $titre
     */
    public function __construct($id_post, $ref_utilisateur, $ref_canal, $date_heure_creation, $contenu, $titre)
    {
        $this->id_post = $id_post;
        $this->ref_utilisateur = $ref_utilisateur;
        $this->ref_canal = $ref_canal;
        $this->date_heure_creation = $date_heure_creation;
        $this->contenu = $contenu;
        $this->titre = $titre;
    }

    /**
     * @return mixed
     */
    public function getIdPost()
    {
        return $this->id_post;
    }

    /**
     * @param mixed $id_post
     */
    public function setIdPost($id_post): void
    {
        $this->id_post = $id_post;
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
    public function getRefCanal()
    {
        return $this->ref_canal;
    }

    /**
     * @param mixed $ref_canal
     */
    public function setRefCanal($ref_canal): void
    {
        $this->ref_canal = $ref_canal;
    }

    /**
     * @return mixed
     */
    public function getDateHeureCreation()
    {
        return $this->date_heure_creation;
    }

    /**
     * @param mixed $date_heure_creation
     */
    public function setDateHeureCreation($date_heure_creation): void
    {
        $this->date_heure_creation = $date_heure_creation;
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


}