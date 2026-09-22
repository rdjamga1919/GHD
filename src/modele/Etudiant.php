<?php

require_once __DIR__ . '/Utilisateur.php';

class Etudiant extends Utilisateur
{
    private ?int $id_etudiant;
    private ?string $cv_etudiant;
    private string $formation;
    private int $ref_etablissement;

    public function __construct(
        ?int $id_utilisateur, string $nom, string $prenom, string $email, string $mot_de_Passe,
        string $formation, int $ref_etablissement,
        ?string $cv_etudiant = null, ?int $id_etudiant = null,
        bool $est_valide = false, ?string $date_creation = null
    ) {
        parent::__construct($id_utilisateur, $nom, $prenom, $email, $mot_de_Passe, 'etudiant', $est_valide, $date_creation);
        $this->formation = $formation;
        $this->ref_etablissement = $ref_etablissement;
        $this->cv_etudiant = $cv_etudiant;
        $this->id_etudiant = $id_etudiant;
    }

    public function getIdEtudiant(): ?int { return $this->id_etudiant; }
    public function getCvEtudiant(): ?string { return $this->cv_etudiant; }
    public function getFormation(): string { return $this->formation; }
    public function getRefEtablissement(): int { return $this->ref_etablissement; }

    public function setCvEtudiant(?string $cv_etudiant): void { $this->cv_etudiant = $cv_etudiant; }
    public function setFormation(string $formation): void { $this->formation = $formation; }
    public function setRefEtablissement(int $refEtablissement): void { $this->ref_etablissement = $refEtablissement; }
}