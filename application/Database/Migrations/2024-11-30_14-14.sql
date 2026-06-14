-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : sam. 30 nov. 2024 à 14:14
-- Version du serveur : 8.0.40-0ubuntu0.22.04.1
-- Version de PHP : 8.1.2-1ubuntu2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `homepage`
--

-- --------------------------------------------------------

--
-- Structure de la table `planned_event`
--

CREATE TABLE `planned_event` (
  `id` int NOT NULL,
  `topic_name` text NOT NULL,
  `topic_link_or_query` text NOT NULL,
  `picture_url` text NOT NULL,
  `picture_author` text NOT NULL,
  `picture_author_url` text NOT NULL,
  `picture_place` text NOT NULL,
  `from_date` varchar(4) NOT NULL COMMENT 'Date au format MMDD',
  `until_date` varchar(4) NOT NULL COMMENT 'Date au format MMDD'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `planned_event`
--
ALTER TABLE `planned_event`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `planned_event`
--
ALTER TABLE `planned_event`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
