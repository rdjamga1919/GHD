<?php
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
}
