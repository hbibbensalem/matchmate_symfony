-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 11 avr. 2025 à 18:31
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `matchmate`
--

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `id_commande` varchar(255) NOT NULL,
  `idProduit` int(11) DEFAULT NULL,
  `id_user` varchar(255) DEFAULT NULL,
  `date_commande` datetime NOT NULL,
  `quantite_commande` int(11) NOT NULL,
  `status_commande` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250411125603', '2025-04-11 14:56:07', 242);

-- --------------------------------------------------------

--
-- Structure de la table `event`
--

CREATE TABLE `event` (
  `idevent` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `description` varchar(10000) NOT NULL,
  `id_user` varchar(255) DEFAULT NULL,
  `capacite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `listinscri`
--

CREATE TABLE `listinscri` (
  `id` int(11) NOT NULL,
  `matchId` int(11) DEFAULT NULL,
  `id_user` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `nb_personne` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `typesport` varchar(255) NOT NULL,
  `statut` varchar(255) NOT NULL,
  `id_user` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `participation`
--

CREATE TABLE `participation` (
  `idparticipation` int(11) NOT NULL,
  `id_user` varchar(255) DEFAULT NULL,
  `idevent` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE `produit` (
  `id_produit` int(11) NOT NULL,
  `nom_produit` varchar(110) NOT NULL,
  `description_produit` varchar(110) NOT NULL,
  `prix_produit` int(11) NOT NULL,
  `quantite_produit` int(11) NOT NULL,
  `image_produit` varchar(255) NOT NULL,
  `ref_produit` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id_produit`, `nom_produit`, `description_produit`, `prix_produit`, `quantite_produit`, `image_produit`, `ref_produit`) VALUES
(40, 'Raquette Ping Pong', 'Pratique', 120, 0, 'file:/C:/Users/MSI/Desktop/MatchMate/RaquettePingPongRouge.jpeg', '8483-RAQ-6306'),
(41, 'tenue femme', '2 piéces : jupe et top couleur blanche', 200, 42, 'file:/C:/Users/MSI/Desktop/MatchMate/TenuNoir.png', '3385-TEN-8662'),
(43, 'Balles de tennis', 'Lots de 4 balles', 70, 0, 'file:/C:/Users/MSI/Desktop/MatchMate/BallesTennis.jpeg', '7793-BAL-7391'),
(45, 'Tenue homme', 'T-SHIRT && SHORT NOIR', 170, 92, 'file:/C:/Users/MSI/Desktop/MatchMate/TenuHomme.jpeg', '1694-TEN-1539'),
(48, 'raquette', 'HHHHHHHHHHHH', 200, -3, 'file:/C:/Users/MSI/Desktop/MatchMate/RaquettePadel.jpeg', '7884-RAQ-9643'),
(49, 'table PingPong', '444444', 334, 28, 'file:/C:/Users/MSI/Desktop/MatchMate/TableTennis.jpg', '4093-TAB-1119'),
(50, 'Pure whey ', 'Jusqu\'à 79% de protéines', 126, 0, 'file:/C:/Users/MSI/Desktop/MatchMate/prot.jpg', '9719-PUR-1714'),
(51, 'Raquette', 'SPORT LIFE', 130, 0, 'file:/C:/Users/MSI/Desktop/MatchMate/RaquettePadelNoire.jpg', '6775-RAQ-6477'),
(52, 'cb', 'HHHHHHH VETEMNENT', 448, 6, 'file:/C:/Users/MSI/Desktop/MatchMate/BallesDepadel.jpg', '3547-CB-6434');

-- --------------------------------------------------------

--
-- Structure de la table `reclamation`
--

CREATE TABLE `reclamation` (
  `id_r` int(11) NOT NULL,
  `contenu` varchar(255) NOT NULL,
  `categorie` varchar(255) NOT NULL,
  `support` varchar(255) NOT NULL,
  `id_s` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reclamation`
--

INSERT INTO `reclamation` (`id_r`, `contenu`, `categorie`, `support`, `id_s`) VALUES
(2, 'azert', 'proposition', '', 2);

-- --------------------------------------------------------

--
-- Structure de la table `regime`
--

CREATE TABLE `regime` (
  `regime_id` int(11) NOT NULL,
  `id_user` varchar(255) DEFAULT NULL,
  `objectif` varchar(100) NOT NULL,
  `calories_journalieres` int(11) NOT NULL,
  `proteines` int(11) NOT NULL,
  `glucides` int(11) NOT NULL,
  `lipides` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `statut` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `suivi`
--

CREATE TABLE `suivi` (
  `suivi_id` int(11) NOT NULL,
  `id_user` varchar(255) DEFAULT NULL,
  `regime_id` int(11) DEFAULT NULL,
  `poids` double NOT NULL,
  `tour_de_taille` double NOT NULL,
  `imc` double NOT NULL,
  `date_suivi` date NOT NULL,
  `taille` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `support`
--

CREATE TABLE `support` (
  `id_s` int(11) NOT NULL,
  `nom_responsable` varchar(255) NOT NULL,
  `num_tel` int(255) NOT NULL,
  `domaine` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `support`
--

INSERT INTO `support` (`id_s`, `nom_responsable`, `num_tel`, `domaine`) VALUES
(2, 'amine', 25256422, 'info');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id_user` varchar(255) NOT NULL,
  `email_user` varchar(255) NOT NULL,
  `password_user` varchar(255) NOT NULL,
  `nom_user` varchar(255) NOT NULL,
  `prenom_user` varchar(255) NOT NULL,
  `sexe_user` varchar(255) NOT NULL,
  `telephone_user` varchar(15) NOT NULL,
  `adresse_user` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `experience` varchar(255) NOT NULL,
  `salaire` double NOT NULL,
  `niveau_joueur` varchar(255) NOT NULL,
  `max_distance_user` int(11) NOT NULL,
  `is_premium` varchar(1) NOT NULL,
  `photo_user` varchar(255) NOT NULL,
  `piece_jointe` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiration` datetime DEFAULT NULL,
  `date_naissance_user` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `reactivate_at` datetime DEFAULT NULL,
  `taille` varchar(255) DEFAULT NULL,
  `poids` varchar(255) DEFAULT NULL,
  `tour_de_taille` varchar(255) DEFAULT NULL,
  `maladie` varchar(255) DEFAULT NULL,
  `description_user` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id_user`, `email_user`, `password_user`, `nom_user`, `prenom_user`, `sexe_user`, `telephone_user`, `adresse_user`, `role`, `experience`, `salaire`, `niveau_joueur`, `max_distance_user`, `is_premium`, `photo_user`, `piece_jointe`, `reset_token`, `token_expiration`, `date_naissance_user`, `is_active`, `reactivate_at`, `taille`, `poids`, `tour_de_taille`, `maladie`, `description_user`) VALUES
