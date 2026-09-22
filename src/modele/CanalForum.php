<?php

class CanalForum
{
    private $id_canal;
    private $nom_canal;
    private $acces;

    public function __construct($id_canal, $nom_canal, $acces)
    {
        $this->id_canal = $id_canal;
        $this->nom_canal = $nom_canal;
        $this->acces = $acces;
    }

    /**
     * @return mixed
     */
    public function getIdCanal()
    {
        return $this->id_canal;
    }

    /**
     * @param mixed $id_canal
     */
    public function setIdCanal($id_canal): void
    {
        $this->id_canal = $id_canal;
    }

    /**
     * @return mixed
     */
    public function getNomCanal()
    {
        return $this->nom_canal;
    }

    /**
     * @param mixed $nom_canal
     */
    public function setNomCanal($nom_canal): void
    {
        $this->nom_canal = $nom_canal;
    }

    /**
     * @return mixed
     */
    public function getAcces()
    {
        return $this->acces;
    }

    /**
     * @param mixed $acces
     */
    public function setAcces($acces): void
    {
        $this->acces = $acces;
    }

}