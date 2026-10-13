<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Partenaire.php';
require_once __DIR__ . '/UtilisateurRepository.php';

class PartenaireRepository
{
    private PDO $pdo;

    private const SELECT = 'SELECT u.*, p.id_partenaire, p.poste, p.ref_entreprise
                            FROM utilisateur u
                            JOIN partenaire p ON p.ref_utilisateur = u.id_utilisateur';

    public function __construct()
    {
        $this->pdo = Bdd::getConnexion();
    }

    private function hydrate(array $row): Partenaire
    {
        return new Partenaire(
            (int) $row['id_utilisateur'], $row['nom'], $row['prenom'], $row['email'], $row['mot_de_passe'],
            $row['poste'], (int) $row['ref_entreprise'], (int) $row['id_partenaire'],
            (bool) $row['est_valide'], $row['date_creation']
        );
    }

    public function findAll(): array
    {
        return array_map([$this, 'hydrate'], $this->pdo->query(self::SELECT)->fetchAll());
    }

    public function findById(int $idUtilisateur): ?Partenaire
    {
        $stmt = $this->pdo->prepare(self::SELECT . ' WHERE u.id_utilisateur = :id');
        $stmt->execute(['id' => $idUtilisateur]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function insert(Partenaire $p): int
    {
        $this->pdo->beginTransaction();
        try {
            $idUtilisateur = (new UtilisateurRepository())->insert($p);
            $stmt = $this->pdo->prepare(
                'INSERT INTO partenaire (poste, ref_utilisateur, ref_entreprise)
                 VALUES (:poste, :utilisateur, :entreprise)'
            );
            $stmt->execute([
                'poste'       => $p->getPoste(),
                'utilisateur' => $idUtilisateur,
                'entreprise'  => $p->getRefEntreprise(),
            ]);
            $this->pdo->commit();
            return $idUtilisateur;
        } catch (Throwable $ex) {
            $this->pdo->rollBack();
            throw $ex;
        }
    }

    public function update(Partenaire $p): void
    {
        $this->pdo->beginTransaction();
        try {
            (new UtilisateurRepository())->update($p);
            $stmt = $this->pdo->prepare(
                'UPDATE partenaire SET poste = :poste, ref_entreprise = :entreprise
                 WHERE ref_utilisateur = :utilisateur'
            );
            $stmt->execute([
                'poste'       => $p->getPoste(),
                'entreprise'  => $p->getRefEntreprise(),
                'utilisateur' => $p->getIdUtilisateur(),
            ]);
            $this->pdo->commit();
        } catch (Throwable $ex) {
            $this->pdo->rollBack();
            throw $ex;
        }
    }

    public function delete(int $idUtilisateur): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare('DELETE FROM partenaire WHERE ref_utilisateur = :id')->execute(['id' => $idUtilisateur]);
            (new UtilisateurRepository())->delete($idUtilisateur);
            $this->pdo->commit();
        } catch (Throwable $ex) {
            $this->pdo->rollBack();
            throw $ex;
        }
    }
}