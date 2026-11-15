-- ADMIN-02 : analyse de l'existant (point 1 du ticket)
-- A exécuter sur la base du projet (branche test). Notez les résultats dans la PR.

-- 1. Colonnes exactes de la table des spécialités (clé primaire, NOT NULL, type)
SHOW COLUMNS FROM Specialite;

-- 2. Définition complète (UNIQUE, clés, moteur, collation)
SHOW CREATE TABLE Specialite;

-- 3. Toutes les clés étrangères qui pointent vers Specialite, avec la règle ON DELETE
SELECT k.TABLE_NAME, k.COLUMN_NAME, k.CONSTRAINT_NAME,
       r.DELETE_RULE, r.UPDATE_RULE
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
         JOIN INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS r
              ON r.CONSTRAINT_NAME = k.CONSTRAINT_NAME
                  AND r.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA
WHERE k.TABLE_SCHEMA = DATABASE()
  AND LOWER(k.REFERENCED_TABLE_NAME) = 'specialite';

-- 4. Contraintes UNIQUE existantes sur Specialite
SHOW INDEX FROM Specialite WHERE Non_unique = 0;

-- 5. Spécialités actuellement utilisées (table de jonction Posseder)
SELECT s.id_specialite, s.libelle_specialite, COUNT(p.ref_medecin) AS nb_medecins
FROM Specialite s
         LEFT JOIN Posseder p ON p.ref_specialite = s.id_specialite
GROUP BY s.id_specialite, s.libelle_specialite
ORDER BY s.libelle_specialite;

-- Résultats attendus avec le script gdh_hsp :
--   PK : id_specialite (INT AUTO_INCREMENT) ; libelle_specialite VARCHAR(100) NOT NULL
--   UNIQUE : uq_specialite_libelle (libelle_specialite)
--   FK entrante : Posseder.ref_specialite -> ON DELETE CASCADE
--   Collation utf8mb4_unicode_ci : « cardiologue » = « Cardiologue » (doublon),
--   et les accents sont ignorés (« Pediatrie » = « Pédiatrie »).