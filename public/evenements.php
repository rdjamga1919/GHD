<?php

/**
 * Modèle représentant un événement (table Evenement).
 */
class Evenement
{
    private ?int $idEvenement;
    private string $titre;
    private string $description;
    private string $typeEvenement;
    private string $adresseLieu;
    private ?string $elementRequis;
    private int $nombrePlace;
    private string $dateHeureEvenement; // format 'Y-m-d H:i:s'

    public function __construct(
        string $titre,
        string $description,
        string $typeEvenement,
        string $adresseLieu,
        ?string $elementRequis,
        int $nombrePlace,
        string $dateHeureEvenement,
        ?int $idEvenement = null
    ) {
        $this->idEvenement        = $idEvenement;
        $this->titre              = $titre;
        $this->description        = $description;
        $this->typeEvenement      = $typeEvenement;
        $this->adresseLieu        = $adresseLieu;
        $this->elementRequis      = $elementRequis;
        $this->nombrePlace        = $nombrePlace;
        $this->dateHeureEvenement = $dateHeureEvenement;
    }

    /**
     * Construit un Evenement à partir d'une ligne SQL (tableau associatif).
     */
    public static function fromArray(array $row): self
    {
        return new self(
            $row['titre'],
            $row['description'],
            $row['type_evenement'],
            $row['adresse_lieu'],
            $row['element_requis'] ?? null,
            (int) $row['nombre_place'],
            $row['date_heure_evenement'],
            isset($row['id_evenement']) ? (int) $row['id_evenement'] : null
        );
    }

    // ----------------------------- Getters -----------------------------

    public function getIdEvenement(): ?int
    {
        return $this->idEvenement;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTypeEvenement(): string
    {
        return $this->typeEvenement;
    }

    public function getAdresseLieu(): string
    {
        return $this->adresseLieu;
    }

    public function getElementRequis(): ?string
    {
        return $this->elementRequis;
    }

    public function getNombrePlace(): int
    {
        return $this->nombrePlace;
    }

    public function getDateHeureEvenement(): string
    {
        return $this->dateHeureEvenement;
    }

    // ----------------------------- Setters -----------------------------

    public function setIdEvenement(?int $idEvenement): void
    {
        $this->idEvenement = $idEvenement;
    }

    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setTypeEvenement(string $typeEvenement): void
    {
        $this->typeEvenement = $typeEvenement;
    }

    public function setAdresseLieu(string $adresseLieu): void
    {
        $this->adresseLieu = $adresseLieu;
    }

    public function setElementRequis(?string $elementRequis): void
    {
        $this->elementRequis = $elementRequis;
    }

    public function setNombrePlace(int $nombrePlace): void
    {
        $this->nombrePlace = $nombrePlace;
    }

    public function setDateHeureEvenement(string $dateHeureEvenement): void
    {
        $this->dateHeureEvenement = $dateHeureEvenement;
    }
}