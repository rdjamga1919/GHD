<?php

require_once __DIR__ . '/Utilisateur.php';

class Etudiant extends Utilisateur
{
    private ?int $idEtudiant;
    private ?string $cvEtudiant;
    private string $formation;
    private int $refEtablissement;

    public function __construct(
        ?int $idUtilisateur, string $nom, string $prenom, string $email, string $motDePasse,
        string $formation, int $refEtablissement,
        ?string $cvEtudiant = null, ?int $idEtudiant = null,
        bool $estValide = false, ?string $dateCreation = null
    ) {
        parent::__construct($idUtilisateur, $nom, $prenom, $email, $motDePasse, 'etudiant', $estValide, $dateCreation);
        $this->formation = $formation;
        $this->refEtablissement = $refEtablissement;
        $this->cvEtudiant = $cvEtudiant;
        $this->idEtudiant = $idEtudiant;
    }

    public function getIdEtudiant(): ?int { return $this->idEtudiant; }
    public function getCvEtudiant(): ?string { return $this->cvEtudiant; }
    public function getFormation(): string { return $this->formation; }
    public function getRefEtablissement(): int { return $this->refEtablissement; }

    public function setCvEtudiant(?string $cvEtudiant): void { $this->cvEtudiant = $cvEtudiant; }
    public function setFormation(string $formation): void { $this->formation = $formation; }
    public function setRefEtablissement(int $refEtablissement): void { $this->refEtablissement = $refEtablissement; }
}