('085ADM3163', 'admin@gmail.com', '$2y$13$8f6dsQ3c/avxlYhh8NiUlugMt1g8dKzc1Z6em.hvpX4GCAb/5t1KG', 'bensalem', 'hbib', 'M', '26934999', 'cité ghazella , ariana', 'ADMIN', 'TWO_YEARS', 900, 'DEBUTANT', 9, '0', '67f9389e6da3b.png', '67f9389e6dfe1.txt', NULL, NULL, '2025-04-09 00:00:00', 1, NULL, NULL, NULL, NULL, NULL, 'admin account'),
('402JMT2600', 'hbibbensalem40@gmail.com', '$2y$13$XgbEU1fgC1j1KdXuCpEl9eRQ8dLzbU27mXjpxGs1d2QIsCniCHYhq', 'bensalema', 'hbib', 'M', '26934999', 'cité ghazella , ariana', 'NUTRITIONIST', 'ONE_YEAR', 900, 'INTERMEDIAIRE', 9, '1', '67f93d3476348.png', '67f93d34769f4.txt', NULL, NULL, '2025-04-08 00:00:00', 1, NULL, NULL, NULL, NULL, NULL, 'hbib'),
('716JMT2932', 'hbibbensalem20@gmail.com', '$2y$13$GY9UkygBUrzRsOfqWB701.4pPBFZtDcO8E6DwMESRumIUa6RcuOT6', 'bensalem', 'hbib', 'M', '26934999', 'cité ghazella , ariana', 'PLAYER', 'TWO_YEARS', 900, 'DEBUTANT', 9, '0', '67f92365a5b25.png', '67f92365a5f95.txt', NULL, NULL, '2025-04-01 00:00:00', 1, NULL, NULL, NULL, NULL, NULL, 'sssss');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `IDX_6EEAA67D6B3CA4B` (`id_user`),
  ADD KEY `IDX_6EEAA67D391C87D5` (`idProduit`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`idevent`),
  ADD KEY `IDX_3BAE0AA76B3CA4B` (`id_user`);

--
-- Index pour la table `listinscri`
--
ALTER TABLE `listinscri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F1ECF6016B3CA4B` (`id_user`),
  ADD KEY `IDX_F1ECF601BCC4FBD3` (`matchId`);

--
-- Index pour la table `match1`
--
ALTER TABLE `match1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F3CC40FD6B3CA4B` (`id_user`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`idparticipation`),
  ADD KEY `IDX_AB55E24F6B3CA4B` (`id_user`),
  ADD KEY `IDX_AB55E24FEDAB66BE` (`idevent`);

--
-- Index pour la table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id_produit`);

--
-- Index pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD PRIMARY KEY (`id_r`),
  ADD KEY `IDX_CE606404DC9B6574` (`id_s`);

--
-- Index pour la table `regime`
--
ALTER TABLE `regime`
  ADD PRIMARY KEY (`regime_id`),
  ADD KEY `IDX_AA864A7C6B3CA4B` (`id_user`);

--
-- Index pour la table `suivi`
--
ALTER TABLE `suivi`
  ADD PRIMARY KEY (`suivi_id`),
  ADD KEY `IDX_2EBCCA8F6B3CA4B` (`id_user`),
  ADD KEY `IDX_2EBCCA8F35E7D534` (`regime_id`);

--
-- Index pour la table `support`
--
ALTER TABLE `support`
  ADD PRIMARY KEY (`id_s`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `FK_6EEAA67D391C87D5` FOREIGN KEY (`idProduit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_COMMANDE_USER` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `FK_EVENT_USER` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `listinscri`
--
ALTER TABLE `listinscri`
  ADD CONSTRAINT `FK_F1ECF6016B3CA4B` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_LISTINSCRI_MATCH` FOREIGN KEY (`matchId`) REFERENCES `match1` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `match1`
--
ALTER TABLE `match1`
  ADD CONSTRAINT `FK_MATCH1_USER` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `FK_PARTICIPATION_EVENT` FOREIGN KEY (`idevent`) REFERENCES `event` (`idevent`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_PARTICIPATION_USER` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD CONSTRAINT `FK_RECLAMATION_SUPPORT` FOREIGN KEY (`id_s`) REFERENCES `support` (`id_s`) ON DELETE CASCADE;

--
-- Contraintes pour la table `regime`
--
ALTER TABLE `regime`
  ADD CONSTRAINT `FK_REGIME_USER` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `suivi`
--
ALTER TABLE `suivi`
  ADD CONSTRAINT `FK_SUIVI_REGIME` FOREIGN KEY (`regime_id`) REFERENCES `regime` (`regime_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_SUIVI_USER` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
