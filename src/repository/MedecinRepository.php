<?php


require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Medecin.php';
require_once __DIR__ . '/UtilisateurRepository.php';

class MedecinRepository
{
    private $connexionBdd;

    private const SELECT = 'SELECT u.*, m.id_medecin, m.numero_rpps
                            FROM Utilisateur u
                            JOIN Medecin m ON m.ref_utilisateur = u.id_utilisateur';

    public function __construct(?PDO $connexionBdd = null)
    {
        $this->connexionBdd = $connexionBdd ?? (new Bdd())->getConnexionBdd();
    }

    private function hydrate(array $row): Medecin
    {
        return new Medecin(
            (int) $row['id_utilisateur'], $row['nom'], $row['prenom'], $row['email'], $row['mot_de_passe'],
            $row['numero_rpps'], (int) $row['id_medecin'],
            (bool) $row['est_valide'], $row['date_creation']
        );
    }

    public function findAll(): array
    {
        return array_map([$this, 'hydrate'], $this->connexionBdd->query(self::SELECT)->fetchAll());
    }

    public function findById(int $idUtilisateur): ?Medecin
    {
        $stmt = $this->connexionBdd->prepare(self::SELECT . ' WHERE u.id_utilisateur = :id');
        $stmt->execute(['id' => $idUtilisateur]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function insert(Medecin $m): int
    {
        // Une transaction déjà ouverte reste sous la responsabilité de l'appelant.
        $transactionPropre = !$this->connexionBdd->inTransaction();
        if ($transactionPropre) {
            $this->connexionBdd->beginTransaction();
        }
        try {
            $idUtilisateur = (new UtilisateurRepository($this->connexionBdd))->insert($m);
            $stmt = $this->connexionBdd->prepare(
                'INSERT INTO Medecin (numero_rpps, ref_utilisateur) VALUES (:rpps, :utilisateur)'
            );
            $stmt->execute(['rpps' => $m->getNumeroRpps(), 'utilisateur' => $idUtilisateur]);
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

    public function update(Medecin $m): void
    {
        // Une transaction déjà ouverte reste sous la responsabilité de l'appelant.
        $transactionPropre = !$this->connexionBdd->inTransaction();
        if ($transactionPropre) {
            $this->connexionBdd->beginTransaction();
        }
        try {
            (new UtilisateurRepository($this->connexionBdd))->update($m);
            $stmt = $this->connexionBdd->prepare(
                'UPDATE Medecin SET numero_rpps = :rpps WHERE ref_utilisateur = :utilisateur'
            );
            $stmt->execute(['rpps' => $m->getNumeroRpps(), 'utilisateur' => $m->getIdUtilisateur()]);
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
            $this->connexionBdd->prepare('DELETE FROM Medecin WHERE ref_utilisateur = :id')->execute(['id' => $idUtilisateur]);
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