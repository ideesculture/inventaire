-- MySQL dump 10.13  Distrib 5.5.31, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: inventairemusee
-- ------------------------------------------------------
-- Server version	5.5.31-0ubuntu0.12.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `inventaire_depot`
--

DROP TABLE IF EXISTS `inventaire_depot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventaire_depot` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventaire_depot`
--

LOCK TABLES `inventaire_depot` WRITE;
/*!40000 ALTER TABLE `inventaire_depot` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventaire_depot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventaire_inventaire`
--

DROP TABLE IF EXISTS `inventaire_inventaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventaire_inventaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ca_id` bigint(20) DEFAULT NULL,
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
  UNIQUE KEY `numinv` (`numinv`),
  KEY `ca_id` (`ca_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventaire_inventaire`
--

LOCK TABLES `inventaire_inventaire` WRITE;
/*!40000 ALTER TABLE `inventaire_inventaire` DISABLE KEYS */;
INSERT INTO `inventaire_inventaire` VALUES (1,NULL,'2008.8.5','Plat aux armes de François-Maurice Gontieri, archevêque d\'Avignon','cession à titre gratuit','Henri Crocq (donateur) ; vente Dupille de Saint-Séverin, Paris, 1785/02/21-26, n° 37','2013-01-07','CSN 25/05/2004 F pour un avis favorable rendu le 25 mai 2004 par la commission scientifique nationale.','120','2013-01-08','Plat aux armes de François-Maurice Gontieri, archevêque d\'Avignon','Sur la face A, deux inscriptions désignent, l\'une la déesse (au-dessus de son bras gauche : Ahénaiai), l\'autre le géant (dans son dos : Enkelados) ','bronze (fonte à la cire perdue)','Burin (gravure)','H. 18.5, l. 6.8 (avec socle) ; H. 13.2, l. 4.5, P. 5.6 (sans socle)','bon état ; restauré : 1998. IRRAP','HORDUBOIS Nicolas (peintre)','2e millénaire av JC (fin) ; 1er millénaire av JC (début)','viticulture (utilisation primaire) ; décoration (utilisation détournée)','Cameroun (lieu d\'utilisation)',0),(4,30,'2013.40','Adam et Eve chassés du Paradis','','','June 2 2012','Commission Scientifique des Musées Nationaux (CSMN),Défavorable,September 9 2012','EUR 1567.00','','aucune observations','','technique bois_ivoire\n','peinture à l\'huile','88,0 cm,208,0 cm,Hors cadre','-,March 1 2013,parfait','Masaccio (Auteur)','1876','-','',1),(9,5,'2012.10','Le jardin japonais','mode d\'acquisition','Claude Monet (Donateur) Nathalie Felten (Testateur) Gautier Michelin (Vendeur)','2011-11-21','Défavorable,Commission Interministérielle des Dations (CID),ok parfait,January 28 2013','18,00','2013-03-02','observations','justification\n,aucune','technique céramique\n','peinture à l\'huile','2 296,0 cm,1 528,0 cm,Hors cadre','Traces d\'humidité,February 21 2013,commentaires du constat d\'état','Claude Monet (Auteur)','1900','abattage des animaux\n','Paraguay\n France\n',0),(10,36,'2013.67','La mosquée du Shah','Achats','','1970-01-01','','0,00','1970-01-01','','','','','','','','','','',0),(13,11,'2012.25','Porte d\'Ishtar','','Nabuchodonosor II (Auteur)','','','','','','','','','28,0 m,11,0 m,-','','Nabuchodonosor II (Auteur)','','','',0);
/*!40000 ALTER TABLE `inventaire_inventaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventaire_photo`
--

DROP TABLE IF EXISTS `inventaire_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventaire_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventaire_id` int(11) NOT NULL,
  `credits` text NOT NULL,
  `file` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventaire_id` (`inventaire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventaire_photo`
--

LOCK TABLES `inventaire_photo` WRITE;
/*!40000 ALTER TABLE `inventaire_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventaire_photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventaire_users`
--

DROP TABLE IF EXISTS `inventaire_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventaire_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventaire_users`
--

LOCK TABLES `inventaire_users` WRITE;
/*!40000 ALTER TABLE `inventaire_users` DISABLE KEYS */;
INSERT INTO `inventaire_users` VALUES (1,'test','contact@ideesculture.com','Utilisateur Test','$2y$14$oGcW9PEox6Z1EA7551Jv2.sVNvwv0bcpTKTzkicuR.635zJgxCNnC');
/*!40000 ALTER TABLE `inventaire_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-05-06 23:20:59
