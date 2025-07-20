-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 20 juil. 2025 à 12:51
-- Version du serveur : 8.2.0
-- Version de PHP : 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `demande_amarna`
--

-- --------------------------------------------------------

--
-- Structure de la table `archives`
--

DROP TABLE IF EXISTS `archives`;
CREATE TABLE IF NOT EXISTS `archives` (
  `id` int NOT NULL AUTO_INCREMENT,
  `commande_id` int NOT NULL,
  `date_saisie` datetime NOT NULL,
  `adjoint_mg_valide` tinyint(1) NOT NULL,
  `admin_valide` tinyint(1) NOT NULL,
  `daf_valide` tinyint(1) NOT NULL,
  `date_archive` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `prix_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `commande_id` (`commande_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `avance_sur_salaire`
--

DROP TABLE IF EXISTS `avance_sur_salaire`;
CREATE TABLE IF NOT EXISTS `avance_sur_salaire` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_demande_avance` int NOT NULL,
  `date` datetime NOT NULL,
  `mois` tinyint UNSIGNED NOT NULL,
  `annee` int NOT NULL,
  `codeavance` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_demande_avance` (`id_demande_avance`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `avance_sur_salaire`
--

INSERT INTO `avance_sur_salaire` (`id`, `id_demande_avance`, `date`, `mois`, `annee`, `codeavance`) VALUES
(23, 43, '2024-09-26 07:11:58', 9, 2024, '43/09/2024'),
(22, 37, '2024-09-25 21:51:30', 9, 2024, '37/09/2024'),
(21, 36, '2024-09-14 17:43:10', 9, 2024, '36/09/2024'),
(24, 54, '2024-09-26 09:04:52', 9, 2024, '54/09/2024'),
(25, 57, '2024-09-26 09:04:57', 9, 2024, '57/09/2024'),
(26, 65, '2024-09-26 15:13:26', 9, 2024, '65/09/2024'),
(27, 67, '2024-09-26 15:19:52', 9, 2024, '67/09/2024');

-- --------------------------------------------------------

--
-- Structure de la table `carburant`
--

DROP TABLE IF EXISTS `carburant`;
CREATE TABLE IF NOT EXISTS `carburant` (
  `id` int NOT NULL AUTO_INCREMENT,
  `categorie` varchar(50) DEFAULT 'CARBURANT',
  `libelle` varchar(255) NOT NULL,
  `piece` blob,
  `prix` decimal(10,2) NOT NULL,
  `beneficiaire` varchar(255) NOT NULL,
  `quantite` int NOT NULL,
  `n_stock` varchar(50) NOT NULL,
  `validated` tinyint(1) NOT NULL DEFAULT '0',
  `Objet` varchar(255) NOT NULL,
  `Total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `carburant`
--

INSERT INTO `carburant` (`id`, `categorie`, `libelle`, `piece`, `prix`, `beneficiaire`, `quantite`, `n_stock`, `validated`, `Objet`, `Total`) VALUES
(15, 'CARBURANT', 'test3', 0x6461662e6a666966, 2.99, 'Saleck', 5000, 'SLK-24-BYA', 0, 'demande test2', 2495000.00),
(14, 'CARBURANT', 'carburant test 1', 0x69636f6e2d636172627572616e74202831292e706e67, 39.00, 'Kenz Mining', 350, 'MSA-24KA', 1, 'demande carburant test 1', 43440.00);

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `code` int NOT NULL AUTO_INCREMENT,
  `Titre` varchar(255) NOT NULL,
  `Cle` varchar(255) NOT NULL,
  `salaire` int DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`code`, `Titre`, `Cle`, `salaire`) VALUES
(16, 'stagiaire', 'S', 80000),
(15, 'maindoeuvre', 'M', 1000),
(14, 'cadre', 'C', 300),
(13, 'agent', 'A', 200),
(17, 'phamacien', 'P', 100);

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_commande` datetime NOT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'ouvert',
  `notified` tinyint(1) DEFAULT '0',
  `adjoint_mg_valide` tinyint(1) DEFAULT '0',
  `admin_valide` tinyint(1) DEFAULT '0',
  `daf_valide` tinyint(1) DEFAULT '0',
  `prix_total` decimal(10,2) DEFAULT '0.00',
  `date_derniere_modif` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `date_commande`, `status`, `notified`, `adjoint_mg_valide`, `admin_valide`, `daf_valide`, `prix_total`, `date_derniere_modif`) VALUES
(49, '2024-09-23 10:16:04', 'fermé', 0, 1, 0, 0, 312.39, '2024-11-16 10:56:48'),
(48, '2024-09-14 16:28:27', 'fermé', 0, 0, 0, 0, 4.00, '2024-09-22 23:04:53'),
(50, '2024-10-21 13:19:35', 'fermé', 0, 0, 0, 0, 48.00, '2024-10-21 13:24:55');

-- --------------------------------------------------------

--
-- Structure de la table `demande_avance`
--

DROP TABLE IF EXISTS `demande_avance`;
CREATE TABLE IF NOT EXISTS `demande_avance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `montant` decimal(10,2) NOT NULL,
  `mois` tinyint UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `personnel_id` int NOT NULL,
  `user_RH` varchar(50) DEFAULT NULL,
  `accord_DAF` tinyint(1) DEFAULT '0',
  `visa_DAF` blob,
  `traitée` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_personnel` (`personnel_id`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `demande_avance`
--

INSERT INTO `demande_avance` (`id`, `montant`, `mois`, `date`, `personnel_id`, `user_RH`, `accord_DAF`, `visa_DAF`, `traitée`) VALUES
(36, 100.00, 9, '2024-09-14 17:42:46', 1, NULL, 1, NULL, 1),
(37, 100.00, 9, '2024-09-25 21:49:09', 15, NULL, 1, NULL, 1),
(54, 800.00, 9, '2024-09-26 08:08:08', 23, NULL, 1, NULL, 1),
(65, 1.00, 9, '2024-09-26 15:09:49', 22, NULL, 1, NULL, 1),
(67, 10000000.00, 9, '2024-09-26 15:19:05', 1, NULL, 1, NULL, 1),
(43, 20.00, 9, '2024-09-25 23:51:46', 18, NULL, 1, NULL, 1),
(57, 900.00, 9, '2024-09-26 09:01:41', 27, NULL, 1, NULL, 1),
(69, 20.00, 9, '2024-09-28 07:37:06', 15, NULL, 0, NULL, 0),
(71, 200000.00, 9, '2024-09-28 07:47:37', 21, NULL, 0, NULL, 0),
(72, 99999999.99, 9, '2024-09-28 07:47:48', 23, NULL, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `demande_carburant`
--

DROP TABLE IF EXISTS `demande_carburant`;
CREATE TABLE IF NOT EXISTS `demande_carburant` (
  `id` int NOT NULL AUTO_INCREMENT,
  `carburant_id` int NOT NULL,
  `quantite_demande` int NOT NULL,
  `demandeur` varchar(255) NOT NULL,
  `date_demande` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) NOT NULL DEFAULT 'En attente',
  PRIMARY KEY (`id`),
  KEY `carburant_id` (`carburant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `demande_carburant`
--

INSERT INTO `demande_carburant` (`id`, `carburant_id`, `quantite_demande`, `demandeur`, `date_demande`, `status`) VALUES
(10, 14, 2346, 'saleckbaya5@gmail.com', '2024-09-28 20:11:05', 'En attente'),
(9, 14, 100, 'deddah@gmail.com', '2024-09-08 10:30:27', 'Validée'),
(8, 14, 50, 'azizehadrami@gmail.com', '2024-08-29 09:50:24', 'Validée'),
(7, 14, 100, 'saleckbaya5@gmail.com', '2024-08-29 09:37:00', 'Validée'),
(6, 14, 124, 'med.salem@gmail.com', '2024-08-27 11:30:11', 'Validée'),
(11, 14, 10, 'saleckbaya5@gmail.com', '2024-09-28 20:20:21', 'En attente'),
(12, 14, 1, 'saleckbaya5@gmail.com', '2024-09-29 18:03:54', 'En attente'),
(13, 14, 1, 'saleckbaya5@gmail.com', '2024-09-29 18:10:15', 'En attente'),
(14, 14, 1, 'saleckbaya5@gmail.com', '2024-09-29 18:13:43', 'En attente'),
(15, 14, 2, 'saleckbaya5@gmail.com', '2024-09-30 11:13:33', 'En attente');

-- --------------------------------------------------------

--
-- Structure de la table `departement`
--

DROP TABLE IF EXISTS `departement`;
CREATE TABLE IF NOT EXISTS `departement` (
  `num_departement` int NOT NULL AUTO_INCREMENT,
  `departement` varchar(255) NOT NULL,
  PRIMARY KEY (`num_departement`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `departement`
--

INSERT INTO `departement` (`num_departement`, `departement`) VALUES
(51, 'Ressources Humaine'),
(47, 'Informatique'),
(46, 'Logistique'),
(45, 'Administration');

-- --------------------------------------------------------

--
-- Structure de la table `expression_besoins`
--

DROP TABLE IF EXISTS `expression_besoins`;
CREATE TABLE IF NOT EXISTS `expression_besoins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `date_saisie` date NOT NULL,
  `désignation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `objet` varchar(255) NOT NULL,
  `piece` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `nombres_articles` int NOT NULL,
  `urgence` varchar(50) NOT NULL,
  `type_services` varchar(255) NOT NULL,
  `commande_id` int DEFAULT NULL,
  `prix_unitaire` decimal(10,2) DEFAULT '0.00',
  `notified` tinyint(1) DEFAULT '0',
  `destination` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_commande_id` (`commande_id`)
) ENGINE=MyISAM AUTO_INCREMENT=174 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `expression_besoins`
--

INSERT INTO `expression_besoins` (`id`, `user_id`, `date_saisie`, `désignation`, `objet`, `piece`, `image`, `nombres_articles`, `urgence`, `type_services`, `commande_id`, `prix_unitaire`, `notified`, `destination`) VALUES
(166, 0, '2024-09-23', 'c', 'demande fourniture', 'Cleaning Services Logo.png', 'création-site_infinityfree.png', 2, 'Moyenne', 'Prestation de service', 49, 2.00, 0, 'Chami'),
(165, 0, '2024-09-23', 'b', 'demande fourniture', 'bureau (1).png', 'bureau.png', 5, '0', 'Achat de fournitures', 49, 8.00, 0, 'Chami'),
(164, 0, '2024-09-23', 'a', 'demande fourniture', 'admin-dashboard.png', 'Blue and White Flat Illustrative Cleaning Services Logo.png', 4, 'Élevée', 'Prestation de service', 49, 6.99, 0, 'Chami'),
(167, 0, '2024-09-23', 'd', 'demande fourniture', 'fermer-a-cle.png', 'finance.png', 3, 'Moyenne', 'Achat pour réparation', 49, 1.99, 0, 'Chami'),
(168, 0, '2024-09-23', 'e', 'demande fourniture', 'icon-eye-off.png', 'proj1.png', 6, 'Élevée', 'Achat de matériel', 49, 7.00, 0, 'Chami'),
(169, 0, '2024-09-23', 'f', 'demande fourniture', 'mode-sombre (1).png', 'services_divers.png', 4, 'Basse', 'Achat de fournitures', 49, 4.79, 0, 'Chami'),
(170, 0, '2024-09-23', 'g', 'demande fourniture', 'icon-paramètre.png', 'icon-carburant.png', 17, 'Élevée', 'Achat de fournitures', 49, 9.75, 0, 'Chami'),
(171, 0, '2024-09-23', 'h', 'demande fourniture', 'Cleaning Services Logo.png', 'icon-archive.png', 1, 'Basse', 'Prestation de service', 49, 7.55, 0, 'Chami'),
(173, 0, '2024-10-21', 'b', 'demande fourniture', 'acd9dbf4-bdcf-4015-8ce1-9a16d49a8dc5.jpg', '33a53f95-6d12-4729-bb73-ac541030edb5.jpg', 1, 'Moyenne', 'Achat pour réparation', 50, 0.00, 0, 'Nouakchott');

-- --------------------------------------------------------

--
-- Structure de la table `instances`
--

DROP TABLE IF EXISTS `instances`;
CREATE TABLE IF NOT EXISTS `instances` (
  `id` int NOT NULL AUTO_INCREMENT,
  `commande_id` int DEFAULT NULL,
  `date_saisie` datetime DEFAULT NULL,
  `adjoint_mg_valide` tinyint(1) DEFAULT NULL,
  `admin_valide` tinyint(1) DEFAULT NULL,
  `daf_valide` tinyint(1) DEFAULT NULL,
  `date_instance` datetime DEFAULT CURRENT_TIMESTAMP,
  `prix_total` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `commande_id` (`commande_id`)
) ENGINE=MyISAM AUTO_INCREMENT=275 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `instances`
--

INSERT INTO `instances` (`id`, `commande_id`, `date_saisie`, `adjoint_mg_valide`, `admin_valide`, `daf_valide`, `date_instance`, `prix_total`) VALUES
(8, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-17 16:19:25', 0.00),
(7, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-17 16:19:25', 0.00),
(9, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-17 16:20:35', 0.00),
(10, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-17 16:20:35', 0.00),
(11, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 09:51:48', 0.00),
(12, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 09:51:48', 0.00),
(13, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 10:04:19', 0.00),
(14, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 10:04:19', 0.00),
(15, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 10:04:58', 0.00),
(16, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 10:04:58', 0.00),
(17, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 10:08:23', 0.00),
(18, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 10:08:23', 0.00),
(19, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 10:17:17', 0.00),
(20, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 10:17:17', 0.00),
(21, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 10:26:09', 0.00),
(22, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 10:26:09', 0.00),
(23, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 11:32:05', 320.00),
(24, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 11:32:05', 320.00),
(25, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 17:38:41', 1023.96),
(26, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 17:38:41', 1023.96),
(27, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 19:11:53', 197.96),
(28, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 19:11:53', 197.96),
(29, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 20:12:39', 287.96),
(30, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 20:12:39', 287.96),
(31, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 20:18:25', 8232.00),
(32, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 20:18:25', 8232.00),
(33, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 20:18:59', 8232.00),
(34, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 20:18:59', 8232.00),
(35, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 22:57:14', 46.96),
(36, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 22:57:14', 46.96),
(37, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 22:58:32', 39.96),
(38, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 22:58:32', 39.96),
(39, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 22:59:23', 39.96),
(40, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-22 22:59:23', 39.96),
(41, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 10:13:06', 4.00),
(42, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 10:13:06', 4.00),
(43, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 11:37:46', 0.00),
(44, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 11:37:46', 4.00),
(45, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 11:37:46', 0.00),
(46, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 11:37:46', 4.00),
(47, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 12:31:15', 0.00),
(48, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 12:31:15', 4.00),
(49, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 12:31:15', 0.00),
(50, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 12:31:15', 4.00),
(51, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 12:48:56', 0.00),
(52, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 12:48:56', 4.00),
(53, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 12:48:56', 0.00),
(54, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 12:48:56', 4.00),
(55, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 12:49:10', 0.00),
(56, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 12:49:10', 4.00),
(57, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 12:49:10', 0.00),
(58, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 12:49:10', 4.00),
(59, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 12:49:43', 0.00),
(60, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 12:49:43', 4.00),
(61, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 12:49:43', 0.00),
(62, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 12:49:43', 4.00),
(63, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 12:49:58', 0.00),
(64, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 12:49:58', 4.00),
(65, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 12:49:58', 0.00),
(66, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 12:49:58', 4.00),
(67, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 14:18:22', 0.00),
(68, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 14:18:22', 4.00),
(69, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 14:18:22', 0.00),
(70, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 14:18:22', 4.00),
(71, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 15:56:11', 0.00),
(72, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 15:56:11', 4.00),
(73, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-23 15:56:11', 0.00),
(74, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-23 15:56:11', 4.00),
(75, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-24 06:25:15', 31.95),
(76, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-24 06:25:15', 4.00),
(77, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-24 06:25:15', 31.95),
(78, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-24 06:25:15', 4.00),
(79, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-24 06:47:29', 31.95),
(80, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-24 06:47:29', 4.00),
(81, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-24 06:47:29', 31.95),
(82, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-24 06:47:29', 4.00),
(83, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-24 11:34:37', 31.95),
(84, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-24 11:34:37', 4.00),
(85, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-24 11:34:37', 31.95),
(86, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-24 11:34:37', 4.00),
(87, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-24 11:34:40', 31.95),
(88, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-24 11:34:40', 4.00),
(89, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-24 11:34:40', 31.95),
(90, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-24 11:34:40', 4.00),
(91, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-24 12:18:54', 31.95),
(92, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-24 12:18:54', 4.00),
(93, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-24 12:18:54', 31.95),
(94, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-24 12:18:54', 4.00),
(95, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 11:10:49', 31.95),
(96, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 11:10:49', 4.00),
(97, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 11:10:49', 31.95),
(98, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 11:10:49', 4.00),
(99, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 12:06:53', 44.00),
(100, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 12:06:53', 4.00),
(101, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 12:06:53', 44.00),
(102, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 12:06:53', 4.00),
(103, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 13:19:11', 48.00),
(104, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 13:19:11', 4.00),
(105, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 13:19:11', 48.00),
(106, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 13:19:11', 4.00),
(107, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 13:29:01', 48.00),
(108, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 13:29:01', 4.00),
(109, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 13:29:01', 48.00),
(110, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 13:29:01', 4.00),
(111, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 13:30:34', 48.00),
(112, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 13:30:34', 4.00),
(113, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 13:30:34', 48.00),
(114, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 13:30:34', 4.00),
(115, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 13:31:07', 48.00),
(116, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 13:31:07', 4.00),
(117, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 13:31:07', 48.00),
(118, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 13:31:07', 4.00),
(119, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 13:52:55', 48.00),
(120, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 13:52:55', 4.00),
(121, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 13:52:55', 48.00),
(122, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 13:52:55', 4.00),
(123, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 17:24:36', 48.00),
(124, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 17:24:36', 4.00),
(125, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 17:24:36', 48.00),
(126, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 17:24:36', 4.00),
(127, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 17:37:25', 48.00),
(128, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 17:37:25', 4.00),
(129, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 17:37:25', 48.00),
(130, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 17:37:25', 4.00),
(131, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 17:37:26', 48.00),
(132, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 17:37:26', 4.00),
(133, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 17:37:26', 48.00),
(134, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 17:37:26', 4.00),
(135, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 17:37:27', 48.00),
(136, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 17:37:27', 4.00),
(137, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 17:37:27', 48.00),
(138, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 17:37:27', 4.00),
(139, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 17:39:38', 310.39),
(140, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 17:39:38', 4.00),
(141, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 17:39:38', 310.39),
(142, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 17:39:38', 4.00),
(143, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 17:40:27', 310.39),
(144, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 17:40:27', 4.00),
(145, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 17:40:27', 310.39),
(146, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 17:40:27', 4.00),
(147, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 19:36:19', 310.39),
(148, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 19:36:19', 4.00),
(149, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 19:36:19', 310.39),
(150, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 19:36:19', 4.00),
(151, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 19:50:33', 310.39),
(152, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 19:50:33', 4.00),
(153, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 19:50:33', 310.39),
(154, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 19:50:33', 4.00),
(155, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 20:07:04', 310.39),
(156, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 20:07:04', 4.00),
(157, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 20:07:04', 310.39),
(158, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 20:07:04', 4.00),
(159, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 20:08:34', 310.39),
(160, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 20:08:34', 4.00),
(161, 49, '2024-09-23 10:16:04', 0, 0, 0, '2024-09-25 20:08:34', 310.39),
(162, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 20:08:34', 4.00),
(163, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-25 20:08:41', 310.39),
(164, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 20:08:41', 4.00),
(165, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-25 20:08:41', 310.39),
(166, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 20:08:41', 4.00),
(167, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-25 20:16:23', 310.39),
(168, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 20:16:23', 4.00),
(169, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-25 20:16:23', 310.39),
(170, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 20:16:23', 4.00),
(171, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-25 23:20:51', 310.39),
(172, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 23:20:51', 4.00),
(173, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-25 23:20:51', 310.39),
(174, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 23:20:51', 4.00),
(175, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-25 23:47:16', 310.39),
(176, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 23:47:16', 4.00),
(177, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-25 23:47:16', 310.39),
(178, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-25 23:47:16', 4.00),
(179, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 08:13:57', 310.39),
(180, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 08:13:57', 4.00),
(181, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 08:13:57', 310.39),
(182, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 08:13:57', 4.00),
(183, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 08:14:52', 314.39),
(184, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 08:14:52', 4.00),
(185, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 08:14:52', 314.39),
(186, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 08:14:52', 4.00),
(187, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 10:54:22', 314.39),
(188, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 10:54:22', 4.00),
(189, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 10:54:22', 314.39),
(190, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 10:54:22', 4.00),
(191, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 10:56:53', 314.39),
(192, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 10:56:53', 4.00),
(193, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 10:56:53', 314.39),
(194, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 10:56:53', 4.00),
(195, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 11:02:49', 314.39),
(196, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 11:02:49', 4.00),
(197, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 11:02:49', 314.39),
(198, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 11:02:49', 4.00),
(199, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 11:05:11', 314.39),
(200, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 11:05:11', 4.00),
(201, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 11:05:11', 314.39),
(202, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 11:05:11', 4.00),
(203, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 11:14:25', 314.39),
(204, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 11:14:25', 4.00),
(205, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 11:14:25', 314.39),
(206, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 11:14:25', 4.00),
(207, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 15:42:37', 314.39),
(208, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 15:42:37', 4.00),
(209, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-28 15:42:37', 314.39),
(210, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-28 15:42:37', 4.00),
(211, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-30 11:14:34', 314.39),
(212, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-30 11:14:34', 4.00),
(213, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-30 11:14:34', 314.39),
(214, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-30 11:14:34', 4.00),
(215, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-30 11:15:39', 314.39),
(216, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-30 11:15:39', 4.00),
(217, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-09-30 11:15:39', 314.39),
(218, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-09-30 11:15:39', 4.00),
(219, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-01 17:34:39', 314.39),
(220, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-01 17:34:39', 4.00),
(221, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-01 17:34:39', 314.39),
(222, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-01 17:34:39', 4.00),
(223, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-01 17:38:06', 314.39),
(224, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-01 17:38:06', 4.00),
(225, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-01 17:38:06', 314.39),
(226, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-01 17:38:06', 4.00),
(227, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-05 11:10:24', 314.39),
(228, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-05 11:10:24', 4.00),
(229, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-05 11:10:24', 314.39),
(230, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-05 11:10:24', 4.00),
(231, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-05 11:15:20', 314.39),
(232, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-05 11:15:20', 4.00),
(233, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-05 11:15:20', 314.39),
(234, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-05 11:15:20', 4.00),
(235, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-05 11:21:23', 314.39),
(236, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-05 11:21:23', 4.00),
(237, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-05 11:21:23', 314.39),
(238, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-05 11:21:23', 4.00),
(239, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-05 11:27:04', 314.39),
(240, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-05 11:27:04', 4.00),
(241, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-05 11:27:04', 314.39),
(242, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-05 11:27:04', 4.00),
(243, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-05 11:30:01', 314.39),
(244, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-05 11:30:01', 4.00),
(245, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-05 11:30:01', 314.39),
(246, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-05 11:30:01', 4.00),
(247, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-05 11:30:10', 314.39),
(248, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-05 11:30:10', 4.00),
(249, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-05 11:30:10', 314.39),
(250, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-05 11:30:10', 4.00),
(251, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-05 11:30:13', 314.39),
(252, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-05 11:30:13', 4.00),
(253, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-05 11:30:13', 314.39),
(254, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-05 11:30:13', 4.00),
(255, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-21 13:24:15', 314.39),
(256, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-21 13:24:15', 4.00),
(257, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-21 13:24:15', 314.39),
(258, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-21 13:24:15', 4.00),
(259, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-21 13:26:46', 314.39),
(260, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-21 13:26:46', 4.00),
(261, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-10-21 13:26:46', 314.39),
(262, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-10-21 13:26:46', 4.00),
(263, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-11-16 10:55:31', 314.39),
(264, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-11-16 10:55:31', 4.00),
(265, 50, '2024-10-21 13:19:35', 0, 0, 0, '2024-11-16 10:55:31', 48.00),
(266, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-11-16 10:55:31', 314.39),
(267, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-11-16 10:55:31', 4.00),
(268, 50, '2024-10-21 13:19:35', 0, 0, 0, '2024-11-16 10:55:31', 48.00),
(269, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-11-16 10:57:29', 312.39),
(270, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-11-16 10:57:29', 4.00),
(271, 50, '2024-10-21 13:19:35', 0, 0, 0, '2024-11-16 10:57:29', 48.00),
(272, 49, '2024-09-23 10:16:04', 1, 0, 0, '2024-11-16 10:57:29', 312.39),
(273, 48, '2024-09-14 16:28:27', 0, 0, 0, '2024-11-16 10:57:29', 4.00),
(274, 50, '2024-10-21 13:19:35', 0, 0, 0, '2024-11-16 10:57:29', 48.00);

-- --------------------------------------------------------

--
-- Structure de la table `parametre_appli`
--

DROP TABLE IF EXISTS `parametre_appli`;
CREATE TABLE IF NOT EXISTS `parametre_appli` (
  `id` int NOT NULL AUTO_INCREMENT,
  `raison_social` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `adresse` text NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `parametre_appli`
--

INSERT INTO `parametre_appli` (`id`, `raison_social`, `logo`, `adresse`, `telephone`, `email`) VALUES
(1, 'caricative', 'LOGO.png', 'Nouakchott', '43455259', 'saleckbaya5@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `permission_requests`
--

DROP TABLE IF EXISTS `permission_requests`;
CREATE TABLE IF NOT EXISTS `permission_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `supervisor_id` int NOT NULL,
  `request_date` datetime NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `date_depart` date NOT NULL,
  `date_retour` date NOT NULL,
  `nb_jours` int NOT NULL,
  `notified` tinyint(1) NOT NULL DEFAULT '0',
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `permission_requests`
--

INSERT INTO `permission_requests` (`id`, `user_id`, `supervisor_id`, `request_date`, `status`, `date_depart`, `date_retour`, `nb_jours`, `notified`, `nom`, `prenom`) VALUES
(30, 2, 1, '2024-07-28 16:47:53', 'Approved', '2024-07-29', '2024-08-01', 2, 1, 'HADRAMI', 'Aziza'),
(31, 2, 1, '2024-07-29 10:59:31', 'Approved', '2024-07-29', '2024-08-02', 3, 1, '', ''),
(32, 2, 1, '2024-08-03 09:22:57', 'Approved', '2024-08-02', '2024-08-12', 7, 1, '', ''),
(33, 1, 1, '2024-09-28 07:32:54', 'Pending', '2024-09-28', '2024-10-06', 6, 0, 'BAYA', 'Saleck'),
(34, 1, 1, '2024-09-28 07:33:42', 'Pending', '2024-09-28', '2024-10-06', 6, 0, 'BAYA', 'Saleck');

-- --------------------------------------------------------

--
-- Structure de la table `personnel`
--

DROP TABLE IF EXISTS `personnel`;
CREATE TABLE IF NOT EXISTS `personnel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `num_departement` int DEFAULT NULL,
  `categorie` varchar(255) DEFAULT NULL,
  `salaire` int DEFAULT NULL,
  `nni_emp` varchar(100) NOT NULL,
  `matricule_emp` varchar(100) NOT NULL,
  `lieu_travail` varchar(40) NOT NULL,
  `fonction` varchar(255) NOT NULL,
  `suphierarchie` varchar(255) NOT NULL,
  `emailsup` varchar(255) NOT NULL,
  `datesaisie` varchar(255) NOT NULL,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `num_departement` (`num_departement`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `personnel`
--

INSERT INTO `personnel` (`id`, `nom`, `prenom`, `photo`, `num_departement`, `categorie`, `salaire`, `nni_emp`, `matricule_emp`, `lieu_travail`, `fonction`, `suphierarchie`, `emailsup`, `datesaisie`, `user_id`) VALUES
(1, 'BAYA', 'Saleck', 'photo_profile_cv.jpg', 47, 'agent', 1000000, 'KJ330KJ', 'K?K ?Z332', 'Nouakchott', 'Directeur Administrative', 'PDG', 'A@gmail.com', '2024-09-26', 1),
(15, 'HADRAMI', 'Aziza', 'icon_parametre-appli.png', 47, 'agent', 200, '', '', '', '', '', '', '', 2),
(16, 'HAWE', 'Deddah', 'icon_manage.png', 47, 'maindoeuvre', 100, '', '', '', '', '', '', '', 3),
(18, 'MAHMOUD', 'Mohamed', 'services_divers.png', 46, 'cadre', 400, '12344', 'GH324JB', 'Nouakchott', ' Chef département achat', 'Mohamed Lemine', 'Med.Lemine@gmail.com', '2024-09-08', 6),
(19, 'BAHIA', 'Mohamed Lemine', 'vente_camera.png', 45, 'cadre', 400000, '352566', 'jb6729B', 'Nouakchott', ' Directeur Administrative', 'Mohamed Choueib', 'mohamed.choueib@gmail.com', '2024-09-08', 7),
(21, 'agent carburant', 'numero 1', 'Cleaning Services Logo.png', 49, 'agent', 100000, '2463737', 'jhx231', 'Chami', ' Agent', 'supérieur_carburant', 'sup_c1@gmail.com', '2024-09-08', 10),
(22, 'CHOUEIB', 'Mohamed', 'daf.jfif', 45, 'cadre', 2147483647, '2463737', 'fxfgxfg', 'Nouakchott', ' Directeur Administrative et financier', 'PDG', 'saleckbaya5@gmail.com', '2024-09-10', 8),
(23, 'Ma9be', 'Mohamed salem', 'ordi.png', 45, 'stagiaire', 0, '1235657', '2J23H44', 'Nouakchott', ' agent achat', 'Mohamed Lemine', 'Med.Lemine@gmail.com', '2024-09-10', 12),
(27, 'SEYIDI', 'Zeinebou', 'v1.png', 51, 'cadre', 3000000, '2463737', 'GH324JB', 'Nouakchott', 'Responsable DRH', 'Mohamed Choueib', 'mohamed.choueib@gmail.com', '2024-09-12', 14);

-- --------------------------------------------------------

--
-- Structure de la table `rhuser`
--

DROP TABLE IF EXISTS `rhuser`;
CREATE TABLE IF NOT EXISTS `rhuser` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `rhuser`
--

INSERT INTO `rhuser` (`id`, `email`, `password`) VALUES
(1, 'saleckbaya5@gmail.com', '$2y$10$YfysN0Cwtr1T6X8TMw5pveqjYukiWzzmrzgpklGBeAVMmOEVvuqBy');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` tinyint NOT NULL,
  `previous_role` int DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `previous_role`, `nom`, `prenom`, `photo`) VALUES
(1, 'saleckbaya5@gmail.com', '$2y$10$Ar/wIcqPaogZ2LR5L5FVHej0FESjtVK1n7irv//M81oTaJ9oCy2Hi', 0, NULL, 'BAYA', 'Saleck', 'photo_profile_cv.jpg'),
(2, 'azizehadrami@gmail.com', '$2y$10$Ar/wIcqPaogZ2LR5L5FVHej0FESjtVK1n7irv//M81oTaJ9oCy2Hi', 2, 2, 'HADRAMI', 'Aziza', 'icon_parametre-appli.png'),
(3, 'deddah@gmail.com', '$2y$10$Ar/wIcqPaogZ2LR5L5FVHej0FESjtVK1n7irv//M81oTaJ9oCy2Hi', -2, 3, 'HAWE', 'Deddah', 'icon_manage.png'),
(6, 'Mohamed@gmail.com', '$2y$10$Ar/wIcqPaogZ2LR5L5FVHej0FESjtVK1n7irv//M81oTaJ9oCy2Hi', 31, 31, 'MAHMOUD', 'Mohamed', 'services_divers.png'),
(7, 'Med.Lemine@gmail.com', '$2y$10$Ar/wIcqPaogZ2LR5L5FVHej0FESjtVK1n7irv//M81oTaJ9oCy2Hi', 21, 21, 'BAHIA', 'Mohamed Lemine', 'vente_camera.png'),
(8, 'mohamed.choueib@gmail.com', '$2y$10$Ar/wIcqPaogZ2LR5L5FVHej0FESjtVK1n7irv//M81oTaJ9oCy2Hi', 4, 4, 'CHOUEIB', 'Mohamed', 'daf.jfif'),
(10, 'c1@gmail.com', '$2y$10$Ar/wIcqPaogZ2LR5L5FVHej0FESjtVK1n7irv//M81oTaJ9oCy2Hi', 5, 5, 'agent carburant', 'numero 1', 'Cleaning Services Logo.png'),
(11, 'sup_c1@gmail.com', '$2y$10$Ar/wIcqPaogZ2LR5L5FVHej0FESjtVK1n7irv//M81oTaJ9oCy2Hi', 51, 51, '', '', NULL),
(12, 'med.salem@gmail.com', '$2y$10$Ar/wIcqPaogZ2LR5L5FVHej0FESjtVK1n7irv//M81oTaJ9oCy2Hi', 22, 22, 'Ma9be', 'Mohamed salem', 'ordi.png'),
(13, 'Banna@gmail.com', '$2y$10$Ar/wIcqPaogZ2LR5L5FVHej0FESjtVK1n7irv//M81oTaJ9oCy2Hi', 1, 1, '', '', NULL),
(14, 'zeinebou.seyidi@gmail.com', '$2y$10$Ar/wIcqPaogZ2LR5L5FVHej0FESjtVK1n7irv//M81oTaJ9oCy2Hi', 7, NULL, 'SEYIDI', 'Zeinebou', 'v1.png');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
