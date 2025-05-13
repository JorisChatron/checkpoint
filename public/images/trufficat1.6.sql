-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 06 mai 2025 à 15:36
-- Version du serveur : 8.0.42-0ubuntu0.22.04.1
-- Version de PHP : 8.1.2-1ubuntu2.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `trufficat`
--

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text,
  `animal` enum('chien','chat') NOT NULL,
  `categorie` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `is_vedette` tinyint(1) DEFAULT '0',
  `age` enum('junior','adulte','senior') DEFAULT NULL,
  `saveur` varchar(100) DEFAULT NULL,
  `sans_cereales` tinyint(1) DEFAULT NULL,
  `sterilise` tinyint(1) DEFAULT NULL,
  `marque` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `description`, `animal`, `categorie`, `image`, `prix`, `is_vedette`, `age`, `saveur`, `sans_cereales`, `sterilise`, `marque`) VALUES
(1, 'Os en caoutchouc', 'Un jouet solide pour chien.', 'chien', '', 'chien1.jpg', '0.00', 1, NULL, NULL, NULL, NULL, NULL),
(2, 'Griffoir design', 'Un griffoir stylé pour votre matou.', 'chat', '', 'chat1.jpg', '0.00', 1, NULL, NULL, NULL, NULL, NULL),
(3, 'Croquettes premium', 'Aliment complet pour chien actif.', 'chien', '', 'chien2.jpg', '0.00', 1, NULL, NULL, NULL, NULL, NULL),
(4, 'Jouet plume', 'Jouet interactif pour chat joueur.', 'chat', '', 'chat2.jpg', '0.00', 1, NULL, NULL, NULL, NULL, NULL),
(5, 'Os en caoutchouc', 'Un jouet solide pour chien.', 'chien', '', 'chien1.jpg', '0.00', 1, NULL, NULL, NULL, NULL, NULL),
(6, 'Griffoir design', 'Un griffoir stylé pour votre matou.', 'chat', '', 'chat1.jpg', '0.00', 1, NULL, NULL, NULL, NULL, NULL),
(7, 'Croquettes premium', 'Aliment complet pour chien actif.', 'chien', '', 'chien2.jpg', '0.00', 1, NULL, NULL, NULL, NULL, NULL),
(8, 'Jouet plume', 'Jouet interactif pour chat joueur.', 'chat', '', 'chat2.jpg', '0.00', 1, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `publicites`
--

CREATE TABLE `publicites` (
  `id` int NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `publicites`
--

INSERT INTO `publicites` (`id`, `titre`, `description`, `image`, `url`, `alt_text`) VALUES
(1, 'Publicité 1', 'Balade', 'pub1.png', 'https://exemple.com/promo1', 'Publicité promo friandises'),
(2, 'Publicité 2', 'Anti-parasites', 'pub2.png', 'https://exemple.com/promo2', 'Publicité jouets pour chats'),
(3, 'Publicité 3', 'Promo sur les croquettes sheba', 'pub3.png', 'https://exemple.com/promo3', 'Publicité promo croquettes sheba'),
(4, 'Publicité 4', 'Pub panier', 'pub4.png', 'https://exemple.com/promo4', 'Publicité panier');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `publicites`
--
ALTER TABLE `publicites`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `publicites`
--
ALTER TABLE `publicites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
