-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mer. 06 déc. 2023 à 20:15
-- Version du serveur : 8.0.35-0ubuntu0.22.04.1
-- Version de PHP : 8.1.2-1ubuntu2.14

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
-- Structure de la table `bookmark`
--

CREATE TABLE `bookmark` (
  `uuid` varchar(36) NOT NULL,
  `name` text NOT NULL,
  `url` text NOT NULL,
  `icon` text NOT NULL,
  `user_id` varchar(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `bookmark`
--

INSERT INTO `bookmark` (`uuid`, `name`, `url`, `icon`, `user_id`) VALUES
('2d9b396e-8c9d-11ee-88aa-00155d5b8d37', 'Les Majesticiels', 'https://www.lesmajesticiels.org/', 'https://www.lesmajesticiels.org/assets/logos/lesmajesticiels_icon.png', NULL)

-- --------------------------------------------------------

--
-- Structure de la table `newscategory`
--

CREATE TABLE `newscategory` (
  `uuid` varchar(36) NOT NULL,
  `title_fr` text NOT NULL,
  `display_order` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `newscategory`
--

INSERT INTO `newscategory` (`uuid`, `title_fr`, `display_order`) VALUES
('8c018fd1-8c78-11ee-88aa-00155d5b8d37', 'À la Une', 0);

-- --------------------------------------------------------

--
-- Structure de la table `newscategory_has_newssource`
--

CREATE TABLE `newscategory_has_newssource` (
  `newscategory_uuid` varchar(36) NOT NULL,
  `newssource_uuid` varchar(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `newssource`
--

CREATE TABLE `newssource` (
  `uuid` varchar(36) NOT NULL,
  `name` varchar(64) NOT NULL,
  `logo` text,
  `logo_invertable` TINYINT(1) NOT NULL DEFAULT '1',
  `rss_feed_url` text NOT NULL,
  `access_ok` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `searchengine`
--

CREATE TABLE `searchengine` (
  `uuid` varchar(36) NOT NULL,
  `name` varchar(64) NOT NULL,
  `icon` text,
  `result_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `query_param` varchar(16) DEFAULT 'q'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `searchengine`
--

INSERT INTO `searchengine` (`uuid`, `name`, `icon`, `result_url`, `query_param`) VALUES
('1623d0f1-8e41-11ee-b37c-00155de63b7d', 'Actualités du projet Les Majesticiels', '/assets/logos/TheMajesticProject_icon.png', 'https://www.lesmajesticiels.org/blog/search', 'query')

-- --------------------------------------------------------

--
-- Structure de la table `setting`
--

CREATE TABLE `setting` (
  `name` varchar(64) NOT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `setting`
--

INSERT INTO `setting` (`name`, `value`) VALUES
('default_searchengine', '1623d0f1-8e41-11ee-b37c-00155de63b7d'),
('photo_author', 'Ian Chen'),
('photo_author_url', 'https://unsplash.com/fr/@ymchen'),
('photo_place', 'Port Stephens, Australia'),
('photo_url', 'https://images.unsplash.com/photo-1700833533652-2ebae7f13f47');

-- --------------------------------------------------------

--
-- Structure de la table `topic`
--

CREATE TABLE `topic` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `link_or_query` text NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_official` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `majesticloud_user_id` varchar(36) NOT NULL,
  `majesticloud_session_token` text NOT NULL,
  `set_searchengine` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `set_newssources` json DEFAULT NULL,
  `set_newscategories` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `bookmark`
--
ALTER TABLE `bookmark`
  ADD PRIMARY KEY (`uuid`);

--
-- Index pour la table `newscategory`
--
ALTER TABLE `newscategory`
  ADD PRIMARY KEY (`uuid`);

--
-- Index pour la table `newscategory_has_newssource`
--
ALTER TABLE `newscategory_has_newssource`
  ADD KEY `newscategory_uuid` (`newscategory_uuid`),
  ADD KEY `newssource_uuid` (`newssource_uuid`);

--
-- Index pour la table `newssource`
--
ALTER TABLE `newssource`
  ADD PRIMARY KEY (`uuid`);

--
-- Index pour la table `searchengine`
--
ALTER TABLE `searchengine`
  ADD PRIMARY KEY (`uuid`);

--
-- Index pour la table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`name`);

--
-- Index pour la table `topic`
--
ALTER TABLE `topic`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`majesticloud_user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `topic`
--
ALTER TABLE `topic`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `newscategory_has_newssource`
--
ALTER TABLE `newscategory_has_newssource`
  ADD CONSTRAINT `newscategory_has_newssource_ibfk_1` FOREIGN KEY (`newscategory_uuid`) REFERENCES `newscategory` (`uuid`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `newscategory_has_newssource_ibfk_2` FOREIGN KEY (`newssource_uuid`) REFERENCES `newssource` (`uuid`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
