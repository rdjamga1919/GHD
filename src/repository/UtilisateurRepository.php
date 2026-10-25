<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Utilisateur.php';

class UtilisateurRepository
{
    private $connexionBdd;

    public function __construct(?PDO $connexionBdd = null)
    {
        $this->connexionBdd = $connexionBdd ?? (new Bdd())->getConnexionBdd();
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
        $stmt = $this->connexionBdd->query('SELECT * FROM Utilisateur');
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function findById(int $id): ?Utilisateur
    {
        $stmt = $this->connexionBdd->prepare('SELECT * FROM Utilisateur WHERE id_utilisateur = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function findByEmail(string $email): ?Utilisateur
    {
        $stmt = $this->connexionBdd->prepare('SELECT * FROM Utilisateur WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function insert(Utilisateur $u): int
    {
        $stmt = $this->connexionBdd->prepare(
            'INSERT INTO Utilisateur (nom, prenom, email, mot_de_passe, role, est_valide)
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
        return (int) $this->connexionBdd->lastInsertId();
    }

    public function update(Utilisateur $u): void
    {
        $stmt = $this->connexionBdd->prepare(
            'UPDATE Utilisateur
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

    public function modifierUtilisateur(Utilisateur $utilisateur): void
    {
        $stmt = $this->connexionBdd->prepare(
            'UPDATE Utilisateur
             SET nom = :nom, prenom = :prenom, email = :email
             WHERE id_utilisateur = :id'
        );
        $stmt->execute([
            'nom'    => $utilisateur->getNom(),
            'prenom' => $utilisateur->getPrenom(),
            'email'  => $utilisateur->getEmail(),
            'id'     => $utilisateur->getIdUtilisateur(),
        ]);
    }

    public function emailExistePourAutreUtilisateur(string $email, int $idUtilisateur): bool
    {
        $stmt = $this->connexionBdd->prepare(
            'SELECT COUNT(*) FROM Utilisateur
             WHERE email = :email AND id_utilisateur <> :id'
        );
        $stmt->execute(['email' => $email, 'id' => $idUtilisateur]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function modifierMotDePasseUtilisateur(int $idUtilisateur, string $motDePasseHash): void
    {
        $stmt = $this->connexionBdd->prepare(
            'UPDATE Utilisateur SET mot_de_passe = :mot_de_passe
             WHERE id_utilisateur = :id'
        );
        $stmt->execute(['mot_de_passe' => $motDePasseHash, 'id' => $idUtilisateur]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->connexionBdd->prepare('DELETE FROM Utilisateur WHERE id_utilisateur = :id');
        $stmt->execute(['id' => $id]);
    }

    public function getUtilisateursEnAttente(): array
    {
        $stmt = $this->connexionBdd->query('SELECT * FROM Utilisateur WHERE est_valide = 0');
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }
}
