-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 23 juil. 2024 à 22:35
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
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `code` int NOT NULL AUTO_INCREMENT,
  `Titre` varchar(255) NOT NULL,
  `Cle` varchar(255) NOT NULL,
  `salaire` int DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`code`, `Titre`, `Cle`, `salaire`) VALUES
(11, 'ouvrier', 'O', 100),
(10, 'Agent', 'A', 100),
(9, 'cadre', 'C', 300),
(12, 'stagiaire', 'S', 0);

-- --------------------------------------------------------

--
-- Structure de la table `departement`
--

DROP TABLE IF EXISTS `departement`;
CREATE TABLE IF NOT EXISTS `departement` (
  `num_departement` int NOT NULL AUTO_INCREMENT,
  `departement` varchar(255) NOT NULL,
  PRIMARY KEY (`num_departement`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `departement`
--

INSERT INTO `departement` (`num_departement`, `departement`) VALUES
(47, 'Informatique'),
(46, 'Logistique'),
(45, 'Administration');

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
  PRIMARY KEY (`id`),
  KEY `num_departement` (`num_departement`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `personnel`
--

INSERT INTO `personnel` (`id`, `nom`, `prenom`, `photo`, `num_departement`, `categorie`, `salaire`) VALUES
(5, 'BAYA', 'Saleck', 'photo_profile_cv.jpg', 47, 'stagiaire', 0);

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
  PRIMARY KEY (`id`)
) ;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`) VALUES
(1, 'saleckbaya5@gmail.com', '$2y$10$Ti0Ky5dahwW03zRireCORe1INm4IsnSgzMXA96bJweZaBd3S8DOT2', 0),
(2, 'azizehadrami@gmail.com', '$2y$10$YtzdHSAlD9OXzYBSAChN2u250lLa1xBZ21DaXtSF8Bd8ULlaWkBeq', -1),
(3, 'deddah@gmail.com', '$2y$10$18BEW2/.SiP3Yn/kBycD.OxSopk3VDS9QTEcVLgmvsmqhdPt21WG2', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
