<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Etudiant.php';
require_once __DIR__ . '/UtilisateurRepository.php';

class EtudiantRepository
{
    private PDO $pdo;

    private const SELECT = 'SELECT u.*, e.id_etudiant, e.cv_etudiant, e.formation, e.ref_etablissement
                            FROM utilisateur u
                            JOIN etudiant e ON e.ref_utilisateur = u.id_utilisateur';

    public function __construct()
    {
        $this->pdo = Bdd::getConnexion();
    }

    private function hydrate(array $row): Etudiant
    {
        return new Etudiant(
            (int) $row['id_utilisateur'], $row['nom'], $row['prenom'], $row['email'], $row['mot_de_passe'],
            $row['formation'], (int) $row['ref_etablissement'],
            $row['cv_etudiant'], (int) $row['id_etudiant'],
            (bool) $row['est_valide'], $row['date_creation']
        );
    }

    public function findAll(): array
    {
        return array_map([$this, 'hydrate'], $this->pdo->query(self::SELECT)->fetchAll());
    }

    public function findById(int $idUtilisateur): ?Etudiant
    {
        $stmt = $this->pdo->prepare(self::SELECT . ' WHERE u.id_utilisateur = :id');
        $stmt->execute(['id' => $idUtilisateur]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function insert(Etudiant $e): int
    {
        $this->pdo->beginTransaction();
        try {
            $idUtilisateur = (new UtilisateurRepository())->insert($e);
            $stmt = $this->pdo->prepare(
                'INSERT INTO etudiant (cv_etudiant, formation, ref_utilisateur, ref_etablissement)
                 VALUES (:cv, :formation, :utilisateur, :etablissement)'
            );
            $stmt->execute([
                'cv'            => $e->getCvEtudiant(),
                'formation'     => $e->getFormation(),
                'utilisateur'   => $idUtilisateur,
                'etablissement' => $e->getRefEtablissement(),
            ]);
            $this->pdo->commit();
            return $idUtilisateur;
        } catch (Throwable $ex) {
            $this->pdo->rollBack();
            throw $ex;
        }
    }

    public function update(Etudiant $e): void
    {
        $this->pdo->beginTransaction();
        try {
            (new UtilisateurRepository())->update($e);
            $stmt = $this->pdo->prepare(
                'UPDATE etudiant
                 SET cv_etudiant = :cv, formation = :formation, ref_etablissement = :etablissement
                 WHERE ref_utilisateur = :utilisateur'
            );
            $stmt->execute([
                'cv'            => $e->getCvEtudiant(),
                'formation'     => $e->getFormation(),
                'etablissement' => $e->getRefEtablissement(),
                'utilisateur'   => $e->getIdUtilisateur(),
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
            $this->pdo->prepare('DELETE FROM etudiant WHERE ref_utilisateur = :id')->execute(['id' => $idUtilisateur]);
            (new UtilisateurRepository())->delete($idUtilisateur);
            $this->pdo->commit();
        } catch (Throwable $ex) {
            $this->pdo->rollBack();
            throw $ex;
        }
    }
}