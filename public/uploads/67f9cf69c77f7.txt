-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3307
-- Généré le : mar. 04 mars 2025 à 13:00
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_match`
--

-- --------------------------------------------------------

--
-- Structure de la table `listinscri`
--

CREATE TABLE `listinscri` (
  `id` int(11) NOT NULL,
  `matchId` int(11) NOT NULL,
  `userId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `listinscri`
--

INSERT INTO `listinscri` (`id`, `matchId`, `userId`) VALUES
(41, 14, 10),
(42, 15, 12),
(46, 20, 30),
(47, 16, 28),
(49, 19, 2),
(50, 17, 2),
(51, 22, 30),
(52, 40, 41);

-- --------------------------------------------------------

--
-- Structure de la table `match1`
--

CREATE TABLE `match1` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `heure` varchar(255) NOT NULL,
  `localisation` varchar(255) NOT NULL,
  `terrain` varchar(255) NOT NULL,
  `nbPersonne` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `typesport` varchar(255) NOT NULL,
  `statut` enum('en_attente','confirmé','','') NOT NULL,
  `userId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `match1`
--

INSERT INTO `match1` (`id`, `date`, `heure`, `localisation`, `terrain`, `nbPersonne`, `description`, `typesport`, `statut`, `userId`) VALUES
(14, '2025-03-09', '14:10', 'ARIANA', 'Terrain Municipal d\'Ariana', 2, 'aucun', 'Tennis', 'confirmé', 15),
(15, '2025-04-20', '12:30', 'BEJA', 'Stade Municipal de Béja', 2, 'aucun descri', 'Ping Pong', 'confirmé', 16),
(16, '2025-03-22', '11:00', 'ARIANA', 'Terrain Municipal d\'Ariana', 2, 'oui nchallah', 'Tennis', 'confirmé', 27),
(17, '2025-04-11', '11:00', 'BEJA', 'Stade Municipal de Béja', 2, 'aucune ', 'Padel', 'confirmé', 28),
(18, '2025-04-06', '08:00', 'BEJA', 'Stade Municipal de Béja', 2, 'nchallah', 'Padel', 'en_attente', 30),
(19, '2025-04-18', '11:00', 'GAFSA', 'Terrain de Métlaoui', 2, 'aucun', 'Tennis', 'confirmé', 31),
(20, '2025-07-18', '09:15', 'JENDOUBA', 'Terrain de Tabarka', 2, 'oui nchallah', 'Padel', 'confirmé', 9),
(21, '2025-07-17', '13:00', 'BIZERTE', 'Stade 15 Octobre', 2, 'aaa', 'Tennis', 'en_attente', 30),
(22, '2025-06-13', '12:00', 'ARIANA', 'Terrain Municipal d\'Ariana', 2, 'aucun', 'Tennis', 'confirmé', 32),
(24, '2025-07-17', '08:00', 'JENDOUBA', 'Stade Municipal de Jendouba', 2, 'oui', 'Padel', 'en_attente', 30),
(25, '2025-07-16', '12:00', 'ARIANA', 'Stade Olympique de Radès', 2, 'ouii', 'Padel', 'en_attente', 30),
(26, '2025-06-19', '11:15:00', 'GAFSA', 'Stade Municipal de Gafsa', 4, 'oui oui nchallah nchallah je vais voir', 'Tennis', 'en_attente', 33),
(27, '2025-06-19', '11:00', 'ARIANA', 'Terrain Municipal d\'Ariana', 2, 'oui', 'Tennis', 'en_attente', 33),
(28, '2025-07-26', '11:00', 'ARIANA', 'Stade Olympique de Radès', 2, 'Oui nchallah', 'Tennis', 'en_attente', 34),
(29, '2025-04-06', '09:00:00', 'ARIANA', 'Stade Olympique de Radès', 2, 'oui', 'Tennis', 'en_attente', 33),
(30, '2025-05-23', '12:10', 'KASSERINE', 'Terrain de Sbeitla', 2, 'laaaaaaaa', 'Tennis', 'en_attente', 30),
(31, '2025-03-30', '11:00', 'ARIANA', 'Stade Olympique de Radès', 2, 'nnnnn', 'Padel', 'en_attente', 33),
(32, '2025-05-08', '10:00:00', 'BEJA', 'Stade Municipal de Béja', 2, 'oui nchallah oui', 'Tennis', 'en_attente', 34),
(34, '2025-06-13', '11:00:00', 'ARIANA', 'Stade Olympique de Radès', 2, 'oui oui oui oui', 'Tennis', 'en_attente', 40),
(35, '2025-06-12', '12:00:00', 'ARIANA', 'Terrain Municipal d\'Ariana', 2, 'aucun', 'Tennis', 'en_attente', 41),
(36, '2025-03-30', '10:00', 'ARIANA', 'Stade Olympique de Radès', 2, 'ouiii', 'Tennis', 'en_attente', 41),
(37, '2025-03-16', '10:00', 'ARIANA', 'Stade Olympique de Radès', 2, 'ouiii', 'Tennis', 'en_attente', 42),
(38, '2025-04-26', '10:00', 'ARIANA', 'Terrain Municipal d\'Ariana', 2, 'OUIIIIII', 'Padel', 'en_attente', 42),
(40, '2025-03-22', '10:00:00', 'JENDOUBA', 'Stade Municipal de Jendouba', 2, 'OUIIIIIII NCHALLAHHHH', 'Padel', 'confirmé', 45),
(41, '2025-03-22', '10:00', 'ARIANA', 'Stade Olympique de Radès', 4, 'ouii', 'Tennis', 'en_attente', 46),
(42, '2025-07-31', '09:00:00', 'BEN AROUS', 'Terrain de Mégrine', 2, 'yessss', 'Tennis', 'en_attente', 47),
(43, '2025-08-02', '10:10:00', 'JENDOUBA', 'Stade Municipal de Jendouba', 2, 'OUII ouii', 'Tennis', 'en_attente', 44);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `listinscri`
--
ALTER TABLE `listinscri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_match` (`matchId`);

--
-- Index pour la table `match1`
--
ALTER TABLE `match1`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `listinscri`
--
ALTER TABLE `listinscri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT pour la table `match1`
--
ALTER TABLE `match1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `listinscri`
--
ALTER TABLE `listinscri`
  ADD CONSTRAINT `fk_match` FOREIGN KEY (`matchId`) REFERENCES `match1` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
