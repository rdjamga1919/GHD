<?php

class Utilisateur
{
    private ?int $id_utilisateur;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $mot_de_passe;
    private ?string $date_creation;
    private string $role;      // 'etudiant', 'medecin', 'partenaire' ou 'gestionnaire'
    private bool $est_valide;

    public function __construct(
        ?int $id_utilisateur,
        string $nom,
        string $prenom,
        string $email,
        string $mot_de_passe,
        string $role,
        bool $est_valide = false,
        ?string $date_creation = null
    ) {
        $this->id_utilisateur = $id_utilisateur;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->mot_de_passe = $mot_de_passe;
        $this->role = $role;
        $this->est_valide = $est_valide;
        $this->date_creation = $date_creation;
    }

    public function getIdUtilisateur(): ?int { return $this->id_utilisateur; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getEmail(): string { return $this->email; }
    public function getMotDePasse(): string { return $this->mot_de_passe; }
    public function getDateCreation(): ?string { return $this->date_creation; }
    public function getRole(): string { return $this->role; }
    public function estValide(): bool { return $this->est_valide; }

    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setMotDePasse(string $motDePasse): void { $this->mot_de_passe = $motDePasse; }
    public function setRole(string $role): void { $this->role = $role; }
    public function setEstValide(bool $estValide): void { $this->est_valide = $estValide; }
}