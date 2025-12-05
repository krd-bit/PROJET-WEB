-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : ven. 05 déc. 2025 à 09:54
-- Version du serveur : 11.5.2-MariaDB
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `quizzeo_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `choices`
--

DROP TABLE IF EXISTS `choices`;
CREATE TABLE IF NOT EXISTS `choices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `choice_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `choices`
--

INSERT INTO `choices` (`id`, `question_id`, `choice_text`, `is_correct`) VALUES
(1, 2, 'Oui', 1),
(2, 2, 'Non', 0),
(3, 3, 'Oui', 1),
(4, 3, 'Non', 0),
(5, 4, 'Oui', 1),
(6, 4, 'Non', 0),
(7, 5, 'Oui ', 1),
(8, 5, 'Non', 0);

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `points` int(11) DEFAULT 1,
  `type_question` varchar(20) DEFAULT 'qcm',
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_id` (`quiz_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `points`, `type_question`, `type`) VALUES
(1, 3, '1*1 +2?', 1, 'qcm', '0'),
(2, 4, 'Tu as 18 ans', 1, 'qcm', 'qcm'),
(3, 4, 'Tu t\'appel Momo?', 1, 'qcm', 'qcm'),
(4, 5, 'Bonjour cv ?', 1, 'qcm', 'qcm'),
(5, 5, 'Tu as 18 ans ?', 1, 'qcm', 'qcm');

-- --------------------------------------------------------

--
-- Structure de la table `quizzes`
--

DROP TABLE IF EXISTS `quizzes`;
CREATE TABLE IF NOT EXISTS `quizzes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'building',
  `author_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `status`, `author_id`) VALUES
(1, 'Quizz culture général', 'live', 4),
(2, 'QCM', 'live', 4),
(3, 'Quizz sur le foot', 'live', 4),
(4, 'Quizz test', 'live', 4),
(5, 'Quizz google', 'live', 2);

-- --------------------------------------------------------

--
-- Structure de la table `results`
--

DROP TABLE IF EXISTS `results`;
CREATE TABLE IF NOT EXISTS `results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `date_played` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `quiz_id` (`quiz_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `results`
--

INSERT INTO `results` (`id`, `user_id`, `quiz_id`, `score`, `date_played`) VALUES
(1, 4, 2, 0, '2025-12-03 19:44:04'),
(2, 4, 1, 0, '2025-12-03 19:44:12'),
(3, 5, 4, 1, '2025-12-04 11:11:20'),
(4, 5, 5, 2, '2025-12-04 14:18:02');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `est_actif` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `company_name`, `is_active`, `est_actif`) VALUES
(1, 'admin@quizzeo.com', '1234', 'admin', 'Admin Principal', 1, 1),
(2, 'google@gmail.com', '$2y$10$036utBPQbwLlbtwz.vnA8OcyMRmzjKZRVOyPpN7frtTYwjx7MEGuO', 'company', 'Google', 1, 1),
(3, 'superadmin@quizz.com', '$2y$10$NX13pw7bF0Zm4Q6z89Noe.61wIs709HavfYsHUH17CDrP4lOe1aUa', 'admin', 'Admin', 1, 1),
(4, 'mc@gmail.com', '$2y$10$HGqKIdTR9JwxRBrU41HAceyO91c7JAbTeBY8H6qVSGT2djgUGmOHG', 'school', 'Marcel Sembat', 0, 1),
(5, 'ibra@gmail.com', '$2y$10$QFKTG9RPDadDCzekMCmsFuf8zDqVF1RJXnCvzM5I457K58q1pSiI2', 'user', 'iBRAHIM', 1, 1);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `choices`
--
ALTER TABLE `choices`
  ADD CONSTRAINT `choices_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
