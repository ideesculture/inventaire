-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Ven 23 Novembre 2012 à 18:45
-- Version du serveur: 5.5.28
-- Version de PHP: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `inventaire`
--

-- --------------------------------------------------------

--
-- Structure de la table `inventaire_depot`
--

CREATE TABLE IF NOT EXISTS `inventaire_depot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numdep` varchar(255) NOT NULL,
  `numinv` text NOT NULL,
  `date_ref_acte_depot` text NOT NULL,
  `date_entree` date NOT NULL,
  `proprietaire` text NOT NULL,
  `date_ref_acte_fin` text NOT NULL,
  `date_inscription` date NOT NULL,
  `designation` text NOT NULL,
  `inscription` text,
  `materiaux` text NOT NULL,
  `techniques` text NOT NULL,
  `mesures` text NOT NULL,
  `etat` text NOT NULL,
  `auteur` text NOT NULL,
  `epoque` text NOT NULL,
  `usage` text NOT NULL,
  `provenance` text NOT NULL,
  `observations` text NOT NULL,
  `validated` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numdep` (`numdep`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `inventaire`
--

CREATE TABLE IF NOT EXISTS `inventaire_inventaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numinv` varchar(255) NOT NULL,
  `designation` text,
  `mode_acquisition` text NOT NULL,
  `donateur` text NOT NULL,
  `date_acquisition` text NOT NULL,
  `avis` text NOT NULL,
  `prix` text NOT NULL,
  `date_inscription` text NOT NULL,
  `observations` text NOT NULL,
  `inscription` text,
  `materiaux` text NOT NULL,
  `techniques` text NOT NULL,
  `mesures` text NOT NULL,
  `etat` text NOT NULL,
  `auteur` text NOT NULL,
  `epoque` text NOT NULL,
  `usage` text NOT NULL,
  `provenance` text NOT NULL,
  `validated` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numinv` (`numinv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `photo`
--

CREATE TABLE IF NOT EXISTS `inventaire_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventaire_id` int(11) NOT NULL,
  `credits` text NOT NULL,
  `file` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventaire_id` (`inventaire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `inventaire_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `user`
--

INSERT INTO `inventaire_users` (`user_id`, `username`, `email`, `display_name`, `password`) VALUES
(1, 'test', 'contact@ideesculture.com', 'Utilisateur Test', '$2y$14$oGcW9PEox6Z1EA7551Jv2.sVNvwv0bcpTKTzkicuR.635zJgxCNnC');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
