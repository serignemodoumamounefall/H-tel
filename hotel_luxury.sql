-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 07 juil. 2026 à 16:26
-- Version du serveur : 9.1.0
-- Version de PHP : 8.5.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `hotel_luxury`
--

-- --------------------------------------------------------

--
-- Structure de la table `chambres`
--

DROP TABLE IF EXISTS `chambres`;
CREATE TABLE IF NOT EXISTS `chambres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('Standard','Deluxe','Suite') COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix_nuit` decimal(10,2) NOT NULL,
  `statut` enum('Disponible','Occupée','En entretien') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Disponible',
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero` (`numero`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `chambres`
--

INSERT INTO `chambres` (`id`, `numero`, `type`, `prix_nuit`, `statut`, `description`, `image`) VALUES
(5, '201', 'Standard', 60.00, 'Disponible', NULL, NULL),
(6, '140', 'Deluxe', 100.00, 'Disponible', NULL, NULL),
(7, '108', 'Suite', 140.00, 'Disponible', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `montant_total` decimal(10,2) NOT NULL,
  `statut` enum('En attente','En préparation','Livrée','Annulée') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'En attente',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `utilisateur_id`, `montant_total`, `statut`, `date_creation`) VALUES
(1, 8, 43.00, 'En attente', '2026-06-30 13:06:37'),
(2, 8, 48.00, 'En attente', '2026-06-30 16:49:28'),
(3, 8, 1000.00, 'En attente', '2026-07-07 11:34:32'),
(4, 8, 12.00, 'En attente', '2026-07-07 12:46:23'),
(5, 8, 59.00, 'Livrée', '2026-07-07 13:19:20'),
(6, 8, 59.00, 'En attente', '2026-07-07 13:52:04'),
(7, 8, 12.00, 'En attente', '2026-07-07 14:01:46'),
(8, 8, 47.00, 'Livrée', '2026-07-07 14:02:31'),
(9, 8, 36.00, 'En attente', '2026-07-07 14:05:27'),
(10, 8, 15.00, 'Annulée', '2026-07-07 14:09:28'),
(11, 8, 12.00, 'Livrée', '2026-07-07 14:10:27'),
(12, 8, 12.00, 'Annulée', '2026-07-07 14:16:44'),
(13, 8, 400.00, 'Livrée', '2026-07-07 14:17:33'),
(14, 8, 15.00, 'Annulée', '2026-07-07 14:48:31'),
(15, 8, 47.00, 'Livrée', '2026-07-07 15:51:47'),
(16, 8, 48.00, 'Annulée', '2026-07-07 16:25:11');

-- --------------------------------------------------------

--
-- Structure de la table `commande_details`
--

DROP TABLE IF EXISTS `commande_details`;
CREATE TABLE IF NOT EXISTS `commande_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `commande_id` int NOT NULL,
  `plat_id` int NOT NULL,
  `quantite` int NOT NULL DEFAULT '1',
  `prix_unitaire` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `commande_id` (`commande_id`),
  KEY `plat_id` (`plat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commande_details`
--

INSERT INTO `commande_details` (`id`, `commande_id`, `plat_id`, `quantite`, `prix_unitaire`) VALUES
(8, 4, 15, 1, 12.00),
(9, 5, 15, 2, 12.00),
(10, 5, 13, 1, 20.00),
(11, 5, 12, 1, 15.00),
(12, 6, 13, 1, 20.00),
(13, 6, 15, 2, 12.00),
(14, 6, 12, 1, 15.00),
(15, 7, 15, 1, 12.00),
(16, 8, 15, 1, 12.00),
(17, 8, 12, 1, 15.00),
(18, 8, 11, 1, 20.00),
(19, 9, 10, 2, 12.00),
(20, 9, 15, 1, 12.00),
(21, 10, 12, 1, 15.00),
(22, 11, 15, 1, 12.00),
(23, 12, 15, 1, 12.00),
(24, 13, 16, 2, 200.00),
(25, 14, 8, 1, 15.00),
(26, 15, 13, 1, 20.00),
(27, 15, 15, 1, 12.00),
(28, 15, 8, 1, 15.00),
(29, 16, 14, 2, 12.00),
(30, 16, 10, 1, 12.00),
(31, 16, 15, 1, 12.00);

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
CREATE TABLE IF NOT EXISTS `paiements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `reservation_id` int DEFAULT NULL,
  `commande_id` int DEFAULT NULL,
  `montant` decimal(10,2) NOT NULL,
  `methode` enum('Carte bancaire','Mobile money','PayPal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` enum('En attente','Payé','Échoué') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'En attente',
  `date_paiement` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `reservation_id` (`reservation_id`),
  KEY `commande_id` (`commande_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id`, `utilisateur_id`, `reservation_id`, `commande_id`, `montant`, `methode`, `statut`, `date_paiement`) VALUES
(1, 8, NULL, NULL, 960.00, 'Mobile money', 'Payé', '2026-06-30 13:06:05'),
(2, 8, NULL, 1, 43.00, 'Mobile money', 'Payé', '2026-06-30 13:06:37'),
(3, 8, NULL, NULL, 50.00, 'Mobile money', 'Payé', '2026-06-30 13:07:44'),
(4, 8, 4, NULL, 280.00, 'Mobile money', 'Payé', '2026-06-30 16:48:02'),
(5, 8, NULL, 2, 48.00, 'Carte bancaire', 'Payé', '2026-06-30 16:49:28'),
(6, 8, NULL, 3, 1000.00, 'Carte bancaire', 'Payé', '2026-07-07 11:34:32'),
(7, 8, NULL, 4, 12.00, 'PayPal', 'Payé', '2026-07-07 12:46:23'),
(8, 8, 5, NULL, 200.00, 'Carte bancaire', 'Payé', '2026-07-07 13:19:12'),
(9, 8, NULL, 5, 59.00, 'Carte bancaire', 'Payé', '2026-07-07 13:19:20'),
(10, 8, NULL, 6, 59.00, 'Carte bancaire', 'Payé', '2026-07-07 13:52:04'),
(11, 8, 7, NULL, 60.00, 'Carte bancaire', 'Payé', '2026-07-07 13:55:22'),
(12, 8, NULL, 7, 12.00, 'Carte bancaire', 'Payé', '2026-07-07 14:01:46'),
(13, 8, NULL, 8, 47.00, 'Carte bancaire', 'Payé', '2026-07-07 14:02:31'),
(14, 8, NULL, 9, 36.00, 'Carte bancaire', 'Payé', '2026-07-07 14:05:27'),
(15, 8, NULL, 10, 15.00, 'Carte bancaire', 'Payé', '2026-07-07 14:09:28'),
(16, 8, NULL, 11, 12.00, 'Carte bancaire', 'Payé', '2026-07-07 14:10:27'),
(17, 8, NULL, NULL, 80.00, 'Carte bancaire', 'Payé', '2026-07-07 14:10:50'),
(18, 8, NULL, 12, 12.00, 'Carte bancaire', 'Payé', '2026-07-07 14:16:44'),
(19, 8, NULL, 13, 400.00, 'Carte bancaire', 'Payé', '2026-07-07 14:17:33'),
(20, 8, NULL, NULL, 120.00, 'Carte bancaire', 'Payé', '2026-07-07 14:17:49'),
(21, 8, NULL, 14, 15.00, 'Carte bancaire', 'Payé', '2026-07-07 14:48:31'),
(22, 8, 10, NULL, 60.00, 'Carte bancaire', 'Payé', '2026-07-07 15:51:35'),
(23, 8, NULL, 15, 47.00, 'Carte bancaire', 'Payé', '2026-07-07 15:51:47'),
(24, 8, NULL, 16, 48.00, 'Carte bancaire', 'Payé', '2026-07-07 16:25:11');

-- --------------------------------------------------------

--
-- Structure de la table `plats`
--

DROP TABLE IF EXISTS `plats`;
CREATE TABLE IF NOT EXISTS `plats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categorie` enum('Petit-déjeuner','Déjeuner','Dîner','Jus') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `plats`
--

INSERT INTO `plats` (`id`, `nom`, `categorie`, `prix`, `image`, `disponible`) VALUES
(8, 'Mafé', 'Déjeuner', 15.00, 'uploads/plats/plat_6a4cef513e9f59.64065842.jpg', 1),
(10, 'Bissap', 'Jus', 12.00, 'uploads/plats/plat_6a4cf1da3c0802.09566242.jpg', 1),
(11, 'Salat de poison', 'Dîner', 20.00, 'uploads/plats/plat_6a4cf21d2312d0.96866089.jpg', 1),
(12, 'Thiaguri', 'Petit-déjeuner', 15.00, 'uploads/plats/plat_6a4cf243ea1fd4.65336860.jpg', 1),
(13, 'Ngalakh', 'Petit-déjeuner', 20.00, 'uploads/plats/plat_6a4cf260327bc1.33973711.jpg', 1),
(14, 'Boy', 'Jus', 12.00, 'uploads/plats/plat_6a4cf278098669.47719723.jpg', 1),
(15, 'Dakhar', 'Petit-déjeuner', 12.00, 'uploads/plats/plat_6a4cf287d7a4b8.32035485.jpg', 1),
(16, 'Yacha', 'Déjeuner', 200.00, 'uploads/plats/plat_6a4d0a1e6b4336.48441965.jpg', 1);

-- --------------------------------------------------------

--
-- Structure de la table `reinitialisations_mdp`
--

DROP TABLE IF EXISTS `reinitialisations_mdp`;
CREATE TABLE IF NOT EXISTS `reinitialisations_mdp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `jeton` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_expiration` datetime NOT NULL,
  `utilise` tinyint(1) NOT NULL DEFAULT '0',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jeton` (`jeton`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code_reservation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `utilisateur_id` int NOT NULL,
  `chambre_id` int NOT NULL,
  `date_arrivee` date NOT NULL,
  `date_depart` date NOT NULL,
  `nombre_chambres` int NOT NULL DEFAULT '1',
  `montant_total` decimal(10,2) NOT NULL,
  `statut` enum('En attente','Confirmée','Annulée') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'En attente',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `plat_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_reservation` (`code_reservation`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `chambre_id` (`chambre_id`),
  KEY `reservations_ibfk_plat` (`plat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `code_reservation`, `utilisateur_id`, `chambre_id`, `date_arrivee`, `date_depart`, `nombre_chambres`, `montant_total`, `statut`, `date_creation`, `plat_id`) VALUES
