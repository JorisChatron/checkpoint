-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : dim. 29 juin 2025 à 09:59
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

--
-- Déchargement des données de la table `games`
--

INSERT INTO `games` (`id`, `name`, `platform`, `release_date`, `category`, `developer`, `publisher`, `cover`, `created_at`, `rawg_id`) VALUES
(161, 'Mario Kart Tour', 'iOS', '2019-09-25', 'Action', 'Nintendo', 'Nintendo', 'https://media.rawg.io/media/games/2d5/2d57e7ffa1e3af2fa34229bd1041461d.jpg', '2025-06-06 12:54:14', '316823'),
(162, 'Momodora: Reverie Under the Moonlight', 'Xbox One', '2016-03-03', 'Action', 'ACTIVE GAMING MEDIA, Bombservice, rdein', 'AGM PLAYISM, Playism, Active Gaming Media, DANGEN Entertainment', 'https://media.rawg.io/media/games/302/3021f937f82274320bb2758a0a9bccf6.jpg', '2025-06-06 12:55:13', '161'),
(163, 'AR-K: The Great Escape', 'macOS', '2015-07-14', 'Adventure', 'Gato Salvaje', 'Gato Salvaje', 'https://media.rawg.io/media/screenshots/e35/e35003404d3a8c592a0999e5c370e542.jpg', '2025-06-06 13:04:48', '16947'),
(164, 'FINAL FANTASY VI', 'SNES', '1994-04-02', 'RPG', 'Square Enix, Sony Interactive Entertainment, Square', 'Square Enix, Sony Computer Entertainment, Square', 'https://media.rawg.io/media/games/98c/98c87b286cd2a2ba942167df384a9bd3.jpg', '2025-06-06 13:05:01', '1063'),
(165, 'Fear Effect Sedna', 'PC', '2018-03-06', 'Action', 'Square Enix, Sushee', 'Square Enix, Forever Entertainment', 'https://media.rawg.io/media/games/6f9/6f9bf1251496f71e962ec6e2d542e393.jpg', '2025-06-06 13:05:20', '10377'),
(166, 'You Are Tre', 'PC', '2023-05-12', 'Adventure', 'Nocean Studio', 'Nocean Studio', 'https://media.rawg.io/media/screenshots/886/886ba1b49c29ae7f23dcb25fddb26ce2.jpg', '2025-06-06 13:05:35', '960775'),
(167, 'Cauldron Caution', 'Linux', '2025-06-02', 'Adventure', 'Poisheesh', 'Poisheesh', 'https://media.rawg.io/media/screenshots/423/42384aefac309f1dd6a75decb81e8749.jpg', '2025-06-06 13:26:12', '1004352'),
(168, 'Zelda', 'Web', '2023-02-06', 'Inconnu', 'agizoni', NULL, 'https://media.rawg.io/media/screenshots/caf/caf15f0938b2a58aa046a2007ad7d4ad.jpg', '2025-06-06 13:29:19', '924900'),
(169, 'Marmot Fight Club', 'PC', '2025-05-26', 'Action', 'rentaka', 'rentaka', 'https://media.rawg.io/media/screenshots/987/987a6fe16684d16f4cc39c2b5d6f4165.jpg', '2025-06-06 13:44:37', '1004101'),
(170, 'Capture Nexus', 'PC', '2025-06-02', 'Action', 'Rayleigh Entertainment', 'Rayleigh Entertainment', 'https://media.rawg.io/media/screenshots/c48/c489763759216de9b3e4b309367db95a.jpg', '2025-06-06 13:44:49', '1004322'),
(171, 'Harry Potter: Hogwarts Mystery', 'iOS', '2018-04-25', 'Adventure', 'Jam City', 'Jam City', 'https://media.rawg.io/media/games/04c/04c5980dc9c0091654752957f94dc944.jpg', '2025-06-06 13:47:50', '58277'),
(172, 'Hogwarts Stories', 'PC', '2021-02-09', 'Action', 'maridany', NULL, 'https://media.rawg.io/media/screenshots/a05/a05f3ba032ca453c3ff790a3c1b98698.jpg', '2025-06-06 13:48:09', '556872'),
(173, 'Blightspire', 'Linux', '2025-06-02', 'Action', 'Bubonic Brotherhood', 'Breda University of Applied Sciences', 'https://media.rawg.io/media/screenshots/867/867709d1dd2850238305c979d30861c0.jpg', '2025-06-06 13:50:28', '1004360'),
(174, 'Apocalypse Rush', 'PC', '2025-06-02', 'Action', 'Lost Guys', 'Lost Guys', 'https://media.rawg.io/media/screenshots/0d4/0d4308c1229df0bcaa8a93614a4b5d25.jpg', '2025-06-06 13:50:31', '1004351'),
(175, 'Death Stranding', 'PC', '2019-11-08', 'Action', 'Kojima Productions', 'Sony Interactive Entertainment, 505 Games', 'https://media.rawg.io/media/games/2ad/2ad87a4a69b1104f02435c14c5196095.jpg', '2025-06-06 14:01:39', '50738'),
(176, 'UZG', 'PC', '2025-06-02', 'Action', 'Suppe', 'Suppe', 'https://media.rawg.io/media/screenshots/8a2/8a2f2204a13f3f9059082604f62b5770.jpg', '2025-06-06 15:06:13', '1004340'),
(177, 'Cardrottini', 'PC', '2025-06-02', 'Strategy', 'Don Bobritto', 'Bandito', 'https://media.rawg.io/media/screenshots/f9e/f9eef0385280ba218f99e9fdf40818f6.jpg', '2025-06-06 15:06:44', '1004335'),
(178, 'Terminal One', 'PC', '2025-06-02', 'Strategy', 'Terminal Reality', 'Terminal Reality', 'https://media.rawg.io/media/screenshots/56d/56d876066eae38aa079428b7be945d54.jpg', '2025-06-06 15:11:15', '1004333'),
(179, 'The Alters', 'Xbox Series S/X', '2025-06-13', 'Adventure', '11 Bit Studios', '11 bit studios', 'https://media.rawg.io/media/games/457/457f5a7f204b1bb3dead55aed1b2738f.jpg', '2025-06-09 11:08:00', '801122'),
(180, 'Dropcore', 'PC', '2025-06-02', 'Action', 'Wain Interactive', 'Wain Interactive', 'https://media.rawg.io/media/screenshots/613/613a233933396083e852bb62d0c6e040.jpg', '2025-06-09 11:08:53', '1004355'),
(181, 'Aze\'s Error', 'PC', '2019-05-02', 'Adventure', 'Fall From Grace Studio', NULL, 'https://media.rawg.io/media/screenshots/7dc/7dc92502c1623eafb05c53558908c5e5.jpg', '2025-06-09 11:11:05', '314088'),
(182, 'Grand Theft Auto VI', 'Xbox Series S/X', '2026-05-26', 'Action', 'Rockstar Games', 'Rockstar Games', 'https://media.rawg.io/media/games/734/7342a1cd82c8997ec620084ae4c2e7e4.jpg', '2025-06-10 11:04:07', '972995'),
(183, 'Hogwarts Legacy', 'PC', '2023-02-10', 'Action', 'Avalanche Software, Portkey Games', 'Warner Bros. Interactive, Wizarding World', 'https://media.rawg.io/media/games/044/044b2ee023930ca138deda151f40c18c.jpg', '2025-06-10 11:10:11', '906547'),
(184, 'Pokémon Scarlet and Violet', 'Nintendo Switch', '2022-11-18', 'RPG', 'Game Freak', 'Nintendo', 'https://media.rawg.io/media/games/5ab/5abb8e4af55eb8c867410c3a740355b9.jpg', '2025-06-10 11:46:18', '747505'),
(185, 'TEKKEN 7', 'PlayStation 4', '2015-03-18', 'Action', 'BANDAI NAMCO Entertainment America, Bandai Namco Entertainment', 'Bandai Namco Entertainment, BANDAI NAMCO Entertainment US', 'https://media.rawg.io/media/games/62b/62b035add7205737540d66e082b85930.jpg', '2025-06-10 11:56:23', '36'),
(186, 'Bon-Bon', 'PC', '2023-04-05', 'Adventure', 'Ninten7', NULL, 'https://media.rawg.io/media/screenshots/aa4/aa40d920985e7c9be00defebb571f48b.jpg', '2025-06-10 12:10:03', '952799'),
(187, 'Senran Kagura: Bon Appetit', 'PS Vita', '2014-03-20', 'Arcade', 'XSEED Games, Meteorise', 'XSEED Games, Marvelous USA', 'https://media.rawg.io/media/screenshots/859/859f08965585603dcfd35176ef83ad1d.jpg', '2025-06-10 12:10:17', '333100'),
(188, 'Dune Awakening', 'PC', '2025-06-10', 'Action', 'Funcom', 'Funcom', 'https://media.rawg.io/media/games/593/593c074cdf1ea15b8c9a2513676020e6.jpg', '2025-06-10 12:28:01', '840775'),
(189, 'Dofus', 'PC', '2005-09-01', 'RPG', 'Ankama Games', 'Ankama Games', 'https://media.rawg.io/media/screenshots/31e/31eefd7dbb20e994c8762221599702bc.jpg', '2025-06-10 12:29:14', '37943'),
(190, 'Ratchet & Clank Collection', 'PlayStation 3', '2012-01-01', 'Action, Arcade', 'Sony Interactive Entertainment, Idol Minds', 'Sony Computer Entertainment', 'https://media.rawg.io/media/screenshots/294/294f3bd5a2aa79df10e10d6f3c6ed4a7.jpg', '2025-06-10 13:18:08', NULL),
(191, 'MindsEye', 'PlayStation 5', '2025-01-01', 'Action, Shooter', 'Build a Rocket Boy', 'IO Interactive A/S', 'https://media.rawg.io/media/games/579/5793be6f79c71ad0074d13fe439bc144.jpg', '2025-06-10 13:19:03', NULL),
(192, 'Art of Fury: Virtual Gallery', 'PC', '2021-01-01', 'Casual', 'Raw Fury', 'Raw Fury', 'https://media.rawg.io/media/screenshots/452/4525cc6eca19b75fc7335d2688dcbae8.jpg', '2025-06-10 13:19:28', NULL),
(193, 'Zombies Ate My Neighbors (1993)', 'SNES', '1993-01-01', 'Action, Shooter, Adventure', 'LucasArts Entertainment', 'Konami', 'https://media.rawg.io/media/games/caa/caace5e91603bbeb0cf7d4560d437c1a.jpg', '2025-06-10 13:19:48', NULL),
(194, 'ARE', 'Inconnue', '2015-01-01', 'Action', '', '', 'https://media.rawg.io/media/screenshots/176/17642298b160b088e34a1dd67496dd69.jpg', '2025-06-10 13:27:58', NULL),
(195, 'Harry Potter and the Prisoner of Azkaban (PS2 / Xbox / GameCube)', 'GameCube', '2004-05-29', 'Action', 'EA UK', 'EA Originals', 'https://media.rawg.io/media/screenshots/9c2/9c28c4d2eba7d434ddf5e890af668d5d.jpg', '2025-06-10 14:31:59', '986693'),
(196, 'The Legend of Zelda: Skyward Sword', 'Wii U', '2011-11-20', 'Adventure', 'Nintendo', 'Nintendo', 'https://media.rawg.io/media/games/884/884d12f527a9a12b5e486ee1b79ecf7f.jpeg', '2025-06-10 14:40:17', '26824'),
(197, 'Need For Speed Undercover', 'PSP', '2008-11-17', 'Racing', 'Electronic Arts, Electronic Arts Black Box', 'Electronic Arts', 'https://media.rawg.io/media/games/4ad/4ad6ab9cfe8146224330598a4a62fb14.jpg', '2025-06-10 15:20:04', '5615'),
(198, 'OLI', 'PC', '2021-07-28', 'Adventure', 'Guedes_NK, Nikolas Guedes da Silva', 'NK STUDIO GAMES', 'https://media.rawg.io/media/screenshots/be9/be90657ea9095f7b0872eb2ddc35b134.jpg', '2025-06-10 15:25:03', '642227'),
(199, 'The Legend of Zelda: Breath of the Wild', 'Nintendo Switch', '2017-03-03', 'Action', 'Nintendo', 'Nintendo', 'https://media.rawg.io/media/games/cc1/cc196a5ad763955d6532cdba236f730c.jpg', '2025-06-10 15:32:15', '22511'),
(200, 'Tekken (1994)', 'PlayStation', '1994-12-09', 'Fighting', 'NAMCO', 'SCEE', 'https://media.rawg.io/media/screenshots/18f/18f3ef9aa2b8ed7047a1ee46abcb8228.jpg', '2025-06-10 15:41:57', '57840'),
(201, 'Tekken 5', 'PlayStation 2', '2004-12-09', 'Fighting', 'NAMCO', 'Namco, SCEE', 'https://media.rawg.io/media/games/ff8/ff8bdb62481960550013c57025d47812.jpg', '2025-06-10 15:44:10', '262384'),
(202, 'Rez', 'Dreamcast', '2001-11-22', 'Action', 'Sonic Team, United Game Artists', 'SEGA', 'https://media.rawg.io/media/screenshots/603/603a120aba4ac6d857666deabc928af1.jpg', '2025-06-10 15:49:05', '54729'),
(203, 'Zombies Ate My Neighbors (1993)', 'SNES', '1993-09-24', 'Action', 'LucasArts Entertainment', 'Konami', 'https://media.rawg.io/media/games/caa/caace5e91603bbeb0cf7d4560d437c1a.jpg', '2025-06-11 11:02:21', '57278'),
(204, 'Er-Spectro', 'PC', '2017-12-27', 'Action', 'TheDreik', 'TheDreik', 'https://media.rawg.io/media/screenshots/027/027101ec2023af2725d51d870094ed1d.jpg', '2025-06-11 11:02:40', '50887'),
(205, 'Et Rosinha - Samuel Xavier', 'Web', '2019-09-24', 'Inconnu', 'SuperGeeksDivinopolis', NULL, 'https://media.rawg.io/media/screenshots/643/643f85ee97e216fb08ca10715f399f61.jpg', '2025-06-11 11:02:59', '377537'),
(206, 'Aloe and Cal', 'PC', '2019-12-27', 'Adventure', 'A.S.', 'A.S.', 'https://media.rawg.io/media/screenshots/4c6/4c69f8cf5292a46074e2f541096b2211.jpg', '2025-06-11 12:38:44', '970258'),
(207, 'TankWar (Fer)', 'Android', '2021-06-26', 'Strategy', 'Fer', NULL, 'https://media.rawg.io/media/screenshots/fc7/fc728dbc36712b7e560c3bea4eb6d811.jpg', '2025-06-11 12:39:11', '629969'),
(208, 'The Godfather: The Game', 'PlayStation 2', '2006-03-21', 'Action', 'Electronic Arts Redwood Shores, Page 44 Studios, Headgate Studios', 'Electronic Arts', 'https://media.rawg.io/media/games/826/82651419404a468b4aade80ba9f73e3b.jpg', '2025-06-11 12:39:56', '34947'),
(209, 'DEF JAM: ICON', 'Xbox 360', '2007-03-06', 'Arcade', 'Electronic Arts Chicago', 'Electronic Arts', 'https://media.rawg.io/media/screenshots/77d/77d970c796548d818a67273ba39cd93c.jpg', '2025-06-11 12:40:16', '29021'),
(210, 'SONIC', 'PC', '2019-04-13', 'Inconnu', 'jack w', NULL, 'https://media.rawg.io/media/screenshots/f84/f84eff0df81802ee885018eb4395e352.jpg', '2025-06-11 12:45:48', '309525'),
(211, 'Death Stranding 2: On The Beach', 'PlayStation 5', '2025-06-26', 'Action', 'Kojima Productions', 'Sony Interactive Entertainment', 'https://media.rawg.io/media/games/b85/b85bc300d42588af66fb516b7563f74f.jpg', '2025-06-11 12:46:08', '891532'),
(212, 'Pokémon Ruby, Sapphire, Emerald', 'Game Boy Advance', '2004-09-16', 'Adventure', 'Game Freak, Creatures', 'Nintendo, The Pokemon Company', 'https://media.rawg.io/media/screenshots/7e5/7e56cc0421adcea354ed6b85057f2caa.jpeg', '2025-06-11 13:13:38', '52372'),
(213, 'Pokémon Colosseum', 'GameCube', '2004-02-10', 'RPG', 'Genius Sonority', 'Nintendo', 'https://media.rawg.io/media/games/8f7/8f738dce65f8bd826fc7e1b756376f29.jpg', '2025-06-11 13:13:57', '56174'),
(214, 'AZ Tanks: The Squad', 'PC', '2020-06-22', 'Shooter', 'MniTortoise180', NULL, 'https://media.rawg.io/media/screenshots/ebc/ebc9e427bd318096c9b462450daeb512.jpg', '2025-06-11 13:14:25', '458923'),
(215, 'ZE?T Riddle', 'Linux', '2006-04-21', 'Puzzle', NULL, NULL, 'https://media.rawg.io/media/screenshots/cbe/cbe72a0b53392bb3366c175d4b83f10a.jpg', '2025-06-11 13:19:15', '973001'),
(216, 'Evil Dead: Regeneration', 'PC', '2005-09-13', 'Action', 'Cranky Pants Games', 'THQ', 'https://media.rawg.io/media/screenshots/a68/a686d00f7f56e26176485b2cd1d6926f.jpg', '2025-06-11 14:25:49', '36716'),
(217, 'Minecraft', 'Android', '2009-05-10', 'Action', '4J Studios, Mojang', 'Microsoft Studios, Mojang', 'https://media.rawg.io/media/games/b4e/b4e4c73d5aa4ec66bbf75375c4847a2b.jpg', '2025-06-11 15:02:32', '22509'),
(218, 'Minecraft: Dungeons', 'Xbox One', '2020-05-26', 'Action', 'Double Eleven, Mojang', 'Microsoft Studios', 'https://media.rawg.io/media/games/c14/c146d28ceb14c84ea9fdbd7410701277.jpg', '2025-06-11 15:03:56', '257195'),
(219, 'Mario Kart World', 'Nintendo Switch', '2025-06-05', 'Action', 'Nintendo', 'Nintendo', 'https://media.rawg.io/media/games/1b8/1b8e007c36040ae1f5762a62ba2faeab.jpg', '2025-06-11 15:09:33', '1000574'),
(220, 'AE-Canfield', 'iOS', '2013-01-07', 'Card', 'AE Mobile', 'AE Mobile', 'https://media.rawg.io/media/screenshots/a5e/a5ec0bc493d6ede2842fc163c0b651a5_9LsVFsh.jpg', '2025-06-13 15:29:47', '287723'),
(221, 'AE Basketball', 'iOS', '2012-04-13', 'Action', 'AE Mobile', 'AE Mobile', 'https://media.rawg.io/media/screenshots/588/588aafd015016406f13499ac60a697c1.jpg', '2025-06-13 15:30:03', '287681'),
(222, 'Cyberpunk 2077: Phantom Liberty', 'PlayStation 5', '2023-09-26', 'Action', 'CD PROJEKT RED', 'CD PROJEKT RED', 'https://media.rawg.io/media/games/062/06285b425e61623530c5430f20e5d222.jpg', '2025-06-19 09:50:41', '846303'),
(223, 'APOX', 'PC', '2011-01-20', 'Action', 'BlueGiant Interactive', 'BlueGiant Interactive', 'https://media.rawg.io/media/screenshots/a6b/a6b1a3c2c7ddbd6a1401343b87883022.jpg', '2025-06-19 09:51:00', '19545'),
(224, 'Every Day We Fight', 'PC', '2025-07-09', 'Inconnu', 'Signal Space Lab', 'Hooded Horse', 'https://media.rawg.io/media/screenshots/a79/a79ec40f3806cfa5b010d2035e7efe19.jpg', '2025-06-19 09:52:01', '1002238'),
(225, 'Lost in Random: The Eternal Die', 'Nintendo Switch', '2025-06-17', 'Action', NULL, NULL, 'https://media.rawg.io/media/games/823/823212070bb1e292b593c7f8ea5303bc.jpg', '2025-06-19 11:55:18', '1004662'),
(226, 'REMATCH (2025)', 'Xbox Series S/X', '2025-06-19', 'Sports', 'Sloclap', 'Kepler Interactive, Sloclap', 'https://media.rawg.io/media/screenshots/7f2/7f2fc0ecea075a42b1d030d3c67ab37d.jpg', '2025-06-19 13:04:34', '998669'),
(227, 'Borderlands 4', 'PlayStation 5', '2025-09-12', 'Action', 'Gearbox Software', '2K Games', 'https://media.rawg.io/media/games/9cc/9ccb59a7c634091bc8a9b7978d4dc322.jpg', '2025-06-19 13:20:14', '988615'),
(228, 'Ghost of Yotei', 'PlayStation 5', '2025-10-02', 'Action', 'Sucker Punch Productions', 'Sony Computer Entertainment', 'https://media.rawg.io/media/games/30b/30b195c2321d763f807366967ffad793.jpg', '2025-06-19 13:20:56', '989329'),
(229, 'Metal Gear Solid Delta: Snake Eater', 'Xbox Series S/X', '2025-08-28', 'Action', 'Virtuos Games', 'Konami', 'https://media.rawg.io/media/games/1c0/1c0548b761f7c4e4c0da71172b3362bf.jpg', '2025-06-19 13:21:45', '961198'),
(230, 'The Outer Worlds 2', 'PC', '2025-10-29', 'Action', 'Obsidian Entertainment', 'Xbox Game Studios', 'https://media.rawg.io/media/games/021/021d48108ec99947d8f09dc6abe3f980.jpg', '2025-06-19 13:22:17', '617120'),
(231, 'Celeste', 'Xbox One', '2018-01-25', 'Indie', 'Matt Makes Games, Extremely OK Games, Noel', 'Matt Makes Games', 'https://media.rawg.io/media/games/594/59487800889ebac294c7c2c070d02356.jpg', '2025-06-19 13:23:16', '22121'),
(232, 'The Legend of Zelda: The Minish Cap', 'Wii U', '2004-11-04', 'Action', 'Capcom, Nintendo, Flagship', 'Nintendo', 'https://media.rawg.io/media/games/25d/25d9592abbd02dccd67d83108ae79582.jpg', '2025-06-19 13:24:09', '27418'),
(233, 'Super Smash Bros. Melee', 'GameCube', '2001-11-21', 'Action', 'HAL Laboratory', 'Nintendo', 'https://media.rawg.io/media/games/b78/b780abf866cba1422d01bfa75612dd29.jpg', '2025-06-19 13:25:33', '56222'),
(234, 'Call of Duty 4: Modern Warfare', 'Nintendo DS', '2007-11-05', 'Action', 'Raven Software, Treyarch, Infinity Ward, n-Space', 'Square Enix, Aspyr, Activision Blizzard, Activison, Active', 'https://media.rawg.io/media/games/9fb/9fbaea2168caea1f806546dfdaaeb1da.jpg', '2025-06-19 13:26:07', '4535'),
(235, 'Cyber Hook', 'PC', '2020-09-23', 'Action', 'Graffiti_Games, Blazing Stick', 'Graffiti Games', 'https://media.rawg.io/media/screenshots/f4b/f4bbf37efa563ed46791742b4682242c.jpg', '2025-06-19 13:27:33', '367183'),
(236, 'Final Fantasy X', 'PlayStation 2', '2001-07-19', 'RPG', 'Square', 'Square', 'https://media.rawg.io/media/games/ddc/ddc65c56f16bc3effb8d2645b095a8c5.jpg', '2025-06-19 13:35:34', '41128'),
(237, 'Halo: Reach', 'Xbox 360', '2010-09-14', 'Shooter', 'Bungie', 'Microsoft Studios', 'https://media.rawg.io/media/games/045/0457f748c9492261ccb46147edf9c761.jpg', '2025-06-19 13:35:59', '28613'),
(238, 'Need for Speed: Underground 2', 'Nintendo DS', '2004-11-09', 'Racing', 'Electronic Arts Canada, Electronic Arts Black Box, Pocketeers', 'Electronic Arts', 'https://media.rawg.io/media/games/dc6/dc68ca77e06ad993aade7faf645f5ec2.jpg', '2025-06-19 13:36:29', '53446'),
(239, 'FBC: Firebreak', 'Inconnue', '2025-06-17', 'Action', NULL, NULL, 'https://media.rawg.io/media/games/bd6/bd6b68a7dee43d07db95ed6cdef5cb37.jpg', '2025-06-19 15:54:16', '1001848'),
(240, 'Leximorph - Word Merge Game', 'PC', '2025-06-16', 'Casual', 'Two For Joy Games', 'Two For Joy Games', 'https://media.rawg.io/media/screenshots/5eb/5eb412f8e6c1c75006b567d901605c2b.jpg', '2025-06-20 12:16:53', '1005085'),
(241, 'A Building Full of Cats 2', 'Linux', '2025-06-16', 'Adventure', 'Devcats', 'Devcats', 'https://media.rawg.io/media/screenshots/d6e/d6e1998bad0dd7b113c598fb2da7fe9c.jpg', '2025-06-20 12:17:05', '1005102'),
(242, 'Zombies and Strangers', 'PC', '2025-06-16', 'Action', 'jieasobi', 'jieasobi', 'https://media.rawg.io/media/screenshots/56b/56bfbe42c082073e42b28ec2e7dfa9ae.jpg', '2025-06-20 13:04:40', '1005092'),
(243, 'In Game Adventure: Legend of Monsters', 'PC', '2017-06-29', 'Action', 'Alysson Moraes, MoraesStudio', 'Moraes Studio', 'https://media.rawg.io/media/screenshots/7d6/7d67bf2a0313c74c5fb8901e3afa4607.jpg', '2025-06-20 13:14:40', '43960'),
(244, 'OK.', 'PC', '2022-02-13', 'Puzzle', 'Rogocat', NULL, 'https://media.rawg.io/media/screenshots/2eb/2ebc3b3c09d935ef56864784ebb2cf19.jpg', '2025-06-20 13:17:30', '738466'),
(245, 'XENO SIEGE', 'PC', '2025-06-16', 'RPG', 'Bulanke', 'Bulanke', 'https://media.rawg.io/media/screenshots/06e/06e0c1796330430c53d794a883ac6a9d.jpg', '2025-06-21 18:19:12', '1005079'),
(246, 'Drive-By Hero', 'PC', '2017-06-12', 'Action', 'Idea Cabin', 'Idea Cabin', 'https://media.rawg.io/media/screenshots/87d/87d141765c73e84baeb06cc80f02b21d.jpg', '2025-06-21 18:30:17', '28067'),
(247, 'Tel-Tel Stadium', 'Genesis', '1990-10-10', 'Sports', 'SunSoft', 'Sunsoft', 'https://media.rawg.io/media/screenshots/0bc/0bcfb2c4225db82ef906242e0013beb9.jpg', '2025-06-21 21:41:09', '57282'),
(248, 'THE', 'Web', '2019-08-04', 'Action', 'HarryWood', NULL, 'https://media.rawg.io/media/screenshots/bf7/bf7faa9e5599bff6261ec22ddd87af14.jpg', '2025-06-21 21:41:45', '360918'),
(249, 'Tony Hawk\'s Pro Skater 3 + 4', 'Nintendo Switch', '2025-07-11', 'Simulation', NULL, NULL, 'https://media.rawg.io/media/games/fb5/fb5453ec554d23caa221a6c8c4fd9279.jpg', '2025-06-25 11:16:32', '998678'),
(250, 'Ruffy and the Riverside', 'Xbox Series S/X', '2025-06-26', 'Action', NULL, NULL, 'https://media.rawg.io/media/screenshots/4ff/4ff240226c92c2deee63a7dbea00e955.jpg', '2025-06-25 11:34:25', '955276'),
(251, 'The Breeding: The Fog', 'PC', '2017-12-01', 'Action', 'GBROSSOFT', 'GBROSSOFT', 'https://media.rawg.io/media/screenshots/01e/01e0744525edd745ea038646f213c99e.jpg', '2025-06-25 11:34:42', '49471'),
(252, 'Yo-yo', 'PC', '2022-04-17', 'Action', 'Emulization', NULL, 'https://media.rawg.io/media/screenshots/533/53343ff9bfbe52ae2957f60119682ac5.jpg', '2025-06-25 11:35:16', '771502'),
(253, 'Yo-Yo Shuriken', 'PC', '2019-11-28', 'Action', 'Dr. Ludos', NULL, 'https://media.rawg.io/media/screenshots/182/182b0be16c5a1498165435aaf80308c1.jpg', '2025-06-25 11:35:21', '394611'),
(254, 'Q-YO Blaster', 'Nintendo Switch', '2019-06-27', 'Action', 'Team Robot Black Hat', 'Forever Entertainment, Team Robot Black Hat', 'https://media.rawg.io/media/screenshots/6d3/6d369a146596c6670c3458607f5323bb.jpg', '2025-06-25 11:35:34', '51277'),
(255, 'Ad Infinitum', 'PC', '2023-09-13', 'Action', 'Hekate', 'Nacon', 'https://media.rawg.io/media/screenshots/d9b/d9b676027a253474e281ff405bfecf4b.jpg', '2025-06-25 11:36:01', '966228'),
(256, 'DZ Puzzle - Free Edition', 'Inconnue', '2015-11-29', 'Puzzle', 'divisaozero', NULL, 'https://media.rawg.io/media/screenshots/ebc/ebc8d9474d47c049b7b65d25f4e9f97e.jpg', '2025-06-25 11:36:13', '151823');

