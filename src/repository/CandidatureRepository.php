<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Candidature.php';

class CandidatureRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getNbCandidature(): int
    {
        $sql = "SELECT COUNT(*) FROM Candidature";
        $req = $this->connexionBdd->query($sql);
        return $req->fetchColumn();
    }

    public function getCandidature($idCandidature)
    {
        $sql = "SELECT * FROM Candidature WHERE id_candidature = :idCandidature";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idCandidature', $idCandidature, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();
        if (!$result) {
            return null;
        }
        return new Candidature($result["id_candidature"], $result["motivation"], $result["date_candidature"], $result["statut"], $result["ref_utilisateur"], $result["ref_offre"]);
    }

    public function getAllCandidature(): array
    {
        $sql = "SELECT * FROM Candidature";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabCandidature = [];
        foreach ($results as $result) {
            $tabCandidature[] = new Candidature($result["id_candidature"], $result["motivation"], $result["date_candidature"], $result["statut"], $result["ref_utilisateur"], $result["ref_offre"]);
        }
        return $tabCandidature;
    }

    public function ajouterCandidature(Candidature $candidature): void
    {
        $sql = "INSERT INTO Candidature (motivation, date_candidature, statut, ref_utilisateur, ref_offre)
                VALUES (:motivation, :date_candidature, :statut, :ref_utilisateur, :ref_offre)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':motivation', $candidature->getMotivation());
        $req->bindValue(':date_candidature', $candidature->getDateCandidature());
        $req->bindValue(':statut', $candidature->getStatut());
        $req->bindValue(':ref_utilisateur', $candidature->getRefUtilisateur());
        $req->bindValue(':ref_offre', $candidature->getRefOffre());
        $req->execute();
    }

    public function modifierCandidature(Candidature $candidature): void
    {
        $sql = "UPDATE Candidature
                SET motivation = :motivation, date_candidature = :date_candidature, statut = :statut, ref_utilisateur = :ref_utilisateur, ref_offre = :ref_offre
                WHERE id_candidature = :id_candidature";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_candidature', $candidature->getIdCandidature(), PDO::PARAM_INT);
        $req->bindValue(':motivation', $candidature->getMotivation());
        $req->bindValue(':date_candidature', $candidature->getDateCandidature());
        $req->bindValue(':statut', $candidature->getStatut());
        $req->bindValue(':ref_utilisateur', $candidature->getRefUtilisateur());
        $req->bindValue(':ref_offre', $candidature->getRefOffre());
        $req->execute();
    }

    public function supprimerCandidature($idCandidature): void
    {
        $sql = "DELETE FROM Candidature WHERE id_candidature = :idCandidature";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idCandidature', $idCandidature, PDO::PARAM_INT);
        $req->execute();
    }
    public function getCandidaturesParOffre(int $idOffre, int $idUtilisateur): array
    {
        $sql = "SELECT
                c.id_candidature,
                c.date_candidature,
                c.motivation,
                c.statut,
                u.nom,
                u.prenom,
                u.email,
                o.titre AS titre_offre
            FROM Candidature c
            JOIN Offre o ON o.id_offre = c.ref_offre
            JOIN Utilisateur u ON u.id_utilisateur = c.ref_utilisateur
            WHERE c.ref_offre = :id_offre
              AND o.ref_utilisateur = :id_utilisateur
            ORDER BY c.date_candidature DESC";

        $req = $this->connexionBdd->prepare($sql);

        $req->execute([
            'id_offre' => $idOffre,
            'id_utilisateur' => $idUtilisateur
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Récupère toutes les candidatures d'un étudiant
     * avec les informations de l'offre et de l'entreprise.
     */
    public function findByEtudiant(int $idUtilisateur): array
    {
        $sql = "SELECT
                c.id_candidature,
                c.date_candidature,
                c.statut,
                o.id_offre,
                o.titre AS titre_offre,
                e.id_entreprise,
                e.nom_entreprise
            FROM Candidature c
            JOIN Offre o ON o.id_offre = c.ref_offre
            LEFT JOIN Partenaire p
                ON p.ref_utilisateur = o.ref_utilisateur
            LEFT JOIN Entreprise e
                ON e.id_entreprise = p.ref_entreprise
            WHERE c.ref_utilisateur = :idUtilisateur
            ORDER BY c.date_candidature DESC,
                     c.id_candidature DESC";

        $req = $this->connexionBdd->prepare($sql);

        $req->bindValue(
            ':idUtilisateur',
            $idUtilisateur,
            PDO::PARAM_INT
        );

        $req->execute();

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}
