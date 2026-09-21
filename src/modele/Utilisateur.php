<?php

class Utilisateur
{
    private ?int $idUtilisateur;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $motDePasse;
    private ?string $dateCreation;
    private string $role;      // 'etudiant', 'medecin', 'partenaire' ou 'gestionnaire'
    private bool $estValide;

    public function __construct(
        ?int $idUtilisateur,
        string $nom,
        string $prenom,
        string $email,
        string $motDePasse,
        string $role,
        bool $estValide = false,
        ?string $dateCreation = null
    ) {
        $this->idUtilisateur = $idUtilisateur;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->role = $role;
        $this->estValide = $estValide;
        $this->dateCreation = $dateCreation;
    }

    public function getIdUtilisateur(): ?int { return $this->idUtilisateur; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getEmail(): string { return $this->email; }
    public function getMotDePasse(): string { return $this->motDePasse; }
    public function getDateCreation(): ?string { return $this->dateCreation; }
    public function getRole(): string { return $this->role; }
    public function estValide(): bool { return $this->estValide; }

    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setMotDePasse(string $motDePasse): void { $this->motDePasse = $motDePasse; }
    public function setRole(string $role): void { $this->role = $role; }
    public function setEstValide(bool $estValide): void { $this->estValide = $estValide; }
}