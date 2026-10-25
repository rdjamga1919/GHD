<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Etudiant.php';
require_once __DIR__ . '/UtilisateurRepository.php';

class EtudiantRepository
{
    private $connexionBdd;

    private const SELECT = 'SELECT u.*, e.id_etudiant, e.cv_etudiant, e.formation, e.ref_etablissement
                            FROM Utilisateur u
                            JOIN Etudiant e ON e.ref_utilisateur = u.id_utilisateur';

    public function __construct(?PDO $connexionBdd = null)
    {
        $this->connexionBdd = $connexionBdd ?? (new Bdd())->getConnexionBdd();
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
        return array_map([$this, 'hydrate'], $this->connexionBdd->query(self::SELECT)->fetchAll());
    }

    public function findById(int $idUtilisateur): ?Etudiant
    {
        $stmt = $this->connexionBdd->prepare(self::SELECT . ' WHERE u.id_utilisateur = :id');
        $stmt->execute(['id' => $idUtilisateur]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function insert(Etudiant $e): int
    {
        // Une transaction déjà ouverte reste sous la responsabilité de l'appelant.
        $transactionPropre = !$this->connexionBdd->inTransaction();
        if ($transactionPropre) {
            $this->connexionBdd->beginTransaction();
        }
        try {
            $idUtilisateur = (new UtilisateurRepository($this->connexionBdd))->insert($e);
            $stmt = $this->connexionBdd->prepare(
                'INSERT INTO Etudiant (cv_etudiant, formation, ref_utilisateur, ref_etablissement)
                 VALUES (:cv, :formation, :utilisateur, :etablissement)'
            );
            $stmt->execute([
                'cv'            => $e->getCvEtudiant(),
                'formation'     => $e->getFormation(),
                'utilisateur'   => $idUtilisateur,
                'etablissement' => $e->getRefEtablissement(),
            ]);
            if ($transactionPropre) {
                $this->connexionBdd->commit();
            }
            return $idUtilisateur;
        } catch (Throwable $ex) {
            if ($transactionPropre && $this->connexionBdd->inTransaction()) {
                $this->connexionBdd->rollBack();
            }
            throw $ex;
        }
    }

    public function update(Etudiant $e): void
    {
        // Une transaction déjà ouverte reste sous la responsabilité de l'appelant.
        $transactionPropre = !$this->connexionBdd->inTransaction();
        if ($transactionPropre) {
            $this->connexionBdd->beginTransaction();
        }
        try {
            (new UtilisateurRepository($this->connexionBdd))->update($e);
            $stmt = $this->connexionBdd->prepare(
                'UPDATE Etudiant
                 SET cv_etudiant = :cv, formation = :formation, ref_etablissement = :etablissement
                 WHERE ref_utilisateur = :utilisateur'
            );
            $stmt->execute([
                'cv'            => $e->getCvEtudiant(),
                'formation'     => $e->getFormation(),
                'etablissement' => $e->getRefEtablissement(),
                'utilisateur'   => $e->getIdUtilisateur(),
            ]);
            if ($transactionPropre) {
                $this->connexionBdd->commit();
            }
        } catch (Throwable $ex) {
            if ($transactionPropre && $this->connexionBdd->inTransaction()) {
                $this->connexionBdd->rollBack();
            }
            throw $ex;
        }
    }

    public function delete(int $idUtilisateur): void
    {
        // Une transaction déjà ouverte reste sous la responsabilité de l'appelant.
        $transactionPropre = !$this->connexionBdd->inTransaction();
        if ($transactionPropre) {
            $this->connexionBdd->beginTransaction();
        }
        try {
            $this->connexionBdd->prepare('DELETE FROM Etudiant WHERE ref_utilisateur = :id')->execute(['id' => $idUtilisateur]);
            (new UtilisateurRepository($this->connexionBdd))->delete($idUtilisateur);
            if ($transactionPropre) {
                $this->connexionBdd->commit();
            }
        } catch (Throwable $ex) {
            if ($transactionPropre && $this->connexionBdd->inTransaction()) {
                $this->connexionBdd->rollBack();
            }
            throw $ex;
        }
    }
}