(4, 'RSV-20260630-E151', 8, 7, '2026-07-01', '2026-07-02', 2, 280.00, 'Confirmée', '2026-06-30 16:47:53', NULL),
(5, 'RSV-20260707-AD1B', 8, 6, '2026-07-07', '2026-07-08', 2, 200.00, 'Confirmée', '2026-07-07 13:19:10', NULL),
(7, 'RSV-20260707-CBCE', 8, 5, '2026-07-07', '2026-07-08', 1, 60.00, 'Confirmée', '2026-07-07 13:55:20', NULL),
(10, 'RSV-20260707-F568', 8, 5, '2026-07-07', '2026-07-08', 1, 60.00, 'Confirmée', '2026-07-07 15:51:33', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_complet` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('client','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'client',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `jeton_reinitialisation` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jeton_expiration` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom_complet`, `email`, `telephone`, `mot_de_passe`, `role`, `date_creation`, `jeton_reinitialisation`, `jeton_expiration`) VALUES
(7, 'Administrateur du hotel', 'admin@hotel.com', '+221770248472', '$2y$12$ZJ0BcJCPhvYXT3Bpykz9nuoVS1bpxj6bFydFWY0HciRC9uQv5MJGS', 'admin', '2026-06-30 13:01:30', 'e35ef20c83838acc2e1eb2cef4c0b94290a2b345ea35d86aa2f315387b7af543', '2026-06-30 17:31:53'),
(8, 'Elh Toure', 'elhajitoure720@gmail.com', '+221774393608', '$2y$12$b7ftzUOuUDYJzuu0/FNLGOFz1qk4Ek592dQ8etToPSfXCAAVDdzsu', 'client', '2026-06-30 13:04:36', NULL, NULL);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `commande_details`
--
ALTER TABLE `commande_details`
  ADD CONSTRAINT `commande_details_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commande_details_ibfk_2` FOREIGN KEY (`plat_id`) REFERENCES `plats` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `paiements_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `paiements_ibfk_2` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `paiements_ibfk_3` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `reinitialisations_mdp`
--
ALTER TABLE `reinitialisations_mdp`
  ADD CONSTRAINT `reinitialisations_mdp_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`chambre_id`) REFERENCES `chambres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_plat` FOREIGN KEY (`plat_id`) REFERENCES `plats` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
