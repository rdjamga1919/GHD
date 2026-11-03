<?php

require_once __DIR__ . '/../bdd/Bdd.php';

class InscriptionEvenementRepository
{
    public const ROLES_AUTORISES = ['etudiant', 'medecin', 'partenaire'];

    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getEtat($idEvenement): ?array
    {
        $sql = "SELECT e.nombre_place,
                       (e.date_heure_evenement <= NOW()) AS passe,
                       (SELECT COUNT(*) FROM S_inscrire s WHERE s.ref_evenement = e.id_evenement) AS nb_inscrits
                FROM Evenement e
                WHERE e.id_evenement = :idEvenement";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        $nombrePlace = (int) $result['nombre_place'];
        $nbInscrits = (int) $result['nb_inscrits'];
        return [
            'nombre_place' => $nombrePlace,
            'nb_inscrits' => $nbInscrits,
            'places_restantes' => max(0, $nombrePlace - $nbInscrits),
            'passe' => (bool) $result['passe'],
        ];
    }

    public function estInscrit($idEvenement, $idUtilisateur): bool
    {
        $sql = "SELECT COUNT(*) FROM S_inscrire
                WHERE ref_evenement = :idEvenement AND ref_utilisateur = :idUtilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $req->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $req->execute();
        return (int) $req->fetchColumn() > 0;
    }

    public function inscrire($idEvenement, $idUtilisateur): string
    {
        $this->connexionBdd->beginTransaction();

        try {
            $sql = "SELECT nombre_place, (date_heure_evenement <= NOW()) AS passe
                    FROM Evenement
                    WHERE id_evenement = :idEvenement
                    FOR UPDATE";
            $req = $this->connexionBdd->prepare($sql);
            $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
            $req->execute();
            $evenement = $req->fetch(PDO::FETCH_ASSOC);

            if (!$evenement) {
                $this->connexionBdd->rollBack();
                return 'introuvable';
            }

            if ((bool) $evenement['passe']) {
                $this->connexionBdd->rollBack();
                return 'passe';
            }

            if ($this->estInscrit($idEvenement, $idUtilisateur)) {
                $this->connexionBdd->rollBack();
                return 'deja_inscrit';
            }

            $sql = "SELECT COUNT(*) FROM S_inscrire WHERE ref_evenement = :idEvenement";
            $req = $this->connexionBdd->prepare($sql);
            $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
            $req->execute();
            $nbInscrits = (int) $req->fetchColumn();

            if ($nbInscrits >= (int) $evenement['nombre_place']) {
                $this->connexionBdd->rollBack();
                return 'complet';
            }

            $sql = "INSERT INTO S_inscrire (ref_evenement, ref_utilisateur)
                    VALUES (:idEvenement, :idUtilisateur)";
            $req = $this->connexionBdd->prepare($sql);
            $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
            $req->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
            $req->execute();

            $this->connexionBdd->commit();
            return 'ok';
        } catch (PDOException $e) {
            if ($this->connexionBdd->inTransaction()) {
                $this->connexionBdd->rollBack();
            }
            if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
                return 'deja_inscrit';
            }
            throw $e;
        }
    }

    public function desinscrire($idEvenement, $idUtilisateur): bool
    {
        $sql = "DELETE FROM S_inscrire
                WHERE ref_evenement = :idEvenement AND ref_utilisateur = :idUtilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $req->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $req->execute();
        return $req->rowCount() > 0;
    }
}