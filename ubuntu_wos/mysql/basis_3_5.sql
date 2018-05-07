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
-- Table structure for table `answers`
--

DROP TABLE IF EXISTS `answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `answers` (
  `answer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `answer_text` varchar(100) CHARACTER SET utf8 NOT NULL,
  `answer_order_id` int(4) unsigned NOT NULL,
  `is_right_answer` tinyint(4) NOT NULL DEFAULT '0',
  `question_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`answer_id`),
  KEY `answers_ibfk_1` (`question_id`),
  CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answers`
--

LOCK TABLES `answers` WRITE;
/*!40000 ALTER TABLE `answers` DISABLE KEYS */;
INSERT INTO `answers` VALUES (3,'23_answer_1.png',1,1,3),(4,'23_answer_2.png',2,0,3),(5,'24_answer_1.png',1,0,4),(6,'24_answer_2.png',2,0,4),(7,'24_answer_3.png',3,1,4),(8,'25_answer_1.png',1,0,5),(9,'25_answer_2.png',2,0,5),(10,'25_answer_3.png',3,1,5),(11,'25_answer_4.png',4,1,5),(12,'26_answer_1.png',1,0,6),(13,'26_answer_2.png',2,1,6),(14,'26_answer_3.png',3,0,6);
/*!40000 ALTER TABLE `answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lections`
--

DROP TABLE IF EXISTS `lections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lections` (
  `lection_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lection_name` varchar(40) CHARACTER SET utf8 NOT NULL,
  `lection_link` varchar(40) NOT NULL DEFAULT 'default',
  `is_file_opened` tinyint(4) NOT NULL DEFAULT '0',
  `topic_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`lection_id`),
  UNIQUE KEY `lection_link` (`lection_link`),
  KEY `lection_name` (`lection_name`(5)),
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
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questions` (
  `question_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_text` varchar(500) CHARACTER SET utf8 NOT NULL,
  `question_image` varchar(40) DEFAULT NULL,
  `test_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`question_id`),
  KEY `questions_ibfk_1` (`test_id`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `tests` (`test_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
INSERT INTO `questions` VALUES (3,'Найти производную','32_question_0.png',2),(4,'Продифференцируй','32_question_1.png',2),(5,'Определение производной:<br>Производной f(x) в точке x называется:','',2),(6,'Найти производную фукнции','32_question_3.png',2);
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sign_in`
--

DROP TABLE IF EXISTS `sign_in`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sign_in` (
  `user_id` int(10) unsigned NOT NULL,
  `email` varchar(50) NOT NULL,
  `nickname` varchar(30) CHARACTER SET utf8 NOT NULL,
  `profile_link` VARCHAR(40) NOT NULL,
  `password` varchar(30) CHARACTER SET utf8 NOT NULL,
  `editor` tinyint(4) NOT NULL DEFAULT '0',
  UNIQUE KEY `nickname` (`nickname`),
  UNIQUE KEY `email` (`email`),
  KEY `nickname_2` (`nickname`(5)),
  KEY `user_id` (`user_id`),
  CONSTRAINT `sign_in_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_primary_data` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sign_in`
--

LOCK TABLES `sign_in` WRITE;
/*!40000 ALTER TABLE `sign_in` DISABLE KEYS */;
INSERT INTO `sign_in` VALUES (1,'trymote@mail.ru','trymote','trymote.php','3eHNXeMwobaN2',1);
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
  `status_name` varchar(40) CHARACTER SET utf8 NOT NULL,
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
INSERT INTO `statuses` VALUES (2,'Посвященный', 100);
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_results`
--

DROP TABLE IF EXISTS `users_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_results` (
  `user_id` int(10) unsigned NOT NULL,
  `health_left` int(10) unsigned NOT NULL,
  `test_id` int(10) unsigned NOT NULL,
  `date_record` datetime DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `users_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `sign_in` (`user_id`),
  CONSTRAINT `users_results_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `tests` (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_results`
--

LOCK TABLES `users_results` WRITE;
/*!40000 ALTER TABLE `users_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_results` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teachers` (
  `teacher_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `teacher_name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `subject_id` varchar(5) NOT NULL,
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
-- Table structure for table `tests`
--

DROP TABLE IF EXISTS `tests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tests` (
  `test_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `test_link` varchar(40) NOT NULL DEFAULT 'default',
  `topic_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`test_id`),
  UNIQUE KEY `test_link` (`test_link`),
  KEY `tests_ibfk_1` (`topic_id`),
  CONSTRAINT `tests_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`topic_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tests`
--

LOCK TABLES `tests` WRITE;
/*!40000 ALTER TABLE `tests` DISABLE KEYS */;
INSERT INTO `tests` VALUES (2,'m3_test_.php',3);
/*!40000 ALTER TABLE `tests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `topics`
--

DROP TABLE IF EXISTS `topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `topics` (
  `topic_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topic_name` varchar(40) CHARACTER SET utf8 NOT NULL,
  `test_id` int(10) unsigned DEFAULT '0',
  `topic_image` varchar(40) NOT NULL DEFAULT 'default',
  `topic_attack` varchar(40) NOT NULL DEFAULT 'default',
  `topic_fail` varchar(40) NOT NULL DEFAULT 'default',
  `subject_id` varchar(5) NOT NULL,
  PRIMARY KEY (`topic_id`),
  UNIQUE KEY `topic_name` (`topic_name`),
  KEY `topic_name_2` (`topic_name`(5)),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `topics`
--

LOCK TABLES `topics` WRITE;
/*!40000 ALTER TABLE `topics` DISABLE KEYS */;
INSERT INTO `topics` VALUES (3,'Производные',2,'default','default','default','M');
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
  `first_name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `last_name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `email_ver` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `first_name` (`first_name`(5)),
  KEY `last_name` (`last_name`(5))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_primary_data`
--

LOCK TABLES `user_primary_data` WRITE;
/*!40000 ALTER TABLE `user_primary_data` DISABLE KEYS */;
INSERT INTO `user_primary_data` VALUES (1,'Мир','Сессий', 1);
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
  `gender` varchar(6) DEFAULT NULL,
  `creation_date` datetime DEFAULT CURRENT_TIMESTAMP,
  KEY `user_xp` (`user_xp`),
  KEY `gender` (`gender`(1)),
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
INSERT INTO `user_second_data` VALUES (1,'default.png',999999,99,'','2018-04-27 22:49:49');
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
  `topic_id` int(10) unsigned NOT NULL,
  `progress` int(3) unsigned NOT NULL DEFAULT '0',
  `start_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `progress` (`progress`),
  KEY `start_date` (`start_date`),
  KEY `user_id` (`user_id`),
  KEY `topic_id` (`topic_id`),
  CONSTRAINT `user_subjects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_primary_data` (`user_id`),
  CONSTRAINT `user_subjects_ibfk_2` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`topic_id`)
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

-- Dump completed on 2018-04-27 22:51:18
