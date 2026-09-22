<?php
class DemandeContactRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getNbDemandeContact(): int
    {
        $sql = "SELECT COUNT(*) FROM Demande_contact";
        $req = $this->connexionBdd->query($sql);
        return $req->fetchColumn();
    }

    public function getDemandeContact($idDemande)
    {
        $sql = "SELECT * FROM Demande_contact WHERE id_demande = :idDemande";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idDemande', $idDemande, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();
        if (!$result) {
            return null;
        }
        return new DemandeContact($result["id_demande"], $result["nom_expediteur"], $result["email_expediteur"], $result["sujet"], $result["message"], $result["date_d_envoi"], $result["ref_utilisateur"]);
    }

    public function getAllDemandeContact(): array
    {
        $sql = "SELECT * FROM Demande_contact";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabDemandeContact = [];
        foreach ($results as $result) {
            $tabDemandeContact[] = new DemandeContact($result["id_demande"], $result["nom_expediteur"], $result["email_expediteur"], $result["sujet"], $result["message"], $result["date_d_envoi"], $result["ref_utilisateur"]);
        }
        return $tabDemandeContact;
    }

    public function ajouterDemandeContact(DemandeContact $demandeContact): void
    {
        $sql = "INSERT INTO Demande_contact (nom_expediteur, email_expediteur, sujet, message, date_d_envoi, ref_utilisateur)
                VALUES (:nom_expediteur, :email_expediteur, :sujet, :message, :date_d_envoi, :ref_utilisateur)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom_expediteur', $demandeContact->getNomExpediteur());
        $req->bindValue(':email_expediteur', $demandeContact->getEmailExpediteur());
        $req->bindValue(':sujet', $demandeContact->getSujet());
        $req->bindValue(':message', $demandeContact->getMessage());
        $req->bindValue(':date_d_envoi', $demandeContact->getDateDEnvoi());
        $req->bindValue(':ref_utilisateur', $demandeContact->getRefUtilisateur());
        $req->execute();
    }

    public function modifierDemandeContact(DemandeContact $demandeContact): void
    {
        $sql = "UPDATE Demande_contact
                SET nom_expediteur = :nom_expediteur, email_expediteur = :email_expediteur, sujet = :sujet, message = :message, date_d_envoi = :date_d_envoi, ref_utilisateur = :ref_utilisateur
                WHERE id_demande = :id_demande";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_demande', $demandeContact->getIdDemande(), PDO::PARAM_INT);
        $req->bindValue(':nom_expediteur', $demandeContact->getNomExpediteur());
        $req->bindValue(':email_expediteur', $demandeContact->getEmailExpediteur());
        $req->bindValue(':sujet', $demandeContact->getSujet());
        $req->bindValue(':message', $demandeContact->getMessage());
        $req->bindValue(':date_d_envoi', $demandeContact->getDateDEnvoi());
        $req->bindValue(':ref_utilisateur', $demandeContact->getRefUtilisateur());
        $req->execute();
    }

    public function supprimerDemandeContact($idDemande): void
    {
        $sql = "DELETE FROM Demande_contact WHERE id_demande = :idDemande";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idDemande', $idDemande, PDO::PARAM_INT);
        $req->execute();
    }
}
