<?php

/**
 * Modèle d'un événement (table Evenement).
 * Ordre du constructeur identique à celui utilisé par EvenementRepository.
 */
class Evenement
{
    private ?int $idEvenement;
    private string $dateHeureEvenement; // 'Y-m-d H:i:s'
    private ?string $elementRequis;
    private int $nombrePlace;
    private string $typeEvenement;
    private string $adresseLieu;
    private string $description;
    private string $titre;

    public function __construct(
        ?int $idEvenement,
        string $dateHeureEvenement,
        ?string $elementRequis,
        int $nombrePlace,
        string $typeEvenement,
        string $adresseLieu,
        string $description,
        string $titre
    ) {
        $this->idEvenement        = $idEvenement;
        $this->dateHeureEvenement = $dateHeureEvenement;
        $this->elementRequis      = $elementRequis;
        $this->nombrePlace        = $nombrePlace;
        $this->typeEvenement      = $typeEvenement;
        $this->adresseLieu        = $adresseLieu;
        $this->description        = $description;
        $this->titre              = $titre;
    }

    public function getIdEvenement(): ?int { return $this->idEvenement; }
    public function getDateHeureEvenement(): string { return $this->dateHeureEvenement; }
    public function getElementRequis(): ?string { return $this->elementRequis; }
    public function getNombrePlace(): int { return $this->nombrePlace; }
    public function getTypeEvenement(): string { return $this->typeEvenement; }
    public function getAdresseLieu(): string { return $this->adresseLieu; }
    public function getDescription(): string { return $this->description; }
    public function getTitre(): string { return $this->titre; }

    public function setIdEvenement(?int $v): void { $this->idEvenement = $v; }
    public function setDateHeureEvenement(string $v): void { $this->dateHeureEvenement = $v; }
    public function setElementRequis(?string $v): void { $this->elementRequis = $v; }
    public function setNombrePlace(int $v): void { $this->nombrePlace = $v; }
    public function setTypeEvenement(string $v): void { $this->typeEvenement = $v; }
    public function setAdresseLieu(string $v): void { $this->adresseLieu = $v; }
    public function setDescription(string $v): void { $this->description = $v; }
    public function setTitre(string $v): void { $this->titre = $v; }
}