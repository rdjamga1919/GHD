<?php

require_once __DIR__ . '/../bdd/Bdd.new.php';
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
        $this->pdo = (new Bdd())->getConnexionBdd();
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

    public function getSpecialites(int $idMedecin): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT s.*
             FROM specialite s
             JOIN posseder p ON p.ref_specialite = s.id_specialite
             WHERE p.ref_medecin = :id'
        );
        $stmt->execute(['id' => $idMedecin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHopitaux(int $idMedecin): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT h.*
             FROM hopital h
             JOIN exercer e ON e.ref_hopital = h.id_hopital
             WHERE e.ref_medecin = :id'
        );
        $stmt->execute(['id' => $idMedecin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getToutesSpecialites(): array
    {
        return $this->pdo
            ->query('SELECT id_specialite, libelle_specialite FROM specialite ORDER BY libelle_specialite')
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTousHopitaux(): array
    {
        return $this->pdo
            ->query('SELECT id_hopital, nom_hopital, commune FROM hopital ORDER BY nom_hopital')
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouterSpecialite(int $idMedecin, int $idSpecialite): bool
    {
        $check = $this->pdo->prepare('SELECT 1 FROM specialite WHERE id_specialite = :id');
        $check->execute(['id' => $idSpecialite]);
        if (!$check->fetchColumn()) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'INSERT IGNORE INTO posseder (ref_medecin, ref_specialite) VALUES (:medecin, :specialite)'
        );
        $stmt->execute(['medecin' => $idMedecin, 'specialite' => $idSpecialite]);
        return $stmt->rowCount() === 1;
    }
    public function retirerSpecialite(int $idMedecin, int $idSpecialite): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM posseder WHERE ref_medecin = :medecin AND ref_specialite = :specialite'
        );
        $stmt->execute(['medecin' => $idMedecin, 'specialite' => $idSpecialite]);
        return $stmt->rowCount() === 1;
    }

    public function ajouterHopital(int $idMedecin, int $idHopital): bool
    {
        $check = $this->pdo->prepare('SELECT 1 FROM hopital WHERE id_hopital = :id');
        $check->execute(['id' => $idHopital]);
        if (!$check->fetchColumn()) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'INSERT IGNORE INTO exercer (ref_medecin, ref_hopital) VALUES (:medecin, :hopital)'
        );
        $stmt->execute(['medecin' => $idMedecin, 'hopital' => $idHopital]);
        return $stmt->rowCount() === 1;
    }

    public function retirerHopital(int $idMedecin, int $idHopital): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM exercer WHERE ref_medecin = :medecin AND ref_hopital = :hopital'
        );
        $stmt->execute(['medecin' => $idMedecin, 'hopital' => $idHopital]);
        return $stmt->rowCount() === 1;
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