<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Partenaire.php';
require_once __DIR__ . '/UtilisateurRepository.php';

class PartenaireRepository
{
    private $connexionBdd;

    private const SELECT = 'SELECT u.*, p.id_partenaire, p.poste, p.ref_entreprise
                            FROM Utilisateur u
                            JOIN Partenaire p ON p.ref_utilisateur = u.id_utilisateur';

    public function __construct(?PDO $connexionBdd = null)
    {
        $this->connexionBdd = $connexionBdd ?? (new Bdd())->getConnexionBdd();
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
        return array_map([$this, 'hydrate'], $this->connexionBdd->query(self::SELECT)->fetchAll());
    }

    public function findById(int $idUtilisateur): ?Partenaire
    {
        $stmt = $this->connexionBdd->prepare(self::SELECT . ' WHERE u.id_utilisateur = :id');
        $stmt->execute(['id' => $idUtilisateur]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    // Partenaires (comptes validés) rattachés à une entreprise : jointure Partenaire / Utilisateur
    public function getPartenairesParEntreprise(int $idEntreprise): array
    {
        $stmt = $this->connexionBdd->prepare(
            self::SELECT . ' WHERE p.ref_entreprise = :entreprise AND u.est_valide = 1
                             ORDER BY u.nom, u.prenom'
        );
        $stmt->bindValue(':entreprise', $idEntreprise, PDO::PARAM_INT);
        $stmt->execute();
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    // Rattache le partenaire identifié par $idUtilisateur (toujours l'id de la SESSION) à une entreprise
    public function modifierPartenaire(int $idUtilisateur, int $refEntreprise): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE partenaire SET ref_entreprise = :entreprise WHERE ref_utilisateur = :utilisateur'
        );
        $stmt->bindValue(':entreprise', $refEntreprise, PDO::PARAM_INT);
        $stmt->bindValue(':utilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function insert(Partenaire $p): int
    {
        // Une transaction déjà ouverte reste sous la responsabilité de l'appelant.
        $transactionPropre = !$this->connexionBdd->inTransaction();
        if ($transactionPropre) {
            $this->connexionBdd->beginTransaction();
        }
        try {
            $idUtilisateur = (new UtilisateurRepository($this->connexionBdd))->insert($p);
            $stmt = $this->connexionBdd->prepare(
                'INSERT INTO Partenaire (poste, ref_utilisateur, ref_entreprise)
                 VALUES (:poste, :utilisateur, :entreprise)'
            );
            $stmt->execute([
                'poste'       => $p->getPoste(),
                'utilisateur' => $idUtilisateur,
                'entreprise'  => $p->getRefEntreprise(),
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

    public function update(Partenaire $p): void
    {
        // Une transaction déjà ouverte reste sous la responsabilité de l'appelant.
        $transactionPropre = !$this->connexionBdd->inTransaction();
        if ($transactionPropre) {
            $this->connexionBdd->beginTransaction();
        }
        try {
            (new UtilisateurRepository($this->connexionBdd))->update($p);
            $stmt = $this->connexionBdd->prepare(
                'UPDATE Partenaire SET poste = :poste, ref_entreprise = :entreprise
                 WHERE ref_utilisateur = :utilisateur'
            );
            $stmt->execute([
                'poste'       => $p->getPoste(),
                'entreprise'  => $p->getRefEntreprise(),
                'utilisateur' => $p->getIdUtilisateur(),
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
            $this->connexionBdd->prepare('DELETE FROM Partenaire WHERE ref_utilisateur = :id')->execute(['id' => $idUtilisateur]);
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