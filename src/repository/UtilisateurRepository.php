<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Utilisateur.php';

class UtilisateurRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Bdd::getConnexion();
    }

    private function hydrate(array $row): Utilisateur
    {
        return new Utilisateur(
            (int) $row['id_utilisateur'],
            $row['nom'],
            $row['prenom'],
            $row['email'],
            $row['mot_de_passe'],
            $row['role'],
            (bool) $row['est_valide'],
            $row['date_creation']
        );
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM utilisateur');
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function findById(int $id): ?Utilisateur
    {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateur WHERE id_utilisateur = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function findByEmail(string $email): ?Utilisateur
    {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateur WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function insert(Utilisateur $u): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, est_valide)
             VALUES (:nom, :prenom, :email, :mdp, :role, :valide)'
        );
        $stmt->execute([
            'nom'    => $u->getNom(),
            'prenom' => $u->getPrenom(),
            'email'  => $u->getEmail(),
            'mdp'    => password_hash($u->getMotDePasse(), PASSWORD_DEFAULT),
            'role'   => $u->getRole(),
            'valide' => (int) $u->estValide(),
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(Utilisateur $u): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE utilisateur
             SET nom = :nom, prenom = :prenom, email = :email, role = :role, est_valide = :valide
             WHERE id_utilisateur = :id'
        );
        $stmt->execute([
            'nom'    => $u->getNom(),
            'prenom' => $u->getPrenom(),
            'email'  => $u->getEmail(),
            'role'   => $u->getRole(),
            'valide' => (int) $u->estValide(),
            'id'     => $u->getIdUtilisateur(),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM utilisateur WHERE id_utilisateur = :id');
        $stmt->execute(['id' => $id]);
    }
}