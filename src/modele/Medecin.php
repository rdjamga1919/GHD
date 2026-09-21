<?php

require_once __DIR__ . '/Utilisateur.php';

class Medecin extends Utilisateur
{
    private ?int $idMedecin;
    private string $numeroRpps;

    public function __construct(
        ?int $idUtilisateur, string $nom, string $prenom, string $email, string $motDePasse,
        string $numeroRpps, ?int $idMedecin = null,
        bool $estValide = false, ?string $dateCreation = null
    ) {
        parent::__construct($idUtilisateur, $nom, $prenom, $email, $motDePasse, 'medecin', $estValide, $dateCreation);
        $this->numeroRpps = $numeroRpps;
        $this->idMedecin = $idMedecin;
    }

    public function getIdMedecin(): ?int { return $this->idMedecin; }
    public function getNumeroRpps(): string { return $this->numeroRpps; }
    public function setNumeroRpps(string $numeroRpps): void { $this->numeroRpps = $numeroRpps; }
}