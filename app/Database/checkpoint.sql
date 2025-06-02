-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : lun. 02 juin 2025 à 12:49
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
  `developer` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `cover` varchar(512) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `rawg_id` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

-- --------------------------------------------------------

--
-- Structure de la table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `game_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT pour la table `game_stats`
--
ALTER TABLE `game_stats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

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
