<?php
class ReponseForum
{
    private $id_reponse;
    private $contenu;
    private $date_heure_reponse;
    private $ref_post;
    private $ref_utilisateur;
    // Infos de l'auteur (remplies quand on lit une réponse depuis la BDD)
    private $nom_auteur;
    private $prenom_auteur;

    /**
     * @param $id_reponse
     * @param $ref_post
     * @param $contenu
     * @param $date_heure_reponse
     * @param $ref_utilisateur
     * @param $nom_auteur
     * @param $prenom_auteur
     */
    public function __construct($id_reponse, $ref_post, $contenu, $date_heure_reponse, $ref_utilisateur, $nom_auteur = null, $prenom_auteur = null)
    {
        $this->id_reponse = $id_reponse;
        $this->ref_post = $ref_post;
        $this->contenu = $contenu;
        $this->date_heure_reponse = $date_heure_reponse;
        $this->ref_utilisateur = $ref_utilisateur;
        $this->nom_auteur = $nom_auteur;
        $this->prenom_auteur = $prenom_auteur;
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

    /**
     * @return mixed
     */
    public function getNomAuteur()
    {
        return $this->nom_auteur;
    }

    /**
     * @return mixed
     */
    public function getPrenomAuteur()
    {
        return $this->prenom_auteur;
    }

}