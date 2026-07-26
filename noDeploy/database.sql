-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: yassjjzw_pfc
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
  `tipoEducacion` enum('grado_medio','grado_superior','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'otro',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `anuncios`
--

LOCK TABLES `anuncios` WRITE;
/*!40000 ALTER TABLE `anuncios` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_tokens`
--

LOCK TABLES `api_tokens` WRITE;
/*!40000 ALTER TABLE `api_tokens` DISABLE KEYS */;
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
  `fecha` date NOT NULL,
  `estado` enum('presente','ausente','retraso','justificado') NOT NULL DEFAULT 'presente',
  `observacion` varchar(255) DEFAULT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAsistencia`),
  UNIQUE KEY `idx_asistencia_unica` (`idEstudiante`,`idModulo`,`fecha`),
  KEY `idModulo` (`idModulo`),
  KEY `idProfesor` (`idProfesor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencias`
--

LOCK TABLES `asistencias` WRITE;
/*!40000 ALTER TABLE `asistencias` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_archivo_accesos`
--

LOCK TABLES `aula_archivo_accesos` WRITE;
/*!40000 ALTER TABLE `aula_archivo_accesos` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_archivos`
--

LOCK TABLES `aula_archivos` WRITE;
/*!40000 ALTER TABLE `aula_archivos` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_carpetas`
--

LOCK TABLES `aula_carpetas` WRITE;
/*!40000 ALTER TABLE `aula_carpetas` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_entregas`
--

LOCK TABLES `aula_entregas` WRITE;
/*!40000 ALTER TABLE `aula_entregas` DISABLE KEYS */;
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
  `tipo` enum('archivo_subido','entrega_enviada','entrega_corregida','tarea_nueva','sesion_nueva','correccion','comentario') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `leida` tinyint(1) NOT NULL DEFAULT '0',
  `idReferencia` int DEFAULT NULL,
  `tipoReferencia` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idNotificacion`),
  KEY `idx_aula_notif_usr` (`idUsuario`,`tipoUsuario`,`leida`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_notificaciones`
--

LOCK TABLES `aula_notificaciones` WRITE;
/*!40000 ALTER TABLE `aula_notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `aula_notificaciones` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_sesiones_vivas`
--

LOCK TABLES `aula_sesiones_vivas` WRITE;
/*!40000 ALTER TABLE `aula_sesiones_vivas` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aula_tareas`
--

LOCK TABLES `aula_tareas` WRITE;
/*!40000 ALTER TABLE `aula_tareas` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aulas`
--

LOCK TABLES `aulas` WRITE;
/*!40000 ALTER TABLE `aulas` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calificaciones_ce`
--

LOCK TABLES `calificaciones_ce` WRITE;
/*!40000 ALTER TABLE `calificaciones_ce` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calificaciones_modulos`
--

LOCK TABLES `calificaciones_modulos` WRITE;
/*!40000 ALTER TABLE `calificaciones_modulos` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_gasto`
--

LOCK TABLES `categorias_gasto` WRITE;
/*!40000 ALTER TABLE `categorias_gasto` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_conversaciones`
--

LOCK TABLES `chat_conversaciones` WRITE;
/*!40000 ALTER TABLE `chat_conversaciones` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_mensajes`
--

LOCK TABLES `chat_mensajes` WRITE;
/*!40000 ALTER TABLE `chat_mensajes` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ciclos`
--

LOCK TABLES `ciclos` WRITE;
/*!40000 ALTER TABLE `ciclos` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `criterios_evaluacion`
--

LOCK TABLES `criterios_evaluacion` WRITE;
/*!40000 ALTER TABLE `criterios_evaluacion` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cursos_academicos`
--

LOCK TABLES `cursos_academicos` WRITE;
/*!40000 ALTER TABLE `cursos_academicos` DISABLE KEYS */;
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
  `mfa_secret` text COLLATE utf8mb4_unicode_ci,
  `mfa_backup_codes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idDirector`),
  UNIQUE KEY `uk_email_dir` (`emailDirector`),
  UNIQUE KEY `uk_dni_dir` (`dniDirector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `directores`
--

LOCK TABLES `directores` WRITE;
/*!40000 ALTER TABLE `directores` DISABLE KEYS */;
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
  PRIMARY KEY (`idDispositivo`),
  UNIQUE KEY `uk_serie` (`numeroSerie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dispositivos`
--

LOCK TABLES `dispositivos` WRITE;
/*!40000 ALTER TABLE `dispositivos` DISABLE KEYS */;
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
  `mfa_secret` text COLLATE utf8mb4_unicode_ci,
  `mfa_backup_codes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idEstudiante`),
  UNIQUE KEY `uk_email_est` (`emailEstudiante`),
  UNIQUE KEY `uk_dni_est` (`dniEstudiante`),
  KEY `idx_est_ciclo` (`idCiclo`),
  KEY `idx_est_curso` (`idCurso`),
  CONSTRAINT `fk_estudiantes_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudiantes`
--

LOCK TABLES `estudiantes` WRITE;
/*!40000 ALTER TABLE `estudiantes` DISABLE KEYS */;
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
  PRIMARY KEY (`idEvento`),
  KEY `idx_evento_fecha` (`fechaEvento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventos`
--

LOCK TABLES `eventos` WRITE;
/*!40000 ALTER TABLE `eventos` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fct`
--

LOCK TABLES `fct` WRITE;
/*!40000 ALTER TABLE `fct` DISABLE KEYS */;
/*!40000 ALTER TABLE `fct` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fp_empresas`
--

LOCK TABLES `fp_empresas` WRITE;
/*!40000 ALTER TABLE `fp_empresas` DISABLE KEYS */;
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
  PRIMARY KEY (`idGasto`),
  KEY `idx_gasto_categoria` (`idCategoria`),
  KEY `idx_gasto_ciclo` (`idCiclo`),
  KEY `idx_gasto_fecha` (`fecha`),
  CONSTRAINT `fk_gasto_cat` FOREIGN KEY (`idCategoria`) REFERENCES `categorias_gasto` (`idCategoria`) ON DELETE RESTRICT,
  CONSTRAINT `fk_gasto_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gastos`
--

LOCK TABLES `gastos` WRITE;
/*!40000 ALTER TABLE `gastos` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_secretarias`
--

LOCK TABLES `historial_secretarias` WRITE;
/*!40000 ALTER TABLE `historial_secretarias` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horario_franjas`
--

LOCK TABLES `horario_franjas` WRITE;
/*!40000 ALTER TABLE `horario_franjas` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
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
  `motivo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `archivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estadoOriginal` enum('ausente','retraso') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ausente',
  `estado` enum('pendiente','aprobada','rechazada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `idProfesorResuelve` int DEFAULT NULL,
  `motivoRechazo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaSolicitud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fechaResolucion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idJustificacion`),
  KEY `idx_asistencia` (`idAsistencia`),
  KEY `idx_estudiante` (`idEstudiante`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `justificaciones_falta`
--

LOCK TABLES `justificaciones_falta` WRITE;
/*!40000 ALTER TABLE `justificaciones_falta` DISABLE KEYS */;
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
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `etiqueta` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `resumen` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `descripcion` mediumtext COLLATE utf8mb4_unicode_ci,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `precio` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `duracion` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `modalidad` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `publicado` tinyint(1) NOT NULL DEFAULT '0',
  `destacado` tinyint(1) NOT NULL DEFAULT '0',
  `orden` int NOT NULL DEFAULT '0',
  `creadoEn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizadoEn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idLandingCiclo`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_publicado` (`publicado`,`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_ciclos`
--

LOCK TABLES `landing_ciclos` WRITE;
/*!40000 ALTER TABLE `landing_ciclos` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_acciones`
--

LOCK TABLES `log_acciones` WRITE;
/*!40000 ALTER TABLE `log_acciones` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulos`
--

LOCK TABLES `modulos` WRITE;
/*!40000 ALTER TABLE `modulos` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `niveles`
--

LOCK TABLES `niveles` WRITE;
/*!40000 ALTER TABLE `niveles` DISABLE KEYS */;
/*!40000 ALTER TABLE `niveles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificaciones` (
  `idNotificacion` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `tipoUsuario` enum('admin','profesor','secretaria','estudiante','tutor') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `fechaCreacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idNotificacion`),
  KEY `idx_usuario_no_leidas` (`idUsuario`,`tipoUsuario`,`leido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
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
  `monto` decimal(10,2) NOT NULL,
  `fechaPago` date NOT NULL,
  `fechaProximoPago` date DEFAULT NULL,
  `tipoPago` enum('mensual','trimestral','semestral','unico') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comprobante` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prorrogaHasta` date DEFAULT NULL,
  `estadoComprobante` enum('ninguno','verificando','aprobado','rechazado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ninguno',
  PRIMARY KEY (`idPago`),
  KEY `idx_pago_est` (`idEstudiante`),
  CONSTRAINT `fk_pag_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_matriculas`
--

LOCK TABLES `pre_matriculas` WRITE;
/*!40000 ALTER TABLE `pre_matriculas` DISABLE KEYS */;
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
  PRIMARY KEY (`idPrestamo`),
  KEY `idx_pres_est` (`idEstudiante`),
  KEY `idx_pres_serie` (`numeroSerie`),
  CONSTRAINT `fk_pres_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prestamos`
--

LOCK TABLES `prestamos` WRITE;
/*!40000 ALTER TABLE `prestamos` DISABLE KEYS */;
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
  `mfa_secret` text COLLATE utf8mb4_unicode_ci,
  `mfa_backup_codes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idProfesor`),
  UNIQUE KEY `uk_email_prof` (`emailProfesor`),
  KEY `idx_prof_dni` (`dniProfesor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profesores`
--

LOCK TABLES `profesores` WRITE;
/*!40000 ALTER TABLE `profesores` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rate_limits`
--

LOCK TABLES `rate_limits` WRITE;
/*!40000 ALTER TABLE `rate_limits` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reclamaciones`
--

LOCK TABLES `reclamaciones` WRITE;
/*!40000 ALTER TABLE `reclamaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `reclamaciones` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resultados_aprendizaje`
--

LOCK TABLES `resultados_aprendizaje` WRITE;
/*!40000 ALTER TABLE `resultados_aprendizaje` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `retos`
--

LOCK TABLES `retos` WRITE;
/*!40000 ALTER TABLE `retos` DISABLE KEYS */;
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
  `rolSesion` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idUsuario` int NOT NULL,
  `nombreUsuario` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `emailUsuario` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `motivo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('pendiente','resuelta') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
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
  `mfa_secret` text COLLATE utf8mb4_unicode_ci,
  `mfa_backup_codes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idSecretaria`),
  UNIQUE KEY `uq_email_sec` (`emailSecretaria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `secretarias`
--

LOCK TABLES `secretarias` WRITE;
/*!40000 ALTER TABLE `secretarias` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tutores`
--

LOCK TABLES `tutores` WRITE;
/*!40000 ALTER TABLE `tutores` DISABLE KEYS */;
/*!40000 ALTER TABLE `tutores` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tours_completados`
--

LOCK TABLES `tours_completados` WRITE;
/*!40000 ALTER TABLE `tours_completados` DISABLE KEYS */;
/*!40000 ALTER TABLE `tours_completados` ENABLE KEYS */;
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

--
-- Dumping events for database 'yassjjzw_pfc'
--

--
-- Dumping routines for database 'yassjjzw_pfc'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-21 18:23:54
