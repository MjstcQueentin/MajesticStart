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
('2d9b396e-8c9d-11ee-88aa-00155d5b8d37', 'YouTube', 'https://www.youtube.com/', 'https://www.youtube.com/s/desktop/bd3558ba/img/favicon_96x96.png', NULL),
('2d9b40f7-8c9d-11ee-88aa-00155d5b8d37', 'Bluesky', 'https://bsky.app/', 'https://bsky.app/static/apple-touch-icon.png', NULL),
('2d9b47ce-8c9d-11ee-88aa-00155d5b8d37', 'Instagram', 'https://www.instagram.com/rsrc.php/v3/ys/r/aM-g435MtEX.png', 'https://static.cdninstagram.com/rsrc.php/v3/ys/r/aM-g435MtEX.png', NULL),
('880eba27-8c9d-11ee-88aa-00155d5b8d37', 'Dailymotion', 'https://www.dailymotion.com/', 'https://static1.dmcdn.net/neon/prod/favicons/apple-icon-180x180.e7d328cd765717dda45b13daf846547d.png', NULL),
('ce4ea1ed-8c9c-11ee-88aa-00155d5b8d37', 'X', 'https://twitter.com', 'https://abs.twimg.com/responsive-web/client-web/icon-ios.77d25eba.png', NULL),
('d9a6ab73-8c9d-11ee-88aa-00155d5b8d37', 'leboncoin', 'https://www.leboncoin.fr', 'https://www.leboncoin.fr/favicons/lbc.png', NULL),
('d9a6b3ef-8c9d-11ee-88aa-00155d5b8d37', 'Twitch', 'https://www.twitch.tv', 'https://static.twitchcdn.net/assets/favicon-32-e29e246c157142c94346.png', NULL),
('ed735754-8c9b-11ee-88aa-00155d5b8d37', 'Facebook', 'https://www.facebook.com', 'https://static.xx.fbcdn.net/rsrc.php/v3/y0/r/eFZD1KABzRA.png', NULL);

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
('1506fd50-8c86-11ee-88aa-00155d5b8d37', 'Queer', 1),
('8c018fd1-8c78-11ee-88aa-00155d5b8d37', 'À la Une', 0);

-- --------------------------------------------------------

--
-- Structure de la table `newscategory_has_newssource`
--

CREATE TABLE `newscategory_has_newssource` (
  `newscategory_uuid` varchar(36) NOT NULL,
  `newssource_uuid` varchar(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `newscategory_has_newssource`
--

INSERT INTO `newscategory_has_newssource` (`newscategory_uuid`, `newssource_uuid`) VALUES
('8c018fd1-8c78-11ee-88aa-00155d5b8d37', '14e4e8b0-8c78-11ee-88aa-00155d5b8d37'),
('1506fd50-8c86-11ee-88aa-00155d5b8d37', '4a34da48-8c86-11ee-88aa-00155d5b8d37'),
('1506fd50-8c86-11ee-88aa-00155d5b8d37', '63821acc-8e45-11ee-b37c-00155de63b7d');

-- --------------------------------------------------------

--
-- Structure de la table `newssource`
--

CREATE TABLE `newssource` (
  `uuid` varchar(36) NOT NULL,
  `name` varchar(64) NOT NULL,
  `icon` text,
  `logo` text,
  `rss_feed_url` text NOT NULL,
  `homepage_url` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `newssource`
--

INSERT INTO `newssource` (`uuid`, `name`, `icon`, `logo`, `rss_feed_url`, `homepage_url`) VALUES
('14e4e8b0-8c78-11ee-88aa-00155d5b8d37', 'Franceinfo - Les Titres', 'https://www.francetvinfo.fr/favicon.ico', 'https://www.francetvinfo.fr/assets/common/images/logos/franceinfo-619c7b27.svg', 'https://www.francetvinfo.fr/titres.rss', 'https://www.francetvinfo.fr/'),
('4a34da48-8c86-11ee-88aa-00155d5b8d37', 'Komitid', 'https://www.komitid.fr/wp-content/themes/komitid/imgs/favicon-32x32.png', 'https://www.komitid.fr/wp-content/themes/komitid/imgs/logo_header.png', 'https://www.komitid.fr/feed/', 'https://www.komitid.fr/'),
('63821acc-8e45-11ee-b37c-00155de63b7d', 'Têtu', 'https://tetu.com/wp-content/uploads/2022/03/cropped-logo-te%CC%82tu-1-180x180.png', 'https://tetu.com/wp-content/uploads/2022/03/cropped-logo-te%CC%82tu-1-180x180.png', 'https://tetu.com/feed/', 'https://tetu.com/');

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
('1623d0f1-8e41-11ee-b37c-00155de63b7d', 'X', '/assets/logos/twitter.png', 'https://twitter.com/search', 'q'),
('1623d757-8e41-11ee-b37c-00155de63b7d', 'Yahoo', '/assets/logos/yahoo.png', 'https://fr.search.yahoo.com/search', 'q'),
('1ec8e0df-8e44-11ee-b37c-00155de63b7d', 'Startpage', '/assets/logos/startpage.png', 'https://www.startpage.com/sp/search', 'q'),
('79ae1763-8e40-11ee-b37c-00155de63b7d', 'Google', '/assets/logos/google.png', 'https://www.google.com/search', 'q'),
('8296b5e8-8e41-11ee-b37c-00155de63b7d', 'Ecosia', '/assets/logos/ecosia.png', 'https://www.ecosia.org/search', 'q'),
('8296bc56-8e41-11ee-b37c-00155de63b7d', 'Lilo', '/assets/logos/lilo.png', 'https://search.lilo.org/', 'q'),
('8296c27c-8e41-11ee-b37c-00155de63b7d', 'Wikipedia', '/assets/logos/wikipedia.png', 'https://fr.wikipedia.org/w/index.php', 'search'),
('8296c81c-8e41-11ee-b37c-00155de63b7d', 'YouTube', '/assets/logos/youtube.png', 'https://www.youtube.com/results', 'search_query'),
('90550ccd-8e40-11ee-b37c-00155de63b7d', 'Bing', '/assets/logos/bing.png', 'https://www.bing.com/search', 'q'),
('905512fb-8e40-11ee-b37c-00155de63b7d', 'DuckDuckGo', '/assets/logos/duckduckgo.png', 'https://duckduckgo.com/', 'q'),
('c2e270e4-8c71-11ee-88aa-00155d5b8d37', 'Qwant', '/assets/logos/qwant.png', 'https://www.qwant.com/', 'q'),
('d2045a04-8e40-11ee-b37c-00155de63b7d', 'Brave Search', '/assets/logos/brave.png', 'https://search.brave.com/search', 'q'),
('d204601c-8e40-11ee-b37c-00155de63b7d', 'The Internet Archive', '/assets/logos/archive.png', 'https://archive.org/search', 'query');

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
('default_searchengine', 'c2e270e4-8c71-11ee-88aa-00155d5b8d37'),
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