-- --------------------------------------------------------

--
-- Structure de la table `game_stats`
--

CREATE TABLE `game_stats` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `game_id` int NOT NULL,
  `play_time` int DEFAULT '0',
  `status` varchar(20) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `game_stats`
--

INSERT INTO `game_stats` (`id`, `user_id`, `game_id`, `play_time`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(164, 1, 191, 234, 'termine', 'der', '2025-06-10 13:19:03', NULL),
(191, 1, 188, 2004, 'en cours', 'ddee', '2025-06-11 13:13:06', NULL),
(193, 1, 213, 1, 'termine', 'f', '2025-06-11 13:13:57', NULL),
(201, 1, 219, 3333, 'termine', 'f', '2025-06-11 15:10:00', NULL),
(208, 1, 231, 234, 'termine', '', '2025-06-19 13:23:16', NULL),
(209, 1, 199, 234, 'complete', 'okkkkk', '2025-06-19 13:23:43', NULL),
(210, 1, 232, 633, 'en cours', 'didon', '2025-06-19 13:24:09', NULL),
(211, 1, 189, 2004, 'termine', 'd', '2025-06-19 13:24:20', NULL),
(212, 1, 233, 3333, 'complete', 'd', '2025-06-19 13:25:33', NULL),
(213, 1, 234, 200, 'termine', 'd', '2025-06-19 13:26:07', NULL),
(215, 1, 236, 200, 'complete', 'okl', '2025-06-19 13:35:34', NULL),
(216, 1, 237, 200, 'termine', 'j', '2025-06-19 13:35:59', NULL),
(217, 1, 238, 23, 'termine', '', '2025-06-19 13:36:30', NULL);

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
(1, 'admin', 'admin@gmail.com', '$2y$10$R8z2Ldd5iJJeS5fTP//q.OqrDuiidoh4Agp5uHCfMZkZ3QjdCIkQu', '2025-05-20 10:28:07', '2025-06-25 09:17:08', 'uploads/profile_pictures/1750529534_d51c8a82d22ac7fd4690.png');

-- --------------------------------------------------------

--
-- Structure de la table `user_top_games`
--

CREATE TABLE `user_top_games` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `game_id` int NOT NULL,
  `rank_position` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user_top_games`
--

INSERT INTO `user_top_games` (`id`, `user_id`, `game_id`, `rank_position`, `created_at`) VALUES
(91, 1, 231, 1, '2025-06-26 08:38:32'),
(92, 1, 189, 2, '2025-06-26 08:38:32'),
(93, 1, 236, 3, '2025-06-26 08:38:32'),
(94, 1, 234, 4, '2025-06-26 08:38:32'),
(95, 1, 219, 5, '2025-06-26 08:38:32');

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
-- Déchargement des données de la table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `game_id`, `created_at`) VALUES
(135, 1, 182, '2025-06-19 12:32:37'),
(137, 1, 227, '2025-06-19 13:20:14'),
(138, 1, 228, '2025-06-19 13:20:56'),
(139, 1, 229, '2025-06-19 13:21:45'),
(141, 1, 222, '2025-06-19 13:24:47');

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
  ADD UNIQUE KEY `unique_user_position` (`user_id`,`rank_position`),
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- AUTO_INCREMENT pour la table `game_stats`
--
ALTER TABLE `game_stats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `user_top_games`
--
ALTER TABLE `user_top_games`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT pour la table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

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
