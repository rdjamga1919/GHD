<?php

require_once __DIR__ . '/Utilisateur.php';

class Partenaire extends Utilisateur
{
    private ?int $idPartenaire;
    private string $poste;
    private int $refEntreprise;

    public function __construct(
        ?int $idUtilisateur, string $nom, string $prenom, string $email, string $motDePasse,
        string $poste, int $refEntreprise, ?int $idPartenaire = null,
        bool $estValide = false, ?string $dateCreation = null
    ) {
        parent::__construct($idUtilisateur, $nom, $prenom, $email, $motDePasse, 'partenaire', $estValide, $dateCreation);
        $this->poste = $poste;
        $this->refEntreprise = $refEntreprise;
        $this->idPartenaire = $idPartenaire;
    }

    public function getIdPartenaire(): ?int { return $this->idPartenaire; }
    public function getPoste(): string { return $this->poste; }
    public function getRefEntreprise(): int { return $this->refEntreprise; }

    public function setPoste(string $poste): void { $this->poste = $poste; }
    public function setRefEntreprise(int $refEntreprise): void { $this->refEntreprise = $refEntreprise; }
}