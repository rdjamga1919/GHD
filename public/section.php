<?php

/**
 * Section du forum = une ligne de la table Canal_forum
 * (id_canal, nom_canal, description, acces).
 */
class Section
{
    /** Valeurs possibles de la colonne `acces` (ENUM du SQL) => libellé affiché. */
    public const ACCES = [
        'generale'  => 'Général',
        'medecins'  => 'Médecins',
        'etudiants' => 'Étudiants',
    ];

    private ?int $idSection;
    private string $nom;
    private ?string $description;
    private string $acces;

    public function __construct(string $nom, string $acces, ?string $description = null, ?int $idSection = null)
    {
        $this->nom = $nom;
        $this->acces = $acces;
        $this->description = $description;
        $this->idSection = $idSection;
    }

    public function getIdSection(): ?int
    {
        return $this->idSection;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getAcces(): string
    {
        return $this->acces;
    }

    public function setAcces(string $acces): void
    {
        $this->acces = $acces;
    }

    public function getLibelleAcces(): string
    {
        return self::ACCES[$this->acces] ?? $this->acces;
    }
}