-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 22 mai 2025 à 15:34
-- Version du serveur : 8.0.42-0ubuntu0.24.04.1
-- Version de PHP : 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `checkpoint`
--

-- --------------------------------------------------------

--
-- Structure de la table `games`
--

CREATE TABLE `games` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `platform` varchar(255) NOT NULL,
  `release_date` date DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `cover` varchar(512) DEFAULT NULL,
  `is_custom` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `rawg_id` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `games`
--

INSERT INTO `games` (`id`, `name`, `platform`, `release_date`, `category`, `cover`, `is_custom`, `created_at`, `rawg_id`) VALUES
(1, 'ArcHero - Archer Hero', 'Web', '2022-01-01', 'Action', 'https://media.rawg.io/media/screenshots/71e/71eb8f155d3621bfb0701f0cf5e2c139.jpg', 0, '2025-05-20 12:33:45', NULL),
(2, 'Disney\'s Tarzan', 'PC, PlayStation, Nintendo 64', '1999-01-01', 'Platformer, Action', 'https://media.rawg.io/media/screenshots/27d/27d2a0d9be67b1bb77f9b1cf038776f1.jpg', 0, '2025-05-20 12:37:55', NULL),
(3, 'The Legend of Zelda: Skyward Sword', 'Nintendo Switch, Wii U, Wii', '2011-01-01', 'Adventure', 'https://media.rawg.io/media/games/884/884d12f527a9a12b5e486ee1b79ecf7f.jpeg', 0, '2025-05-20 12:58:06', NULL),
(4, 'Pokémon Sword and Shield', 'Nintendo Switch', '2019-01-01', 'Adventure, RPG', 'https://media.rawg.io/media/games/82f/82f7b1424c5ee7eebee83faf694889c5.jpg', 0, '2025-05-20 12:59:21', NULL),
(5, 'The Last of Us Part II', 'PlayStation 5, PlayStation 4', '2020-01-01', 'Shooter, Adventure, Action', 'https://media.rawg.io/media/games/909/909974d1c7863c2027241e265fe7011f.jpg', 0, '2025-05-20 13:55:19', NULL),
(6, 'Final Fantasy X', 'PlayStation 2', '2001-01-01', 'RPG', 'https://media.rawg.io/media/games/ddc/ddc65c56f16bc3effb8d2645b095a8c5.jpg', 0, '2025-05-20 14:20:44', NULL),
(7, 'Grand Theft Auto VI', 'PlayStation 5, Xbox Series S/X', '2026-01-01', 'Action', 'https://media.rawg.io/media/games/734/7342a1cd82c8997ec620084ae4c2e7e4.jpg', 0, '2025-05-20 14:33:32', NULL),
(8, 'Apex Legends', 'PC, Xbox One, PlayStation 4, Nintendo Switch, macOS', '2019-01-01', 'Shooter, Action', 'https://media.rawg.io/media/games/737/737ea5662211d2e0bbd6f5989189e4f1.jpg', 0, '2025-05-20 15:22:12', NULL),
(9, 'TEKKEN 7', 'PC, Xbox One, PlayStation 4', '2015-01-01', 'Action, Fighting, Sports', 'https://media.rawg.io/media/games/62b/62b035add7205737540d66e082b85930.jpg', 0, '2025-05-20 15:38:26', NULL),
(10, 'Dragon Ball Z: Budokai 3', 'PlayStation 2', '2004-01-01', 'Fighting', 'https://media.rawg.io/media/screenshots/2f5/2f5aee5d08cd1676c74a62f515719d47.jpeg', 0, '2025-05-20 15:38:51', NULL),
(11, 'Dofus', 'PC', '2005-01-01', 'Massively Multiplayer, RPG', 'https://media.rawg.io/media/screenshots/31e/31eefd7dbb20e994c8762221599702bc.jpg', 0, '2025-05-20 15:42:35', NULL),
(12, 'Call of Duty: Modern Warfare 2', 'PC, Xbox One, macOS, Xbox 360, PlayStation 3', '2009-01-01', 'Shooter', 'https://media.rawg.io/media/games/9af/9af24c1886e2c7b52a4a2c65aa874638.jpg', 0, '2025-05-20 15:45:17', NULL),
(13, 'Star Wars Jedi: Fallen Order', 'PC, PlayStation 5, Xbox One, PlayStation 4', '2019-01-01', 'Adventure, Action', 'https://media.rawg.io/media/games/559/559bc0768f656ad0c63c54b80a82d680.jpg', 0, '2025-05-20 15:45:47', NULL),
(14, 'Palworld', 'PC, Xbox Series S/X', '2024-01-01', 'Indie, Adventure, Action, RPG', 'https://media.rawg.io/media/games/4e9/4e9c951414c732923fa72d5b1da49402.jpg', 0, '2025-05-20 15:46:10', NULL),
(15, 'Red Dead Redemption 2', 'PC, Xbox One, PlayStation 4', '2018-01-01', 'Action', 'https://media.rawg.io/media/games/511/5118aff5091cb3efec399c808f8c598f.jpg', 0, '2025-05-20 15:46:40', NULL),
(16, 'Animal Crossing: New Horizons', 'Nintendo Switch', '2020-01-01', 'Simulation', 'https://media.rawg.io/media/games/42f/42fe1abd4d7c11ca92d93a0fb0f8662b.jpg', 0, '2025-05-20 15:47:03', NULL),
(17, 'Crash Team Racing', 'PlayStation 3, PlayStation, PSP', '1999-01-01', 'Racing, Action', 'https://media.rawg.io/media/screenshots/f8c/f8c6b111e074502aa8da71476885eec8.jpg', 0, '2025-05-20 15:48:17', NULL),
(18, 'Far Cry 4', 'PC, Xbox One, PlayStation 4, Xbox 360, PlayStation 3', '2014-01-01', 'Shooter', 'https://media.rawg.io/media/games/b39/b396dac1f3e0f538841aa0355dd066d3.jpg', 0, '2025-05-20 15:51:27', NULL),
(19, 'Age of Empires II: Age of Kings', 'PC, PlayStation 2', '1999-01-01', 'Strategy', 'https://media.rawg.io/media/screenshots/e90/e90a4f0b878b0206888a56c4155705c8.jpg', 0, '2025-05-21 11:43:11', NULL),
(20, 'Death Stranding 2: On The Beach', 'PlayStation 5', '2025-01-01', 'Adventure, Action', 'https://media.rawg.io/media/games/b85/b85bc300d42588af66fb516b7563f74f.jpg', 0, '2025-05-21 14:56:16', NULL),
(21, 'Cyberpunk 2077', 'PC, PlayStation 5, Xbox One, PlayStation 4, Xbox Series S/X', '2020-01-01', 'Shooter, Action, RPG', 'https://media.rawg.io/media/games/26d/26d4437715bee60138dab4a7c8c59c92.jpg', 0, '2025-05-21 14:56:53', NULL),
(22, 'The Siege And The Sandfox', 'PlayStation 4', '2025-05-20', 'Action', 'https://media.rawg.io/media/screenshots/8eb/8eb2d39fe7b9d0960d0fa6eabade59a5.jpg', 0, '2025-05-22 12:44:25', NULL),
(23, 'Fantasy Life i', 'PlayStation 5', '2025-05-21', 'RPG', 'https://media.rawg.io/media/games/545/5450b378ca92da5ae4cff62227be25e6.jpg', 0, '2025-05-22 13:28:05', NULL),
(24, 'Tainted Grail: The Fall of Avalon', 'PC', '2025-01-01', 'Action', 'https://media.rawg.io/media/games/c73/c73cd3a8bc46ccd7d62621bc19766f01.jpg', 0, '2025-05-22 14:25:32', NULL),
(25, 'Onimusha 2: Samurai\'s Destiny (Remaster)', 'Nintendo Switch', '2025-01-01', 'Action', 'https://media.rawg.io/media/screenshots/89a/89a693cf30b2dd35e7c446e8d1a88b7c.jpg', 0, '2025-05-22 14:25:50', NULL),
(26, 'Elden Ring Nightreign', 'Xbox Series S/X', '2025-01-01', 'Action', 'https://media.rawg.io/media/games/a14/a143ef815d323a3000840fc774d834c7.jpg', 0, '2025-05-22 14:28:11', NULL),
(27, 'Deliver At All Costs', 'PC', '2025-01-01', 'Action', 'https://media.rawg.io/media/screenshots/861/8615eaa9bef077298ed45fe856f6de4b.jpg', 0, '2025-05-22 14:30:24', NULL),
(28, 'Celeste', 'PC, Xbox One, PlayStation 4, Nintendo Switch, macOS, Linux', '2018-01-01', 'Platformer, Indie', 'https://media.rawg.io/media/games/594/59487800889ebac294c7c2c070d02356.jpg', 0, '2025-05-22 14:42:55', NULL),
(29, 'Rune Factory: Guardians of Azuma', 'Nintendo Switch', '2025-01-01', 'Action', '', 0, '2025-05-22 14:44:04', NULL),
(30, 'Japanese Drift Master', 'PC', '2025-05-21', 'Simulation', 'https://media.rawg.io/media/screenshots/126/12610d87e6927c3d93ba62613bfabfd5.jpg', 0, '2025-05-22 14:55:25', NULL),
(31, 'Japanese Drift Master', 'PC', '2025-05-21', 'Simulation', 'https://media.rawg.io/media/screenshots/126/12610d87e6927c3d93ba62613bfabfd5.jpg', 0, '2025-05-22 15:21:47', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `game_stats`
--

CREATE TABLE `game_stats` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `game_id` int NOT NULL,
  `play_time` int DEFAULT '0',
  `progress` int DEFAULT '0',
  `status` varchar(20) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `game_stats`
--

INSERT INTO `game_stats` (`id`, `user_id`, `game_id`, `play_time`, `progress`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(3, 1, 3, 200, 0, 'termine', '', '2025-05-20 12:58:06', NULL),
(4, 1, 4, 200, 0, 'complete', '', '2025-05-20 12:59:21', NULL),
(5, 1, 5, 3333, 0, 'termine', '', '2025-05-20 13:55:19', NULL),
(6, 1, 6, 3333, 0, 'complete', 'sdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdfsdsdfdsfsdf', '2025-05-20 14:20:44', NULL),
(7, 1, 8, 3333, 0, 'en cours', '', '2025-05-20 15:22:12', NULL),
(8, 1, 9, 200, 0, 'en cours', '', '2025-05-20 15:38:26', NULL),
(9, 1, 10, 234, 0, 'complete', '', '2025-05-20 15:38:51', NULL),
(10, 1, 11, 23333, 0, 'complete', '', '2025-05-20 15:42:35', NULL),
(11, 1, 12, 3333, 0, 'abandonne', '', '2025-05-20 15:45:17', NULL),
(12, 1, 13, 115, 0, 'termine', '', '2025-05-20 15:45:47', NULL),
(13, 1, 14, 328, 0, 'termine', '', '2025-05-20 15:46:10', NULL),
(14, 1, 15, 400, 0, 'termine', '', '2025-05-20 15:46:40', NULL),
(15, 1, 16, 145, 0, 'termine', '', '2025-05-20 15:47:03', NULL),
(16, 1, 17, 200, 0, 'termine', '', '2025-05-20 15:48:17', NULL),
(17, 1, 18, 56, 0, 'complete', '', '2025-05-20 15:51:27', NULL),
(18, 1, 19, 544, 0, 'en cours', '', '2025-05-21 11:43:11', NULL),
(19, 1, 28, 2000, 0, 'complete', '', '2025-05-22 14:42:55', NULL),
(20, 1, 30, 0, 0, 'souhaité', NULL, '2025-05-22 15:27:34', NULL),
(21, 1, 25, 0, 0, 'acheté', NULL, '2025-05-22 15:27:53', NULL),
(23, 1, 27, 0, 0, 'acheté', NULL, '2025-05-22 15:28:27', NULL),
(25, 1, 26, 0, 0, 'souhaité', NULL, '2025-05-22 15:29:33', NULL),
(26, 1, 24, 0, 0, 'souhaité', NULL, '2025-05-22 15:33:38', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'images/default-profile.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`, `profile_picture`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$R8z2Ldd5iJJeS5fTP//q.OqrDuiidoh4Agp5uHCfMZkZ3QjdCIkQu', '2025-05-20 10:28:07', '2025-05-20 11:16:36', 'uploads/profile_pictures/1747746996_b91dc773372d7a9ec254.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `user_top_games`
--

CREATE TABLE `user_top_games` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `game_id` int NOT NULL,
  `position` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user_top_games`
--

INSERT INTO `user_top_games` (`id`, `user_id`, `game_id`, `position`, `created_at`) VALUES
(6, 1, 8, 1, '2025-05-20 15:25:13'),
(7, 1, 6, 2, '2025-05-20 15:25:13'),
(8, 1, 4, 3, '2025-05-20 15:25:13'),
(9, 1, 5, 4, '2025-05-20 15:25:13'),
(10, 1, 3, 5, '2025-05-20 15:25:13');

-- --------------------------------------------------------

--
-- Structure de la table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `game_id` int NOT NULL,
  `status` enum('souhaité','acheté','joué') DEFAULT 'souhaité',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `game_id`, `status`, `created_at`) VALUES
(2, 1, 7, 'souhaité', '2025-05-20 14:34:04'),
(3, 1, 20, 'souhaité', '2025-05-21 14:56:16'),
(4, 1, 21, 'acheté', '2025-05-21 14:56:53');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_platform` (`platform`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_games_rawg_id` (`rawg_id`);

--
-- Index pour la table `game_stats`
--
ALTER TABLE `game_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_game` (`user_id`,`game_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `game_id` (`game_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `user_top_games`
--
ALTER TABLE `user_top_games`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_position` (`user_id`,`position`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Index pour la table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_id` (`game_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `games`
--
ALTER TABLE `games`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT pour la table `game_stats`
--
ALTER TABLE `game_stats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `user_top_games`
--
ALTER TABLE `user_top_games`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `game_stats`
--
ALTER TABLE `game_stats`
  ADD CONSTRAINT `game_stats_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_stats_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_top_games`
--
ALTER TABLE `user_top_games`
  ADD CONSTRAINT `user_top_games_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_top_games_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
