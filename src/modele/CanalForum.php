<?php

class CanalForum
{
    private $id_canal;
    private $nom_canal;
    private $acces;
    private $description;

    public function __construct(
        $id_canal,
        $nom_canal,
        $acces,
        $description = null
    ) {
        $this->id_canal = $id_canal;
        $this->nom_canal = $nom_canal;
        $this->acces = $acces;
        $this->description = $description;
    }

    public function getIdCanal()
    {
        return $this->id_canal;
    }

    public function setIdCanal($id_canal): void
    {
        $this->id_canal = $id_canal;
    }

    public function getNomCanal()
    {
        return $this->nom_canal;
    }

    public function setNomCanal($nom_canal): void
    {
        $this->nom_canal = $nom_canal;
    }

    public function getAcces()
    {
        return $this->acces;
    }

    public function setAcces($acces): void
    {
        $this->acces = $acces;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }
}
