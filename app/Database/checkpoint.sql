-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : lun. 19 mai 2025 à 15:00
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `games`
--

INSERT INTO `games` (`id`, `name`, `platform`, `release_date`, `category`, `cover`, `is_custom`, `created_at`) VALUES
(7, 'SpellForce 2 - Demons of the Past', 'PS4', '2014-01-01', 'Strategy, RPG', 'https://media.rawg.io/media/screenshots/99f/99f25e78d6f3b9db7208dab329df0988.jpg', 0, '2025-05-16 13:08:48'),
(8, 'Final Fantasy X-2', 'sqdsq', '2003-01-01', 'RPG', 'https://media.rawg.io/media/games/336/3369c92ff7d489437dc7d337576db9ac.jpg', 0, '2025-05-16 13:25:41'),
(9, 'Final Fantasy IX', 'PS4', '2000-01-01', 'Adventure, RPG', 'https://media.rawg.io/media/games/826/82626e2d7ee7d96656fb9838c2ef7302.jpg', 0, '2025-05-16 13:32:34'),
(10, 'The Last of Us Part II', 'PS3', '2020-01-01', 'Shooter, Adventure, Action', 'https://media.rawg.io/media/games/909/909974d1c7863c2027241e265fe7011f.jpg', 0, '2025-05-16 13:35:13'),
(11, 'The Godfather II', 'PS3', '2009-01-01', 'Strategy, Action', 'https://media.rawg.io/media/games/b0c/b0c8c0f53be36b49140e66371daba4a7.jpg', 0, '2025-05-16 13:37:08'),
(12, 'Minecraft', 'PC', '2009-01-01', 'Action, Arcade, Simulation, Indie, Massively Multiplayer', 'https://media.rawg.io/media/games/b4e/b4e4c73d5aa4ec66bbf75375c4847a2b.jpg', 0, '2025-05-16 13:40:33'),
(13, 'Archero', 'PS4', '2019-01-01', 'Adventure, Action, RPG, Casual, Indie', 'https://media.rawg.io/media/screenshots/cb0/cb02c127fec15c996ccf5340bbbd2180.jpg', 0, '2025-05-16 13:41:51'),
(14, 'The Sims 2', 'PS4', '2004-01-01', 'Simulation', 'https://media.rawg.io/media/games/d26/d263d8d035027185193ddd253a6e3479.jpg', 0, '2025-05-16 13:45:14'),
(15, 'Final Fantasy X', 'PS3', '2001-01-01', 'RPG', 'https://media.rawg.io/media/games/ddc/ddc65c56f16bc3effb8d2645b095a8c5.jpg', 0, '2025-05-16 13:46:01'),
(16, 'Archero 2', 'PS4', '2025-01-01', 'Casual, Arcade', 'https://media.rawg.io/media/screenshots/42d/42dee05b09a308522f920e48dedf681b.jpeg', 0, '2025-05-16 13:54:31'),
(17, 'Archero 2', 'android', '2025-01-01', 'Casual, Arcade', 'https://media.rawg.io/media/screenshots/42d/42dee05b09a308522f920e48dedf681b.jpeg', 0, '2025-05-16 13:55:08'),
(18, 'DS: Dimensional Shooters', 'android', '2022-01-01', 'Action', 'https://media.rawg.io/media/screenshots/94c/94c72004aa1b7b60985cde8c9250b777.jpg', 0, '2025-05-16 14:24:02'),
(19, 'Apex Legends', 'PC', '2019-01-01', 'Shooter, Action', 'https://media.rawg.io/media/games/737/737ea5662211d2e0bbd6f5989189e4f1.jpg', 0, '2025-05-16 14:25:16'),
(20, 'Disney\'s Hercules: The Action Game', 'PS1', '1997-01-01', 'Adventure, Action', 'https://media.rawg.io/media/games/fa4/fa470a3732f428efb79ca5ce24854253.jpg', 0, '2025-05-19 14:40:04'),
(21, 'KP Thunder', 'android', '2021-01-01', 'Platformer', 'https://media.rawg.io/media/screenshots/2bb/2bbad41cff85a67b4c8e884aafa4ddd5.jpg', 0, '2025-05-19 14:45:01'),
(22, 'SS-203x', 'PS4', '2017-01-01', '', 'https://media.rawg.io/media/screenshots/6ca/6ca00520895c10d1ebd87b4ce2b6af7e.jpg', 0, '2025-05-19 14:50:32'),
(23, 'High Archer - Archery Game', 'android', '2017-01-01', 'Arcade, Action', 'https://media.rawg.io/media/screenshots/f8e/f8ee8c05a00dd62015785e1de995841e.jpg', 0, '2025-05-19 14:51:58'),
(24, 'SSX on Tour', 'PS', '2005-01-01', 'Racing', 'https://media.rawg.io/media/games/ce8/ce8655a7e3d6b7a73963df7539221797.jpg', 0, '2025-05-19 14:54:18'),
(25, 'SSX on Tour', 'PS2', '2005-01-01', 'Racing', 'https://media.rawg.io/media/games/ce8/ce8655a7e3d6b7a73963df7539221797.jpg', 0, '2025-05-19 14:54:26'),
(26, 'SSX on Tour', 'ps4', '2005-01-01', 'Racing', 'https://media.rawg.io/media/games/ce8/ce8655a7e3d6b7a73963df7539221797.jpg', 0, '2025-05-19 14:54:54');

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
(12, 9, 10, 23, 0, 'termine', 'sdsqd', '2025-05-16 13:35:13', NULL),
(20, 9, 14, 200, 0, 'termine', 'sddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsddddddddddddddsdddddddddddddd', '2025-05-16 13:45:14', NULL),
(21, 9, 15, 200, 0, 'complete', 'mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm', '2025-05-16 13:46:01', NULL),
(23, 10, 16, 23, 0, 'en cours', 'd', '2025-05-16 13:54:31', NULL),
(24, 9, 17, 23, 0, 'en cours', 'dddd', '2025-05-16 13:55:08', NULL),
(25, 9, 18, 23, 0, 'en cours', 'ffff', '2025-05-16 14:24:02', NULL),
(26, 9, 19, 3333, 0, 'en cours', 'fpsssssss', '2025-05-16 14:25:16', NULL),
(27, 9, 25, 3333, 0, 'termine', 'ddd', '2025-05-19 15:00:04', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `game_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `profile_picture` varchar(255) DEFAULT 'images/default-profile.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `profile_picture`) VALUES
(9, 'admin', 'admin@gmail.com', '$2y$10$qOH8JM8lVzsbewcJwydWZ.i.0aSVu4D.4fRV.SyD1TUcb9AHdzudS', '2025-05-15 10:33:35', 'uploads/profile_pictures/1747312427_e1db9f6fc67db688c35a.png'),
(10, 'user', 'user@gmail.com', '$2y$10$qyJtB9NeZ77Z3khgG17pgObtQvcdbNRaKDJT7P88HXdzVEGni9sYu', '2025-05-15 10:34:07', 'uploads/profile_pictures/1747312464_1e1390f62c6478a1ef05.jpg');

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
-- Index pour les tables déchargées
--

--
-- Index pour la table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `game_stats`
--
ALTER TABLE `game_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_game` (`user_id`,`game_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `game_stats`
--
ALTER TABLE `game_stats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
