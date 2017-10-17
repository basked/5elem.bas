CREATE DATABASE  IF NOT EXISTS `user1111058_5elem_db` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `user1111058_5elem_db`;
-- MySQL dump 10.13  Distrib 5.7.12, for Win32 (AMD64)
--
-- Host: 127.0.0.1    Database: user1111058_5elem_db
-- ------------------------------------------------------
-- Server version	5.5.50

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
-- Table structure for table `s_pars_category`
--

DROP TABLE IF EXISTS `s_pars_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s_pars_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `catId` int(11) DEFAULT NULL COMMENT 'id category',
  `catName` varchar(128) DEFAULT NULL COMMENT '''name category''',
  `sectId` int(11) DEFAULT NULL COMMENT 'id section',
  `cntPage` int(11) DEFAULT NULL COMMENT 'count of pages',
  `dateIns` date DEFAULT NULL COMMENT 'date insert record',
  `act` int(11) DEFAULT NULL COMMENT 'actual record',
  `catURL` varchar(200) DEFAULT NULL COMMENT '''url category''',
  `idParsing` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_Parsing_Category_idx` (`idParsing`),
  CONSTRAINT `FK_Main_Category` FOREIGN KEY (`idParsing`) REFERENCES `s_pars_main` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=538 DEFAULT CHARSET=utf8 COMMENT='Table from category info';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s_pars_category`
--

LOCK TABLES `s_pars_category` WRITE;
/*!40000 ALTER TABLE `s_pars_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `s_pars_category` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-10-18  0:00:10
