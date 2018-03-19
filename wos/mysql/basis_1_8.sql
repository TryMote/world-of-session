-- MySQL dump 10.16  Distrib 10.1.30-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: wos
-- ------------------------------------------------------
-- Server version	10.1.30-MariaDB

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
-- Table structure for table `lections`
--
SET NAMES 'utf8';
DROP TABLE IF EXISTS `lections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lections` (
  `lection_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lection_name` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `topic_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`lection_id`),
  UNIQUE KEY `lection_name` (`lection_name`),
  KEY `lection_name_2` (`lection_name`(5)),
  KEY `topic_id` (`topic_id`),
  CONSTRAINT `lections_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lections`
--

LOCK TABLES `lections` WRITE;
/*!40000 ALTER TABLE `lections` DISABLE KEYS */;
/*!40000 ALTER TABLE `lections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sign_in`
--

DROP TABLE IF EXISTS `sign_in`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sign_in` (
  `user_id` int(10) unsigned NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `nickname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `password` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  UNIQUE KEY `nickname` (`nickname`),
  KEY `nickname_2` (`nickname`(5)),
  KEY `user_id` (`user_id`),
  KEY `email` (`email`),
  CONSTRAINT `sign_in_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_primary_data` (`user_id`),
  CONSTRAINT `sign_in_ibfk_2` FOREIGN KEY (`email`) REFERENCES `user_primary_data` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sign_in`
--

LOCK TABLES `sign_in` WRITE;
/*!40000 ALTER TABLE `sign_in` DISABLE KEYS */;
/*!40000 ALTER TABLE `sign_in` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statuses` (
  `status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status_name` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status_xp` int(5) unsigned NOT NULL,
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `status_name` (`status_name`),
  UNIQUE KEY `status_xp` (`status_xp`),
  KEY `status_name_2` (`status_name`(5)),
  KEY `status_xp_2` (`status_xp`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuses`
--

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES (1,'ЕГЭ Мастер',0);
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `subject_id` varchar(4) NOT NULL DEFAULT 'UND',
  `subject_name` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`subject_id`),
  UNIQUE KEY `subject_id` (`subject_id`),
  UNIQUE KEY `subject_name` (`subject_name`),
  KEY `subject_name_2` (`subject_name`(5))
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES ('M','Математика');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teachers` (
  `teacher_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `teacher_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `subject_id` varchar(4) NOT NULL,
  PRIMARY KEY (`teacher_id`),
  KEY `teacher_name` (`teacher_name`(5)),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `topics`
--

DROP TABLE IF EXISTS `topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `topics` (
  `topic_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topic_name` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `subject_id` varchar(2) NOT NULL,
  PRIMARY KEY (`topic_id`),
  UNIQUE KEY `topic_name` (`topic_name`),
  KEY `topic_name_2` (`topic_name`(5)),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `topics`
--

LOCK TABLES `topics` WRITE;
/*!40000 ALTER TABLE `topics` DISABLE KEYS */;
/*!40000 ALTER TABLE `topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_primary_data`
--

DROP TABLE IF EXISTS `user_primary_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_primary_data` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `last_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `first_name` (`first_name`(5)),
  KEY `last_name` (`last_name`(5)),
  KEY `email_2` (`email`(10))
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_primary_data`
--

LOCK TABLES `user_primary_data` WRITE;
/*!40000 ALTER TABLE `user_primary_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_primary_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_second_data`
--

DROP TABLE IF EXISTS `user_second_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_second_data` (
  `user_id` int(10) unsigned NOT NULL,
  `image` varchar(50) DEFAULT NULL,
  `user_xp` int(5) unsigned NOT NULL DEFAULT '0',
  `status_id` int(10) unsigned NOT NULL DEFAULT '1',
  `gender` tinyint(4) DEFAULT '0',
  `creation_date` datetime DEFAULT CURRENT_TIMESTAMP,
  KEY `user_xp` (`user_xp`),
  KEY `gender` (`gender`),
  KEY `creation_date` (`creation_date`),
  KEY `user_id` (`user_id`),
  KEY `status_id` (`status_id`),
  CONSTRAINT `user_second_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_primary_data` (`user_id`),
  CONSTRAINT `user_second_data_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_second_data`
--

LOCK TABLES `user_second_data` WRITE;
/*!40000 ALTER TABLE `user_second_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_second_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_subjects`
--

DROP TABLE IF EXISTS `user_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_subjects` (
  `user_id` int(10) unsigned NOT NULL,
  `lection_id` int(10) unsigned NOT NULL,
  `progress` int(3) unsigned NOT NULL DEFAULT '0',
  `start_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `progress` (`progress`),
  KEY `start_date` (`start_date`),
  KEY `user_id` (`user_id`),
  KEY `lection_id` (`lection_id`),
  CONSTRAINT `user_subjects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_primary_data` (`user_id`),
  CONSTRAINT `user_subjects_ibfk_2` FOREIGN KEY (`lection_id`) REFERENCES `lections` (`lection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_subjects`
--

LOCK TABLES `user_subjects` WRITE;
/*!40000 ALTER TABLE `user_subjects` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_subjects` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-03-19 20:11:03
