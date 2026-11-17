<?php

require_once __DIR__ . '/../bdd/Bdd.php';

/**
 * Statistiques du tableau de bord administratif.
 * Uniquement des requêtes de comptage : aucune dépendance aux pages CRUD des autres modules.
 */
class AdminDashboardRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    /**
     * Retourne les indicateurs sous forme d'entiers (0 si aucune donnée).
     *
     * @return array<string,int>
     * @throws PDOException si une table ou une colonne est inaccessible
     */
    public function getStatistiques(): array
    {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM utilisateur)                            AS utilisateurs,
                    (SELECT COUNT(*) FROM utilisateur WHERE role = 'etudiant')     AS etudiants,
                    (SELECT COUNT(*) FROM utilisateur WHERE role = 'medecin')      AS medecins,
                    (SELECT COUNT(*) FROM utilisateur WHERE role = 'partenaire')   AS partenaires,
                    (SELECT COUNT(*) FROM utilisateur WHERE role = 'gestionnaire') AS gestionnaires,
                    (SELECT COUNT(*) FROM utilisateur WHERE est_valide = 0)        AS a_valider,
                    (SELECT COUNT(*) FROM evenement)                               AS evenements,
                    (SELECT COUNT(*) FROM post_forum)                              AS publications,
                    (SELECT COUNT(*) FROM hopital)                                 AS hopitaux,
                    (SELECT COUNT(*) FROM specialite)                              AS specialites";

        $ligne = $this->connexionBdd->query($sql)->fetch(PDO::FETCH_ASSOC) ?: [];

        $statistiques = [];
        foreach (['utilisateurs', 'etudiants', 'medecins', 'partenaires', 'gestionnaires',
                  'a_valider', 'evenements', 'publications', 'hopitaux', 'specialites'] as $cle) {
            $statistiques[$cle] = (int) ($ligne[$cle] ?? 0);
        }

        return $statistiques;
    }
}
