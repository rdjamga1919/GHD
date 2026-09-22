<?php
class DemandeContact
{
    private $id_demande;
    private $nom_expediteur;
    private $email_expediteur;
    private $sujet;
    private $message;
    private $date_d_envoi;
    private $ref_utilisateur;

    /**
     * @param $date_d_envoi
     * @param $ref_utilisateur
     * @param $sujet
     * @param $message
     * @param $id_demande
     * @param $email_expediteur
     * @param $nom_expediteur
     */
    public function __construct($date_d_envoi, $ref_utilisateur, $sujet, $message, $id_demande, $email_expediteur, $nom_expediteur)
    {
        $this->date_d_envoi = $date_d_envoi;
        $this->ref_utilisateur = $ref_utilisateur;
        $this->sujet = $sujet;
        $this->message = $message;
        $this->id_demande = $id_demande;
        $this->email_expediteur = $email_expediteur;
        $this->nom_expediteur = $nom_expediteur;
    }

    /**
     * @return mixed
     */
    public function getIdDemande()
    {
        return $this->id_demande;
    }

    /**
     * @param mixed $id_demande
     * @return DemandeContact
     */
    public function setIdDemande($id_demande)
    {
        $this->id_demande = $id_demande;
        return $this;
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
     * @return DemandeContact
     */
    public function setRefUtilisateur($ref_utilisateur)
    {
        $this->ref_utilisateur = $ref_utilisateur;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateDEnvoi()
    {
        return $this->date_d_envoi;
    }

    /**
     * @param mixed $date_d_envoi
     * @return DemandeContact
     */
    public function setDateDEnvoi($date_d_envoi)
    {
        $this->date_d_envoi = $date_d_envoi;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return DemandeContact
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSujet()
    {
        return $this->sujet;
    }

    /**
     * @param mixed $sujet
     * @return DemandeContact
     */
    public function setSujet($sujet)
    {
        $this->sujet = $sujet;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmailExpediteur()
    {
        return $this->email_expediteur;
    }

    /**
     * @param mixed $email_expediteur
     * @return DemandeContact
     */
    public function setEmailExpediteur($email_expediteur)
    {
        $this->email_expediteur = $email_expediteur;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNomExpediteur()
    {
        return $this->nom_expediteur;
    }

    /**
     * @param mixed $nom_expediteur
     * @return DemandeContact
     */
    public function setNomExpediteur($nom_expediteur)
    {
        $this->nom_expediteur = $nom_expediteur;
        return $this;
    }




}