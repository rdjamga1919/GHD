-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Oct 28, 2026 at 06:40 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ghd_hsp`
--

-- --------------------------------------------------------

--
-- Table structure for table `Canal_forum`
--

CREATE TABLE `Canal_forum` (
                               `id_canal` int NOT NULL,
                               `nom_canal` varchar(100) NOT NULL,
                               `acces` enum('generale','medecins','etudiants') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Canal_forum`
--

INSERT INTO `Canal_forum` (`id_canal`, `nom_canal`, `acces`) VALUES
                                                                 (1, 'Général', 'generale'),
                                                                 (2, 'Médecins', 'medecins'),
                                                                 (3, 'Étudiants', 'etudiants');

-- --------------------------------------------------------

--
-- Table structure for table `Candidature`
--

CREATE TABLE `Candidature` (
                               `id_candidature` int NOT NULL,
                               `motivation` text NOT NULL,
                               `date_candidature` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                               `statut` enum('en_attente','accepte','bloque') NOT NULL DEFAULT 'en_attente',
                               `ref_utilisateur` int NOT NULL,
                               `ref_offre` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Candidature`
--

INSERT INTO `Candidature` (`id_candidature`, `motivation`, `date_candidature`, `statut`, `ref_utilisateur`, `ref_offre`) VALUES
    (1, 'COUCOU CA VA J\'AIMERAIS CANDIDATER', '2026-10-27 14:23:51', 'en_attente', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Demande_contact`
--

CREATE TABLE `Demande_contact` (
  `id_demande` int NOT NULL,
  `nom_expediteur` varchar(150) NOT NULL,
  `email_expediteur` varchar(150) NOT NULL,
  `sujet` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `date_d_envoi` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ref_utilisateur` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Demande_etablissement`
--

CREATE TABLE `Demande_etablissement` (
  `id_demande` int NOT NULL,
  `ref_etudiant` int NOT NULL,
  `nom_etablissement` varchar(150) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `statut` enum('en_attente','acceptee','refusee') NOT NULL DEFAULT 'en_attente',
  `date_demande` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Enseigner`
--

CREATE TABLE `Enseigner` (
  `ref_etablissement` int NOT NULL,
  `ref_medecin` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Enseigner`
--

INSERT INTO `Enseigner` (`ref_etablissement`, `ref_medecin`) VALUES
(1, 1),
(2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Entreprise`
--

CREATE TABLE `Entreprise` (
  `id_entreprise` int NOT NULL,
  `nom_entreprise` varchar(150) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `site_web` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Entreprise`
--

INSERT INTO `Entreprise` (`id_entreprise`, `nom_entreprise`, `adresse`, `site_web`) VALUES
(1, 'TechSolutions', '15 rue de Paris, 75001 Paris', 'https://www.techsolutions.example'),
(2, 'MediConnect', '28 avenue Victor Hugo, 69003 Lyon', 'https://www.mediconnect.example'),
(3, 'DataNova', '42 boulevard Saint-Michel, 75005 Paris', 'https://www.datanova.example');

-- --------------------------------------------------------

--
-- Table structure for table `Etablissement`
--

CREATE TABLE `Etablissement` (
  `id_etablissement` int NOT NULL,
  `nom_etablissement` varchar(150) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `site_web` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Etablissement`
--

INSERT INTO `Etablissement` (`id_etablissement`, `nom_etablissement`, `adresse`, `site_web`) VALUES
(1, 'Université Paris Cité', '85 boulevard Saint-Germain, 75006 Paris', 'https://u-paris.fr'),
(2, 'Université Sorbonne Paris Nord', '99 avenue Jean-Baptiste Clément, 93430 Villetaneuse', 'https://www.univ-spn.fr'),
(3, 'Université Paris 8 Vincennes-Saint-Denis', '2 rue de la Liberté, 93200 Saint-Denis', 'https://www.univ-paris8.fr'),
(4, 'Université Paris Nanterre', '200 avenue de la République, 92000 Nanterre', 'https://www.parisnanterre.fr'),
(5, 'Lycée Robert Schuman', '5 avenue du Général de Gaulle - 93440 Dugny', 'https://www.lyceerobertschuman.com');

-- --------------------------------------------------------

--
-- Table structure for table `Etudiant`
--

CREATE TABLE `Etudiant` (
  `id_etudiant` int NOT NULL,
  `cv_etudiant` varchar(255) DEFAULT NULL,
  `formation` varchar(150) NOT NULL,
  `ref_utilisateur` int NOT NULL,
  `ref_etablissement` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Etudiant`
--

INSERT INTO `Etudiant` (`id_etudiant`, `cv_etudiant`, `formation`, `ref_utilisateur`, `ref_etablissement`) VALUES
(1, NULL, 'Licence de physique', 4, 3),
(2, NULL, 'Licence AES', 6, 1),
(3, NULL, 'Licence AES', 7, 2);

-- --------------------------------------------------------

--
-- Table structure for table `Evenement`
--

CREATE TABLE `Evenement` (
  `id_evenement` int NOT NULL,
  `titre` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `type_evenement` varchar(100) NOT NULL,
  `adresse_lieu` varchar(255) NOT NULL,
  `element_requis` varchar(255) DEFAULT NULL,
  `nombre_place` int NOT NULL,
  `date_heure_evenement` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Exercer`
--

CREATE TABLE `Exercer` (
  `ref_medecin` int NOT NULL,
  `ref_hopital` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Exercer`
--

INSERT INTO `Exercer` (`ref_medecin`, `ref_hopital`) VALUES
(1, 3),
(1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `Hopital`
--

CREATE TABLE `Hopital` (
  `id_hopital` int NOT NULL,
  `nom_hopital` varchar(150) NOT NULL,
  `commune` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Hopital`
--

INSERT INTO `Hopital` (`id_hopital`, `nom_hopital`, `commune`) VALUES
(1, 'Hôpital Sud Paris', 'Marreuil-sur-Seine'),
(2, 'Hôpital Nord Paris', 'Saint-Maximin'),
(3, 'Hôpital Général de Lyon', 'Limonest'),
(4, 'Hôpital Général de Marseille', 'Peypin'),
(5, 'Hôpital Général de Bordeaux', 'Beychac-et-Caillau'),
(6, 'Hôpital Général de Nantes', 'Mauves-sur-Loire'),
(7, 'Hôpital Général de Toulouse', 'Muret'),
(8, 'Hôpital Général de Strasbourg', 'Vendenheim'),
(9, 'Hôpital Général de Rennes', 'Pont-Péan');

-- --------------------------------------------------------

--
-- Table structure for table `Medecin`
--

CREATE TABLE `Medecin` (
  `id_medecin` int NOT NULL,
  `numero_rpps` varchar(20) NOT NULL,
  `ref_utilisateur` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Medecin`
--

INSERT INTO `Medecin` (`id_medecin`, `numero_rpps`, `ref_utilisateur`) VALUES
(1, '1234567', 2);

-- --------------------------------------------------------

--
-- Table structure for table `Offre`
--

CREATE TABLE `Offre` (
  `id_offre` int NOT NULL,
  `titre` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `missions` text NOT NULL,
  `salaire` decimal(10,2) DEFAULT NULL,
  `type_d_offre` enum('stage','alternance','cdd','cdi') NOT NULL,
  `etat` enum('ouverte','fermee') NOT NULL DEFAULT 'ouverte',
  `date_publication` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ref_utilisateur` int NOT NULL,
  `ref_entreprise` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Offre`
--

INSERT INTO `Offre` (`id_offre`, `titre`, `description`, `missions`, `salaire`, `type_d_offre`, `etat`, `date_publication`, `ref_utilisateur`, `ref_entreprise`) VALUES
(1, 'Développeur web junior', 'Développement d\'une application web PHP', 'Création de pages, requêtes SQL et tests', 1200.00, 'alternance', 'ouverte', '2026-10-27 15:05:42', 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `Organiser`
--

CREATE TABLE `Organiser` (
                             `ref_evenement` int NOT NULL,
                             `ref_utilisateur` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Partenaire`
--

CREATE TABLE `Partenaire` (
                              `id_partenaire` int NOT NULL,
                              `poste` varchar(100) NOT NULL,
                              `ref_utilisateur` int NOT NULL,
                              `ref_entreprise` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Partenaire`
--

INSERT INTO `Partenaire` (`id_partenaire`, `poste`, `ref_utilisateur`, `ref_entreprise`) VALUES
    (1, 'RH', 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `Posseder`
--

CREATE TABLE `Posseder` (
                            `ref_medecin` int NOT NULL,
                            `ref_specialite` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Posseder`
--

INSERT INTO `Posseder` (`ref_medecin`, `ref_specialite`) VALUES
    (1, 18);

-- --------------------------------------------------------

--
-- Table structure for table `Post_forum`
--

CREATE TABLE `Post_forum` (
                              `id_post` int NOT NULL,
                              `titre` varchar(150) NOT NULL,
                              `contenu` text NOT NULL,
                              `date_heure_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              `ref_canal` int NOT NULL,
                              `ref_utilisateur` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Reponse_forum`
--

CREATE TABLE `Reponse_forum` (
                                 `id_reponse` int NOT NULL,
                                 `contenu` text NOT NULL,
                                 `date_heure_reponse` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                 `ref_post` int NOT NULL,
                                 `ref_utilisateur` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Specialite`
--

CREATE TABLE `Specialite` (
                              `id_specialite` int NOT NULL,
                              `libelle_specialite` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Specialite`
--

INSERT INTO `Specialite` (`id_specialite`, `libelle_specialite`) VALUES
                                                                     (18, 'Anesthésiologue'),
                                                                     (1, 'Cardiologue'),
                                                                     (17, 'Chirurgien orthopédique'),
                                                                     (4, 'Dermatologue'),
                                                                     (9, 'Endocrinologue'),
                                                                     (3, 'Gastroentérologue'),
                                                                     (11, 'Gynécologue'),
                                                                     (15, 'Hématologue'),
                                                                     (20, 'Médecin généraliste'),
                                                                     (19, 'Médecin interne'),
                                                                     (14, 'Néphrologue'),
                                                                     (2, 'Neurologue'),
                                                                     (6, 'Oncologue'),
                                                                     (10, 'Ophtalmologue'),
                                                                     (16, 'Oto-rhino-laryngologue'),
                                                                     (5, 'Pédiatrie'),
                                                                     (7, 'Pneumologue'),
                                                                     (12, 'Psychiatre'),
                                                                     (8, 'Rhumatologue'),
                                                                     (13, 'Urologue');

-- --------------------------------------------------------

--
-- Table structure for table `S_inscrire`
--

CREATE TABLE `S_inscrire` (
                              `ref_evenement` int NOT NULL,
                              `ref_utilisateur` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Utilisateur`
--

CREATE TABLE `Utilisateur` (
                               `id_utilisateur` int NOT NULL,
                               `nom` varchar(100) NOT NULL,
                               `prenom` varchar(100) NOT NULL,
                               `email` varchar(150) NOT NULL,
                               `mot_de_passe` varchar(255) NOT NULL,
                               `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                               `role` enum('etudiant','medecin','partenaire','gestionnaire') NOT NULL,
                               `est_valide` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Utilisateur`
--

INSERT INTO `Utilisateur` (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `date_creation`, `role`, `est_valide`) VALUES
                                                                                                                                  (1, 'test', 'test', 'test@test.fr', '$2y$12$Vg4tR6zLgQ0RY0YddbNSo.Q59rDeIBQrRJ7wGxq6bFdhuC7cv2KlS', '2026-09-29 10:59:44', 'etudiant', 1),
                                                                                                                                  (2, 'Lionel', 'lion', 'lion.l@ghd.fr', '$2y$10$jise0AwZZNkFomFKMgBW7.nWY6k/NGFCSXrVI8tgQdhlz9g3KLO/m', '2026-10-13 07:58:55', 'medecin', 1),
                                                                                                                                  (3, 'Test', 'Gestionnaire', 'gestionnaire.test@exemple.com', '$2y$12$m8uyIkH0yajJoc30NOTy2en7VkZqPGPOGfkyViBWy1f7cnCHix3zC', '2026-10-13 08:04:21', 'gestionnaire', 1),
                                                                                                                                  (4, 'Branco', 'Juan', 'j.branco@u-paris.fr', '$2y$10$2lhX2O9AQvAcwSBPytgDZub1zQkZMrXWdBGsydcfOsGtsjaqYSd9.', '2026-10-21 23:42:56', 'etudiant', 1),
                                                                                                                                  (5, 'Neeson', 'Liam', 'l.neeson@datanova.fr', '$2y$10$wvoIJmPLxLL.Fidd0Ji91uDmeFH3ezY10jVys.Rg3fd0A7SbvV7Si', '2026-10-22 07:08:03', 'partenaire', 1),
                                                                                                                                  (6, 'Iman', 'Mani', 'i.mani@gmail.com', '$2y$10$pNr0Mb42DT7nnl7AvoSERuYhM2kwVHpWXsDzxMTqfrxtezZuYf64W', '2026-10-22 13:29:36', 'etudiant', 0),
                                                                                                                                  (7, 'Li', 'Na', 'Lian@gmail.com', '$2y$10$HIVcCYSYge./ytU24lRd.OwFhK/knM69QklUyH3kVypc9umDlMRkW', '2026-10-22 13:30:56', 'etudiant', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Canal_forum`
--
ALTER TABLE `Canal_forum`
    ADD PRIMARY KEY (`id_canal`);

--
-- Indexes for table `Candidature`
--
ALTER TABLE `Candidature`
    ADD PRIMARY KEY (`id_candidature`),
  ADD UNIQUE KEY `uq_candidature_utilisateur_offre` (`ref_utilisateur`,`ref_offre`),
  ADD KEY `fk_candidature_offre` (`ref_offre`);

--
-- Indexes for table `Demande_contact`
--
ALTER TABLE `Demande_contact`
    ADD PRIMARY KEY (`id_demande`),
  ADD KEY `fk_demande_contact_utilisateur` (`ref_utilisateur`);

--
-- Indexes for table `Demande_etablissement`
--
ALTER TABLE `Demande_etablissement`
    ADD PRIMARY KEY (`id_demande`),
  ADD KEY `fk_demande_etudiant` (`ref_etudiant`);

--
-- Indexes for table `Enseigner`
--
ALTER TABLE `Enseigner`
    ADD PRIMARY KEY (`ref_etablissement`,`ref_medecin`),
  ADD KEY `fk_enseigner_medecin` (`ref_medecin`);

--
-- Indexes for table `Entreprise`
--
ALTER TABLE `Entreprise`
    ADD PRIMARY KEY (`id_entreprise`),
  ADD UNIQUE KEY `uq_entreprise_site` (`site_web`);

--
-- Indexes for table `Etablissement`
--
ALTER TABLE `Etablissement`
    ADD PRIMARY KEY (`id_etablissement`),
  ADD UNIQUE KEY `uq_etablissement_site` (`site_web`);

--
-- Indexes for table `Etudiant`
--
ALTER TABLE `Etudiant`
    ADD PRIMARY KEY (`id_etudiant`),
  ADD UNIQUE KEY `uq_etudiant_utilisateur` (`ref_utilisateur`),
  ADD KEY `fk_etudiant_etablissement` (`ref_etablissement`);

--
-- Indexes for table `Evenement`
--
ALTER TABLE `Evenement`
    ADD PRIMARY KEY (`id_evenement`);

--
-- Indexes for table `Exercer`
--
ALTER TABLE `Exercer`
    ADD PRIMARY KEY (`ref_medecin`,`ref_hopital`),
  ADD KEY `fk_exercer_hopital` (`ref_hopital`);

--
-- Indexes for table `Hopital`
--
ALTER TABLE `Hopital`
    ADD PRIMARY KEY (`id_hopital`);

--
-- Indexes for table `Medecin`
--
ALTER TABLE `Medecin`
    ADD PRIMARY KEY (`id_medecin`),
  ADD UNIQUE KEY `uq_medecin_rpps` (`numero_rpps`),
  ADD UNIQUE KEY `uq_medecin_utilisateur` (`ref_utilisateur`);

--
-- Indexes for table `Offre`
--
ALTER TABLE `Offre`
    ADD PRIMARY KEY (`id_offre`),
  ADD KEY `fk_offre_utilisateur` (`ref_utilisateur`),
  ADD KEY `fk_offre_entreprise` (`ref_entreprise`);

--
-- Indexes for table `Organiser`
--
ALTER TABLE `Organiser`
    ADD PRIMARY KEY (`ref_evenement`,`ref_utilisateur`),
  ADD KEY `fk_organiser_utilisateur` (`ref_utilisateur`);

--
-- Indexes for table `Partenaire`
--
ALTER TABLE `Partenaire`
    ADD PRIMARY KEY (`id_partenaire`),
  ADD UNIQUE KEY `uq_partenaire_utilisateur` (`ref_utilisateur`),
  ADD KEY `fk_partenaire_entreprise` (`ref_entreprise`);

--
-- Indexes for table `Posseder`
--
ALTER TABLE `Posseder`
    ADD PRIMARY KEY (`ref_medecin`,`ref_specialite`),
  ADD KEY `fk_posseder_specialite` (`ref_specialite`);

--
-- Indexes for table `Post_forum`
--
ALTER TABLE `Post_forum`
    ADD PRIMARY KEY (`id_post`),
  ADD KEY `fk_post_canal` (`ref_canal`),
  ADD KEY `fk_post_utilisateur` (`ref_utilisateur`);

--
-- Indexes for table `Reponse_forum`
--
ALTER TABLE `Reponse_forum`
    ADD PRIMARY KEY (`id_reponse`),
  ADD KEY `fk_reponse_post` (`ref_post`),
  ADD KEY `fk_reponse_utilisateur` (`ref_utilisateur`);

--
-- Indexes for table `Specialite`
--
ALTER TABLE `Specialite`
    ADD PRIMARY KEY (`id_specialite`),
  ADD UNIQUE KEY `uq_specialite_libelle` (`libelle_specialite`);

--
-- Indexes for table `S_inscrire`
--
ALTER TABLE `S_inscrire`
    ADD PRIMARY KEY (`ref_evenement`,`ref_utilisateur`),
  ADD KEY `fk_sinscrire_utilisateur` (`ref_utilisateur`);

--
-- Indexes for table `Utilisateur`
--
ALTER TABLE `Utilisateur`
    ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `uq_utilisateur_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Canal_forum`
--
ALTER TABLE `Canal_forum`
    MODIFY `id_canal` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Candidature`
--
ALTER TABLE `Candidature`
    MODIFY `id_candidature` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Demande_contact`
--
ALTER TABLE `Demande_contact`
    MODIFY `id_demande` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Demande_etablissement`
--
ALTER TABLE `Demande_etablissement`
    MODIFY `id_demande` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Entreprise`
--
ALTER TABLE `Entreprise`
    MODIFY `id_entreprise` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Etablissement`
--
ALTER TABLE `Etablissement`
    MODIFY `id_etablissement` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Etudiant`
--
ALTER TABLE `Etudiant`
    MODIFY `id_etudiant` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Evenement`
--
ALTER TABLE `Evenement`
    MODIFY `id_evenement` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Hopital`
--
ALTER TABLE `Hopital`
    MODIFY `id_hopital` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `Medecin`
--
ALTER TABLE `Medecin`
    MODIFY `id_medecin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Offre`
--
ALTER TABLE `Offre`
    MODIFY `id_offre` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Partenaire`
--
ALTER TABLE `Partenaire`
    MODIFY `id_partenaire` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Post_forum`
--
ALTER TABLE `Post_forum`
    MODIFY `id_post` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Reponse_forum`
--
ALTER TABLE `Reponse_forum`
    MODIFY `id_reponse` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Specialite`
--
ALTER TABLE `Specialite`
    MODIFY `id_specialite` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `Utilisateur`
--
ALTER TABLE `Utilisateur`
    MODIFY `id_utilisateur` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Candidature`
--
ALTER TABLE `Candidature`
    ADD CONSTRAINT `fk_candidature_offre` FOREIGN KEY (`ref_offre`) REFERENCES `Offre` (`id_offre`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_candidature_utilisateur` FOREIGN KEY (`ref_utilisateur`) REFERENCES `Utilisateur` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Constraints for table `Demande_contact`
--
ALTER TABLE `Demande_contact`
    ADD CONSTRAINT `fk_demande_contact_utilisateur` FOREIGN KEY (`ref_utilisateur`) REFERENCES `Utilisateur` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Constraints for table `Demande_etablissement`
--
ALTER TABLE `Demande_etablissement`
    ADD CONSTRAINT `fk_demande_etudiant` FOREIGN KEY (`ref_etudiant`) REFERENCES `Etudiant` (`id_etudiant`) ON DELETE CASCADE;

--
-- Constraints for table `Enseigner`
--
ALTER TABLE `Enseigner`
    ADD CONSTRAINT `fk_enseigner_etablissement` FOREIGN KEY (`ref_etablissement`) REFERENCES `Etablissement` (`id_etablissement`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enseigner_medecin` FOREIGN KEY (`ref_medecin`) REFERENCES `Medecin` (`id_medecin`) ON DELETE CASCADE;

--
-- Constraints for table `Etudiant`
--
ALTER TABLE `Etudiant`
    ADD CONSTRAINT `fk_etudiant_etablissement` FOREIGN KEY (`ref_etablissement`) REFERENCES `Etablissement` (`id_etablissement`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_etudiant_utilisateur` FOREIGN KEY (`ref_utilisateur`) REFERENCES `Utilisateur` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Constraints for table `Exercer`
--
ALTER TABLE `Exercer`
    ADD CONSTRAINT `fk_exercer_hopital` FOREIGN KEY (`ref_hopital`) REFERENCES `Hopital` (`id_hopital`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_exercer_medecin` FOREIGN KEY (`ref_medecin`) REFERENCES `Medecin` (`id_medecin`) ON DELETE CASCADE;

--
-- Constraints for table `Medecin`
--
ALTER TABLE `Medecin`
    ADD CONSTRAINT `fk_medecin_utilisateur` FOREIGN KEY (`ref_utilisateur`) REFERENCES `Utilisateur` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Constraints for table `Offre`
--
ALTER TABLE `Offre`
    ADD CONSTRAINT `fk_offre_entreprise` FOREIGN KEY (`ref_entreprise`) REFERENCES `Entreprise` (`id_entreprise`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_offre_utilisateur` FOREIGN KEY (`ref_utilisateur`) REFERENCES `Utilisateur` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Constraints for table `Organiser`
--
ALTER TABLE `Organiser`
    ADD CONSTRAINT `fk_organiser_evenement` FOREIGN KEY (`ref_evenement`) REFERENCES `Evenement` (`id_evenement`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_organiser_utilisateur` FOREIGN KEY (`ref_utilisateur`) REFERENCES `Utilisateur` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Constraints for table `Partenaire`
--
ALTER TABLE `Partenaire`
    ADD CONSTRAINT `fk_partenaire_entreprise` FOREIGN KEY (`ref_entreprise`) REFERENCES `Entreprise` (`id_entreprise`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_partenaire_utilisateur` FOREIGN KEY (`ref_utilisateur`) REFERENCES `Utilisateur` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Constraints for table `Posseder`
--
ALTER TABLE `Posseder`
    ADD CONSTRAINT `fk_posseder_medecin` FOREIGN KEY (`ref_medecin`) REFERENCES `Medecin` (`id_medecin`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_posseder_specialite` FOREIGN KEY (`ref_specialite`) REFERENCES `Specialite` (`id_specialite`) ON DELETE CASCADE;

--
-- Constraints for table `Post_forum`
--
ALTER TABLE `Post_forum`
    ADD CONSTRAINT `fk_post_canal` FOREIGN KEY (`ref_canal`) REFERENCES `Canal_forum` (`id_canal`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_post_utilisateur` FOREIGN KEY (`ref_utilisateur`) REFERENCES `Utilisateur` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Constraints for table `Reponse_forum`
--
ALTER TABLE `Reponse_forum`
    ADD CONSTRAINT `fk_reponse_post` FOREIGN KEY (`ref_post`) REFERENCES `Post_forum` (`id_post`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reponse_utilisateur` FOREIGN KEY (`ref_utilisateur`) REFERENCES `Utilisateur` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Constraints for table `S_inscrire`
--
ALTER TABLE `S_inscrire`
    ADD CONSTRAINT `fk_sinscrire_evenement` FOREIGN KEY (`ref_evenement`) REFERENCES `Evenement` (`id_evenement`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sinscrire_utilisateur` FOREIGN KEY (`ref_utilisateur`) REFERENCES `Utilisateur` (`id_utilisateur`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
