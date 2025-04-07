-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : lun. 07 avr. 2025 à 10:47
-- Version du serveur : 8.0.40
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `leboncoin`
--
CREATE DATABASE IF NOT EXISTS `leboncoin` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `leboncoin`;

-- --------------------------------------------------------

--
-- Structure de la table `annonces`
--

CREATE TABLE `annonces` (
  `id` int NOT NULL,
  `titre` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `ville` varchar(80) NOT NULL,
  `categorie` varchar(50) NOT NULL,
  `user_id` int NOT NULL,
  `date_publication` datetime DEFAULT CURRENT_TIMESTAMP,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `annonces`
--

INSERT INTO `annonces` (`id`, `titre`, `description`, `prix`, `ville`, `categorie`, `user_id`, `date_publication`, `image`) VALUES
(1, 'Vélo de course', 'Vélo de course en bon état, taille 54cm', 250.00, 'Paris', 'Sport', 2, '2025-04-07 01:10:39', 'velo.jpg'),
(2, 'Canapé d\'angle', 'Canapé cuir beige 3 places', 450.00, 'Lyon', 'Maison', 2, '2025-04-07 01:10:39', 'canape.jpg'),
(3, 'zdzd', 'AASASAS', 1902.00, 'Paris', 'Maison', 2, '2025-04-07 01:10:44', NULL),
(6, 'test', 'zdkjznkyzgzd', 98273.00, 'Paris', 'Maison', 3, '2025-04-07 11:08:02', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'john', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'john@example.com', '2025-04-07 01:10:39'),
(2, 'emma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'emma@example.com', '2025-04-07 01:10:39'),
(3, 'aiman_', '$2y$10$.Mi1g8MjQPqd6U0FXhQYSekvo09zxzkeXRBnfYT3mt4g16Aoe6oqa', 'tahiraiman338@gmail.com', '2025-04-07 01:16:55');

-- --------------------------------------------------------

--
-- Structure de la table `user_favoris`
--

CREATE TABLE `user_favoris` (
  `user_id` int NOT NULL,
  `annonce_id` int NOT NULL,
  `date_ajout` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user_favoris`
--

INSERT INTO `user_favoris` (`user_id`, `annonce_id`, `date_ajout`) VALUES
(1, 1, '2025-04-07 01:10:39'),
(1, 2, '2025-04-07 01:10:39'),
(2, 1, '2025-04-07 01:10:39'),
(3, 1, '2025-04-07 10:40:05'),
(3, 2, '2025-04-07 10:40:04');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `user_favoris`
--
ALTER TABLE `user_favoris`
  ADD PRIMARY KEY (`user_id`,`annonce_id`),
  ADD KEY `annonce_id` (`annonce_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annonces`
--
ALTER TABLE `annonces`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `user_favoris`
--
ALTER TABLE `user_favoris`
  ADD CONSTRAINT `user_favoris_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_favoris_ibfk_2` FOREIGN KEY (`annonce_id`) REFERENCES `annonces` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
