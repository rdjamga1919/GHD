<?php

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Specialite.php';

/**
 * Exception destinée à être affichée telle quelle à l'utilisateur
 * (aucun détail technique SQL dedans).
 */
class SpecialiteException extends RuntimeException
{
}

class SpecialiteRepository
{
    /*
     * Schéma gdh_hsp : un médecin a ses spécialités dans la table de jonction
     * Posseder(ref_medecin, ref_specialite), avec ON DELETE CASCADE sur ref_specialite.
     * Sans contrôle préalable, supprimer une spécialité effacerait donc en silence
     * les associations des médecins : le contrôle ci-dessous est indispensable.
     *
     * Ces constantes ne servent de repli que si INFORMATION_SCHEMA ne renvoie rien.
     */
    private const REPLI_TABLE_MEDECIN = 'Posseder';
    private const REPLI_COLONNE_MEDECIN = 'ref_specialite';

    // VARCHAR(100) dans le SQL
    private const LONGUEUR_MAX_LIBELLE = 100;

    private $connexionBdd;
    private $relationsCache = null;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    /**
     * @return Specialite[]
     */
    public function findAll()
    {
        try {
            $stmt = $this->connexionBdd->query(
                "SELECT * FROM Specialite ORDER BY libelle_specialite"
            );

            $specialites = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $specialites[] = new Specialite(
                    $row['id_specialite'],
                    $row['libelle_specialite']
                );
            }
            return $specialites;
        } catch (PDOException $e) {
            $this->journaliser($e);
            throw new SpecialiteException("Impossible de charger les spécialités.");
        }
    }

    /**
     * @return Specialite|null
     */
    public function findById($id_specialite)
    {
        try {
            $stmt = $this->connexionBdd->prepare(
                "SELECT * FROM Specialite WHERE id_specialite = :id_specialite"
            );
            $stmt->execute(['id_specialite' => $id_specialite]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row
                ? new Specialite($row['id_specialite'], $row['libelle_specialite'])
                : null;
        } catch (PDOException $e) {
            $this->journaliser($e);
            throw new SpecialiteException("Impossible de charger la spécialité.");
        }
    }

    /**
     * Vérifie si un libellé existe déjà (la sensibilité à la casse dépend de la collation).
     *
     * @param string   $libelle
     * @param int|null $idAExclure ID à ignorer (cas de la modification)
     */
    public function existeParLibelle($libelle, $idAExclure = null)
    {
        try {
            $sql = "SELECT COUNT(*) FROM Specialite WHERE libelle_specialite = :libelle";
            $params = ['libelle' => $libelle];

            if ($idAExclure !== null) {
                $sql .= " AND id_specialite <> :id";
                $params['id'] = $idAExclure;
            }

            $stmt = $this->connexionBdd->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            $this->journaliser($e);
            throw new SpecialiteException("Impossible de vérifier l'unicité de la spécialité.");
        }
    }

    /**
     * Relations réelles : toutes les clés étrangères de la base qui pointent
     * vers la table Specialite (lues dans INFORMATION_SCHEMA, donc fidèles au SQL réel).
     *
     * @return array<int, array{table: string, colonne: string}>
     */
    public function trouverRelations()
    {
        if ($this->relationsCache !== null) {
            return $this->relationsCache;
        }

        try {
            $stmt = $this->connexionBdd->query(
                "SELECT TABLE_NAME AS t, COLUMN_NAME AS c
                 FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND LOWER(REFERENCED_TABLE_NAME) = 'specialite'"
            );

            $relations = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $relations[] = ['table' => $row['t'], 'colonne' => $row['c']];
            }
        } catch (PDOException $e) {
            $this->journaliser($e);
            $relations = [];
        }

        if (empty($relations)) {
            $relations[] = [
                'table' => self::REPLI_TABLE_MEDECIN,
                'colonne' => self::REPLI_COLONNE_MEDECIN
            ];
        }

        $this->relationsCache = $relations;
        return $relations;
    }

    /**
     * Nombre total de rattachements (médecins, ou toute autre table liée par clé étrangère).
     */
    public function compterUtilisations($id_specialite)
    {
        $total = 0;

        foreach ($this->trouverRelations() as $relation) {
            // Identifiants issus d'INFORMATION_SCHEMA (pas de saisie utilisateur), protégés par des backticks.
            $table = str_replace('`', '', $relation['table']);
            $colonne = str_replace('`', '', $relation['colonne']);

            try {
                $stmt = $this->connexionBdd->prepare(
                    "SELECT COUNT(*) FROM `$table` WHERE `$colonne` = :id_specialite"
                );
                $stmt->execute(['id_specialite' => $id_specialite]);
                $total += (int) $stmt->fetchColumn();
            } catch (PDOException $e) {
                $this->journaliser($e);
                throw new SpecialiteException("Impossible de vérifier les médecins rattachés.");
            }
        }

        return $total;
    }

    /** Alias lisible : le ticket parle de médecins. */
    public function compterMedecins($id_specialite)
    {
        return $this->compterUtilisations($id_specialite);
    }

    public function estUtilisee($id_specialite)
    {
        return $this->compterUtilisations($id_specialite) > 0;
    }

    /**
     * @throws SpecialiteException si le libellé est vide ou déjà utilisé
     */
    public function create(Specialite $specialite)
    {
        $libelle = trim((string) $specialite->getLibelle());
        if ($libelle === '') {
            throw new SpecialiteException("Le nom de la spécialité est obligatoire.");
        }
        if (mb_strlen($libelle, 'UTF-8') > self::LONGUEUR_MAX_LIBELLE) {
            throw new SpecialiteException("Le nom de la spécialité ne doit pas dépasser 100 caractères.");
        }
        if ($this->existeParLibelle($libelle)) {
            throw new SpecialiteException("Cette spécialité existe déjà.");
        }

        try {
            $stmt = $this->connexionBdd->prepare(
                "INSERT INTO Specialite (libelle_specialite) VALUES (:libelle_specialite)"
            );
            $stmt->execute(['libelle_specialite' => $libelle]);
            $specialite->setIdSpecialite($this->connexionBdd->lastInsertId());
        } catch (PDOException $e) {
            $this->journaliser($e);
            // Course possible avec une contrainte UNIQUE (code SQLSTATE 23000)
            if ($e->getCode() === '23000') {
                throw new SpecialiteException("Cette spécialité existe déjà.");
            }
            throw new SpecialiteException("La création de la spécialité a échoué.");
        }
    }

    /**
     * @throws SpecialiteException
     */
    public function update(Specialite $specialite)
    {
        $libelle = trim((string) $specialite->getLibelle());
        if ($libelle === '') {
            throw new SpecialiteException("Le nom de la spécialité est obligatoire.");
        }
        if (mb_strlen($libelle, 'UTF-8') > self::LONGUEUR_MAX_LIBELLE) {
            throw new SpecialiteException("Le nom de la spécialité ne doit pas dépasser 100 caractères.");
        }
        if ($this->findById($specialite->getIdSpecialite()) === null) {
            throw new SpecialiteException("Cette spécialité n'existe pas.");
        }
        if ($this->existeParLibelle($libelle, $specialite->getIdSpecialite())) {
            throw new SpecialiteException("Une autre spécialité porte déjà ce nom.");
        }

        try {
            $stmt = $this->connexionBdd->prepare(
                "UPDATE Specialite
                 SET libelle_specialite = :libelle_specialite
                 WHERE id_specialite = :id_specialite"
            );
            $stmt->execute([
                'id_specialite' => $specialite->getIdSpecialite(),
                'libelle_specialite' => $libelle
            ]);
        } catch (PDOException $e) {
            $this->journaliser($e);
            if ($e->getCode() === '23000') {
                throw new SpecialiteException("Une autre spécialité porte déjà ce nom.");
            }
            throw new SpecialiteException("La modification de la spécialité a échoué.");
        }
    }

    /**
     * Supprime la spécialité seulement si rien n'y est rattaché.
     * Aucune cascade, aucune modification de clé étrangère.
     *
     * @throws SpecialiteException
     */
    public function delete($id_specialite)
    {
        if ($this->findById($id_specialite) === null) {
            throw new SpecialiteException("Cette spécialité n'existe pas.");
        }

        $nb = $this->compterUtilisations($id_specialite);
        if ($nb > 0) {
            throw new SpecialiteException(
                "Suppression impossible : cette spécialité est rattachée à " . $nb
                . " enregistrement(s) existant(s) (médecins). Détachez-les d'abord."
            );
        }

        try {
            $stmt = $this->connexionBdd->prepare(
                "DELETE FROM Specialite WHERE id_specialite = :id_specialite"
            );
            $stmt->execute(['id_specialite' => $id_specialite]);
        } catch (PDOException $e) {
            // Filet de sécurité : une clé étrangère non détectée peut refuser la suppression.
            $this->journaliser($e);
            throw new SpecialiteException(
                "Suppression impossible : cette spécialité est encore utilisée ailleurs."
            );
        }
    }

    private function journaliser(PDOException $e)
    {
        error_log('[SpecialiteRepository] ' . $e->getMessage());
    }
}