<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Evenement.php';

class EvenementRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getNbEvenement(): int
    {
        $sql = "SELECT COUNT(*) FROM Evenement";
        $req = $this->connexionBdd->query($sql);
        return (int) $req->fetchColumn();
    }

    /**
     * Récupère tous les événements (du plus proche au plus lointain).
     *
     * @return Evenement[]
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM Evenement ORDER BY date_heure_evenement ASC";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();

        $tabEvenement = [];
        foreach ($req->fetchAll(PDO::FETCH_ASSOC) as $result) {
            $tabEvenement[] = $this->hydrate($result);
        }
        return $tabEvenement;
    }

    /**
     * Récupère un événement par son id, ou null s'il n'existe pas.
     */
    public function findById(int $idEvenement): ?Evenement
    {
        $sql = "SELECT * FROM Evenement WHERE id_evenement = :idEvenement";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $req->execute();

        $result = $req->fetch(PDO::FETCH_ASSOC);
        return $result ? $this->hydrate($result) : null;
    }

    /**
     * Crée un événement et retourne l'id généré.
     */
    public function insert(Evenement $evenement): int
    {
        $sql = "INSERT INTO Evenement (titre, description, type_evenement, adresse_lieu, element_requis, nombre_place, date_heure_evenement)
                VALUES (:titre, :description, :type_evenement, :adresse_lieu, :element_requis, :nombre_place, :date_heure_evenement)";
        $req = $this->connexionBdd->prepare($sql);
        $this->bindEvenement($req, $evenement);
        $req->execute();

        return (int) $this->connexionBdd->lastInsertId();
    }

    /**
     * Modifie un événement existant.
     */
    public function update(Evenement $evenement): bool
    {
        $sql = "UPDATE Evenement
                SET titre = :titre, description = :description, type_evenement = :type_evenement, adresse_lieu = :adresse_lieu, element_requis = :element_requis, nombre_place = :nombre_place, date_heure_evenement = :date_heure_evenement
                WHERE id_evenement = :id_evenement";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_evenement', $evenement->getIdEvenement(), PDO::PARAM_INT);
        $this->bindEvenement($req, $evenement);

        return $req->execute();
    }

    /**
     * Supprime un événement. Retourne true si une ligne a été supprimée.
     * (Organiser et S_inscrire sont nettoyées par ON DELETE CASCADE.)
     */
    public function delete(int $idEvenement): bool
    {
        $sql = "DELETE FROM Evenement WHERE id_evenement = :idEvenement";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $req->execute();

        return $req->rowCount() > 0;
    }

    // ------------------------------------------------------------------
    // Inscriptions, organisateurs et opérations transactionnelles
    // ------------------------------------------------------------------

    /**
     * Nombre de personnes inscrites à un événement (table S_inscrire).
     */
    public function compterInscrits(int $idEvenement): int
    {
        $sql = "SELECT COUNT(*) FROM S_inscrire WHERE ref_evenement = :idEvenement";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $req->execute();

        return (int) $req->fetchColumn();
    }

    /**
     * Indique si l'utilisateur est organisateur de l'événement (table Organiser).
     */
    public function estOrganisateur(int $idEvenement, int $idUtilisateur): bool
    {
        $sql = "SELECT COUNT(*) FROM Organiser
                WHERE ref_evenement = :idEvenement AND ref_utilisateur = :idUtilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $req->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $req->execute();

        return (int) $req->fetchColumn() > 0;
    }

    /**
     * Crée l'événement ET l'enregistre comme organisé par l'utilisateur,
     * dans une seule transaction (tout ou rien).
     *
     * @return int l'id du nouvel événement
     */
    public function insertAvecOrganisateur(Evenement $evenement, int $idUtilisateur): int
    {
        $this->connexionBdd->beginTransaction();
        try {
            $idEvenement = $this->insert($evenement);

            $sql = "INSERT INTO Organiser (ref_evenement, ref_utilisateur)
                    VALUES (:idEvenement, :idUtilisateur)";
            $req = $this->connexionBdd->prepare($sql);
            $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
            $req->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
            $req->execute();

            $this->connexionBdd->commit();
            return $idEvenement;
        } catch (Throwable $t) {
            if ($this->connexionBdd->inTransaction()) {
                $this->connexionBdd->rollBack();
            }
            throw $t;
        }
    }

    /**
     * Modifie l'événement en vérifiant, dans la même transaction, que la
     * nouvelle capacité n'est pas inférieure au nombre d'inscrits.
     * La ligne de l'événement est verrouillée pendant la vérification.
     *
     * @throws DomainException si la capacité est inférieure au nombre d'inscrits
     * @throws RuntimeException si l'événement n'existe plus
     */
    public function updateAvecControleCapacite(Evenement $evenement): void
    {
        $idEvenement = (int) $evenement->getIdEvenement();

        $this->connexionBdd->beginTransaction();
        try {
            $req = $this->connexionBdd->prepare(
                "SELECT id_evenement FROM Evenement WHERE id_evenement = :idEvenement FOR UPDATE"
            );
            $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
            $req->execute();
            if ($req->fetchColumn() === false) {
                throw new RuntimeException("Événement introuvable.");
            }

            $nbInscrits = $this->compterInscrits($idEvenement);
            if ($evenement->getNombrePlace() < $nbInscrits) {
                throw new DomainException(
                    "Impossible de passer sous le nombre de personnes déjà inscrites ($nbInscrits)."
                );
            }

            $this->update($evenement);
            $this->connexionBdd->commit();
        } catch (Throwable $t) {
            if ($this->connexionBdd->inTransaction()) {
                $this->connexionBdd->rollBack();
            }
            throw $t;
        }
    }

    // ------------------------------------------------------------------
    // Anciens noms conservés pour ne pas casser les contrôleurs existants
    // ------------------------------------------------------------------

    public function getEvenement($idEvenement)
    {
        return $this->findById((int) $idEvenement);
    }

    public function getAllEvenement(): array
    {
        return $this->findAll();
    }

    public function ajouterEvenement(Evenement $evenement): void
    {
        $this->insert($evenement);
    }

    public function modifierEvenement(Evenement $evenement): void
    {
        $this->update($evenement);
    }

    public function supprimerEvenement($idEvenement): void
    {
        $this->delete((int) $idEvenement);
    }

    // ------------------------------------------------------------------
    // Méthodes privées
    // ------------------------------------------------------------------

    /**
     * Transforme une ligne SQL en objet Evenement
     * (même ordre de paramètres que ton constructeur actuel).
     */
    private function hydrate(array $result): Evenement
    {
        return new Evenement(
            $result["id_evenement"],
            $result["date_heure_evenement"],
            $result["element_requis"],
            $result["nombre_place"],
            $result["type_evenement"],
            $result["adresse_lieu"],
            $result["description"],
            $result["titre"]
        );
    }

    /**
     * Lie les paramètres communs à insert() et update().
     */
    private function bindEvenement(PDOStatement $req, Evenement $evenement): void
    {
        $elementRequis = $evenement->getElementRequis();

        $req->bindValue(':titre', $evenement->getTitre());
        $req->bindValue(':description', $evenement->getDescription());
        $req->bindValue(':type_evenement', $evenement->getTypeEvenement());
        $req->bindValue(':adresse_lieu', $evenement->getAdresseLieu());
        $req->bindValue(
            ':element_requis',
            $elementRequis,
            $elementRequis === null ? PDO::PARAM_NULL : PDO::PARAM_STR
        );
        $req->bindValue(':nombre_place', $evenement->getNombrePlace(), PDO::PARAM_INT);
        $req->bindValue(':date_heure_evenement', $evenement->getDateHeureEvenement());
    }

    public function getEvenementsAvecDetails(): array
    {
        $sql = "SELECT e.id_evenement, e.titre, e.description, e.type_evenement,
                       e.adresse_lieu, e.nombre_place, e.date_heure_evenement,
                       (SELECT COUNT(*) FROM s_inscrire si
                         WHERE si.ref_evenement = e.id_evenement) AS nb_inscrits,
                       GROUP_CONCAT(DISTINCT CONCAT(u.prenom, ' ', u.nom) SEPARATOR ', ') AS organisateurs
                FROM evenement e
                LEFT JOIN organiser o ON o.ref_evenement = e.id_evenement
                LEFT JOIN utilisateur u ON u.id_utilisateur = o.ref_utilisateur
                GROUP BY e.id_evenement
                ORDER BY e.date_heure_evenement ASC";
        return $this->connexionBdd->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Détail d'un événement (null si inexistant) avec le nombre d'inscrits.
     */
    public function getEvenementDetail(int $idEvenement): ?array
    {
        $sql = "SELECT e.*,
                       (SELECT COUNT(*) FROM s_inscrire si
                         WHERE si.ref_evenement = e.id_evenement) AS nb_inscrits
                FROM evenement e
                WHERE e.id_evenement = :idEvenement";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Organisateurs d'un événement (tableau vide si aucun).
     */
    public function getOrganisateurs(int $idEvenement): array
    {
        $sql = "SELECT u.id_utilisateur, u.nom, u.prenom, u.role
                FROM organiser o
                INNER JOIN utilisateur u ON u.id_utilisateur = o.ref_utilisateur
                WHERE o.ref_evenement = :idEvenement
                ORDER BY u.nom, u.prenom";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idEvenement', $idEvenement, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

}