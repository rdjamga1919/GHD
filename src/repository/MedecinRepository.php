<?php


require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Medecin.php';
require_once __DIR__ . '/UtilisateurRepository.php';

class MedecinRepository
{
    private PDO $pdo;

    private const SELECT = 'SELECT u.*, m.id_medecin, m.numero_rpps
                            FROM utilisateur u
                            JOIN medecin m ON m.ref_utilisateur = u.id_utilisateur';

    public function __construct()
    {
        $this->pdo = Bdd::getConnexion();
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
        return array_map([$this, 'hydrate'], $this->pdo->query(self::SELECT)->fetchAll());
    }

    public function findById(int $idUtilisateur): ?Medecin
    {
        $stmt = $this->pdo->prepare(self::SELECT . ' WHERE u.id_utilisateur = :id');
        $stmt->execute(['id' => $idUtilisateur]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function insert(Medecin $m): int
    {
        $this->pdo->beginTransaction();
        try {
            $idUtilisateur = (new UtilisateurRepository())->insert($m);
            $stmt = $this->pdo->prepare(
                'INSERT INTO medecin (numero_rpps, ref_utilisateur) VALUES (:rpps, :utilisateur)'
            );
            $stmt->execute(['rpps' => $m->getNumeroRpps(), 'utilisateur' => $idUtilisateur]);
            $this->pdo->commit();
            return $idUtilisateur;
        } catch (Throwable $ex) {
            $this->pdo->rollBack();
            throw $ex;
        }
    }

    public function update(Medecin $m): void
    {
        $this->pdo->beginTransaction();
        try {
            (new UtilisateurRepository())->update($m);
            $stmt = $this->pdo->prepare(
                'UPDATE medecin SET numero_rpps = :rpps WHERE ref_utilisateur = :utilisateur'
            );
            $stmt->execute(['rpps' => $m->getNumeroRpps(), 'utilisateur' => $m->getIdUtilisateur()]);
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
            $this->pdo->prepare('DELETE FROM medecin WHERE ref_utilisateur = :id')->execute(['id' => $idUtilisateur]);
            (new UtilisateurRepository())->delete($idUtilisateur);
            $this->pdo->commit();
        } catch (Throwable $ex) {
            $this->pdo->rollBack();
            throw $ex;
        }
    }
}