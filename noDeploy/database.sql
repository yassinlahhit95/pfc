-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: yassjjzw_pfc
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academic_config`
--

DROP TABLE IF EXISTS `academic_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_config` (
  `idConfig` int NOT NULL AUTO_INCREMENT,
  `idCentro` int DEFAULT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Configuración académica',
  `anioAcademico` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipoEducacion` enum('grado_basico','grado_medio','grado_superior','colegio','otro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'otro',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creadoEn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizadoEn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idConfig`),
  KEY `idx_ac_centro_activo` (`idCentro`,`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_config`
--

LOCK TABLES `academic_config` WRITE;
/*!40000 ALTER TABLE `academic_config` DISABLE KEYS */;
INSERT INTO `academic_config` VALUES (1,NULL,'Configuración heredada (auto-generada)',NULL,'otro',1,'2026-07-21 16:23:31','2026-07-21 16:23:31');
/*!40000 ALTER TABLE `academic_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_periods`
--

DROP TABLE IF EXISTS `academic_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_periods` (
  `idPeriodo` int NOT NULL AUTO_INCREMENT,
  `idConfig` int NOT NULL,
  `nombre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('evaluacion','recuperacion','ordinaria','extraordinaria','final','proyecto','practicas','certificacion','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'evaluacion',
  `fechaInicio` date DEFAULT NULL,
  `fechaFin` date DEFAULT NULL,
  `orden` int NOT NULL DEFAULT '1',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `bloqueado` tinyint(1) NOT NULL DEFAULT '0',
  `peso` decimal(5,2) NOT NULL DEFAULT '100.00',
  `idPeriodoRecuperaDe` int DEFAULT NULL,
  PRIMARY KEY (`idPeriodo`),
  KEY `idPeriodoRecuperaDe` (`idPeriodoRecuperaDe`),
  KEY `idx_periodo_config_orden` (`idConfig`,`orden`),
  CONSTRAINT `academic_periods_ibfk_1` FOREIGN KEY (`idConfig`) REFERENCES `academic_config` (`idConfig`) ON DELETE CASCADE,
  CONSTRAINT `academic_periods_ibfk_2` FOREIGN KEY (`idPeriodoRecuperaDe`) REFERENCES `academic_periods` (`idPeriodo`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_periods`
--

LOCK TABLES `academic_periods` WRITE;
/*!40000 ALTER TABLE `academic_periods` DISABLE KEYS */;
INSERT INTO `academic_periods` VALUES (1,1,'1ª Evaluación','evaluacion',NULL,NULL,1,1,0,100.00,NULL),(2,1,'2ª Evaluación','evaluacion',NULL,NULL,3,1,0,100.00,NULL),(3,1,'Recuperación 1ª Evaluación','recuperacion',NULL,NULL,2,1,0,100.00,1),(4,1,'Recuperación 2ª Evaluación','recuperacion',NULL,NULL,4,1,0,100.00,2);
/*!40000 ALTER TABLE `academic_periods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_templates`
--

DROP TABLE IF EXISTS `academic_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_templates` (
  `idPlantilla` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `configuracionJson` json NOT NULL,
  `editable` tinyint(1) NOT NULL DEFAULT '1',
  `creadoEn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idPlantilla`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_templates`
--

LOCK TABLES `academic_templates` WRITE;
/*!40000 ALTER TABLE `academic_templates` DISABLE KEYS */;
INSERT INTO `academic_templates` VALUES (1,'Estándar FP Grado Medio','Configuración de partida para ciclos de Grado Medio: 2 evaluaciones + recuperación, examen 75% / reto 25%, aprobado 5.','{\"config\": {\"activo\": 1, \"nombre\": \"Configuración heredada (auto-generada)\", \"creadoEn\": \"2026-07-21 18:23:31\", \"idCentro\": null, \"idConfig\": 1, \"actualizadoEn\": \"2026-07-21 18:23:31\", \"anioAcademico\": null, \"tipoEducacion\": \"grado_medio\"}, \"periods\": [{\"peso\": \"100.00\", \"tipo\": \"evaluacion\", \"orden\": 1, \"nombre\": \"1ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 1, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": null}, {\"peso\": \"100.00\", \"tipo\": \"recuperacion\", \"orden\": 2, \"nombre\": \"Recuperación 1ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 3, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": 1}, {\"peso\": \"100.00\", \"tipo\": \"evaluacion\", \"orden\": 3, \"nombre\": \"2ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 2, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": null}, {\"peso\": \"100.00\", \"tipo\": \"recuperacion\", \"orden\": 4, \"nombre\": \"Recuperación 2ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 4, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": 2}], \"tfg_config\": {\"idConfig\": 1, \"habilitado\": 1, \"notaMinima\": \"5.00\", \"idConfigTFG\": 1, \"pesoEnMedia\": \"1.00\", \"requiereComite\": 0, \"requiereDefensa\": 0, \"permiteRecuperacion\": 1}, \"grading_policy\": {\"idConfig\": 1, \"decimales\": 2, \"escalaMax\": \"10.00\", \"escalaMin\": \"0.00\", \"idPolitica\": 1, \"notaAprobado\": \"5.00\", \"pesoTfgEnMedia\": \"1.00\"}, \"promotion_rule\": {\"idRegla\": 1, \"idConfig\": 1, \"notaMinimaGlobal\": \"5.00\", \"requiereTodosModulos\": 1, \"permiteModulosPendientes\": 0}, \"assessment_types\": [{\"peso\": \"3.00\", \"orden\": 1, \"idTipo\": 1, \"nombre\": \"Examen\", \"origen\": \"examen\", \"visible\": 1, \"idConfig\": 1, \"notaMaxima\": \"10.00\", \"obligatorio\": 1, \"recuperable\": 1, \"aprobadoMinimo\": null, \"incluirEnMedia\": 1, \"editableDirector\": 1, \"editableProfesor\": 1}, {\"peso\": \"1.00\", \"orden\": 2, \"idTipo\": 2, \"nombre\": \"Reto\", \"origen\": \"reto\", \"visible\": 1, \"idConfig\": 1, \"notaMaxima\": \"10.00\", \"obligatorio\": 0, \"recuperable\": 1, \"aprobadoMinimo\": null, \"incluirEnMedia\": 1, \"editableDirector\": 1, \"editableProfesor\": 1}], \"challenge_config\": {\"idConfig\": 1, \"pesoDefecto\": \"1.00\", \"idConfigReto\": 1, \"permiteFases\": 0, \"permiteGrupal\": 0, \"evaluacionPares\": 0, \"requiereRubrica\": 0}, \"internship_config\": {\"idConfig\": 1, \"habilitado\": 0, \"idConfigFCT\": 1, \"pesoEnMedia\": \"0.00\", \"metodoEvaluacion\": \"ambos\", \"horasRequeridasDefecto\": 0, \"requiereAprobarParaTitular\": 1}}',1,'2026-07-21 16:23:31'),(2,'Estándar FP Grado Superior','Configuración de partida para ciclos de Grado Superior: misma estructura que Grado Medio, totalmente editable tras aplicarla.','{\"config\": {\"activo\": 1, \"nombre\": \"Configuración heredada (auto-generada)\", \"creadoEn\": \"2026-07-21 18:23:31\", \"idCentro\": null, \"idConfig\": 1, \"actualizadoEn\": \"2026-07-21 18:23:31\", \"anioAcademico\": null, \"tipoEducacion\": \"grado_superior\"}, \"periods\": [{\"peso\": \"100.00\", \"tipo\": \"evaluacion\", \"orden\": 1, \"nombre\": \"1ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 1, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": null}, {\"peso\": \"100.00\", \"tipo\": \"recuperacion\", \"orden\": 2, \"nombre\": \"Recuperación 1ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 3, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": 1}, {\"peso\": \"100.00\", \"tipo\": \"evaluacion\", \"orden\": 3, \"nombre\": \"2ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 2, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": null}, {\"peso\": \"100.00\", \"tipo\": \"recuperacion\", \"orden\": 4, \"nombre\": \"Recuperación 2ª Evaluación\", \"visible\": 1, \"fechaFin\": null, \"idConfig\": 1, \"bloqueado\": 0, \"idPeriodo\": 4, \"fechaInicio\": null, \"idPeriodoRecuperaDe\": 2}], \"tfg_config\": {\"idConfig\": 1, \"habilitado\": 1, \"notaMinima\": \"5.00\", \"idConfigTFG\": 1, \"pesoEnMedia\": \"1.00\", \"requiereComite\": 0, \"requiereDefensa\": 0, \"permiteRecuperacion\": 1}, \"grading_policy\": {\"idConfig\": 1, \"decimales\": 2, \"escalaMax\": \"10.00\", \"escalaMin\": \"0.00\", \"idPolitica\": 1, \"notaAprobado\": \"5.00\", \"pesoTfgEnMedia\": \"1.00\"}, \"promotion_rule\": {\"idRegla\": 1, \"idConfig\": 1, \"notaMinimaGlobal\": \"5.00\", \"requiereTodosModulos\": 1, \"permiteModulosPendientes\": 0}, \"assessment_types\": [{\"peso\": \"3.00\", \"orden\": 1, \"idTipo\": 1, \"nombre\": \"Examen\", \"origen\": \"examen\", \"visible\": 1, \"idConfig\": 1, \"notaMaxima\": \"10.00\", \"obligatorio\": 1, \"recuperable\": 1, \"aprobadoMinimo\": null, \"incluirEnMedia\": 1, \"editableDirector\": 1, \"editableProfesor\": 1}, {\"peso\": \"1.00\", \"orden\": 2, \"idTipo\": 2, \"nombre\": \"Reto\", \"origen\": \"reto\", \"visible\": 1, \"idConfig\": 1, \"notaMaxima\": \"10.00\", \"obligatorio\": 0, \"recuperable\": 1, \"aprobadoMinimo\": null, \"incluirEnMedia\": 1, \"editableDirector\": 1, \"editableProfesor\": 1}], \"challenge_config\": {\"idConfig\": 1, \"pesoDefecto\": \"1.00\", \"idConfigReto\": 1, \"permiteFases\": 0, \"permiteGrupal\": 0, \"evaluacionPares\": 0, \"requiereRubrica\": 0}, \"internship_config\": {\"idConfig\": 1, \"habilitado\": 0, \"idConfigFCT\": 1, \"pesoEnMedia\": \"0.00\", \"metodoEvaluacion\": \"ambos\", \"horasRequeridasDefecto\": 0, \"requiereAprobarParaTitular\": 1}}',1,'2026-07-21 16:23:31');
/*!40000 ALTER TABLE `academic_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account_lockout`
--

DROP TABLE IF EXISTS `account_lockout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_lockout` (
  `email` varchar(190) NOT NULL,
  `intentos` int unsigned NOT NULL DEFAULT '0',
  `window_start` int unsigned NOT NULL,
  `locked_until` int unsigned DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_lockout`
--

LOCK TABLES `account_lockout` WRITE;
/*!40000 ALTER TABLE `account_lockout` DISABLE KEYS */;
INSERT INTO `account_lockout` VALUES ('laura@aulapro.com',4,1784840639,NULL);
/*!40000 ALTER TABLE `account_lockout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `anuncios`
--

DROP TABLE IF EXISTS `anuncios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `anuncios` (
  `idAnuncio` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fechaAnuncio` datetime DEFAULT CURRENT_TIMESTAMP,
  `fechaExpiracion` date NOT NULL,
  `dirigidoA` enum('todos','estudiantes','profesores','tutores') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'todos',
  PRIMARY KEY (`idAnuncio`),
  KEY `idx_anuncio_fecha` (`fechaAnuncio`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `anuncios`
--

LOCK TABLES `anuncios` WRITE;
/*!40000 ALTER TABLE `anuncios` DISABLE KEYS */;
INSERT INTO `anuncios` VALUES (1,'Bienvenida al Año Académico 2026/2027','Les damos la más cordial bienvenida a todos los estudiantes y profesores a este nuevo año académico. Las clases comienzan el 15 de Septiembre a las 8:30.','2026-07-27 17:36:17','2026-10-31','todos'),(2,'Entrega de Proyectos TFG','Se recuerda a los estudiantes de 2º año que el plazo máximo para la subida del TFG y su documentación al Aula Virtual es el 15 de Junio.','2026-07-27 17:36:17','2027-06-15','estudiantes'),(3,'Reunión Extraordinaria de Claustro','Estimados docentes, se convoca una reunión extraordinaria de claustro para tratar las nuevas normativas de FP Dual el lunes 2 de Agosto a las 16:30.','2026-07-27 17:36:17','2026-08-03','profesores');
/*!40000 ALTER TABLE `anuncios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_tokens`
--

DROP TABLE IF EXISTS `api_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_type` enum('estudiante','profesor','director','tutor','secretaria') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `token` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_info` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL,
  `last_used_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_token` (`token`),
  KEY `idx_user` (`user_type`,`user_id`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_tokens`
--

LOCK TABLES `api_tokens` WRITE;
/*!40000 ALTER TABLE `api_tokens` DISABLE KEYS */;
INSERT INTO `api_tokens` VALUES (1,'director',1,'99c0bc6f50e5dca133a3f2df5f06ea2d250bf6fa9b299e3d6564f29adbfe3ecf','Android','2026-07-23 18:22:43','2026-08-22 18:22:43','2026-07-23 18:22:44'),(4,'director',1,'dc86f9a8d7466b44afbfbd53d8c3fbf0a4e13c678174f5f9c690d9e0e9a4ff68','','2026-07-23 18:28:47','2026-08-22 18:28:47','2026-07-23 18:28:47'),(5,'tutor',1,'aa695151610220e0b5007a378c70bf384f356319c31ff2db5e07f0a41630fd2b','','2026-07-23 18:28:47','2026-08-22 18:28:47','2026-07-23 18:28:47'),(8,'director',1,'1ee7f54e45391d4e4d55a24b407186163b0c5992c94104e6eaa248902a4397cc','','2026-07-23 18:40:17','2026-08-22 18:40:17','2026-07-23 18:40:17'),(9,'profesor',1,'3cac4f6184ac1cba8625e6db804537b72458220f6a530bc420b2cc565da72952','','2026-07-23 18:40:18','2026-08-22 18:40:18','2026-07-23 18:40:18'),(10,'director',1,'898c18e2fbe260264cb849b4494346a0caf42a09da936cc766ae03bf48e38740','Android','2026-07-23 18:47:32','2026-08-22 18:47:32','2026-07-23 18:51:01'),(15,'tutor',1,'d1c0f552e4dd21f3aa0fd127e694f96dfa549e026b5b1bbfe7b670704d1efa05','','2026-07-23 19:01:36','2026-08-22 19:01:36','2026-07-23 19:01:36'),(16,'profesor',1,'70085cf3d8a136404f6340782aa09ea9afa951c6774304ac97de7c295d181e30','','2026-07-23 19:01:36','2026-08-22 19:01:36','2026-07-23 19:01:36'),(17,'tutor',1,'3a8517515f692950e33968dde3a6e01b7b485cabbedfda37883b9e5d3dbcce38','','2026-07-23 19:02:14','2026-08-22 19:02:14','2026-07-23 19:02:15'),(18,'estudiante',3,'9714c7dfc31587e28c5cdbad742cb1c027766ec58b402026923cf0526ce29f6b','','2026-07-23 19:02:28','2026-08-22 19:02:28','2026-07-23 19:02:28'),(19,'profesor',1,'913d092defb4c92e11781ce80a9eacada0daa9f1804f681d57aca6d599cb1119','','2026-07-23 19:02:28','2026-08-22 19:02:28','2026-07-23 19:02:28'),(20,'director',1,'da14ae3b693c83e522ab2b9f911003a6de41e29aa0117b202fbec1f5aab91936','','2026-07-23 19:02:29','2026-08-22 19:02:29','2026-07-23 19:02:29'),(21,'profesor',1,'f609270be2c3487fcea8867ec1b8f5c21ab548bbb2cc31224a989998c9d7486a','','2026-07-23 19:04:22','2026-08-22 19:04:22','2026-07-23 19:04:22'),(22,'director',1,'a0293feccb6e81f27f90c1afea03dbc5ef376c50daf2f7b92f6511898226bd70','','2026-07-23 19:08:49','2026-08-22 19:08:49','2026-07-23 19:08:49'),(23,'director',1,'d66db94ef0c5dc418147469b89b52e59f9f72d9de8b338e0018cb56196b2c0c8','','2026-07-23 19:08:58','2026-08-22 19:08:58','2026-07-23 19:08:58'),(30,'estudiante',1,'8a62e25dd6c84fc41a27b90b1b1718e91d82b2380e4f28a4ae7b505940645767','','2026-07-23 19:42:23','2026-08-22 19:42:23','2026-07-23 19:42:23'),(31,'estudiante',1,'2e19cb79a10cfdfe401b0d195d63a66408de3f68d8049e4b7596d2b6f5722c10','','2026-07-23 19:42:24','2026-08-22 19:42:24',NULL),(32,'tutor',1,'1d579aa1d89579fd3b169e2232d04c3b6f0764dbda3cbbb193cf99e924b4c687','','2026-07-23 19:42:34','2026-08-22 19:42:34','2026-07-23 19:42:34'),(35,'tutor',1,'e58c27edc5025289cdebbc70f001a2808bbdfbc57f461c15eddf3a44a9c69ea5','Android','2026-07-23 21:51:16','2026-08-22 21:51:16','2026-07-23 21:54:56'),(36,'estudiante',2,'9c9da5c9a13190f93e7a8fe9d105dc4654fcc19a7adaae88c3981e3b8f45c616','','2026-07-23 22:05:02','2026-08-22 22:05:02','2026-07-23 22:05:02'),(37,'estudiante',2,'5d50c6a368f7545733e1d297f6d938273cec6c1a94c94f56818ffdf7f65dd363','','2026-07-23 22:05:20','2026-08-22 22:05:20','2026-07-23 22:05:20'),(38,'estudiante',2,'8967cb09781fa7c081c592cae5f1e1521cfacac2cc7338a6626dcb45103f5187','','2026-07-23 22:05:50','2026-08-22 22:05:50','2026-07-23 22:05:50'),(39,'estudiante',2,'4fdffd02e34612eec1c645813d0ae08dc85ff7ff339044c2db19b5ad3079d6f0','','2026-07-23 22:06:14','2026-08-22 22:06:14','2026-07-23 22:06:15'),(40,'tutor',2,'339443d9008100a8599d3159339ea32de030dab0a83f984ed2bb0e54f3fdaa7a','','2026-07-23 22:06:33','2026-08-22 22:06:33','2026-07-23 22:06:33'),(41,'profesor',2,'f71d43bdfffb3e5a674a2d8855679fc4abaadbe5cbd586ab6b1a50d1f2b61052','','2026-07-23 22:06:33','2026-08-22 22:06:33','2026-07-23 22:06:33'),(42,'profesor',1,'0dd02b69b57f5edd5ece99aca6fcd02fb5f786c605d59883221cda14c6c32011','Android','2026-07-23 22:20:52','2026-08-22 22:20:52','2026-07-23 23:43:26'),(43,'secretaria',1,'7cc5f823db354da192ffd759dbbec6ceaa1a3b98f0eab43609418864e0629b3b','','2026-07-23 23:03:59','2026-08-22 23:03:59','2026-07-23 23:04:40'),(44,'director',1,'373c27b3f1c28e5bde7f061c5791cb9eeae7320e86b0ce0b34d6897a1ca00fef','','2026-07-23 23:04:24','2026-08-22 23:04:24','2026-07-23 23:06:28'),(45,'profesor',2,'c7face573e51e73706cb85a691089332b2fe7be9b40b8474a30641ffe4d95eb4','','2026-07-23 23:04:24','2026-08-22 23:04:24','2026-07-23 23:04:59'),(46,'estudiante',2,'d9d9e2ebf85ee7ae31241528f18be2495cc64da33c74489eb0902f6ab2f4bee5','','2026-07-23 23:04:24','2026-08-22 23:04:24','2026-07-23 23:06:28'),(47,'tutor',1,'76ae15b721b71e9b2028733897bd71a15e1915860946ec647c195a4fad643dfd','','2026-07-23 23:04:24','2026-08-22 23:04:24','2026-07-23 23:04:42'),(48,'director',1,'911675bec53ba63c8cbad1f7e9eba5f58aaa48359eaffbe76566f0e38c1e66f6','','2026-07-24 01:01:40','2026-08-23 01:01:40','2026-07-24 01:01:49'),(49,'director',1,'28a50fe39e1339bedabfbf5a90db2322647618582516a1d4fb766d0480eee3eb','Android','2026-07-24 01:08:19','2026-08-23 01:08:19','2026-07-24 01:09:50'),(50,'estudiante',2,'34ebbfb5071a035a688c1aefe35ebe00c7813bf88429a20ee62fed79df6e2a1d','Android','2026-07-24 02:45:40','2026-08-23 02:45:40','2026-07-24 03:04:44'),(51,'director',1,'7a29b0c91c24d426687d50ad53171691528b3edbbe22f7ca0879508d2d7f9350','claude-audit','2026-07-24 19:27:18','2026-08-23 19:27:18','2026-07-24 19:31:38'),(52,'estudiante',1,'d9fae6d8e172f463a8c3eee6480aaa2fa5663c06b48f956794a5b24787b0fdcd','','2026-07-24 19:47:29','2026-08-23 19:47:29','2026-07-24 19:47:31'),(53,'estudiante',1,'110d1ed40b131f3cc746af0cef073324c2664bdee9a6f9f74ad24b1a15447ef4','','2026-07-24 19:47:46','2026-08-23 19:47:46','2026-07-24 19:47:47'),(54,'director',1,'d66be5fd028fce95051bf0b7a12675a03e158a16b6384e0b9d0d2e31ed4d77f3','','2026-07-24 20:40:12','2026-08-23 20:40:12','2026-07-24 20:40:12'),(55,'director',1,'1cf55157ac412921e4bf713284ae5a167254e5bd0a51c9c5727eda8c3a2d2a92','','2026-07-24 21:00:47','2026-08-23 21:00:47','2026-07-24 21:01:03'),(56,'profesor',1,'2b2dcbf16574deb71c653561b20f72bc85a95114ea8b8582c3589300b18790bc','','2026-07-24 21:01:20','2026-08-23 21:01:20','2026-07-24 21:01:21'),(57,'director',1,'6d3d20ef2b6b6f94366bb0e327d15831b5fc04ff681a42f7e448144d7c1e083d','','2026-07-24 21:18:18','2026-08-23 21:18:18','2026-07-24 21:18:18');
/*!40000 ALTER TABLE `api_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asistencias`
--

DROP TABLE IF EXISTS `asistencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asistencias` (
  `idAsistencia` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int NOT NULL,
  `idModulo` int NOT NULL,
  `idProfesor` int NOT NULL,
  `rolRegistrador` enum('profesor','secretaria','director') DEFAULT 'profesor',
  `idRegistrador` int DEFAULT NULL,
  `fecha` date NOT NULL,
  `estado` enum('presente','ausente','retraso','justificado') NOT NULL DEFAULT 'presente',
  `observacion` varchar(255) DEFAULT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `hora` time DEFAULT NULL,
  PRIMARY KEY (`idAsistencia`),
  UNIQUE KEY `idx_asistencia_unica` (`idEstudiante`,`idModulo`,`fecha`),
  KEY `idModulo` (`idModulo`),
  KEY `idProfesor` (`idProfesor`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencias`
--

LOCK TABLES `asistencias` WRITE;
/*!40000 ALTER TABLE `asistencias` DISABLE KEYS */;
INSERT INTO `asistencias` VALUES (1,1,3,1,'profesor',NULL,'2026-07-26','presente','Llegó a la hora correcta','2026-07-27 15:36:17',NULL),(2,2,3,1,'profesor',NULL,'2026-07-26','retraso','Llegó 15 minutos tarde por tráfico','2026-07-27 15:36:17',NULL),(3,3,1,2,'profesor',NULL,'2026-07-26','presente',NULL,'2026-07-27 15:36:17',NULL),(4,4,1,2,'profesor',NULL,'2026-07-26','ausente','No comunicó la falta','2026-07-27 15:36:17',NULL),(5,1,4,1,'profesor',NULL,'2026-07-27','presente',NULL,'2026-07-27 15:36:17',NULL),(6,2,4,1,'profesor',NULL,'2026-07-27','justificado','Tiene justificante de cita médica','2026-07-27 15:36:17',NULL),(7,1,1,1,'profesor',NULL,'2026-07-25','ausente','Sin justificación','2026-07-28 13:24:46',NULL),(8,1,2,2,'profesor',NULL,'2026-07-22','presente',NULL,'2026-07-28 13:24:46',NULL),(9,2,1,1,'profesor',NULL,'2026-07-24','justificado','Cita médica','2026-07-28 13:24:46',NULL),(10,2,2,2,'profesor',NULL,'2026-07-20','retraso','Autobús con retraso','2026-07-28 13:24:46',NULL),(11,3,4,1,'profesor',NULL,'2026-07-23','presente',NULL,'2026-07-28 13:24:46',NULL),(12,3,2,2,'profesor',NULL,'2026-07-26','ausente',NULL,'2026-07-28 13:24:46',NULL),(13,4,2,2,'profesor',NULL,'2026-07-25','retraso','Tráfico','2026-07-28 13:24:46',NULL),(14,4,3,1,'profesor',NULL,'2026-07-22','justificado','Justificante médico','2026-07-28 13:24:46',NULL),(15,5,7,3,'profesor',NULL,'2026-07-27','ausente','Sin aviso','2026-07-28 13:24:46',NULL),(16,5,9,4,'profesor',NULL,'2026-07-24','presente',NULL,'2026-07-28 13:24:46',NULL),(17,5,10,2,'profesor',NULL,'2026-07-21','justificado','Cita médica','2026-07-28 13:24:46',NULL),(18,6,7,3,'profesor',NULL,'2026-07-26','presente',NULL,'2026-07-28 13:24:46',NULL),(19,6,9,4,'profesor',NULL,'2026-07-23','retraso','Llegó tarde','2026-07-28 13:24:46',NULL),(20,6,10,2,'profesor',NULL,'2026-07-19','ausente',NULL,'2026-07-28 13:24:46',NULL);
/*!40000 ALTER TABLE `asistencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_types`
--

DROP TABLE IF EXISTS `assessment_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assessment_types` (
  `idTipo` int NOT NULL AUTO_INCREMENT,
  `idConfig` int NOT NULL,
  `nombre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notaMaxima` decimal(4,2) NOT NULL DEFAULT '10.00',
  `peso` decimal(6,2) NOT NULL DEFAULT '1.00',
  `aprobadoMinimo` decimal(4,2) DEFAULT NULL,
  `obligatorio` tinyint(1) NOT NULL DEFAULT '0',
  `recuperable` tinyint(1) NOT NULL DEFAULT '1',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `editableProfesor` tinyint(1) NOT NULL DEFAULT '1',
  `editableDirector` tinyint(1) NOT NULL DEFAULT '1',
  `incluirEnMedia` tinyint(1) NOT NULL DEFAULT '1',
  `origen` enum('examen','reto','ra_ce','fct','tfg','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'otro',
  `orden` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`idTipo`),
  KEY `idx_tipo_config` (`idConfig`),
  CONSTRAINT `assessment_types_ibfk_1` FOREIGN KEY (`idConfig`) REFERENCES `academic_config` (`idConfig`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_types`
--

LOCK TABLES `assessment_types` WRITE;
/*!40000 ALTER TABLE `assessment_types` DISABLE KEYS */;
INSERT INTO `assessment_types` VALUES (1,1,'Examen',10.00,3.00,NULL,1,1,1,1,1,1,'examen',1),(2,1,'Reto',10.00,1.00,NULL,0,1,1,1,1,1,'reto',2);
/*!40000 ALTER TABLE `assessment_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_almacenamiento_ciclo`
--

DROP TABLE IF EXISTS `aula_almacenamiento_ciclo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_almacenamiento_ciclo` (
  `idCiclo` int NOT NULL,
  `limiteBytes` bigint NOT NULL DEFAULT '5368709120',
  PRIMARY KEY (`idCiclo`),
  CONSTRAINT `fk_aulaalm_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_almacenamiento_ciclo`
--

LOCK TABLES `aula_almacenamiento_ciclo` WRITE;
/*!40000 ALTER TABLE `aula_almacenamiento_ciclo` DISABLE KEYS */;
/*!40000 ALTER TABLE `aula_almacenamiento_ciclo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_analytics`
--

DROP TABLE IF EXISTS `aula_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_analytics` (
  `idAnalytics` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `tipoUsuario` enum('estudiante','profesor') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `accion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idModulo` int DEFAULT NULL,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `userAgent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `metadatos` json DEFAULT NULL,
  `fechaCreacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAnalytics`),
  KEY `idx_analytics_usr` (`idUsuario`),
  KEY `idx_analytics_mod` (`idModulo`),
  KEY `idx_analytics_fecha` (`fechaCreacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_analytics`
--

LOCK TABLES `aula_analytics` WRITE;
/*!40000 ALTER TABLE `aula_analytics` DISABLE KEYS */;
/*!40000 ALTER TABLE `aula_analytics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_archivo_accesos`
--

DROP TABLE IF EXISTS `aula_archivo_accesos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_archivo_accesos` (
  `idAcceso` int NOT NULL AUTO_INCREMENT,
  `idArchivo` int NOT NULL,
  `idEstudiante` int NOT NULL,
  `tipo` enum('vista','descarga') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vista',
  `fechaAcceso` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAcceso`),
  KEY `fk_aulaacc_arch` (`idArchivo`),
  KEY `fk_aulaacc_est` (`idEstudiante`),
  CONSTRAINT `fk_aulaacc_arch` FOREIGN KEY (`idArchivo`) REFERENCES `aula_archivos` (`idArchivo`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulaacc_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_archivo_accesos`
--

LOCK TABLES `aula_archivo_accesos` WRITE;
/*!40000 ALTER TABLE `aula_archivo_accesos` DISABLE KEYS */;
INSERT INTO `aula_archivo_accesos` VALUES (1,1,1,'descarga','2026-07-23 18:35:13'),(2,1,1,'descarga','2026-07-23 18:56:08'),(3,1,1,'descarga','2026-07-23 19:31:27');
/*!40000 ALTER TABLE `aula_archivo_accesos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_archivo_versiones`
--

DROP TABLE IF EXISTS `aula_archivo_versiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_archivo_versiones` (
  `idVersion` int NOT NULL AUTO_INCREMENT,
  `idArchivo` int NOT NULL,
  `nombreArchivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombreOriginal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tamanio` int DEFAULT '0',
  `version` int NOT NULL,
  `idProfesor` int NOT NULL,
  `fechaVersion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idVersion`),
  KEY `fk_aulaver_arch` (`idArchivo`),
  KEY `fk_aulaver_prof` (`idProfesor`),
  CONSTRAINT `fk_aulaver_arch` FOREIGN KEY (`idArchivo`) REFERENCES `aula_archivos` (`idArchivo`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulaver_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_archivo_versiones`
--

LOCK TABLES `aula_archivo_versiones` WRITE;
/*!40000 ALTER TABLE `aula_archivo_versiones` DISABLE KEYS */;
/*!40000 ALTER TABLE `aula_archivo_versiones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_archivos`
--

DROP TABLE IF EXISTS `aula_archivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_archivos` (
  `idArchivo` int NOT NULL AUTO_INCREMENT,
  `nombreArchivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombreOriginal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tamanio` int DEFAULT '0',
  `descripcion` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idCarpeta` int DEFAULT NULL,
  `idModulo` int NOT NULL,
  `idProfesor` int NOT NULL,
  `version` int NOT NULL DEFAULT '1',
  `fijado` tinyint(1) NOT NULL DEFAULT '0',
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `fechaEliminacion` datetime DEFAULT NULL,
  `fechaSubida` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idArchivo`),
  KEY `idx_aula_arch_mod` (`idModulo`),
  KEY `idx_aula_arch_carp` (`idCarpeta`),
  KEY `idx_aula_arch_elim` (`eliminado`),
  KEY `fk_aulaarch_prof` (`idProfesor`),
  CONSTRAINT `fk_aulaarch_carp` FOREIGN KEY (`idCarpeta`) REFERENCES `aula_carpetas` (`idCarpeta`) ON DELETE SET NULL,
  CONSTRAINT `fk_aulaarch_mod` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulaarch_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_archivos`
--

LOCK TABLES `aula_archivos` WRITE;
/*!40000 ALTER TABLE `aula_archivos` DISABLE KEYS */;
INSERT INTO `aula_archivos` VALUES (1,'demo_apuntes_prog.txt','Apuntes - Tema 1 Introduccion.txt','txt',282,'Apuntes de la primera unidad',1,1,1,1,0,0,NULL,'2026-07-23 18:35:03'),(2,'demo_guia_ejercicios.txt','Guia de Ejercicios - Bloque 1.txt','txt',299,'Ejercicios practicos para entregar',1,1,1,1,0,0,NULL,'2026-07-23 18:35:03');
/*!40000 ALTER TABLE `aula_archivos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_asistencia_sesion`
--

DROP TABLE IF EXISTS `aula_asistencia_sesion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_asistencia_sesion` (
  `idAsistencia` int NOT NULL AUTO_INCREMENT,
  `idSesion` int NOT NULL,
  `idEstudiante` int NOT NULL,
  `horaUnion` time DEFAULT NULL,
  `horaSalida` time DEFAULT NULL,
  `duracion` int DEFAULT NULL,
  `presente` tinyint(1) NOT NULL DEFAULT '1',
  `fechaRegistro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAsistencia`),
  UNIQUE KEY `uk_sesion_estudiante` (`idSesion`,`idEstudiante`),
  KEY `fk_aulasis_est` (`idEstudiante`),
  CONSTRAINT `fk_aulasis_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulasis_sesion` FOREIGN KEY (`idSesion`) REFERENCES `aula_sesiones_vivas` (`idSesion`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_asistencia_sesion`
--

LOCK TABLES `aula_asistencia_sesion` WRITE;
/*!40000 ALTER TABLE `aula_asistencia_sesion` DISABLE KEYS */;
/*!40000 ALTER TABLE `aula_asistencia_sesion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_carpetas`
--

DROP TABLE IF EXISTS `aula_carpetas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_carpetas` (
  `idCarpeta` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idModulo` int NOT NULL,
  `idProfesor` int NOT NULL,
  `idPadre` int DEFAULT NULL,
  `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#0ea5e9',
  `icono` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fa-folder',
  `fijado` tinyint(1) NOT NULL DEFAULT '0',
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `fechaEliminacion` datetime DEFAULT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idCarpeta`),
  KEY `idx_aula_carp_mod` (`idModulo`),
  KEY `idx_aula_carp_padre` (`idPadre`),
  KEY `fk_aulacarp_prof` (`idProfesor`),
  CONSTRAINT `fk_aulacarp_mod` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulacarp_padre` FOREIGN KEY (`idPadre`) REFERENCES `aula_carpetas` (`idCarpeta`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulacarp_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_carpetas`
--

LOCK TABLES `aula_carpetas` WRITE;
/*!40000 ALTER TABLE `aula_carpetas` DISABLE KEYS */;
INSERT INTO `aula_carpetas` VALUES (1,'Material del curso',1,1,NULL,'#4F46E5','folder',0,0,NULL,'2026-07-23 18:35:03');
/*!40000 ALTER TABLE `aula_carpetas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_comentarios`
--

DROP TABLE IF EXISTS `aula_comentarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_comentarios` (
  `idComentario` int NOT NULL AUTO_INCREMENT,
  `idEntrega` int NOT NULL,
  `idUsuario` int NOT NULL,
  `tipoUsuario` enum('profesor','estudiante') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `archivoCorreccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaComentario` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idComentario`),
  KEY `fk_aulacomen_entr` (`idEntrega`),
  CONSTRAINT `fk_aulacomen_entr` FOREIGN KEY (`idEntrega`) REFERENCES `aula_entregas` (`idEntrega`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_comentarios`
--

LOCK TABLES `aula_comentarios` WRITE;
/*!40000 ALTER TABLE `aula_comentarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `aula_comentarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_entregas`
--

DROP TABLE IF EXISTS `aula_entregas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_entregas` (
  `idEntrega` int NOT NULL AUTO_INCREMENT,
  `idTarea` int NOT NULL,
  `idEstudiante` int NOT NULL,
  `archivoEntrega` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `respuesta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `version` int NOT NULL DEFAULT '1',
  `fechaEntrega` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nota` decimal(4,2) DEFAULT NULL,
  `estado` enum('enviada','corregida') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enviada',
  `comentarioCalificacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `archivoCorreccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idEntrega`),
  UNIQUE KEY `uk_aula_entrega` (`idTarea`,`idEstudiante`),
  KEY `idx_aula_entr_est` (`idEstudiante`),
  CONSTRAINT `fk_aulaentr_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulaentr_tar` FOREIGN KEY (`idTarea`) REFERENCES `aula_tareas` (`idTarea`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_entregas`
--

LOCK TABLES `aula_entregas` WRITE;
/*!40000 ALTER TABLE `aula_entregas` DISABLE KEYS */;
INSERT INTO `aula_entregas` VALUES (1,1,1,'practica_1_ana_silva.zip','Profesor, adjunto la práctica resuelta. He añadido como extra una vista HTML básica para probar las validaciones.',1,'2026-07-25 17:36:17',8.80,'corregida','Excelente código, muy limpio y estructurado. Los extras están muy bien implementados.',NULL),(2,1,2,'practica_1_david_ortiz.zip','Hola Juan, aquí tiene mi entrega de PHP. Un saludo.',1,'2026-07-25 17:36:17',7.20,'corregida','Buen trabajo en general. Ten cuidado con los nombres de variables y la indentación.',NULL),(3,2,1,'api_rest_ana_silva.zip','He diseñado los endpoints según los estándares REST. Se incluye archivo OpenAPI (Swagger) de documentación.',1,'2026-07-26 17:36:17',NULL,'enviada',NULL,NULL),(4,3,1,'dom_ana_silva.zip','Adjunto código JS listo. He implementado delegación de eventos en la tabla para optimizar rendimiento.',1,'2026-07-27 05:36:17',9.60,'corregida','Fantástico uso de delegación de eventos y modularización del script JS. ¡Enhorabuena!',NULL);
/*!40000 ALTER TABLE `aula_entregas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_favoritos`
--

DROP TABLE IF EXISTS `aula_favoritos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_favoritos` (
  `idFavorito` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int NOT NULL,
  `idArchivo` int NOT NULL,
  `fechaMarcado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idFavorito`),
  UNIQUE KEY `uk_aulafav` (`idEstudiante`,`idArchivo`),
  KEY `fk_aulafav_arch` (`idArchivo`),
  CONSTRAINT `fk_aulafav_arch` FOREIGN KEY (`idArchivo`) REFERENCES `aula_archivos` (`idArchivo`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulafav_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_favoritos`
--

LOCK TABLES `aula_favoritos` WRITE;
/*!40000 ALTER TABLE `aula_favoritos` DISABLE KEYS */;
/*!40000 ALTER TABLE `aula_favoritos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_kanban_estado`
--

DROP TABLE IF EXISTS `aula_kanban_estado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_kanban_estado` (
  `idEstado` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int NOT NULL,
  `idTarea` int NOT NULL,
  `estado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'todo',
  PRIMARY KEY (`idEstado`),
  UNIQUE KEY `uk_est_tarea` (`idEstudiante`,`idTarea`),
  KEY `fk_kanban_tarea` (`idTarea`),
  CONSTRAINT `fk_kanban_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_kanban_tarea` FOREIGN KEY (`idTarea`) REFERENCES `aula_tareas` (`idTarea`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_kanban_estado`
--

LOCK TABLES `aula_kanban_estado` WRITE;
/*!40000 ALTER TABLE `aula_kanban_estado` DISABLE KEYS */;
/*!40000 ALTER TABLE `aula_kanban_estado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_notificaciones`
--

DROP TABLE IF EXISTS `aula_notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_notificaciones` (
  `idNotificacion` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `tipoUsuario` enum('profesor','estudiante','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('archivo_subido','entrega_enviada','correccion','comentario') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `leida` tinyint(1) NOT NULL DEFAULT '0',
  `idReferencia` int DEFAULT NULL,
  `tipoReferencia` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idNotificacion`),
  KEY `idx_aula_notif_usr` (`idUsuario`,`tipoUsuario`,`leida`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_notificaciones`
--

LOCK TABLES `aula_notificaciones` WRITE;
/*!40000 ALTER TABLE `aula_notificaciones` DISABLE KEYS */;
INSERT INTO `aula_notificaciones` VALUES (1,1,'estudiante','archivo_subido','Nuevo archivo en Programación','1 ha subido: Prueba de subida.txt',0,3,'archivo','2026-07-24 02:37:13'),(2,2,'estudiante','archivo_subido','Nuevo archivo en Programación','1 ha subido: Prueba de subida.txt',0,3,'archivo','2026-07-24 02:37:13'),(3,4,'estudiante','archivo_subido','Nuevo archivo en Programación','1 ha subido: Prueba de subida.txt',0,3,'archivo','2026-07-24 02:37:13'),(4,5,'estudiante','archivo_subido','Nuevo archivo en Programación','1 ha subido: Prueba de subida.txt',0,3,'archivo','2026-07-24 02:37:13'),(5,6,'estudiante','archivo_subido','Nuevo archivo en Programación','1 ha subido: Prueba de subida.txt',0,3,'archivo','2026-07-24 02:37:13'),(6,7,'estudiante','archivo_subido','Nuevo archivo en Programación','1 ha subido: Prueba de subida.txt',0,3,'archivo','2026-07-24 02:37:13');
/*!40000 ALTER TABLE `aula_notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_retos`
--

DROP TABLE IF EXISTS `aula_retos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_retos` (
  `idReto` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `idModulo` int NOT NULL,
  `idProfesor` int NOT NULL,
  `archivoAdjunto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publicado` tinyint(1) NOT NULL DEFAULT '1',
  `fechaCreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idReto`),
  KEY `idModulo` (`idModulo`),
  KEY `idProfesor` (`idProfesor`),
  CONSTRAINT `aula_retos_ibfk_1` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `aula_retos_ibfk_2` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_retos`
--

LOCK TABLES `aula_retos` WRITE;
/*!40000 ALTER TABLE `aula_retos` DISABLE KEYS */;
/*!40000 ALTER TABLE `aula_retos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_retos_entregas`
--

DROP TABLE IF EXISTS `aula_retos_entregas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_retos_entregas` (
  `idEntrega` int NOT NULL AUTO_INCREMENT,
  `idReto` int NOT NULL,
  `idEstudiante` int NOT NULL,
  `respuesta` text COLLATE utf8mb4_unicode_ci,
  `archivoEntrega` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nota` decimal(5,2) DEFAULT NULL,
  `comentario` text COLLATE utf8mb4_unicode_ci,
  `fechaEntrega` datetime DEFAULT CURRENT_TIMESTAMP,
  `fechaCorreccion` datetime DEFAULT NULL,
  PRIMARY KEY (`idEntrega`),
  KEY `idReto` (`idReto`),
  KEY `idEstudiante` (`idEstudiante`),
  CONSTRAINT `aula_retos_entregas_ibfk_1` FOREIGN KEY (`idReto`) REFERENCES `aula_retos` (`idReto`) ON DELETE CASCADE,
  CONSTRAINT `aula_retos_entregas_ibfk_2` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_retos_entregas`
--

LOCK TABLES `aula_retos_entregas` WRITE;
/*!40000 ALTER TABLE `aula_retos_entregas` DISABLE KEYS */;
/*!40000 ALTER TABLE `aula_retos_entregas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_sesiones_vivas`
--

DROP TABLE IF EXISTS `aula_sesiones_vivas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_sesiones_vivas` (
  `idSesion` int NOT NULL AUTO_INCREMENT,
  `idModulo` int NOT NULL,
  `idProfesor` int NOT NULL,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fechaSesion` date NOT NULL,
  `horaSesion` time NOT NULL,
  `enlaceReunion` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plataforma` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('programada','en_vivo','finalizada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'programada',
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idSesion`),
  KEY `idx_sesion_mod` (`idModulo`),
  KEY `idx_sesion_prof` (`idProfesor`),
  CONSTRAINT `fk_aulasesion_mod` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulasesion_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_sesiones_vivas`
--

LOCK TABLES `aula_sesiones_vivas` WRITE;
/*!40000 ALTER TABLE `aula_sesiones_vivas` DISABLE KEYS */;
INSERT INTO `aula_sesiones_vivas` VALUES (1,3,1,'Resolución de Dudas: API REST','Revisión grupal y solución de dudas sobre cómo diseñar e integrar los verbos y códigos de respuesta en endpoints.','2026-07-29','11:00:00','https://meet.google.com/xyz-pdq-abc','Google Meet','programada','2026-07-27 17:36:17'),(2,4,1,'Taller JavaScript: Programación Asíncrona','Explicación detallada y práctica sobre el flujo con Event Loop, Promises, Fetch API y Async/Await.','2026-07-30','10:00:00','https://meet.google.com/uvw-xyz-rst','Google Meet','programada','2026-07-27 17:36:17');
/*!40000 ALTER TABLE `aula_sesiones_vivas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_tareas`
--

DROP TABLE IF EXISTS `aula_tareas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_tareas` (
  `idTarea` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `idModulo` int NOT NULL,
  `idProfesor` int NOT NULL,
  `archivoAdjunto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publicado` tinyint(1) NOT NULL DEFAULT '1',
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idTarea`),
  KEY `idx_aula_tarea_mod` (`idModulo`),
  KEY `fk_aulatar_prof` (`idProfesor`),
  CONSTRAINT `fk_aulatar_mod` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulatar_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_tareas`
--

LOCK TABLES `aula_tareas` WRITE;
/*!40000 ALTER TABLE `aula_tareas` DISABLE KEYS */;
INSERT INTO `aula_tareas` VALUES (1,'Estructuras de Control en PHP','Desarrollar una biblioteca básica de validación de datos utilizando sentencias condicionales, bucles anidados y arrays asociativos.',3,1,NULL,1,'2026-07-27 17:36:17'),(2,'Diseño e Implementación de API REST','Crear una API RESTful para la gestión de productos con soporte para operaciones CRUD y respuestas estructuradas en formato JSON.',3,1,NULL,1,'2026-07-27 17:36:17'),(3,'Manipulación Dinámica del DOM','Desarrollar una aplicación interactiva simple en JavaScript que agregue, elimine y filtre elementos de una tabla usando eventos y selectores nativos.',4,1,NULL,1,'2026-07-27 17:36:17'),(4,'Maquetación Avanzada con CSS Grid','Crear un dashboard de administración responsive utilizando exclusivamente CSS Grid y Flexbox para organizar la cuadrícula.',5,3,NULL,1,'2026-07-27 17:36:17');
/*!40000 ALTER TABLE `aula_tareas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aula_versiones_entrega`
--

DROP TABLE IF EXISTS `aula_versiones_entrega`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aula_versiones_entrega` (
  `idVersion` int NOT NULL AUTO_INCREMENT,
  `idTarea` int NOT NULL,
  `idEstudiante` int NOT NULL,
  `archivoEntrega` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `respuesta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `version` int NOT NULL,
  `fechaVersion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idVersion`),
  KEY `fk_aulavers_tar` (`idTarea`),
  KEY `fk_aulavers_est` (`idEstudiante`),
  CONSTRAINT `fk_aulavers_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulavers_tar` FOREIGN KEY (`idTarea`) REFERENCES `aula_tareas` (`idTarea`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_versiones_entrega`
--

LOCK TABLES `aula_versiones_entrega` WRITE;
/*!40000 ALTER TABLE `aula_versiones_entrega` DISABLE KEYS */;
/*!40000 ALTER TABLE `aula_versiones_entrega` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aulas`
--

DROP TABLE IF EXISTS `aulas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aulas` (
  `idAula` int NOT NULL AUTO_INCREMENT,
  `planta` tinyint NOT NULL,
  `numero` int NOT NULL,
  `codigoAula` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (concat(`planta`,lpad(`numero`,2,_utf8mb4'0'))) STORED,
  `nombreAula` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipoAula` enum('teoria','laboratorio','taller','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'teoria',
  `capacidad` int DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idAula`),
  UNIQUE KEY `uk_aula_planta_numero` (`planta`,`numero`),
  UNIQUE KEY `uk_aula_codigo` (`codigoAula`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aulas`
--

LOCK TABLES `aulas` WRITE;
/*!40000 ALTER TABLE `aulas` DISABLE KEYS */;
INSERT INTO `aulas` (`idAula`, `planta`, `numero`, `nombreAula`, `tipoAula`, `capacidad`, `activa`) VALUES (1,1,1,'Laboratorio Informática I','laboratorio',25,1),(2,1,2,'Laboratorio Informática II','laboratorio',25,1),(3,2,1,'Aula de Teoría 201','teoria',30,1),(4,2,2,'Taller de Hardware','taller',20,1);
/*!40000 ALTER TABLE `aulas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_posts` (
  `idPost` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `resumen` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `contenido` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `categoria` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `autor` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `publicado` tinyint(1) NOT NULL DEFAULT '0',
  `destacado` tinyint(1) NOT NULL DEFAULT '0',
  `fechaPublicacion` datetime DEFAULT NULL,
  `creadoEn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizadoEn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idPost`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_publicado` (`publicado`,`fechaPublicacion`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
INSERT INTO `blog_posts` VALUES (1,'Apertura del Centro Formativo','apertura','Comienza un nuevo año con gran ilusión.','El día 15 damos el pistoletazo de salida...','','','',1,1,'2026-09-01 00:00:00','2026-07-23 16:05:10','2026-07-23 16:05:10');
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boletines_log`
--

DROP TABLE IF EXISTS `boletines_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boletines_log` (
  `serial` varchar(40) NOT NULL,
  `idEstudiante` int NOT NULL,
  `idCiclo` int NOT NULL,
  `nombreEstudiante` varchar(255) NOT NULL,
  `nombreCiclo` varchar(255) NOT NULL,
  `cursoEscolar` varchar(20) NOT NULL,
  `fechaGeneracion` datetime DEFAULT CURRENT_TIMESTAMP,
  `scan_count` int unsigned NOT NULL DEFAULT '0',
  `last_scan_at` datetime DEFAULT NULL,
  `last_scan_ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`serial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boletines_log`
--

LOCK TABLES `boletines_log` WRITE;
/*!40000 ALTER TABLE `boletines_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `boletines_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calificaciones_ce`
--

DROP TABLE IF EXISTS `calificaciones_ce`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calificaciones_ce` (
  `idCalificacionCE` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int NOT NULL,
  `idCE` int NOT NULL,
  `nota` decimal(4,2) DEFAULT NULL,
  PRIMARY KEY (`idCalificacionCE`),
  UNIQUE KEY `idx_estudiante_ce` (`idEstudiante`,`idCE`),
  KEY `idCE` (`idCE`),
  CONSTRAINT `calificaciones_ce_ibfk_1` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `calificaciones_ce_ibfk_2` FOREIGN KEY (`idCE`) REFERENCES `criterios_evaluacion` (`idCE`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calificaciones_ce`
--

LOCK TABLES `calificaciones_ce` WRITE;
/*!40000 ALTER TABLE `calificaciones_ce` DISABLE KEYS */;
INSERT INTO `calificaciones_ce` VALUES (1,1,1,9.50);
/*!40000 ALTER TABLE `calificaciones_ce` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calificaciones_modulos`
--

DROP TABLE IF EXISTS `calificaciones_modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calificaciones_modulos` (
  `idCalificacion` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int NOT NULL,
  `idModulo` int NOT NULL,
  `nota_1ev` decimal(4,2) DEFAULT NULL,
  `nota_1final` decimal(4,2) DEFAULT NULL,
  `nota_2ev` decimal(4,2) DEFAULT NULL,
  `nota_2final` decimal(4,2) DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estado_1ev` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_1final` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_2ev` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_2final` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idCalificacion`),
  UNIQUE KEY `uk_est_mod` (`idEstudiante`,`idModulo`),
  KEY `idx_cm_mod` (`idModulo`),
  CONSTRAINT `fk_cm_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_cm_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calificaciones_modulos`
--

LOCK TABLES `calificaciones_modulos` WRITE;
/*!40000 ALTER TABLE `calificaciones_modulos` DISABLE KEYS */;
INSERT INTO `calificaciones_modulos` VALUES (1,1,1,8.50,NULL,NULL,NULL,NULL,'CO',NULL,NULL,NULL);
/*!40000 ALTER TABLE `calificaciones_modulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calificaciones_periodo`
--

DROP TABLE IF EXISTS `calificaciones_periodo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calificaciones_periodo` (
  `idCalificacion` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int NOT NULL,
  `idModulo` int NOT NULL,
  `idPeriodo` int NOT NULL,
  `idTipo` int NOT NULL,
  `nota` decimal(4,2) DEFAULT NULL,
  `estado` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `actualizadoEn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idCalificacion`),
  UNIQUE KEY `uk_cp_est_mod_periodo_tipo` (`idEstudiante`,`idModulo`,`idPeriodo`,`idTipo`),
  KEY `idModulo` (`idModulo`),
  KEY `idTipo` (`idTipo`),
  KEY `idPeriodo` (`idPeriodo`),
  CONSTRAINT `calificaciones_periodo_ibfk_1` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `calificaciones_periodo_ibfk_2` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `calificaciones_periodo_ibfk_4` FOREIGN KEY (`idTipo`) REFERENCES `assessment_types` (`idTipo`) ON DELETE CASCADE,
  CONSTRAINT `calificaciones_periodo_ibfk_5` FOREIGN KEY (`idPeriodo`) REFERENCES `academic_periods` (`idPeriodo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calificaciones_periodo`
--

LOCK TABLES `calificaciones_periodo` WRITE;
/*!40000 ALTER TABLE `calificaciones_periodo` DISABLE KEYS */;
/*!40000 ALTER TABLE `calificaciones_periodo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calificaciones_retos`
--

DROP TABLE IF EXISTS `calificaciones_retos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calificaciones_retos` (
  `idCalificacion` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int NOT NULL,
  `idReto` int NOT NULL,
  `nota` decimal(4,2) NOT NULL,
  PRIMARY KEY (`idCalificacion`),
  KEY `idx_cal_reto_est` (`idEstudiante`),
  KEY `idx_cal_reto_reto` (`idReto`),
  CONSTRAINT `fk_cr_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_cr_reto` FOREIGN KEY (`idReto`) REFERENCES `retos` (`idReto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calificaciones_retos`
--

LOCK TABLES `calificaciones_retos` WRITE;
/*!40000 ALTER TABLE `calificaciones_retos` DISABLE KEYS */;
/*!40000 ALTER TABLE `calificaciones_retos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calificaciones_tfg`
--

DROP TABLE IF EXISTS `calificaciones_tfg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calificaciones_tfg` (
  `idCalificacion` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int NOT NULL,
  `convocatoria` enum('ordinaria','extraordinaria') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ordinaria',
  `nota` decimal(4,2) NOT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idCalificacion`),
  UNIQUE KEY `uk_est_tfg` (`idEstudiante`,`convocatoria`),
  CONSTRAINT `fk_ctfg_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calificaciones_tfg`
--

LOCK TABLES `calificaciones_tfg` WRITE;
/*!40000 ALTER TABLE `calificaciones_tfg` DISABLE KEYS */;
/*!40000 ALTER TABLE `calificaciones_tfg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias_gasto`
--

DROP TABLE IF EXISTS `categorias_gasto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias_gasto` (
  `idCategoria` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `presupuestoAnual` decimal(10,2) DEFAULT '0.00',
  `color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#6c757d',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idCategoria`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_gasto`
--

LOCK TABLES `categorias_gasto` WRITE;
/*!40000 ALTER TABLE `categorias_gasto` DISABLE KEYS */;
INSERT INTO `categorias_gasto` VALUES (1,'Licencias de Software',5000.00,'#0ea5e9',1),(2,'Material e Instrumentos de Laboratorio',10000.00,'#10b981',1),(3,'Material de Oficina e Imprenta',2000.00,'#f59e0b',1),(4,'Infraestructura, Servidores y Cableado',8000.00,'#ef4444',1);
/*!40000 ALTER TABLE `categorias_gasto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `challenge_config`
--

DROP TABLE IF EXISTS `challenge_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `challenge_config` (
  `idConfigReto` int NOT NULL AUTO_INCREMENT,
  `idConfig` int NOT NULL,
  `pesoDefecto` decimal(6,2) NOT NULL DEFAULT '1.00',
  `permiteGrupal` tinyint(1) NOT NULL DEFAULT '0',
  `permiteFases` tinyint(1) NOT NULL DEFAULT '0',
  `requiereRubrica` tinyint(1) NOT NULL DEFAULT '0',
  `evaluacionPares` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idConfigReto`),
  UNIQUE KEY `uk_cc_config` (`idConfig`),
  CONSTRAINT `challenge_config_ibfk_1` FOREIGN KEY (`idConfig`) REFERENCES `academic_config` (`idConfig`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `challenge_config`
--

LOCK TABLES `challenge_config` WRITE;
/*!40000 ALTER TABLE `challenge_config` DISABLE KEYS */;
INSERT INTO `challenge_config` VALUES (1,1,1.00,0,0,0,0);
/*!40000 ALTER TABLE `challenge_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_conversaciones`
--

DROP TABLE IF EXISTS `chat_conversaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_conversaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_a_rol` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_a_id` int NOT NULL,
  `user_b_rol` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_b_id` int NOT NULL,
  `last_message_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_conv_pair` (`user_a_rol`,`user_a_id`,`user_b_rol`,`user_b_id`),
  KEY `idx_conv_b` (`user_b_rol`,`user_b_id`),
  KEY `idx_conv_last` (`last_message_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_conversaciones`
--

LOCK TABLES `chat_conversaciones` WRITE;
/*!40000 ALTER TABLE `chat_conversaciones` DISABLE KEYS */;
INSERT INTO `chat_conversaciones` VALUES (1,'profesor',1,'estudiante',1,'2026-07-27 17:26:17'),(2,'profesor',1,'estudiante',2,'2026-07-27 16:36:17');
/*!40000 ALTER TABLE `chat_conversaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_mensajes`
--

DROP TABLE IF EXISTS `chat_mensajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_mensajes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `conversacion_id` int NOT NULL,
  `emisor_rol` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `emisor_id` int NOT NULL,
  `contenido` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_msg_conv` (`conversacion_id`),
  KEY `idx_msg_fecha` (`fecha`),
  KEY `idx_msg_leido` (`leido`),
  KEY `idx_msg_conv_leido` (`conversacion_id`,`leido`),
  CONSTRAINT `fk_msg_conv` FOREIGN KEY (`conversacion_id`) REFERENCES `chat_conversaciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_mensajes`
--

LOCK TABLES `chat_mensajes` WRITE;
/*!40000 ALTER TABLE `chat_mensajes` DISABLE KEYS */;
INSERT INTO `chat_mensajes` VALUES (1,1,'estudiante',1,'Hola profesor, ¿el lunes es festivo o hay entrega normal de la práctica 2?',1,'2026-07-27 17:06:17'),(2,1,'profesor',1,'Hola Ana. Es día lectivo normal, por lo tanto la entrega se mantiene para las 23:59 de ese día.',1,'2026-07-27 17:11:17'),(3,1,'estudiante',1,'Perfecto, ya la tengo casi lista. Muchas gracias por la aclaración.',0,'2026-07-27 17:26:17'),(4,2,'estudiante',2,'Hola Juan, tengo un fallo al validar el token en la práctica de REST. ¿Me podría guiar un poco?',1,'2026-07-27 16:36:17'),(5,2,'profesor',1,'Hola David. Revisa la cabecera \"Authorization\" en tu middleware. Asegúrate de separar el prefijo \"Bearer \" del token propiamente dicho.',0,'2026-07-27 16:51:17');
/*!40000 ALTER TABLE `chat_mensajes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ciclo_profesor`
--

DROP TABLE IF EXISTS `ciclo_profesor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ciclo_profesor` (
  `idCiclo` int NOT NULL,
  `idProfesor` int NOT NULL,
  PRIMARY KEY (`idCiclo`,`idProfesor`),
  KEY `fk_rel_prof` (`idProfesor`),
  CONSTRAINT `fk_rel_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `fk_rel_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ciclo_profesor`
--

LOCK TABLES `ciclo_profesor` WRITE;
/*!40000 ALTER TABLE `ciclo_profesor` DISABLE KEYS */;
INSERT INTO `ciclo_profesor` VALUES (1,1),(1,2),(2,3),(3,3);
/*!40000 ALTER TABLE `ciclo_profesor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ciclos`
--

DROP TABLE IF EXISTS `ciclos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ciclos` (
  `idCiclo` int NOT NULL AUTO_INCREMENT,
  `nombreCiclo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abreviaturaCiclo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `precioCiclo` decimal(10,2) DEFAULT NULL,
  `idNivel` int DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fechaArchivado` datetime DEFAULT NULL,
  PRIMARY KEY (`idCiclo`),
  KEY `idx_ciclo_nivel` (`idNivel`),
  CONSTRAINT `fk_ciclos_niveles` FOREIGN KEY (`idNivel`) REFERENCES `niveles` (`idNivel`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ciclos`
--

LOCK TABLES `ciclos` WRITE;
/*!40000 ALTER TABLE `ciclos` DISABLE KEYS */;
INSERT INTO `ciclos` VALUES (1,'Desarrollo de Aplicaciones Web','DAW',1200.00,1,1,NULL),(2,'Desarrollo de Aplicaciones Multiplataforma','DAM',1200.00,1,1,NULL),(3,'Sistemas Microinformáticos y Redes','SMR',900.00,2,1,NULL);
/*!40000 ALTER TABLE `ciclos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cola_emails`
--

DROP TABLE IF EXISTS `cola_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cola_emails` (
  `id` int NOT NULL AUTO_INCREMENT,
  `destinatario_email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `destinatario_nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asunto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `html_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('pendiente','enviado','fallido') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `intentos` tinyint NOT NULL DEFAULT '0',
  `ultimo_error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `enviado_at` datetime DEFAULT NULL,
  `creado_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cola_estado` (`estado`,`intentos`),
  KEY `idx_cola_creado` (`creado_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cola_emails`
--

LOCK TABLES `cola_emails` WRITE;
/*!40000 ALTER TABLE `cola_emails` DISABLE KEYS */;
/*!40000 ALTER TABLE `cola_emails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracion_centro`
--

DROP TABLE IF EXISTS `configuracion_centro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracion_centro` (
  `idConfig` int NOT NULL DEFAULT '1',
  `nombreCentro` varchar(200) DEFAULT 'Centro de Formación Profesional',
  `codigoCentro` varchar(50) DEFAULT '',
  `nifCifCentro` varchar(20) NOT NULL DEFAULT '',
  `direccionCentro` varchar(200) DEFAULT '',
  `ciudadCentro` varchar(100) DEFAULT '',
  `cpCentro` varchar(10) DEFAULT '',
  `telefonoCentro` varchar(20) DEFAULT '',
  `emailCentro` varchar(100) DEFAULT '',
  `cursoEscolar` varchar(20) DEFAULT '2024-2025',
  `logoCentro` varchar(255) DEFAULT '',
  `logoGobierno1` varchar(255) DEFAULT '',
  `logoGobierno2` varchar(255) DEFAULT '',
  `colorAcento` varchar(7) DEFAULT NULL,
  `textoLegal` text,
  `nombreDirectorFirmante` varchar(150) DEFAULT '',
  `feature_prematricula` tinyint(1) NOT NULL DEFAULT '1',
  `feature_chat` tinyint(1) NOT NULL DEFAULT '1',
  `feature_inventario` tinyint(1) NOT NULL DEFAULT '1',
  `feature_subida_tfg` tinyint(1) NOT NULL DEFAULT '1',
  `instance_status` enum('active','suspended','pending') NOT NULL DEFAULT 'active',
  `suspension_message` text,
  `saas_lock_features` tinyint(1) NOT NULL DEFAULT '0',
  `saas_message` text,
  `saas_message_type` varchar(20) NOT NULL DEFAULT 'info',
  `saas_last_sync` datetime DEFAULT NULL,
  `license_token` text,
  `license_token_exp` datetime DEFAULT NULL,
  `feature_horario` tinyint(1) DEFAULT '1',
  `feature_anuncios` tinyint(1) DEFAULT '1',
  `feature_eventos` tinyint(1) DEFAULT '1',
  `feature_retos` tinyint(1) DEFAULT '1',
  `feature_mensajes` tinyint(1) DEFAULT '1',
  `feature_pagos` tinyint(1) DEFAULT '1',
  `feature_gastos` tinyint(1) DEFAULT '1',
  `feature_informes` tinyint(1) DEFAULT '1',
  `feature_geoblock_admin` tinyint(1) NOT NULL DEFAULT '1',
  `feature_ra_ce` tinyint(1) DEFAULT '0',
  `feature_fp_dual` tinyint(1) DEFAULT '0',
  `feature_landing` tinyint(1) NOT NULL DEFAULT '1',
  `prematricula_filtrar_niveles` tinyint(1) NOT NULL DEFAULT '0',
  `feature_academico_config` tinyint(1) NOT NULL DEFAULT '0',
  `feature_fct` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idConfig`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion_centro`
--

LOCK TABLES `configuracion_centro` WRITE;
/*!40000 ALTER TABLE `configuracion_centro` DISABLE KEYS */;
INSERT INTO `configuracion_centro` VALUES (1,'AulaPro Formación Profesional','CENTRO001','B12345678','Av. de la Innovación 42','Madrid','28042','912345678','info@aulapro.com','2026-2027','','','','Aviso legal: Este es un entorno de demostración de AulaPro.','Carlos Mendoza',1,1,1,1,'active',NULL,0,NULL,'info',NULL,NULL,NULL,1,1,1,1,1,1,1,1,1,1,1,1,0,0,1);
/*!40000 ALTER TABLE `configuracion_centro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consentimientos`
--

DROP TABLE IF EXISTS `consentimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consentimientos` (
  `idConsentimiento` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int NOT NULL,
  `tipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idConsentimiento`),
  KEY `idx_consentimiento_estudiante` (`idEstudiante`),
  CONSTRAINT `fk_consentimiento_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consentimientos`
--

LOCK TABLES `consentimientos` WRITE;
/*!40000 ALTER TABLE `consentimientos` DISABLE KEYS */;
/*!40000 ALTER TABLE `consentimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `criterios_evaluacion`
--

DROP TABLE IF EXISTS `criterios_evaluacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `criterios_evaluacion` (
  `idCE` int NOT NULL AUTO_INCREMENT,
  `idRA` int NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`idCE`),
  KEY `idRA` (`idRA`),
  CONSTRAINT `criterios_evaluacion_ibfk_1` FOREIGN KEY (`idRA`) REFERENCES `resultados_aprendizaje` (`idRA`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `criterios_evaluacion`
--

LOCK TABLES `criterios_evaluacion` WRITE;
/*!40000 ALTER TABLE `criterios_evaluacion` DISABLE KEYS */;
INSERT INTO `criterios_evaluacion` VALUES (1,1,'CE1.a','Declara variables y estructuras de control.'),(2,2,'CE2.a','Instancia clases y usa herencia.');
/*!40000 ALTER TABLE `criterios_evaluacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cursos_academicos`
--

DROP TABLE IF EXISTS `cursos_academicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cursos_academicos` (
  `idCurso` int NOT NULL AUTO_INCREMENT,
  `idCiclo` int NOT NULL,
  `nombre` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`idCurso`),
  UNIQUE KEY `uk_curso_ciclo_orden` (`idCiclo`,`orden`),
  CONSTRAINT `cursos_academicos_ibfk_1` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cursos_academicos`
--

LOCK TABLES `cursos_academicos` WRITE;
/*!40000 ALTER TABLE `cursos_academicos` DISABLE KEYS */;
INSERT INTO `cursos_academicos` VALUES (1,1,'1º DAW',1),(2,1,'2º DAW',2),(3,2,'1º DAM',1),(4,2,'2º DAM',2),(5,3,'1º SMR',1),(6,3,'2º SMR',2);
/*!40000 ALTER TABLE `cursos_academicos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `directores`
--

DROP TABLE IF EXISTS `directores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `directores` (
  `idDirector` int NOT NULL AUTO_INCREMENT,
  `nombreDirector` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `emailDirector` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',
  `telefonoDirector` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `dniDirector` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fechaNacimientoDirector` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaAltaDirector` date DEFAULT NULL,
  `direccionDirector` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ciudadDirector` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigoPostalDirector` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacionesDirector` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fcm_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `mfa_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mfa_backup_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idDirector`),
  UNIQUE KEY `uk_email_dir` (`emailDirector`),
  UNIQUE KEY `uk_dni_dir` (`dniDirector`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `directores`
--

LOCK TABLES `directores` WRITE;
/*!40000 ALTER TABLE `directores` DISABLE KEYS */;
INSERT INTO `directores` VALUES (1,'Carlos Mendoza','admin@aulapro.com','$2y$10$Gp4xQNd.vwU/YaqDxWrvF.pzy/MKEcfSVR1LP6e2nu0lQAgn4NnoC','600111222','12345678A','1980-05-15','2024-09-01','Calle Mayor 1','Madrid','28001','Director General de AulaPro',NULL,0,NULL,NULL);
/*!40000 ALTER TABLE `directores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dispositivos`
--

DROP TABLE IF EXISTS `dispositivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispositivos` (
  `idDispositivo` int NOT NULL AUTO_INCREMENT,
  `nombreDispositivo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `numeroSerie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estadoDispositivo` enum('disponible','prestado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'disponible',
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cantidad` int NOT NULL DEFAULT '1',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idDispositivo`),
  UNIQUE KEY `uk_serie` (`numeroSerie`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dispositivos`
--

LOCK TABLES `dispositivos` WRITE;
/*!40000 ALTER TABLE `dispositivos` DISABLE KEYS */;
INSERT INTO `dispositivos` VALUES (1,'Portátil Dell Latitude','DL-2025-001','disponible',NULL,1,NULL),(2,'Portátil Lenovo ThinkPad','LN-2025-002','prestado',NULL,1,NULL);
/*!40000 ALTER TABLE `dispositivos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estudiante_tutor`
--

DROP TABLE IF EXISTS `estudiante_tutor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estudiante_tutor` (
  `idEstudiante` int NOT NULL,
  `idTutor` int NOT NULL,
  `parentesco` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Tutor',
  PRIMARY KEY (`idEstudiante`,`idTutor`),
  KEY `idx_tutor` (`idTutor`),
  CONSTRAINT `fk_et_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_et_tut` FOREIGN KEY (`idTutor`) REFERENCES `tutores` (`idTutor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudiante_tutor`
--

LOCK TABLES `estudiante_tutor` WRITE;
/*!40000 ALTER TABLE `estudiante_tutor` DISABLE KEYS */;
INSERT INTO `estudiante_tutor` VALUES (1,1,'Padre'),(2,2,'Madre'),(3,3,'Madre');
/*!40000 ALTER TABLE `estudiante_tutor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estudiantes`
--

DROP TABLE IF EXISTS `estudiantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estudiantes` (
  `idEstudiante` int NOT NULL AUTO_INCREMENT,
  `nombreEstudiante` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `emailEstudiante` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',
  `telefonoEstudiante` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `dniEstudiante` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fechaNacimientoEstudiante` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaAltaEstudiante` date DEFAULT NULL,
  `direccionEstudiante` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ciudadEstudiante` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigoPostalEstudiante` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacionesEstudiante` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `idCiclo` int DEFAULT NULL,
  `curso` enum('Grado Medio','Grado Superior') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anioEstudio` enum('1º','2º') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idCurso` int DEFAULT NULL,
  `archivoTFG` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tituloTFG` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaSubidaTFG` datetime DEFAULT NULL,
  `fcm_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_eliminacion` datetime DEFAULT NULL,
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `mfa_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mfa_backup_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `idGrupo` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idEstudiante`),
  UNIQUE KEY `uk_email_est` (`emailEstudiante`),
  UNIQUE KEY `uk_dni_est` (`dniEstudiante`),
  KEY `idx_est_ciclo` (`idCiclo`),
  KEY `idx_est_curso` (`idCurso`),
  KEY `fk_estudiantes_grupo` (`idGrupo`),
  CONSTRAINT `fk_estudiantes_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL,
  CONSTRAINT `fk_estudiantes_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudiantes`
--

LOCK TABLES `estudiantes` WRITE;
/*!40000 ALTER TABLE `estudiantes` DISABLE KEYS */;
INSERT INTO `estudiantes` VALUES (1,'Ana Silva','ana.silva@aulapro.com','$2y$12$KvgcgImetxRJJLTc8LPaauhIUxjvmQlfLPbROwcC0rAfxKq6DkqUy','600666777','56789012E','2005-04-10','2024-09-01','Calle Verde 5','Madrid','28005','Delegada de clase. Excelente rendimiento académico.',1,'Grado Superior','2º',2,NULL,NULL,NULL,NULL,0,NULL,0,NULL,NULL,2,NULL),(2,'David Ortiz','david.ortiz@aulapro.com','$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu','600777888','67890123F','2005-09-18','2024-09-01','Calle Azul 6','Madrid','28006','Participativo y muy interesado en diseño Frontend.',1,'Grado Superior','2º',2,NULL,NULL,NULL,NULL,0,NULL,0,NULL,NULL,2,NULL),(3,'Elena Pastor','elena.pastor@aulapro.com','$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu','600888999','78901234G','2006-01-22','2025-09-01','Calle Roja 7','Madrid','28007','Interés en frameworks modernos y diseño UI/UX.',1,'Grado Superior','1º',1,NULL,NULL,NULL,NULL,0,NULL,0,NULL,NULL,1,NULL),(4,'Javier Ruiz','javier.ruiz@aulapro.com','$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu','600999000','89012345H','2006-05-30','2025-09-01','Calle Amarilla 8','Madrid','28008','Tiene conocimientos previos de programación autodidacta.',1,'Grado Superior','1º',1,NULL,NULL,NULL,NULL,0,NULL,0,NULL,NULL,1,NULL),(5,'Lucía Mendez','lucia.mendez@aulapro.com','$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu','600000111','90123456I','2005-11-05','2024-09-01','Calle Naranja 9','Madrid','28009','Estudiante de 2º DAM. Interesada en desarrollo de videojuegos.',2,'Grado Superior','2º',4,NULL,NULL,NULL,NULL,0,NULL,0,NULL,NULL,3,NULL),(6,'Sergio Abad','sergio.abad@aulapro.com','$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu','600111000','01234567J','2005-02-14','2024-09-01','Calle Violeta 10','Madrid','28010','Interés en administración de servidores y redes.',2,'Grado Superior','2º',4,NULL,NULL,NULL,NULL,0,NULL,0,NULL,NULL,3,NULL);
/*!40000 ALTER TABLE `estudiantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventos`
--

DROP TABLE IF EXISTS `eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eventos` (
  `idEvento` int NOT NULL AUTO_INCREMENT,
  `tituloEvento` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcionEvento` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fechaEvento` date NOT NULL,
  `horaEvento` time DEFAULT NULL,
  `ubicacionEvento` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idCreador` int NOT NULL DEFAULT '1',
  `tipo_visibilidad` enum('publica','roles','personalizado','privada') COLLATE utf8mb4_unicode_ci DEFAULT 'publica',
  `audiencia_json` json DEFAULT NULL,
  `activo` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idEvento`),
  KEY `idx_evento_fecha` (`fechaEvento`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventos`
--

LOCK TABLES `eventos` WRITE;
/*!40000 ALTER TABLE `eventos` DISABLE KEYS */;
INSERT INTO `eventos` VALUES (1,'Exposición de Proyectos de Fin de Grado','Defensa de los Proyectos TFG/Proyectos Integradores ante el comité evaluador.','2026-06-20','09:00:00','Salón de Actos - Edificio Central',1,'publica',NULL,1,'2026-07-28 12:11:21','2026-07-28 12:11:21'),(2,'Jornada Informativa: Inicio de FCT','Charla obligatoria sobre el proceso, documentación y pautas a seguir durante el periodo de prácticas FCT.','2026-02-15','12:30:00','Laboratorio Informática I',1,'publica',NULL,1,'2026-07-28 12:11:21','2026-07-28 12:11:21'),(3,'Conferencia: Salidas Laborales en el Ámbito Web','Charla tecnológica a cargo de directores de desarrollo y talento de Tech Solutions.','2026-07-30','16:00:00','Salón de Actos',1,'publica',NULL,1,'2026-07-28 12:11:21','2026-07-28 12:11:21'),(4,'Prueba Evento',NULL,'2026-08-15','10:00:00',NULL,1,'publica','{}',0,'2026-07-28 12:11:28','2026-07-28 12:13:30'),(5,'Prueba Actualizado',NULL,'2026-08-15','10:00:00',NULL,1,'publica','{}',0,'2026-07-28 12:11:48','2026-07-28 12:11:48'),(6,'Prueba Actualizado',NULL,'2026-08-15','10:00:00',NULL,1,'publica','{}',0,'2026-07-28 12:12:55','2026-07-28 12:12:55'),(7,'Solo profesores',NULL,'2026-09-01',NULL,NULL,1,'roles','{\"roles\": [\"profesor\"]}',0,'2026-07-28 12:13:06','2026-07-28 12:13:06'),(8,'Solo usuario 1',NULL,'2026-09-02',NULL,NULL,1,'personalizado','{\"usuarios_custom\": [1, 2, 3]}',0,'2026-07-28 12:13:06','2026-07-28 12:13:06'),(12,'Updated Title','Evento de prueba Task16','2026-07-28','10:00:00','Aula QA',1,'roles','{\"roles\": [\"profesor\"]}',0,'2026-07-28 12:59:05','2026-07-28 13:08:01'),(17,'QA Cron Test','','2026-07-28','10:00:00','Aula QA',1,'publica',NULL,0,'2026-07-28 13:08:35','2026-07-28 13:21:37'),(18,'QA Secretaria Test Edited','','2026-08-01',NULL,'',1,'publica',NULL,0,'2026-07-28 13:13:06','2026-07-28 13:13:20'),(19,'Reunión Consejo Académico','Revisión de avances y problemas académicos del cuatrimestre','2026-07-31','10:00:00','Sala de Juntas',1,'roles','{\"roles\": [\"director\", \"profesor\"]}',1,'2026-07-28 13:24:46','2026-07-28 13:24:46'),(20,'Entrega de Retos Finales','Última fecha para entregar los retos del ciclo','2026-08-04','23:59:59','Plataforma Virtual',1,'publica',NULL,1,'2026-07-28 13:24:46','2026-07-28 13:24:46'),(21,'Jornada de Tutoría','Sesión de tutoría individual con estudiantes','2026-08-02','14:00:00','Oficina de Tutoría',1,'roles','{\"roles\": [\"tutor\"]}',1,'2026-07-28 13:24:46','2026-07-28 13:24:46'),(22,'Charla de Orientación Laboral','Salidas profesionales y mercado de trabajo del sector','2026-08-11','09:30:00','Salón de Actos',1,'publica',NULL,1,'2026-07-28 13:24:46','2026-07-28 13:24:46'),(23,'Reunión de Padres y Tutores','Seguimiento individual del alumnado con tutores legales','2026-08-18','17:00:00','Aula 201',1,'personalizado','{\"usuarios_custom\": [{\"id\": 1, \"tipo\": \"estudiante\"}, {\"id\": 2, \"tipo\": \"estudiante\"}]}',1,'2026-07-28 13:24:46','2026-07-28 13:24:46');
/*!40000 ALTER TABLE `eventos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fct`
--

DROP TABLE IF EXISTS `fct`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fct` (
  `idFCT` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int NOT NULL,
  `idCiclo` int NOT NULL,
  `empresa` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idEmpresa` int DEFAULT NULL,
  `tutorEmpresa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `emailTutorEmpresa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `telefonoEmpresa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ciudadEmpresa` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaInicio` date DEFAULT NULL,
  `fechaFin` date DEFAULT NULL,
  `horasTotales` int DEFAULT NULL,
  `horasRealizadas` int DEFAULT NULL,
  `nota` decimal(4,2) DEFAULT NULL,
  `apto` tinyint(1) DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `idProfesorTutor` int DEFAULT NULL,
  `fase` int NOT NULL DEFAULT '1',
  `creado_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idFCT`),
  UNIQUE KEY `uq_fct_est_ciclo_fase` (`idEstudiante`,`idCiclo`,`fase`),
  KEY `idx_fct_ciclo` (`idCiclo`),
  KEY `idx_fct_profesor` (`idProfesorTutor`),
  KEY `idx_fct_empresa` (`idEmpresa`),
  CONSTRAINT `fk_fct_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `fk_fct_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_fct_prof` FOREIGN KEY (`idProfesorTutor`) REFERENCES `profesores` (`idProfesor`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fct`
--

LOCK TABLES `fct` WRITE;
/*!40000 ALTER TABLE `fct` DISABLE KEYS */;
INSERT INTO `fct` VALUES (1,1,1,'Tech Solutions S.L.',1,'Ramón Gómez','ramon.gomez@techsolutions.com','655987654','Madrid','2026-03-01','2026-06-30',400,400,9.20,1,'Excelente desempeño en el stack de desarrollo backend con PHP.',1,1,'2026-07-27 17:36:17'),(2,2,1,'Global Web Developers',2,'Sofía Martínez','sofia.martinez@globalweb.com','655654321','Madrid','2026-03-01','2026-06-30',400,260,NULL,NULL,'Buen ritmo en maquetación. En progreso continuo.',1,1,'2026-07-27 17:36:17');
/*!40000 ALTER TABLE `fct` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fct_diarios`
--

DROP TABLE IF EXISTS `fct_diarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fct_diarios` (
  `idDiario` int NOT NULL AUTO_INCREMENT,
  `idFCT` int NOT NULL,
  `fecha` date NOT NULL,
  `horas` decimal(4,2) NOT NULL,
  `actividades` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `observacionesTutor` text COLLATE utf8mb4_unicode_ci,
  `tokenAprobacion` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creadoEn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idDiario`),
  KEY `idx_fct_diarios_fct` (`idFCT`),
  CONSTRAINT `fk_fct_diarios_fct` FOREIGN KEY (`idFCT`) REFERENCES `fct` (`idFCT`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fct_diarios`
--

LOCK TABLES `fct_diarios` WRITE;
/*!40000 ALTER TABLE `fct_diarios` DISABLE KEYS */;
INSERT INTO `fct_diarios` VALUES (1,1,'2026-07-24',8.00,'Configuración del entorno de desarrollo local con Docker. Clonado del repositorio y primer contacto con el esquema de base de datos.','aprobado','Buen comienzo. Entorno configurado correctamente.',NULL,'2026-07-27 15:36:17'),(2,1,'2026-07-25',8.00,'Desarrollo de los endpoints de la API de autenticación y validación de tokens JWT.','aprobado','Código limpio y siguiendo las directrices de seguridad.',NULL,'2026-07-27 15:36:17'),(3,1,'2026-07-26',8.00,'Creación de pruebas unitarias para los controladores de usuarios y resolución de bugs menores en el middleware.','pendiente',NULL,NULL,'2026-07-27 15:36:17');
/*!40000 ALTER TABLE `fct_diarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fp_empresas`
--

DROP TABLE IF EXISTS `fp_empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fp_empresas` (
  `idEmpresa` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `cif` varchar(50) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `persona_contacto` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`idEmpresa`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fp_empresas`
--

LOCK TABLES `fp_empresas` WRITE;
/*!40000 ALTER TABLE `fp_empresas` DISABLE KEYS */;
INSERT INTO `fp_empresas` VALUES (1,'Tech Solutions S.L.','B12345678','Parque Tecnológico, Edificio A','Marta García','600123456','marta.garcia@techsolutions.com',1),(2,'Global Web Developers','B87654321','Avenida de la Informática 10','Luis Naranjo','600987654','luis.naranjo@globalweb.com',1);
/*!40000 ALTER TABLE `fp_empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gastos`
--

DROP TABLE IF EXISTS `gastos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gastos` (
  `idGasto` int NOT NULL AUTO_INCREMENT,
  `idCategoria` int NOT NULL,
  `idCiclo` int DEFAULT NULL,
  `concepto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `tipoJustificante` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numeroReferencia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archivoJustificante` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `creado_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `creadoPorId` int DEFAULT NULL,
  `creadoPorRol` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idGasto`),
  KEY `idx_gasto_categoria` (`idCategoria`),
  KEY `idx_gasto_ciclo` (`idCiclo`),
  KEY `idx_gasto_fecha` (`fecha`),
  CONSTRAINT `fk_gasto_cat` FOREIGN KEY (`idCategoria`) REFERENCES `categorias_gasto` (`idCategoria`) ON DELETE RESTRICT,
  CONSTRAINT `fk_gasto_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gastos`
--

LOCK TABLES `gastos` WRITE;
/*!40000 ALTER TABLE `gastos` DISABLE KEYS */;
INSERT INTO `gastos` VALUES (1,1,1,'Licencias JetBrains IDE (25 licencias académicas)',450.00,'2026-07-12','Factura','JET-2026-001',NULL,'Uso exclusivo para DAW/DAM','2026-07-27 17:36:17',NULL,NULL),(2,2,3,'Componentes de red (Switches administrables y Cat 6)',1200.00,'2026-07-15','Factura','NET-2026-104',NULL,'Para laboratorio del ciclo SMR','2026-07-27 17:36:17',NULL,NULL),(3,3,NULL,'Lote de folios, bolígrafos, tóners y carpetas de secretaría',185.50,'2026-07-17','Ticket','OFF-9923',NULL,'Material de oficina general','2026-07-27 17:36:17',NULL,NULL);
/*!40000 ALTER TABLE `gastos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grading_policies`
--

DROP TABLE IF EXISTS `grading_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grading_policies` (
  `idPolitica` int NOT NULL AUTO_INCREMENT,
  `idConfig` int NOT NULL,
  `escalaMin` decimal(4,2) NOT NULL DEFAULT '0.00',
  `escalaMax` decimal(4,2) NOT NULL DEFAULT '10.00',
  `notaAprobado` decimal(4,2) NOT NULL DEFAULT '5.00',
  `decimales` tinyint NOT NULL DEFAULT '0',
  `pesoTfgEnMedia` decimal(6,2) NOT NULL DEFAULT '1.00',
  PRIMARY KEY (`idPolitica`),
  UNIQUE KEY `uk_gp_config` (`idConfig`),
  CONSTRAINT `grading_policies_ibfk_1` FOREIGN KEY (`idConfig`) REFERENCES `academic_config` (`idConfig`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grading_policies`
--

LOCK TABLES `grading_policies` WRITE;
/*!40000 ALTER TABLE `grading_policies` DISABLE KEYS */;
INSERT INTO `grading_policies` VALUES (1,1,0.00,10.00,5.00,0,1.00);
/*!40000 ALTER TABLE `grading_policies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grupos`
--

DROP TABLE IF EXISTS `grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grupos` (
  `idGrupo` int NOT NULL AUTO_INCREMENT,
  `nombreGrupo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idCiclo` int NOT NULL,
  `anioEstudio` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`idGrupo`),
  KEY `idx_grupos_ciclo` (`idCiclo`),
  CONSTRAINT `fk_grupos_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grupos`
--

LOCK TABLES `grupos` WRITE;
/*!40000 ALTER TABLE `grupos` DISABLE KEYS */;
INSERT INTO `grupos` VALUES (1,'DAW-A',1,'1º'),(2,'DAW-B',1,'2º'),(3,'DAM-A',2,'2º');
/*!40000 ALTER TABLE `grupos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_secretarias`
--

DROP TABLE IF EXISTS `historial_secretarias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_secretarias` (
  `idHistorial` int NOT NULL AUTO_INCREMENT,
  `idSecretaria` int NOT NULL,
  `accion` varchar(100) NOT NULL,
  `entidad` varchar(100) NOT NULL,
  `detalles` text,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idHistorial`),
  KEY `idSecretaria` (`idSecretaria`),
  CONSTRAINT `historial_secretarias_ibfk_1` FOREIGN KEY (`idSecretaria`) REFERENCES `secretarias` (`idSecretaria`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_secretarias`
--

LOCK TABLES `historial_secretarias` WRITE;
/*!40000 ALTER TABLE `historial_secretarias` DISABLE KEYS */;
INSERT INTO `historial_secretarias` VALUES (1,1,'rechazar_comprobante','pagos','ID: 2 Foto borrosa, vuelve a subir','2026-07-26 17:38:51'),(2,1,'insertar','eventos','ID: 18 QA Secretaria Test','2026-07-28 15:13:06'),(3,1,'actualizar','eventos','ID: 18 QA Secretaria Test Edited','2026-07-28 15:13:20'),(4,1,'borrar','eventos','ID: 18 QA Secretaria Test Edited','2026-07-28 15:13:20');
/*!40000 ALTER TABLE `historial_secretarias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horario_franjas`
--

DROP TABLE IF EXISTS `horario_franjas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horario_franjas` (
  `idFranja` int NOT NULL AUTO_INCREMENT,
  `idCiclo` int NOT NULL,
  `horaInicio` time NOT NULL,
  `horaFin` time NOT NULL,
  `esReceso` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idFranja`),
  UNIQUE KEY `uq_ciclo_inicio` (`idCiclo`,`horaInicio`),
  CONSTRAINT `horario_franjas_ibfk_1` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horario_franjas`
--

LOCK TABLES `horario_franjas` WRITE;
/*!40000 ALTER TABLE `horario_franjas` DISABLE KEYS */;
INSERT INTO `horario_franjas` VALUES (1,1,'08:00:00','09:00:00',0),(2,1,'09:00:00','10:00:00',0),(3,1,'10:00:00','11:00:00',0),(4,1,'11:00:00','11:30:00',1),(5,1,'11:30:00','12:30:00',0),(6,1,'12:30:00','13:30:00',0),(7,1,'13:30:00','14:30:00',0);
/*!40000 ALTER TABLE `horario_franjas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horarios`
--

DROP TABLE IF EXISTS `horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horarios` (
  `idHorario` int NOT NULL AUTO_INCREMENT,
  `idCiclo` int NOT NULL,
  `diaSemana` enum('Lunes','Martes','Miércoles','Jueves','Viernes') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `horaInicio` time NOT NULL,
  `horaFin` time NOT NULL,
  `idModulo` int DEFAULT NULL,
  `idProfesor` int DEFAULT NULL,
  `idAula` int DEFAULT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idHorario`),
  UNIQUE KEY `uk_horario_celda` (`idCiclo`,`diaSemana`,`horaInicio`),
  UNIQUE KEY `uk_horario_aula` (`idAula`,`diaSemana`,`horaInicio`),
  UNIQUE KEY `uk_horario_profesor` (`idProfesor`,`diaSemana`,`horaInicio`),
  KEY `indice_horario_modulo` (`idModulo`),
  CONSTRAINT `fk_horario_aula` FOREIGN KEY (`idAula`) REFERENCES `aulas` (`idAula`) ON DELETE SET NULL,
  CONSTRAINT `fk_horario_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `fk_horario_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE SET NULL,
  CONSTRAINT `fk_horario_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=201 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
INSERT INTO `horarios` VALUES (79,1,'Lunes','08:30:00','10:30:00',1,1,1,'2026-07-24 01:57:30'),(80,1,'Lunes','10:30:00','12:30:00',2,2,1,'2026-07-24 01:57:30'),(81,1,'Lunes','12:30:00','14:30:00',3,1,1,'2026-07-24 01:57:30'),(82,1,'Martes','08:30:00','10:30:00',1,1,1,'2026-07-24 01:57:30'),(83,1,'Martes','10:30:00','12:30:00',2,2,1,'2026-07-24 01:57:30'),(84,1,'Martes','12:30:00','14:30:00',3,1,1,'2026-07-24 01:57:30'),(85,1,'Miércoles','08:30:00','10:30:00',1,1,1,'2026-07-24 01:57:30'),(86,1,'Miércoles','10:30:00','12:30:00',2,2,1,'2026-07-24 01:57:30'),(87,1,'Miércoles','12:30:00','14:30:00',3,1,1,'2026-07-24 01:57:30'),(88,1,'Jueves','08:30:00','10:30:00',1,1,1,'2026-07-24 01:57:30'),(89,1,'Jueves','10:30:00','12:30:00',2,2,1,'2026-07-24 01:57:30'),(90,1,'Jueves','12:30:00','14:30:00',3,1,1,'2026-07-24 01:57:30'),(91,1,'Viernes','08:30:00','10:30:00',1,1,1,'2026-07-24 01:57:30'),(92,1,'Viernes','10:30:00','12:30:00',2,2,1,'2026-07-24 01:57:30'),(93,1,'Viernes','12:30:00','14:30:00',3,1,1,'2026-07-24 01:57:30'),(154,2,'Lunes','08:30:00','10:30:00',7,3,2,'2026-07-28 15:22:35'),(155,2,'Lunes','10:30:00','12:30:00',9,4,2,'2026-07-28 15:22:35'),(156,2,'Lunes','12:30:00','14:30:00',10,2,2,'2026-07-28 15:22:35'),(157,2,'Martes','08:30:00','10:30:00',7,3,2,'2026-07-28 15:22:35'),(158,2,'Martes','10:30:00','12:30:00',9,4,2,'2026-07-28 15:22:35'),(159,2,'Martes','12:30:00','14:30:00',10,2,2,'2026-07-28 15:22:35'),(160,2,'Miércoles','08:30:00','10:30:00',7,3,2,'2026-07-28 15:22:35'),(161,2,'Miércoles','10:30:00','12:30:00',9,4,2,'2026-07-28 15:22:35'),(162,2,'Miércoles','12:30:00','14:30:00',10,2,2,'2026-07-28 15:22:35'),(163,2,'Jueves','08:30:00','10:30:00',7,3,2,'2026-07-28 15:22:35'),(164,2,'Jueves','10:30:00','12:30:00',9,4,2,'2026-07-28 15:22:35'),(165,2,'Jueves','12:30:00','14:30:00',10,2,2,'2026-07-28 15:22:35'),(166,2,'Viernes','08:30:00','10:30:00',7,3,2,'2026-07-28 15:22:35'),(167,2,'Viernes','10:30:00','12:30:00',9,4,2,'2026-07-28 15:22:35'),(168,2,'Viernes','12:30:00','14:30:00',10,2,2,'2026-07-28 15:22:35'),(184,3,'Lunes','15:00:00','17:00:00',8,5,3,'2026-07-28 15:24:46'),(185,3,'Lunes','17:00:00','19:00:00',11,4,3,'2026-07-28 15:24:46'),(186,3,'Lunes','19:00:00','21:00:00',12,3,3,'2026-07-28 15:24:46'),(187,3,'Martes','15:00:00','17:00:00',8,5,3,'2026-07-28 15:24:46'),(188,3,'Martes','17:00:00','19:00:00',11,4,3,'2026-07-28 15:24:46'),(189,3,'Martes','19:00:00','21:00:00',12,3,3,'2026-07-28 15:24:46'),(190,3,'Miércoles','15:00:00','17:00:00',8,5,3,'2026-07-28 15:24:46'),(191,3,'Miércoles','17:00:00','19:00:00',11,4,3,'2026-07-28 15:24:46'),(192,3,'Miércoles','19:00:00','21:00:00',12,3,3,'2026-07-28 15:24:46'),(193,3,'Jueves','15:00:00','17:00:00',8,5,3,'2026-07-28 15:24:46'),(194,3,'Jueves','17:00:00','19:00:00',11,4,3,'2026-07-28 15:24:46'),(195,3,'Jueves','19:00:00','21:00:00',12,3,3,'2026-07-28 15:24:46'),(196,3,'Viernes','15:00:00','17:00:00',8,5,3,'2026-07-28 15:24:46'),(197,3,'Viernes','17:00:00','19:00:00',11,4,3,'2026-07-28 15:24:46'),(198,3,'Viernes','19:00:00','21:00:00',12,3,3,'2026-07-28 15:24:46');
/*!40000 ALTER TABLE `horarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `internship_config`
--

DROP TABLE IF EXISTS `internship_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `internship_config` (
  `idConfigFCT` int NOT NULL AUTO_INCREMENT,
  `idConfig` int NOT NULL,
  `habilitado` tinyint(1) NOT NULL DEFAULT '0',
  `horasRequeridasDefecto` int NOT NULL DEFAULT '0',
  `metodoEvaluacion` enum('nota','apto_no_apto','ambos') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ambos',
  `pesoEnMedia` decimal(6,2) NOT NULL DEFAULT '0.00',
  `requiereAprobarParaTitular` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idConfigFCT`),
  UNIQUE KEY `uk_ic_config` (`idConfig`),
  CONSTRAINT `internship_config_ibfk_1` FOREIGN KEY (`idConfig`) REFERENCES `academic_config` (`idConfig`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `internship_config`
--

LOCK TABLES `internship_config` WRITE;
/*!40000 ALTER TABLE `internship_config` DISABLE KEYS */;
INSERT INTO `internship_config` VALUES (1,1,0,0,'ambos',0.00,1);
/*!40000 ALTER TABLE `internship_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventario`
--

DROP TABLE IF EXISTS `inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventario` (
  `idInventario` int NOT NULL AUTO_INCREMENT,
  `nombreArticulo` varchar(150) NOT NULL,
  `descripcion` text,
  `cantidad` int DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idInventario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventario`
--

LOCK TABLES `inventario` WRITE;
/*!40000 ALTER TABLE `inventario` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `justificaciones_falta`
--

DROP TABLE IF EXISTS `justificaciones_falta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `justificaciones_falta` (
  `idJustificacion` int NOT NULL AUTO_INCREMENT,
  `idAsistencia` int NOT NULL,
  `idEstudiante` int NOT NULL,
  `motivo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `archivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estadoOriginal` enum('ausente','retraso') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ausente',
  `estado` enum('pendiente','aprobada','rechazada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `rolSolicitante` enum('estudiante','tutor','profesor','secretaria','director') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'estudiante',
  `idSolicitante` int DEFAULT NULL,
  `idResuelvePor` int DEFAULT NULL,
  `rolResuelve` enum('profesor','secretaria','director') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivoRechazo` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaSolicitud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fechaResolucion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idJustificacion`),
  KEY `idx_asistencia` (`idAsistencia`),
  KEY `idx_estudiante` (`idEstudiante`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `justificaciones_falta`
--

LOCK TABLES `justificaciones_falta` WRITE;
/*!40000 ALTER TABLE `justificaciones_falta` DISABLE KEYS */;
INSERT INTO `justificaciones_falta` VALUES (1,3,3,'Cita médica adjunta.',NULL,'ausente','aprobada','estudiante',NULL,3,NULL,NULL,'2026-10-03 13:00:00','2026-10-04 07:00:00'),(2,1,1,'Problema de transporte.',NULL,'ausente','aprobada','estudiante',NULL,1,NULL,'','2026-10-02 08:00:00','2026-07-23 17:02:28'),(3,2,2,'Cita medica, adjunto justificante','demo_justificante_elena.pdf','ausente','pendiente','estudiante',NULL,NULL,NULL,NULL,'2026-07-23 20:06:04',NULL);
/*!40000 ALTER TABLE `justificaciones_falta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_ciclos`
--

DROP TABLE IF EXISTS `landing_ciclos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_ciclos` (
  `idLandingCiclo` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `etiqueta` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `resumen` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `descripcion` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `precio` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `duracion` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `modalidad` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `publicado` tinyint(1) NOT NULL DEFAULT '0',
  `destacado` tinyint(1) NOT NULL DEFAULT '0',
  `orden` int NOT NULL DEFAULT '0',
  `creadoEn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizadoEn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idLandingCiclo`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_publicado` (`publicado`,`orden`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_ciclos`
--

LOCK TABLES `landing_ciclos` WRITE;
/*!40000 ALTER TABLE `landing_ciclos` DISABLE KEYS */;
INSERT INTO `landing_ciclos` VALUES (1,'Desarrollo de Aplicaciones Web','desarrollo-de-aplicaciones-web','Grado Superior','Aprende a diseñar, crear y mantener aplicaciones web modernas y seguras.','<p>En este ciclo aprenderás a dominar tanto el frontend (HTML, CSS, JS, React) como el backend (PHP, Node, SQL) para convertirte en un Full-Stack Developer muy demandado por el mercado.</p>','','1200€ / año','2 Años (2000 horas)','Presencial / Online',1,1,1,'2026-07-27 15:36:17','2026-07-27 15:36:17'),(2,'Desarrollo de Aplicaciones Multiplataforma','desarrollo-de-aplicaciones-multiplataforma','Grado Superior','Conviértete en desarrollador de apps móviles, de escritorio y videojuegos.','<p>Domina lenguajes como Java, C# y Kotlin para crear software robusto multiplataforma. Incluye programación de interfaces gráficas avanzadas y acceso a datos.</p>','','1200€ / año','2 Años (2000 horas)','Presencial',1,1,2,'2026-07-27 15:36:17','2026-07-27 15:36:17'),(3,'Sistemas Microinformáticos y Redes','sistemas-microinformaticos-y-redes','Grado Medio','Montaje de hardware, instalación de redes y soporte técnico.','<p>Fórmate como técnico de sistemas. Aprenderás a montar servidores, administrar redes locales, configurar routers y resolver incidencias de hardware en empresas.</p>','','900€ / año','2 Años (2000 horas)','Presencial',1,0,3,'2026-07-27 15:36:17','2026-07-27 15:36:17');
/*!40000 ALTER TABLE `landing_ciclos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_config`
--

DROP TABLE IF EXISTS `landing_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_config` (
  `idLanding` int NOT NULL DEFAULT '1',
  `plantilla` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ajustes` json DEFAULT NULL,
  `plantilla_pub` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ajustes_pub` json DEFAULT NULL,
  `publicadoEn` datetime DEFAULT NULL,
  `actualizadoEn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idLanding`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_config`
--

LOCK TABLES `landing_config` WRITE;
/*!40000 ALTER TABLE `landing_config` DISABLE KEYS */;
INSERT INTO `landing_config` VALUES (1,NULL,NULL,NULL,NULL,NULL,'2026-07-21 16:23:31');
/*!40000 ALTER TABLE `landing_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_secciones`
--

DROP TABLE IF EXISTS `landing_secciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_secciones` (
  `idSeccion` int NOT NULL AUTO_INCREMENT,
  `version` enum('draft','live') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `tipo` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `contenido` json DEFAULT NULL,
  PRIMARY KEY (`idSeccion`),
  KEY `idx_landing_version_orden` (`version`,`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_secciones`
--

LOCK TABLES `landing_secciones` WRITE;
/*!40000 ALTER TABLE `landing_secciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `landing_secciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_acciones`
--

DROP TABLE IF EXISTS `log_acciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_acciones` (
  `idLog` int NOT NULL AUTO_INCREMENT,
  `idAdmin` int DEFAULT NULL,
  `accion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabla` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idRegistro` int DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idLog`),
  KEY `idx_log_admin` (`idAdmin`),
  KEY `idx_log_fecha` (`fecha`),
  CONSTRAINT `fk_log_admin` FOREIGN KEY (`idAdmin`) REFERENCES `directores` (`idDirector`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_acciones`
--

LOCK TABLES `log_acciones` WRITE;
/*!40000 ALTER TABLE `log_acciones` DISABLE KEYS */;
INSERT INTO `log_acciones` VALUES (1,1,'add_franja','horario',1,'19:00-19:30','172.20.10.2','2026-07-24 02:18:17'),(2,1,'remove_franja','horario',1,'19:00','172.20.10.2','2026-07-24 02:18:17'),(3,NULL,'insertar','anuncios',NULL,'Prueba API movil','127.0.0.1','2026-07-24 21:00:47'),(6,1,'insertar','eventos',12,'QA Test Evento Fase1','127.0.0.1','2026-07-28 14:59:06'),(7,1,'actualizar','eventos',12,'Updated Title','127.0.0.1','2026-07-28 14:59:23'),(8,1,'borrar','eventos',12,'Updated Title','127.0.0.1','2026-07-28 15:08:01'),(9,1,'insertar','eventos',17,'QA Cron Test','127.0.0.1','2026-07-28 15:08:35'),(10,1,'borrar','eventos',17,'QA Cron Test','127.0.0.1','2026-07-28 15:21:37');
/*!40000 ALTER TABLE `log_acciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_intentos`
--

DROP TABLE IF EXISTS `login_intentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_intentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `intentos` tinyint NOT NULL DEFAULT '0',
  `bloqueado_hasta` datetime DEFAULT NULL,
  `ultimo_intento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_intentos`
--

LOCK TABLES `login_intentos` WRITE;
/*!40000 ALTER TABLE `login_intentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_intentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulo_profesor`
--

DROP TABLE IF EXISTS `modulo_profesor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulo_profesor` (
  `idModulo` int NOT NULL,
  `idProfesor` int NOT NULL,
  PRIMARY KEY (`idModulo`,`idProfesor`),
  UNIQUE KEY `idx_unico_modulo` (`idModulo`),
  KEY `fk_relm_prof` (`idProfesor`),
  CONSTRAINT `fk_relm_mod` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_relm_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulo_profesor`
--

LOCK TABLES `modulo_profesor` WRITE;
/*!40000 ALTER TABLE `modulo_profesor` DISABLE KEYS */;
INSERT INTO `modulo_profesor` VALUES (1,2),(2,2),(3,1),(4,1),(5,3),(6,2),(7,3),(8,3);
/*!40000 ALTER TABLE `modulo_profesor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulo_reto`
--

DROP TABLE IF EXISTS `modulo_reto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulo_reto` (
  `idModulo` int NOT NULL,
  `idReto` int NOT NULL,
  PRIMARY KEY (`idModulo`,`idReto`),
  KEY `fk_mr_reto` (`idReto`),
  CONSTRAINT `fk_mr_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_mr_reto` FOREIGN KEY (`idReto`) REFERENCES `retos` (`idReto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulo_reto`
--

LOCK TABLES `modulo_reto` WRITE;
/*!40000 ALTER TABLE `modulo_reto` DISABLE KEYS */;
INSERT INTO `modulo_reto` VALUES (1,1),(2,1);
/*!40000 ALTER TABLE `modulo_reto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulos`
--

DROP TABLE IF EXISTS `modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulos` (
  `idModulo` int NOT NULL AUTO_INCREMENT,
  `nombreModulo` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigoModulo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `horasMaximas` int DEFAULT NULL,
  `idCiclo` int NOT NULL,
  `idCurso` int DEFAULT NULL,
  `tipoModulo` enum('Específico','Transversal','Proyecto','Empresa') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Específico',
  `cursoAnio` enum('1º','2º') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1º',
  `creditosECTS` int DEFAULT '0',
  PRIMARY KEY (`idModulo`),
  KEY `idx_modulo_ciclo` (`idCiclo`),
  KEY `idx_modulo_curso` (`idCurso`),
  CONSTRAINT `fk_modulos_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulos`
--

LOCK TABLES `modulos` WRITE;
/*!40000 ALTER TABLE `modulos` DISABLE KEYS */;
INSERT INTO `modulos` VALUES (1,'Programación','PRG',240,1,1,'Específico','1º',10),(2,'Bases de Datos','BD',180,1,1,'Específico','1º',8),(3,'Desarrollo Web en Entorno Servidor','DWES',180,1,2,'Específico','2º',9),(4,'Desarrollo Web en Entorno Cliente','DWEC',140,1,2,'Específico','2º',7),(5,'Diseño de Interfaces Web','DIW',120,1,2,'Específico','2º',6),(6,'Entornos de Desarrollo','ED',90,1,1,'Específico','1º',4),(7,'Programación Multimedia y Dispositivos Móviles','PMDM',120,2,4,'Específico','2º',6),(8,'Montaje y Mantenimiento de Equipos','MME',150,3,5,'Específico','1º',8),(9,'Acceso a Datos','AD',160,2,4,'Específico','2º',7),(10,'Desarrollo de Interfaces','DI',160,2,4,'Específico','2º',7),(11,'Redes Locales','RL',130,3,5,'Específico','1º',6),(12,'Sistemas Operativos Monopuesto','SOM',130,3,5,'Específico','1º',6);
/*!40000 ALTER TABLE `modulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `niveles`
--

DROP TABLE IF EXISTS `niveles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `niveles` (
  `idNivel` int NOT NULL AUTO_INCREMENT,
  `nombreNivel` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`idNivel`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `niveles`
--

LOCK TABLES `niveles` WRITE;
/*!40000 ALTER TABLE `niveles` DISABLE KEYS */;
INSERT INTO `niveles` VALUES (1,'Grado Superior'),(2,'Grado Medio'),(3,'Grado Básico'),(4,'Colegio (Primaria/ESO/Bachillerato)');
/*!40000 ALTER TABLE `niveles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones_recordatorios`
--

DROP TABLE IF EXISTS `notificaciones_recordatorios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificaciones_recordatorios` (
  `idNotificacionRecordatorio` int NOT NULL AUTO_INCREMENT,
  `idEvento` int NOT NULL,
  `idUsuario` int NOT NULL,
  `tipoUsuario` enum('director','profesor','secretaria','estudiante','tutor') NOT NULL,
  `idRecordatorio` int DEFAULT NULL,
  `fecha_programada` datetime NOT NULL,
  `fecha_enviada` datetime DEFAULT NULL,
  `leido` tinyint DEFAULT '0',
  `estado` enum('pendiente','enviado','fallido') DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idNotificacionRecordatorio`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones_recordatorios`
--

LOCK TABLES `notificaciones_recordatorios` WRITE;
/*!40000 ALTER TABLE `notificaciones_recordatorios` DISABLE KEYS */;
INSERT INTO `notificaciones_recordatorios` VALUES (6,17,1,'director',37,'2026-07-28 15:08:53',NULL,1,'pendiente','2026-07-28 13:08:53'),(7,17,1,'profesor',37,'2026-07-28 15:08:53',NULL,0,'pendiente','2026-07-28 13:08:53'),(8,17,2,'profesor',37,'2026-07-28 15:08:53',NULL,0,'pendiente','2026-07-28 13:08:53'),(9,17,3,'profesor',37,'2026-07-28 15:08:53',NULL,0,'pendiente','2026-07-28 13:08:53'),(10,17,1,'secretaria',37,'2026-07-28 15:08:53',NULL,0,'pendiente','2026-07-28 13:08:53'),(11,17,1,'director',38,'2026-07-28 15:08:53',NULL,0,'pendiente','2026-07-28 13:08:53'),(12,17,1,'profesor',38,'2026-07-28 15:08:53',NULL,0,'pendiente','2026-07-28 13:08:53'),(13,17,2,'profesor',38,'2026-07-28 15:08:53',NULL,0,'pendiente','2026-07-28 13:08:53'),(14,17,3,'profesor',38,'2026-07-28 15:08:53',NULL,0,'pendiente','2026-07-28 13:08:53'),(15,17,1,'secretaria',38,'2026-07-28 15:08:53',NULL,0,'pendiente','2026-07-28 13:08:53'),(16,17,1,'director',39,'2026-07-28 15:08:53',NULL,0,'pendiente','2026-07-28 13:08:53'),(17,17,1,'profesor',39,'2026-07-28 15:08:53',NULL,0,'pendiente','2026-07-28 13:08:53'),(18,17,2,'profesor',39,'2026-07-28 15:08:53',NULL,0,'pendiente','2026-07-28 13:08:53'),(19,17,3,'profesor',39,'2026-07-28 15:08:53',NULL,0,'pendiente','2026-07-28 13:08:53'),(20,17,1,'secretaria',39,'2026-07-28 15:08:53',NULL,0,'pendiente','2026-07-28 13:08:53');
/*!40000 ALTER TABLE `notificaciones_recordatorios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos`
--

DROP TABLE IF EXISTS `pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos` (
  `idPago` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int NOT NULL,
  `rolRegistrador` enum('secretaria','director') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idRegistrador` int DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fechaPago` date NOT NULL,
  `fechaProximoPago` date DEFAULT NULL,
  `tipoPago` enum('mensual','trimestral','semestral','unico') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comprobante` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prorrogaHasta` date DEFAULT NULL,
  `estadoComprobante` enum('ninguno','verificando','aprobado','rechazado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ninguno',
  `motivoRechazoComprobante` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idPago`),
  KEY `idx_pago_est` (`idEstudiante`),
  CONSTRAINT `fk_pag_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
INSERT INTO `pagos` VALUES (1,1,NULL,NULL,2500.00,'2026-08-15',NULL,'unico',NULL,NULL,'aprobado',NULL),(2,2,NULL,NULL,2500.00,'2026-08-20',NULL,'unico',NULL,NULL,'aprobado',NULL);
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_usuario` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT '0',
  `creado_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_token` (`token`),
  KEY `idx_pr_email` (`email`),
  KEY `idx_pr_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_matricula_archivos`
--

DROP TABLE IF EXISTS `pre_matricula_archivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_matricula_archivos` (
  `idArchivo` int NOT NULL AUTO_INCREMENT,
  `idPreMatricula` int NOT NULL,
  `tipoDocumento` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombreArchivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rutaArchivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fechaSubida` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idArchivo`),
  KEY `idx_pma_prematricula` (`idPreMatricula`),
  CONSTRAINT `fk_pma_pm` FOREIGN KEY (`idPreMatricula`) REFERENCES `pre_matriculas` (`idPreMatricula`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_matricula_archivos`
--

LOCK TABLES `pre_matricula_archivos` WRITE;
/*!40000 ALTER TABLE `pre_matricula_archivos` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_matricula_archivos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_matriculas`
--

DROP TABLE IF EXISTS `pre_matriculas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_matriculas` (
  `idPreMatricula` int NOT NULL AUTO_INCREMENT,
  `dni` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idCiclo` int NOT NULL,
  `curso` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1º',
  `nombreTutor` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dniTutor` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emailTutor` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefonoTutor` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parentescoTutor` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('pendiente','revisando','aceptada','rechazada','subsanacion') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fechaSolicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idPreMatricula`),
  KEY `idx_pm_ciclo` (`idCiclo`),
  KEY `idx_pm_estado` (`estado`),
  KEY `idx_pm_dni` (`dni`),
  CONSTRAINT `fk_pm_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_matriculas`
--

LOCK TABLES `pre_matriculas` WRITE;
/*!40000 ALTER TABLE `pre_matriculas` DISABLE KEYS */;
INSERT INTO `pre_matriculas` VALUES (1,'77777771Z','Nuevo Alumno 1','Martín Silva','nuevo1@aulapro.com','677111222',1,'Primero',NULL,NULL,NULL,NULL,NULL,'pendiente','','2026-08-01 10:00:00'),(2,'77777772Z','Nuevo Alumno 2','García López','nuevo2@aulapro.com','677222333',2,'Segundo',NULL,NULL,NULL,NULL,NULL,'aceptada',NULL,'2026-08-02 11:30:00');
/*!40000 ALTER TABLE `pre_matriculas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prestamos`
--

DROP TABLE IF EXISTS `prestamos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prestamos` (
  `idPrestamo` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int NOT NULL,
  `numeroSerie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fechaPrestamo` date NOT NULL,
  `fechaDevolucion` date DEFAULT NULL,
  `estadoPrestamo` enum('en curso','devuelto') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'en curso',
  `idDispositivo` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idPrestamo`),
  KEY `idx_pres_est` (`idEstudiante`),
  KEY `idx_pres_serie` (`numeroSerie`),
  CONSTRAINT `fk_pres_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prestamos`
--

LOCK TABLES `prestamos` WRITE;
/*!40000 ALTER TABLE `prestamos` DISABLE KEYS */;
INSERT INTO `prestamos` VALUES (1,1,'LN-2025-002','2026-09-15',NULL,'en curso',2,NULL),(2,3,'DL-2025-001','2026-07-23','2026-07-23','devuelto',1,NULL);
/*!40000 ALTER TABLE `prestamos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profesores`
--

DROP TABLE IF EXISTS `profesores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profesores` (
  `idProfesor` int NOT NULL AUTO_INCREMENT,
  `nombreProfesor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `emailProfesor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',
  `telefonoProfesor` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dniProfesor` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaNacimientoProfesor` date DEFAULT NULL,
  `fechaAltaProfesor` date DEFAULT NULL,
  `direccionProfesor` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudadProfesor` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigoPostalProfesor` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacionesProfesor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fcm_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `esTutor` tinyint(1) DEFAULT '0',
  `idCicloTutor` int DEFAULT NULL,
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `mfa_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mfa_backup_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idProfesor`),
  UNIQUE KEY `uk_email_prof` (`emailProfesor`),
  KEY `idx_prof_dni` (`dniProfesor`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profesores`
--

LOCK TABLES `profesores` WRITE;
/*!40000 ALTER TABLE `profesores` DISABLE KEYS */;
INSERT INTO `profesores` VALUES (1,'Juan Pérez','juan.perez@aulapro.com','$2y$12$NoDoFaNeZT43YYAR1XnAGOEhZHc9NGdJXxGc.JOceS21paDUZnQRq','600333444','23456789B','1985-10-20','2024-09-01','Calle Secundaria 2','Madrid','28002','Profesor especialista en Backend. Tutor de 2º DAW.',NULL,1,1,0,NULL,NULL),(2,'María Rodríguez','maria.rodriguez@aulapro.com','$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu','600444555','34567890C','1988-03-12','2024-09-01','Avenida Principal 3','Madrid','28003','Profesora de programación e iniciación al desarrollo.',NULL,0,NULL,0,NULL,NULL),(3,'Pedro Martínez','pedro.martinez@aulapro.com','$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu','600555666','45678901D','1982-07-25','2024-09-01','Paseo del Prado 4','Madrid','28004','Profesor de multiplataforma y hardware. Tutor de 2º DAM.',NULL,1,2,0,NULL,NULL),(4,'Laura Gómez','laura.gomez@aulapro.com','$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',NULL,'56789012E',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,NULL,NULL),(5,'Miguel Torres','miguel.torres@aulapro.com','$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',NULL,'67890123F',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,NULL,NULL);
/*!40000 ALTER TABLE `profesores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotion_rules`
--

DROP TABLE IF EXISTS `promotion_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotion_rules` (
  `idRegla` int NOT NULL AUTO_INCREMENT,
  `idConfig` int NOT NULL,
  `requiereTodosModulos` tinyint(1) NOT NULL DEFAULT '1',
  `notaMinimaGlobal` decimal(4,2) NOT NULL DEFAULT '5.00',
  `permiteModulosPendientes` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`idRegla`),
  UNIQUE KEY `uk_pr_config` (`idConfig`),
  CONSTRAINT `promotion_rules_ibfk_1` FOREIGN KEY (`idConfig`) REFERENCES `academic_config` (`idConfig`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotion_rules`
--

LOCK TABLES `promotion_rules` WRITE;
/*!40000 ALTER TABLE `promotion_rules` DISABLE KEYS */;
INSERT INTO `promotion_rules` VALUES (1,1,1,5.00,0);
/*!40000 ALTER TABLE `promotion_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rate_limits`
--

DROP TABLE IF EXISTS `rate_limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rate_limits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(64) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `hits` int unsigned NOT NULL DEFAULT '0',
  `window_start` int unsigned NOT NULL,
  `blocked_until` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_scope_ip` (`scope`,`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rate_limits`
--

LOCK TABLES `rate_limits` WRITE;
/*!40000 ALTER TABLE `rate_limits` DISABLE KEYS */;
INSERT INTO `rate_limits` VALUES (1,'api_login','172.20.10.3',1,1784853940,NULL),(2,'apiv1_99c0bc6f','172.20.10.3',3,1784823764,NULL),(3,'api_login','172.20.10.2',1,1784847700,NULL),(4,'apiv1_e1ad8f35','172.20.10.2',6,1784824108,NULL),(5,'apiv1_7b039a22','172.20.10.2',3,1784824126,NULL),(6,'chat_send_estudiante_1','172.20.10.2',1,1784824126,NULL),(7,'apiv1_dc86f9a8','172.20.10.2',1,1784824127,NULL),(8,'apiv1_aa695151','172.20.10.2',1,1784824127,NULL),(9,'apiv1_d88f0c49','172.20.10.2',5,1784824513,NULL),(10,'apiv1_d062516a','172.20.10.2',1,1784824526,NULL),(11,'apiv1_1ee7f54e','172.20.10.2',1,1784824817,NULL),(12,'apiv1_3cac4f61','172.20.10.2',1,1784824818,NULL),(13,'apiv1_898c18e2','172.20.10.3',3,1784825438,NULL),(14,'chat_send_admin_1','172.20.10.3',1,1784825263,NULL),(15,'apiv1_6255e3e5','172.20.10.2',5,1784825554,NULL),(16,'apiv1_04eb4a84','172.20.10.2',1,1784825768,NULL),(17,'apiv1_30747044','172.20.10.2',1,1784826096,NULL),(18,'apiv1_d1c0f552','172.20.10.2',1,1784826096,NULL),(19,'apiv1_70085cf3','172.20.10.2',1,1784826096,NULL),(20,'apiv1_3a851751','172.20.10.2',2,1784826135,NULL),(21,'apiv1_9714c7df','172.20.10.2',1,1784826148,NULL),(22,'apiv1_913d092d','172.20.10.2',2,1784826148,NULL),(23,'apiv1_da14ae3b','172.20.10.2',3,1784826149,NULL),(24,'apiv1_f609270b','172.20.10.2',1,1784826262,NULL),(25,'apiv1_a0293fec','172.20.10.2',3,1784826529,NULL),(26,'apiv1_d66db94e','172.20.10.2',2,1784826538,NULL),(27,'apiv1_c1e12e34','172.20.10.3',21,1784827760,NULL),(28,'apiv1_73676419','172.20.10.3',1,1784828397,NULL),(29,'chat_send_estudiante_1','172.20.10.3',2,1784827918,NULL),(30,'apiv1_b06c58f5','172.20.10.2',5,1784828490,NULL),(31,'apiv1_69bf054b','172.20.10.2',2,1784828527,NULL),(32,'apiv1_8a62e25d','172.20.10.2',1,1784828543,NULL),(33,'apiv1_1d579aa1','172.20.10.2',1,1784828554,NULL),(34,'apiv1_168c54b9','172.20.10.3',20,1784836110,NULL),(35,'apiv1_ff523a84','172.20.10.3',17,1784836201,NULL),(36,'apiv1_e58c27ed','172.20.10.3',4,1784836472,NULL),(37,'apiv1_9c9da5c9','172.20.10.2',1,1784837102,NULL),(38,'apiv1_5d50c6a3','172.20.10.2',1,1784837120,NULL),(39,'apiv1_8967cb09','172.20.10.2',2,1784837150,NULL),(40,'apiv1_4fdffd02','172.20.10.2',1,1784837175,NULL),(41,'apiv1_339443d9','172.20.10.2',1,1784837193,NULL),(42,'apiv1_f71d43bd','172.20.10.2',1,1784837193,NULL),(43,'apiv1_0dd02b69','172.20.10.3',3,1784842991,NULL),(44,'apiv1_7cc5f823','172.20.10.2',2,1784840680,NULL),(45,'apiv1_373c27b3','172.20.10.2',2,1784840788,NULL),(46,'apiv1_c7face57','172.20.10.2',5,1784840681,NULL),(47,'apiv1_d9d9e2eb','172.20.10.2',3,1784840788,NULL),(48,'apiv1_76ae15b7','172.20.10.2',4,1784840682,NULL),(49,'chat_send_estudiante_2','172.20.10.2',1,1784840699,NULL),(50,'apiv1_911675be','172.20.10.2',3,1784847701,NULL),(51,'apiv1_28a50fe3','172.20.10.3',4,1784848165,NULL),(52,'apiv1_34ebbfb5','172.20.10.3',1,1784855084,NULL),(53,'chat_send_estudiante_2','172.20.10.3',1,1784853950,NULL),(54,'api_login','127.0.0.1',1,1784920698,NULL),(55,'apiv1_7a29b0c9','127.0.0.1',1,1784914298,NULL),(56,'apiv1_d9fae6d8','127.0.0.1',3,1784915251,NULL),(57,'apiv1_110d1ed4','127.0.0.1',1,1784915267,NULL),(58,'apiv1_d66be5fd','127.0.0.1',2,1784918412,NULL),(59,'apiv1_1cf55157','127.0.0.1',2,1784919647,NULL),(60,'apiv1_2b2dcbf1','127.0.0.1',1,1784919681,NULL),(61,'apiv1_6d3d20ef','127.0.0.1',3,1784920698,NULL),(62,'apiv1_d223dc88','127.0.0.1',4,1785080278,NULL),(63,'apiv1_2a9b6632','127.0.0.1',2,1785080105,NULL),(64,'apiv1_a582c74b','127.0.0.1',2,1785080279,NULL),(65,'apiv1_f85bae39','127.0.0.1',2,1785080329,NULL);
/*!40000 ALTER TABLE `rate_limits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reclamaciones`
--

DROP TABLE IF EXISTS `reclamaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reclamaciones` (
  `idReclamacion` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int DEFAULT NULL,
  `idProfesor` int DEFAULT NULL,
  `id_parent` int DEFAULT NULL,
  `emisor_rol` enum('estudiante','profesor','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `asunto` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estadoReclamacion` enum('pendiente','atendido') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `leido` tinyint(1) DEFAULT '0',
  `respuesta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idReclamacion`),
  KEY `idx_rec_est` (`idEstudiante`),
  KEY `idx_rec_prof` (`idProfesor`),
  KEY `fk_reclamaciones_parent` (`id_parent`),
  CONSTRAINT `fk_rec_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_rec_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE SET NULL,
  CONSTRAINT `fk_reclamaciones_parent` FOREIGN KEY (`id_parent`) REFERENCES `reclamaciones` (`idReclamacion`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reclamaciones`
--

LOCK TABLES `reclamaciones` WRITE;
/*!40000 ALTER TABLE `reclamaciones` DISABLE KEYS */;
INSERT INTO `reclamaciones` VALUES (1,1,1,NULL,'estudiante','Revisión nota Tarea 1','Creo que el ejercicio 3 está correcto.','2026-10-15 10:00:00','atendido',1,NULL),(2,1,1,1,'estudiante','Revisión nota Tarea 1','Gracias por revisarlo','2026-07-23 18:28:46','atendido',1,'');
/*!40000 ALTER TABLE `reclamaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recordatorios`
--

DROP TABLE IF EXISTS `recordatorios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recordatorios` (
  `idRecordatorio` int NOT NULL AUTO_INCREMENT,
  `idEvento` int NOT NULL,
  `tipo_recordatorio` enum('24h_antes','1h_antes','en_inicio') NOT NULL,
  `minutos_antes` int NOT NULL,
  `activo` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idRecordatorio`),
  UNIQUE KEY `uk_evento_tipo` (`idEvento`,`tipo_recordatorio`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recordatorios`
--

LOCK TABLES `recordatorios` WRITE;
/*!40000 ALTER TABLE `recordatorios` DISABLE KEYS */;
INSERT INTO `recordatorios` VALUES (1,5,'24h_antes',1440,1,'2026-07-28 12:11:48'),(2,5,'1h_antes',60,0,'2026-07-28 12:11:48'),(3,5,'en_inicio',0,0,'2026-07-28 12:11:48'),(4,6,'24h_antes',1440,1,'2026-07-28 12:12:55'),(5,6,'1h_antes',60,0,'2026-07-28 12:12:55'),(6,6,'en_inicio',0,0,'2026-07-28 12:12:55'),(7,7,'24h_antes',1440,0,'2026-07-28 12:13:06'),(8,7,'1h_antes',60,0,'2026-07-28 12:13:06'),(9,7,'en_inicio',0,0,'2026-07-28 12:13:06'),(10,8,'24h_antes',1440,0,'2026-07-28 12:13:06'),(11,8,'1h_antes',60,0,'2026-07-28 12:13:06'),(12,8,'en_inicio',0,0,'2026-07-28 12:13:06'),(16,10,'24h_antes',1440,0,'2026-07-28 12:55:17'),(17,10,'1h_antes',60,0,'2026-07-28 12:55:17'),(18,10,'en_inicio',0,0,'2026-07-28 12:55:17'),(19,11,'24h_antes',1440,0,'2026-07-28 12:55:17'),(20,11,'1h_antes',60,0,'2026-07-28 12:55:17'),(21,11,'en_inicio',0,0,'2026-07-28 12:55:17'),(22,12,'24h_antes',1440,1,'2026-07-28 12:59:05'),(23,12,'1h_antes',60,0,'2026-07-28 12:59:05'),(24,12,'en_inicio',0,0,'2026-07-28 12:59:05'),(37,17,'24h_antes',1440,1,'2026-07-28 13:08:35'),(38,17,'1h_antes',60,1,'2026-07-28 13:08:35'),(39,17,'en_inicio',0,1,'2026-07-28 13:08:35'),(40,18,'24h_antes',1440,0,'2026-07-28 13:13:06'),(41,18,'1h_antes',60,0,'2026-07-28 13:13:06'),(42,18,'en_inicio',0,0,'2026-07-28 13:13:06'),(43,19,'24h_antes',1440,1,'2026-07-28 13:24:46'),(44,20,'24h_antes',1440,1,'2026-07-28 13:24:46'),(45,21,'24h_antes',1440,1,'2026-07-28 13:24:46'),(46,22,'24h_antes',1440,1,'2026-07-28 13:24:46'),(47,23,'24h_antes',1440,1,'2026-07-28 13:24:46'),(50,19,'1h_antes',60,1,'2026-07-28 13:24:46'),(51,20,'1h_antes',60,1,'2026-07-28 13:24:46'),(52,21,'1h_antes',60,1,'2026-07-28 13:24:46'),(53,22,'1h_antes',60,1,'2026-07-28 13:24:46'),(54,23,'1h_antes',60,1,'2026-07-28 13:24:46'),(57,19,'en_inicio',0,1,'2026-07-28 13:24:46'),(58,21,'en_inicio',0,1,'2026-07-28 13:24:46'),(59,23,'en_inicio',0,1,'2026-07-28 13:24:46'),(60,20,'en_inicio',0,0,'2026-07-28 13:24:46'),(61,22,'en_inicio',0,0,'2026-07-28 13:24:46');
/*!40000 ALTER TABLE `recordatorios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resultados_aprendizaje`
--

DROP TABLE IF EXISTS `resultados_aprendizaje`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resultados_aprendizaje` (
  `idRA` int NOT NULL AUTO_INCREMENT,
  `idModulo` int NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `descripcion` text,
  `porcentaje` int DEFAULT '0',
  `idTipo` int DEFAULT NULL,
  PRIMARY KEY (`idRA`),
  KEY `idModulo` (`idModulo`),
  KEY `idx_ra_tipo` (`idTipo`),
  CONSTRAINT `resultados_aprendizaje_ibfk_1` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resultados_aprendizaje`
--

LOCK TABLES `resultados_aprendizaje` WRITE;
/*!40000 ALTER TABLE `resultados_aprendizaje` DISABLE KEYS */;
INSERT INTO `resultados_aprendizaje` VALUES (1,1,'RA1','Programa aplicaciones básicas.',50,NULL),(2,1,'RA2','Usa POO en Java.',50,NULL);
/*!40000 ALTER TABLE `resultados_aprendizaje` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reto_archivos`
--

DROP TABLE IF EXISTS `reto_archivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reto_archivos` (
  `idArchivo` int NOT NULL AUTO_INCREMENT,
  `idReto` int NOT NULL,
  `nombreArchivo` varchar(255) NOT NULL,
  `rutaArchivo` varchar(255) NOT NULL,
  `tipoArchivo` varchar(50) DEFAULT NULL,
  `fechaSubida` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idArchivo`),
  KEY `idReto` (`idReto`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reto_archivos`
--

LOCK TABLES `reto_archivos` WRITE;
/*!40000 ALTER TABLE `reto_archivos` DISABLE KEYS */;
/*!40000 ALTER TABLE `reto_archivos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `retos`
--

DROP TABLE IF EXISTS `retos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `retos` (
  `idReto` int NOT NULL AUTO_INCREMENT,
  `nombreReto` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fechaInicio` date NOT NULL,
  `fechaFin` date NOT NULL,
  `horasReto` int NOT NULL,
  PRIMARY KEY (`idReto`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `retos`
--

LOCK TABLES `retos` WRITE;
/*!40000 ALTER TABLE `retos` DISABLE KEYS */;
INSERT INTO `retos` VALUES (1,'Crear E-Commerce desde cero','2026-01-01','2026-12-31',100);
/*!40000 ALTER TABLE `retos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rgpd_eliminaciones`
--

DROP TABLE IF EXISTS `rgpd_eliminaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rgpd_eliminaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idAdmin` int NOT NULL,
  `entidad` varchar(50) NOT NULL,
  `idRegistro` int NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `datos_backup` longtext NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rgpd_eliminaciones`
--

LOCK TABLES `rgpd_eliminaciones` WRITE;
/*!40000 ALTER TABLE `rgpd_eliminaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `rgpd_eliminaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rgpd_solicitudes`
--

DROP TABLE IF EXISTS `rgpd_solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rgpd_solicitudes` (
  `idSolicitud` int NOT NULL AUTO_INCREMENT,
  `rolSesion` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idUsuario` int NOT NULL,
  `nombreUsuario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `emailUsuario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `motivo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('pendiente','resuelta') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `resueltaPorAdmin` int DEFAULT NULL,
  `fechaSolicitud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fechaResolucion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idSolicitud`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rgpd_solicitudes`
--

LOCK TABLES `rgpd_solicitudes` WRITE;
/*!40000 ALTER TABLE `rgpd_solicitudes` DISABLE KEYS */;
/*!40000 ALTER TABLE `rgpd_solicitudes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `secretarias`
--

DROP TABLE IF EXISTS `secretarias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `secretarias` (
  `idSecretaria` int NOT NULL AUTO_INCREMENT,
  `nombreSecretaria` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `emailSecretaria` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activoSecretaria` tinyint(1) NOT NULL DEFAULT '1',
  `token_fcm` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `must_change_password` tinyint(1) NOT NULL DEFAULT '1',
  `pwd_changed_at` datetime DEFAULT NULL,
  `fechaAltaSecretaria` datetime DEFAULT CURRENT_TIMESTAMP,
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `mfa_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mfa_backup_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idSecretaria`),
  UNIQUE KEY `uq_email_sec` (`emailSecretaria`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `secretarias`
--

LOCK TABLES `secretarias` WRITE;
/*!40000 ALTER TABLE `secretarias` DISABLE KEYS */;
INSERT INTO `secretarias` VALUES (1,'Laura Gómez','laura.gomez@aulapro.com','$2y$12$4H2Qo1/AFoW4f1oMOVBgauHxMMKa2dWes6FoLoKsWlSPSIwO.jNIC',1,NULL,0,NULL,'2026-07-27 17:36:16',0,NULL,NULL);
/*!40000 ALTER TABLE `secretarias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tfg_config`
--

DROP TABLE IF EXISTS `tfg_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tfg_config` (
  `idConfigTFG` int NOT NULL AUTO_INCREMENT,
  `idConfig` int NOT NULL,
  `habilitado` tinyint(1) NOT NULL DEFAULT '1',
  `requiereComite` tinyint(1) NOT NULL DEFAULT '0',
  `requiereDefensa` tinyint(1) NOT NULL DEFAULT '0',
  `notaMinima` decimal(4,2) NOT NULL DEFAULT '5.00',
  `pesoEnMedia` decimal(6,2) NOT NULL DEFAULT '1.00',
  `permiteRecuperacion` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idConfigTFG`),
  UNIQUE KEY `uk_tc_config` (`idConfig`),
  CONSTRAINT `tfg_config_ibfk_1` FOREIGN KEY (`idConfig`) REFERENCES `academic_config` (`idConfig`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tfg_config`
--

LOCK TABLES `tfg_config` WRITE;
/*!40000 ALTER TABLE `tfg_config` DISABLE KEYS */;
INSERT INTO `tfg_config` VALUES (1,1,1,0,0,5.00,1.00,1);
/*!40000 ALTER TABLE `tfg_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tours_completados`
--

DROP TABLE IF EXISTS `tours_completados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tours_completados` (
  `idTourCompletado` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `tipoUsuario` enum('admin','profesor','secretaria','estudiante','tutor') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tour_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `completado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idTourCompletado`),
  UNIQUE KEY `uniq_usuario_tour` (`idUsuario`,`tipoUsuario`,`tour_key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tours_completados`
--

LOCK TABLES `tours_completados` WRITE;
/*!40000 ALTER TABLE `tours_completados` DISABLE KEYS */;
INSERT INTO `tours_completados` VALUES (1,1,'admin','primeros_pasos_v1','2026-07-28 14:50:26');
/*!40000 ALTER TABLE `tours_completados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tutores`
--

DROP TABLE IF EXISTS `tutores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutores` (
  `idTutor` int NOT NULL AUTO_INCREMENT,
  `nombreTutor` varchar(150) NOT NULL,
  `emailTutor` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefonoTutor` varchar(20) DEFAULT NULL,
  `dniTutor` varchar(20) DEFAULT NULL,
  `fcm_token` text,
  `must_change_password` tinyint(1) NOT NULL DEFAULT '1',
  `pwd_changed_at` datetime DEFAULT NULL,
  `idEstudiante` int DEFAULT NULL,
  `fechaAltaTutor` datetime DEFAULT CURRENT_TIMESTAMP,
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `mfa_secret` text,
  `mfa_backup_codes` text,
  PRIMARY KEY (`idTutor`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tutores`
--

LOCK TABLES `tutores` WRITE;
/*!40000 ALTER TABLE `tutores` DISABLE KEYS */;
INSERT INTO `tutores` VALUES (1,'Pedro Silva','pedro.silva@aulapro.com','$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu','655111222','A1234567B',NULL,0,NULL,1,'2026-07-27 17:36:17',0,NULL,NULL),(2,'Marta Ortiz','marta.ortiz@aulapro.com','$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu','655222333','B2345678C',NULL,0,NULL,2,'2026-07-27 17:36:17',0,NULL,NULL),(3,'Carmen Pastor','carmen.pastor@aulapro.com','$2y$10$PugX2tbyWKGALOu735cnru2cB.jFIbHL3diNQzTSGLvZAmkcDVblu','655333444','C3456789D',NULL,0,NULL,3,'2026-07-27 17:36:17',0,NULL,NULL);
/*!40000 ALTER TABLE `tutores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `verificaciones_log`
--

DROP TABLE IF EXISTS `verificaciones_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `verificaciones_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `serial_buscado` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `resultado` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_verif_ip_fecha` (`ip`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `verificaciones_log`
--

LOCK TABLES `verificaciones_log` WRITE;
/*!40000 ALTER TABLE `verificaciones_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `verificaciones_log` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-30  3:26:16
