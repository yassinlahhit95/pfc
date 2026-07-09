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
INSERT INTO `account_lockout` VALUES ('carlos.sanchez@aulapro.com',1,1782295716,NULL),('juan.garcia@aulapro.com',3,1782295616,NULL),('pablo.martinez@aulapro.com',1,1782297121,NULL),('secretaria@aulapro.com',1,1782296130,NULL);
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
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fechaAnuncio` datetime DEFAULT CURRENT_TIMESTAMP,
  `fechaExpiracion` date NOT NULL,
  `dirigidoA` enum('todos','estudiantes','profesores') COLLATE utf8mb4_unicode_ci DEFAULT 'todos',
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
  KEY `idEstudiante` (`idEstudiante`),
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
-- Table structure for table `auditoria`
--

DROP TABLE IF EXISTS `auditoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auditoria` (
  `idAuditoria` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `tipoUsuario` enum('admin','profesor','estudiante') COLLATE utf8mb4_unicode_ci NOT NULL,
  `accion` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabla` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detalles` text COLLATE utf8mb4_unicode_ci,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAuditoria`),
  KEY `idx_auditoria_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditoria`
--

LOCK TABLES `auditoria` WRITE;
/*!40000 ALTER TABLE `auditoria` DISABLE KEYS */;
/*!40000 ALTER TABLE `auditoria` ENABLE KEYS */;
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
INSERT INTO `aula_almacenamiento_ciclo` VALUES (1,5368709120),(2,5368709120),(3,5368709120);
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
  `tipoUsuario` enum('estudiante','profesor') COLLATE utf8mb4_unicode_ci NOT NULL,
  `accion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idModulo` int DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `userAgent` text COLLATE utf8mb4_unicode_ci,
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
  `tipo` enum('vista','descarga') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vista',
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
  `nombreArchivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombreOriginal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `nombreArchivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombreOriginal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tamanio` int DEFAULT '0',
  `descripcion` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idModulo` int NOT NULL,
  `idProfesor` int NOT NULL,
  `idPadre` int DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#0ea5e9',
  `icono` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fa-folder',
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
  `tipoUsuario` enum('profesor','estudiante') COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `archivoCorreccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `archivoEntrega` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `respuesta` text COLLATE utf8mb4_unicode_ci,
  `version` int NOT NULL DEFAULT '1',
  `fechaEntrega` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nota` decimal(4,2) DEFAULT NULL,
  `estado` enum('enviada','corregida') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enviada',
  `comentarioCalificacion` text COLLATE utf8mb4_unicode_ci,
  `archivoCorreccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'todo',
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
  `tipoUsuario` enum('profesor','estudiante','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('archivo_subido','entrega_enviada','correccion','comentario') COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci,
  `leida` tinyint(1) NOT NULL DEFAULT '0',
  `idReferencia` int DEFAULT NULL,
  `tipoReferencia` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fechaSesion` date NOT NULL,
  `horaSesion` time NOT NULL,
  `enlaceReunion` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plataforma` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('programada','en_vivo','finalizada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'programada',
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
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `idModulo` int NOT NULL,
  `idProfesor` int NOT NULL,
  `archivoAdjunto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `archivoEntrega` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `respuesta` text COLLATE utf8mb4_unicode_ci,
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
  `codigoAula` varchar(10) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (concat(`planta`,lpad(`numero`,2,_utf8mb4'0'))) STORED,
  `nombreAula` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipoAula` enum('teoria','laboratorio','taller','otro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'teoria',
  `capacidad` int DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idAula`),
  UNIQUE KEY `uk_aula_planta_numero` (`planta`,`numero`),
  UNIQUE KEY `uk_aula_codigo` (`codigoAula`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aulas`
--

LOCK TABLES `aulas` WRITE;
/*!40000 ALTER TABLE `aulas` DISABLE KEYS */;
INSERT INTO `aulas` (`idAula`, `planta`, `numero`, `nombreAula`, `tipoAula`, `capacidad`, `activa`) VALUES (1,1,1,'Aula 101','teoria',30,1),(2,1,2,'Aula 102','teoria',30,1),(3,2,1,'Laboratorio 201','laboratorio',24,1),(4,2,2,'Laboratorio 202','laboratorio',24,1),(5,0,1,'Aula 001 (Taller)','taller',20,1);
/*!40000 ALTER TABLE `aulas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boletines_log`
--

DROP TABLE IF EXISTS `boletines_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boletines_log` (
  `serial` varchar(25) NOT NULL,
  `idEstudiante` int NOT NULL,
  `idCiclo` int NOT NULL,
  `nombreEstudiante` varchar(255) NOT NULL,
  `nombreCiclo` varchar(255) NOT NULL,
  `cursoEscolar` varchar(20) NOT NULL,
  `fechaGeneracion` datetime DEFAULT CURRENT_TIMESTAMP,
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
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `estado_1ev` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_1final` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_2ev` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_2final` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idCalificacion`),
  UNIQUE KEY `uk_est_mod` (`idEstudiante`,`idModulo`),
  KEY `idx_cm_est` (`idEstudiante`),
  KEY `idx_cm_mod` (`idModulo`),
  CONSTRAINT `fk_cm_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_cm_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calificaciones_modulos`
--

LOCK TABLES `calificaciones_modulos` WRITE;
/*!40000 ALTER TABLE `calificaciones_modulos` DISABLE KEYS */;
INSERT INTO `calificaciones_modulos` VALUES (1,1,3,NULL,NULL,NULL,NULL,'','CO','CO','CO','CO'),(2,2,3,7.00,7.00,7.00,7.00,'',NULL,NULL,NULL,NULL),(3,3,3,NULL,9.00,8.00,7.00,'','NP',NULL,NULL,NULL),(4,1,5,6.00,6.00,6.00,6.00,'',NULL,NULL,NULL,NULL),(5,2,5,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL),(6,3,5,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL),(7,1,1,7.00,7.00,8.00,9.00,'',NULL,NULL,NULL,NULL),(8,2,1,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL),(9,3,1,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL),(10,1,2,4.00,5.00,8.00,7.00,'',NULL,NULL,NULL,NULL),(11,2,2,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL),(12,3,2,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL),(13,1,4,6.00,8.00,4.00,5.00,'',NULL,NULL,NULL,NULL),(14,2,4,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL),(15,3,4,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `calificaciones_modulos` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calificaciones_retos`
--

LOCK TABLES `calificaciones_retos` WRITE;
/*!40000 ALTER TABLE `calificaciones_retos` DISABLE KEYS */;
INSERT INTO `calificaciones_retos` VALUES (1,1,1,7.50);
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
  `nota` decimal(4,2) NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idCalificacion`),
  UNIQUE KEY `uk_est_tfg` (`idEstudiante`),
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
-- Table structure for table `carpetas_ejercicios`
--

DROP TABLE IF EXISTS `carpetas_ejercicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carpetas_ejercicios` (
  `idCarpeta` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#0ea5e9',
  `icono` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fa-folder',
  `idProfesor` int NOT NULL,
  `idCiclo` int NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idCarpeta`),
  KEY `fk_carp_prof_ej` (`idProfesor`),
  KEY `fk_carp_ciclo_ej` (`idCiclo`),
  CONSTRAINT `fk_carp_ciclo_ej` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `fk_carp_prof_ej` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carpetas_ejercicios`
--

LOCK TABLES `carpetas_ejercicios` WRITE;
/*!40000 ALTER TABLE `carpetas_ejercicios` DISABLE KEYS */;
/*!40000 ALTER TABLE `carpetas_ejercicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias_gasto`
--

DROP TABLE IF EXISTS `categorias_gasto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias_gasto` (
  `idCategoria` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `presupuestoAnual` decimal(10,2) DEFAULT '0.00',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#6c757d',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idCategoria`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_gasto`
--

LOCK TABLES `categorias_gasto` WRITE;
/*!40000 ALTER TABLE `categorias_gasto` DISABLE KEYS */;
INSERT INTO `categorias_gasto` VALUES (1,'Material escolar',2000.00,'#0d6efd',1),(2,'Mantenimiento',3000.00,'#198754',1),(3,'Equipamiento TIC',5000.00,'#0dcaf0',1),(4,'Actividades',1500.00,'#ffc107',1),(5,'Administraci¢n',1000.00,'#6c757d',1);
/*!40000 ALTER TABLE `categorias_gasto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_conversaciones`
--

DROP TABLE IF EXISTS `chat_conversaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_conversaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_a_rol` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_a_id` int NOT NULL,
  `user_b_rol` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_b_id` int NOT NULL,
  `last_message_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_conv_pair` (`user_a_rol`,`user_a_id`,`user_b_rol`,`user_b_id`),
  KEY `idx_conv_a` (`user_a_rol`,`user_a_id`),
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
  `emisor_rol` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emisor_id` int NOT NULL,
  `contenido` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_msg_conv` (`conversacion_id`),
  KEY `idx_msg_fecha` (`fecha`),
  KEY `idx_msg_leido` (`leido`),
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
INSERT INTO `ciclo_profesor` VALUES (1,1),(2,2);
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
  `nombreCiclo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abreviaturaCiclo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `ciclos` VALUES (1,'Desarrollo de Aplicaciones Web','DAW',1200.00,2,1,NULL),(2,'Desarrollo de Aplicaciones Multiplataforma','DAM',1200.00,2,1,NULL),(3,'Sistemas Inform√°ticos en Red','ASIR',1200.00,2,1,NULL);
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
  `destinatario_email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destinatario_nombre` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asunto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `html_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('pendiente','enviado','fallido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `intentos` tinyint NOT NULL DEFAULT '0',
  `ultimo_error` text COLLATE utf8mb4_unicode_ci,
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
  `nombreCentro` varchar(200) DEFAULT 'Centro de Formaci¢n Profesional',
  `codigoCentro` varchar(50) DEFAULT '',
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
  PRIMARY KEY (`idConfig`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion_centro`
--

LOCK TABLES `configuracion_centro` WRITE;
/*!40000 ALTER TABLE `configuracion_centro` DISABLE KEYS */;
INSERT INTO `configuracion_centro` VALUES (1,'Centro de Formaci¢n Profesional','','','','','','','2024-2025','logoCentro_1780959435.jpeg','logoGobierno1_1780959435.png','logoGobierno2_1780959435.png','','',0,1,1,1,'active',NULL,0,NULL,'info',NULL,NULL,NULL,1,1,1,1,1,1,1,1,1,0,0,1,0);
/*!40000 ALTER TABLE `configuracion_centro` ENABLE KEYS */;
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
-- Table structure for table `directores`
--

DROP TABLE IF EXISTS `directores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `directores` (
  `idDirector` int NOT NULL AUTO_INCREMENT,
  `nombreDirector` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emailDirector` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',
  `telefonoDirector` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dniDirector` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fechaNacimientoDirector` date DEFAULT NULL,
  `fechaAltaDirector` date DEFAULT NULL,
  `direccionDirector` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudadDirector` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigoPostalDirector` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacionesDirector` text COLLATE utf8mb4_unicode_ci,
  `fcm_token` text COLLATE utf8mb4_unicode_ci,
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
INSERT INTO `directores` VALUES (1,'Administrador','admin@aulapro.com','$2y$12$aAidhwyJ..kyv17j5bHode8qVyAN4H0pwIXu52zUQEBfM2MWIYSBy',NULL,'00000000T',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
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
  `nombreDispositivo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numeroSerie` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estadoDispositivo` enum('disponible','prestado') COLLATE utf8mb4_unicode_ci DEFAULT 'disponible',
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
-- Table structure for table `ejercicios`
--

DROP TABLE IF EXISTS `ejercicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ejercicios` (
  `idEjercicio` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `idCarpeta` int DEFAULT NULL,
  `idProfesor` int NOT NULL,
  `idCiclo` int NOT NULL,
  `fechaLimite` datetime DEFAULT NULL,
  `archivoAdjunto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publicado` tinyint(1) NOT NULL DEFAULT '1',
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idEjercicio`),
  KEY `fk_ej_carp_ej` (`idCarpeta`),
  KEY `fk_ej_prof_ej` (`idProfesor`),
  KEY `fk_ej_ciclo_ej` (`idCiclo`),
  CONSTRAINT `fk_ej_carp_ej` FOREIGN KEY (`idCarpeta`) REFERENCES `carpetas_ejercicios` (`idCarpeta`) ON DELETE SET NULL,
  CONSTRAINT `fk_ej_ciclo_ej` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `fk_ej_prof_ej` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ejercicios`
--

LOCK TABLES `ejercicios` WRITE;
/*!40000 ALTER TABLE `ejercicios` DISABLE KEYS */;
/*!40000 ALTER TABLE `ejercicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entregas_ejercicios`
--

DROP TABLE IF EXISTS `entregas_ejercicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entregas_ejercicios` (
  `idEntrega` int NOT NULL AUTO_INCREMENT,
  `idEjercicio` int NOT NULL,
  `idEstudiante` int NOT NULL,
  `respuesta` text COLLATE utf8mb4_unicode_ci,
  `archivoEntrega` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaEntrega` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nota` decimal(4,2) DEFAULT NULL,
  `comentarioProfesor` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('entregado','calificado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'entregado',
  PRIMARY KEY (`idEntrega`),
  UNIQUE KEY `uk_entrega_unica_ej` (`idEjercicio`,`idEstudiante`),
  KEY `fk_entr_est_ej` (`idEstudiante`),
  CONSTRAINT `fk_entr_ej_ej` FOREIGN KEY (`idEjercicio`) REFERENCES `ejercicios` (`idEjercicio`) ON DELETE CASCADE,
  CONSTRAINT `fk_entr_est_ej` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entregas_ejercicios`
--

LOCK TABLES `entregas_ejercicios` WRITE;
/*!40000 ALTER TABLE `entregas_ejercicios` DISABLE KEYS */;
/*!40000 ALTER TABLE `entregas_ejercicios` ENABLE KEYS */;
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
  `parentesco` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Tutor',
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
INSERT INTO `estudiante_tutor` VALUES (1,1,'Madre'),(2,2,'Padre'),(3,3,'Madre'),(4,3,'Madre');
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
  `nombreEstudiante` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emailEstudiante` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',
  `telefonoEstudiante` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dniEstudiante` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fechaNacimientoEstudiante` date DEFAULT NULL,
  `fechaAltaEstudiante` date DEFAULT NULL,
  `direccionEstudiante` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudadEstudiante` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigoPostalEstudiante` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacionesEstudiante` text COLLATE utf8mb4_unicode_ci,
  `idCiclo` int DEFAULT NULL,
  `curso` enum('Grado Medio','Grado Superior') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archivoTFG` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tituloTFG` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaSubidaTFG` datetime DEFAULT NULL,
  `fcm_token` text COLLATE utf8mb4_unicode_ci,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_eliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`idEstudiante`),
  UNIQUE KEY `uk_email_est` (`emailEstudiante`),
  UNIQUE KEY `uk_dni_est` (`dniEstudiante`),
  KEY `idx_est_ciclo` (`idCiclo`),
  CONSTRAINT `fk_estudiantes_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudiantes`
--

LOCK TABLES `estudiantes` WRITE;
/*!40000 ALTER TABLE `estudiantes` DISABLE KEYS */;
INSERT INTO `estudiantes` VALUES (1,'Carlos S√°nchez L√≥pez','carlos.sanchez@aulpro.com','$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',NULL,'11111111C','2004-06-10','2023-09-01',NULL,NULL,NULL,NULL,1,'Grado Superior',NULL,NULL,NULL,NULL,0,NULL),(2,'Laura Fern√°ndez Garc√≠a','laura.fernandez@aulpro.com','$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',NULL,'22222222D','2004-08-22','2023-09-01',NULL,NULL,NULL,NULL,1,'Grado Superior',NULL,NULL,NULL,NULL,0,NULL),(3,'Pablo Mart√≠nez Ruiz','pablo.martinez@aulpro.com','$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',NULL,'33333333E','2005-01-18','2023-09-01',NULL,NULL,NULL,NULL,1,'Grado Superior',NULL,NULL,NULL,NULL,0,NULL),(4,'Andrea Jim√©nez Torres','andrea.jimenez@aulpro.com','$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',NULL,'44444444F','2004-07-14','2023-09-01',NULL,NULL,NULL,NULL,2,'Grado Superior',NULL,NULL,NULL,NULL,0,NULL),(5,'David Moreno P√©rez','david.moreno@aulpro.com','$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',NULL,'55555555G','2005-02-28','2023-09-01',NULL,NULL,NULL,NULL,2,'Grado Superior',NULL,NULL,NULL,NULL,0,NULL),(6,'Sof√≠a Gonz√°lez Blanco','sofia.gonzalez@aulpro.com','$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',NULL,'66666666H','2004-11-05','2023-09-01',NULL,NULL,NULL,NULL,2,'Grado Superior',NULL,NULL,NULL,NULL,0,NULL),(7,'Alejandro Ram√≠rez Santos','alejandro.ramirez@aulpro.com','$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',NULL,'77777777I','2004-09-12','2023-09-01',NULL,NULL,NULL,NULL,3,'Grado Superior',NULL,NULL,NULL,NULL,0,NULL),(8,'Cristina D√≠az Mu√±oz','cristina.diaz@aulpro.com','$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',NULL,'88888888J','2005-03-30','2023-09-01',NULL,NULL,NULL,NULL,3,'Grado Superior',NULL,NULL,NULL,NULL,0,NULL),(9,'Roberto Vega Herrera','roberto.vega@aulpro.com','$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',NULL,'99999999K','2004-12-08','2023-09-01',NULL,NULL,NULL,NULL,3,'Grado Superior',NULL,NULL,NULL,NULL,0,NULL);
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
  `tituloEvento` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcionEvento` text COLLATE utf8mb4_unicode_ci,
  `fechaEvento` date NOT NULL,
  `horaEvento` time DEFAULT NULL,
  `ubicacionEvento` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `empresa` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tutorEmpresa` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emailTutorEmpresa` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefonoEmpresa` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudadEmpresa` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaInicio` date DEFAULT NULL,
  `fechaFin` date DEFAULT NULL,
  `horasTotales` int DEFAULT NULL,
  `horasRealizadas` int DEFAULT NULL,
  `nota` decimal(4,2) DEFAULT NULL,
  `apto` tinyint(1) DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `idProfesorTutor` int DEFAULT NULL,
  `fase` int NOT NULL DEFAULT '1',
  `creado_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idFCT`),
  UNIQUE KEY `uq_fct_est_ciclo_fase` (`idEstudiante`,`idCiclo`,`fase`),
  KEY `idx_fct_ciclo` (`idCiclo`),
  KEY `idx_fct_profesor` (`idProfesorTutor`),
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
-- Table structure for table `fp_dual_asignaciones`
--

DROP TABLE IF EXISTS `fp_dual_asignaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fp_dual_asignaciones` (
  `idAsignacion` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int NOT NULL,
  `idEmpresa` int NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `horas_asignadas` int DEFAULT '0',
  `estado` enum('Pendiente','En curso','Finalizado','Cancelado') DEFAULT 'Pendiente',
  PRIMARY KEY (`idAsignacion`),
  KEY `idEstudiante` (`idEstudiante`),
  KEY `idEmpresa` (`idEmpresa`),
  CONSTRAINT `fp_dual_asignaciones_ibfk_1` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fp_dual_asignaciones_ibfk_2` FOREIGN KEY (`idEmpresa`) REFERENCES `fp_empresas` (`idEmpresa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fp_dual_asignaciones`
--

LOCK TABLES `fp_dual_asignaciones` WRITE;
/*!40000 ALTER TABLE `fp_dual_asignaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `fp_dual_asignaciones` ENABLE KEYS */;
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
  `concepto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `tipoJustificante` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numeroReferencia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archivoJustificante` text COLLATE utf8mb4_unicode_ci,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
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
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horario_franjas`
--

LOCK TABLES `horario_franjas` WRITE;
/*!40000 ALTER TABLE `horario_franjas` DISABLE KEYS */;
INSERT INTO `horario_franjas` VALUES (2,1,'09:00:00','10:00:00',0),(3,1,'10:00:00','11:00:00',0),(51,2,'08:00:00','09:00:00',0),(52,2,'09:00:00','10:00:00',0),(53,2,'10:00:00','11:00:00',0),(54,2,'11:00:00','11:30:00',1),(55,2,'11:30:00','12:30:00',0),(56,2,'12:30:00','13:30:00',0),(57,2,'13:30:00','14:30:00',0),(58,3,'08:00:00','09:00:00',0),(59,3,'09:00:00','10:00:00',0),(60,3,'10:00:00','11:00:00',0),(61,3,'11:00:00','11:30:00',1),(63,3,'12:30:00','13:30:00',0),(68,1,'11:00:00','11:30:00',1),(69,1,'11:30:00','12:30:00',0),(70,1,'12:30:00','13:30:00',0),(71,1,'13:30:00','14:30:00',0),(88,1,'14:30:00','15:00:00',1),(96,1,'15:00:00','16:00:00',0),(97,1,'08:00:00','09:00:00',0);
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
  `diaSemana` enum('Lunes','Martes','MiÇrcoles','Jueves','Viernes') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  KEY `indice_horario_ciclo` (`idCiclo`),
  KEY `indice_horario_modulo` (`idModulo`),
  KEY `indice_horario_aula` (`idAula`),
  CONSTRAINT `fk_horario_aula` FOREIGN KEY (`idAula`) REFERENCES `aulas` (`idAula`) ON DELETE SET NULL,
  CONSTRAINT `fk_horario_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `fk_horario_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE SET NULL,
  CONSTRAINT `fk_horario_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
INSERT INTO `horarios` VALUES (2,1,'Lunes','09:00:00','10:00:00',2,1,1,'2026-06-08 11:20:22'),(3,1,'Lunes','10:00:00','11:00:00',3,1,1,'2026-06-08 11:20:22'),(6,1,'MiÇrcoles','09:00:00','10:00:00',3,1,1,'2026-06-08 11:20:22'),(8,1,'Jueves','10:00:00','11:00:00',4,1,3,'2026-06-08 11:20:22');
/*!40000 ALTER TABLE `horarios` ENABLE KEYS */;
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
-- Table structure for table `landing_config`
--

DROP TABLE IF EXISTS `landing_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_config` (
  `idLanding` int NOT NULL DEFAULT '1',
  `plantilla` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ajustes` json DEFAULT NULL,
  `plantilla_pub` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
INSERT INTO `landing_config` VALUES (1,NULL,NULL,'institucional',NULL,'2026-07-05 13:07:55','2026-07-07 02:23:35');
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
  `version` enum('draft','live') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `tipo` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `contenido` json DEFAULT NULL,
  PRIMARY KEY (`idSeccion`),
  KEY `idx_landing_version_orden` (`version`,`orden`)
) ENGINE=InnoDB AUTO_INCREMENT=294 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_secciones`
--

LOCK TABLES `landing_secciones` WRITE;
/*!40000 ALTER TABLE `landing_secciones` DISABLE KEYS */;
INSERT INTO `landing_secciones` VALUES (278,'draft','hero',1,1,'{\"imagen\": \"\", \"titulo\": \"Formaci¢n oficial que abre puertas\", \"eyebrow\": \"Centro Integrado de Formaci¢n Profesional\", \"botonUrl\": \"#oferta_formativa\", \"variante\": \"split\", \"boton2Url\": \"#contacto\", \"subtitulo\": \"Ciclos formativos de grado medio y superior con pr†cticas en empresas colaboradoras y profesorado especialista.\", \"botonTexto\": \"Ver ciclos\", \"boton2Texto\": \"Contacto\"}'),(279,'draft','cifras',2,1,'{\"items\": [{\"numero\": \"95\", \"sufijo\": \"%\", \"etiqueta\": \"Inserci¢n laboral\"}, {\"numero\": \"30\", \"sufijo\": \"+\", \"etiqueta\": \"Empresas colaboradoras\"}, {\"numero\": \"20\", \"sufijo\": \"+\", \"etiqueta\": \"A§os de experiencia\"}, {\"numero\": \"500\", \"sufijo\": \"+\", \"etiqueta\": \"Alumnos titulados\"}]}'),(280,'draft','oferta_formativa',3,1,'{\"titulo\": \"Nuestra oferta formativa\", \"subtitulo\": \"Ciclos formativos oficiales adaptados a las profesiones con m†s demanda.\", \"botonTexto\": \"Solicitar plaza\", \"mostrarPrecio\": \"no\"}'),(281,'draft','porque_elegirnos',4,1,'{\"items\": [{\"icono\": \"fa-briefcase\", \"texto\": \"Convenios con empresas del sector para que hagas pr†cticas reales desde el primer curso.\", \"titulo\": \"Pr†cticas garantizadas\"}, {\"icono\": \"fa-chalkboard-teacher\", \"texto\": \"Docentes con experiencia profesional activa en su especialidad.\", \"titulo\": \"Profesorado experto\"}, {\"icono\": \"fa-award\", \"texto\": \"T°tulos oficiales de Formaci¢n Profesional v†lidos en toda Espa§a y la UE.\", \"titulo\": \"Titulaci¢n oficial\"}], \"titulo\": \"®Por quÇ estudiar con nosotros?\", \"subtitulo\": \"\"}'),(282,'draft','fp_dual',5,1,'{\"items\": [{\"texto\": \"Aprende trabajando en empresas del sector desde el primer a§o.\", \"titulo\": \"Experiencia real\"}, {\"texto\": \"Recibe una compensaci¢n econ¢mica durante tu estancia en la empresa.\", \"titulo\": \"Remuneraci¢n\"}], \"texto\": \"Estudia y trabaja a la vez: la FP Dual combina la formaci¢n en el aula con estancias remuneradas en empresas colaboradoras.\", \"imagen\": \"\", \"titulo\": \"Formaci¢n Profesional Dual\"}'),(283,'draft','instalaciones',6,1,'{\"items\": [], \"titulo\": \"Nuestras instalaciones\", \"subtitulo\": \"Espacios y equipamiento profesional para aprender con las mismas herramientas que usar†s en tu trabajo.\"}'),(284,'draft','prematricula_cta',7,1,'{\"texto\": \"Realiza tu pre-matr°cula online en menos de 10 minutos. Nuestro equipo revisar† tu solicitud y te contactar† con los siguientes pasos.\", \"titulo\": \"Reserva tu plaza para el pr¢ximo curso\", \"notaPlazo\": \"Plazo abierto ? plazas limitadas\", \"botonTexto\": \"Iniciar pre-matr°cula\"}'),(285,'draft','contacto',8,1,'{\"texto\": \"Resolvemos tus dudas sobre ciclos, admisi¢n, becas y convalidaciones.\", \"titulo\": \"®Hablamos?\", \"iframeMapa\": \"<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3037.336113824316!2d-3.705886423377759!3d40.42358895522774!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd42287d55986ec3%3A0x6b8030206122d1!2sPuerta%20del%20Sol!5e0!3m2!1ses!2ses!4v1707920150974!5m2!1ses!2ses\\\" width=\\\"100%\\\" height=\\\"400\\\" style=\\\"border:0; border-radius: 12px;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\"></iframe>\", \"mostrarMapa\": \"si\", \"textoHorario\": \"Lunes a viernes: 9:00 ? 14:00\\nSecretar°a: 9:00 ? 13:00\", \"mostrarFormulario\": \"si\"}'),(286,'live','hero',1,1,'{\"imagen\": \"\", \"titulo\": \"Formaci¢n oficial que abre puertas\", \"eyebrow\": \"Centro Integrado de Formaci¢n Profesional\", \"botonUrl\": \"#oferta_formativa\", \"variante\": \"split\", \"boton2Url\": \"#contacto\", \"subtitulo\": \"Ciclos formativos de grado medio y superior con pr†cticas en empresas colaboradoras y profesorado especialista.\", \"botonTexto\": \"Ver ciclos\", \"boton2Texto\": \"Contacto\"}'),(287,'live','cifras',2,1,'{\"items\": [{\"numero\": \"95\", \"sufijo\": \"%\", \"etiqueta\": \"Inserci¢n laboral\"}, {\"numero\": \"30\", \"sufijo\": \"+\", \"etiqueta\": \"Empresas colaboradoras\"}, {\"numero\": \"20\", \"sufijo\": \"+\", \"etiqueta\": \"A§os de experiencia\"}, {\"numero\": \"500\", \"sufijo\": \"+\", \"etiqueta\": \"Alumnos titulados\"}]}'),(288,'live','oferta_formativa',3,1,'{\"titulo\": \"Nuestra oferta formativa\", \"subtitulo\": \"Ciclos formativos oficiales adaptados a las profesiones con m†s demanda.\", \"botonTexto\": \"Solicitar plaza\", \"mostrarPrecio\": \"no\"}'),(289,'live','porque_elegirnos',4,1,'{\"items\": [{\"icono\": \"fa-briefcase\", \"texto\": \"Convenios con empresas del sector para que hagas pr†cticas reales desde el primer curso.\", \"titulo\": \"Pr†cticas garantizadas\"}, {\"icono\": \"fa-chalkboard-teacher\", \"texto\": \"Docentes con experiencia profesional activa en su especialidad.\", \"titulo\": \"Profesorado experto\"}, {\"icono\": \"fa-award\", \"texto\": \"T°tulos oficiales de Formaci¢n Profesional v†lidos en toda Espa§a y la UE.\", \"titulo\": \"Titulaci¢n oficial\"}], \"titulo\": \"®Por quÇ estudiar con nosotros?\", \"subtitulo\": \"\"}'),(290,'live','fp_dual',5,1,'{\"items\": [{\"texto\": \"Aprende trabajando en empresas del sector desde el primer a§o.\", \"titulo\": \"Experiencia real\"}, {\"texto\": \"Recibe una compensaci¢n econ¢mica durante tu estancia en la empresa.\", \"titulo\": \"Remuneraci¢n\"}], \"texto\": \"Estudia y trabaja a la vez: la FP Dual combina la formaci¢n en el aula con estancias remuneradas en empresas colaboradoras.\", \"imagen\": \"\", \"titulo\": \"Formaci¢n Profesional Dual\"}'),(291,'live','instalaciones',6,1,'{\"items\": [], \"titulo\": \"Nuestras instalaciones\", \"subtitulo\": \"Espacios y equipamiento profesional para aprender con las mismas herramientas que usar†s en tu trabajo.\"}'),(292,'live','prematricula_cta',7,1,'{\"texto\": \"Realiza tu pre-matr°cula online en menos de 10 minutos. Nuestro equipo revisar† tu solicitud y te contactar† con los siguientes pasos.\", \"titulo\": \"Reserva tu plaza para el pr¢ximo curso\", \"notaPlazo\": \"Plazo abierto ? plazas limitadas\", \"botonTexto\": \"Iniciar pre-matr°cula\"}'),(293,'live','contacto',8,1,'{\"texto\": \"Resolvemos tus dudas sobre ciclos, admisi¢n, becas y convalidaciones.\", \"titulo\": \"®Hablamos?\", \"iframeMapa\": \"<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3037.336113824316!2d-3.705886423377759!3d40.42358895522774!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd42287d55986ec3%3A0x6b8030206122d1!2sPuerta%20del%20Sol!5e0!3m2!1ses!2ses!4v1707920150974!5m2!1ses!2ses\\\" width=\\\"100%\\\" height=\\\"400\\\" style=\\\"border:0; border-radius: 12px;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\"></iframe>\", \"mostrarMapa\": \"si\", \"textoHorario\": \"Lunes a viernes: 9:00 ? 14:00\\nSecretar°a: 9:00 ? 13:00\", \"mostrarFormulario\": \"si\"}');
/*!40000 ALTER TABLE `landing_secciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempt_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ip_time` (`ip_address`,`attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_intentos`
--

DROP TABLE IF EXISTS `login_intentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_intentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `intentos` tinyint NOT NULL DEFAULT '0',
  `bloqueado_hasta` datetime DEFAULT NULL,
  `ultimo_intento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ip` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_intentos`
--

LOCK TABLES `login_intentos` WRITE;
/*!40000 ALTER TABLE `login_intentos` DISABLE KEYS */;
INSERT INTO `login_intentos` VALUES (1,'::1',8,NULL,'2026-06-24 12:32:01');
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
INSERT INTO `modulo_profesor` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,2),(7,2),(8,2),(9,2),(10,2);
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
INSERT INTO `modulo_reto` VALUES (1,1),(2,2),(3,3),(4,4),(1,5),(2,5),(3,5),(4,5);
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
  `nombreModulo` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `horasMaximas` int DEFAULT NULL,
  `idCiclo` int NOT NULL,
  `tipoModulo` enum('Espec°fico','Transversal','Proyecto','Empresa') COLLATE utf8mb4_unicode_ci DEFAULT 'Espec°fico',
  `pinAsistencia` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pinAsistenciaExpira` datetime DEFAULT NULL,
  PRIMARY KEY (`idModulo`),
  KEY `idx_modulo_ciclo` (`idCiclo`),
  CONSTRAINT `fk_modulos_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulos`
--

LOCK TABLES `modulos` WRITE;
/*!40000 ALTER TABLE `modulos` DISABLE KEYS */;
INSERT INTO `modulos` VALUES (1,'Lenguajes de Marcas',42,1,'Espec°fico',NULL,NULL),(2,'Programaci√≥n del Lado del Cliente',126,1,'Espec°fico',NULL,NULL),(3,'Bases de Datos',84,1,'Espec°fico',NULL,NULL),(4,'Programaci√≥n del Lado del Servidor',126,1,'Espec°fico',NULL,NULL),(5,'Despliegue de Aplicaciones Web',63,1,'Espec°fico',NULL,NULL),(6,'Lenguajes de Programaci√≥n',105,2,'Espec°fico',NULL,NULL),(7,'Fundamentos de Bases de Datos',84,2,'Espec°fico',NULL,NULL),(8,'Programaci√≥n Multimedia',105,2,'Espec°fico',NULL,NULL),(9,'Acceso a Datos',84,2,'Espec°fico',NULL,NULL),(10,'Interfaces',84,2,'Espec°fico',NULL,NULL),(11,'Planificaci√≥n y Administraci√≥n de Redes',84,3,'Espec°fico',NULL,NULL),(12,'Gesti√≥n e Instalaci√≥n de Sistemas Operativos',105,3,'Espec°fico',NULL,NULL),(13,'Servicios en Red',105,3,'Espec°fico',NULL,NULL),(14,'Sistemas Gestores de Bases de Datos',84,3,'Espec°fico',NULL,NULL),(15,'Seguridad Inform√°tica',105,3,'Espec°fico',NULL,NULL);
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
  `nombreNivel` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`idNivel`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `niveles`
--

LOCK TABLES `niveles` WRITE;
/*!40000 ALTER TABLE `niveles` DISABLE KEYS */;
INSERT INTO `niveles` VALUES (1,'Grado Medio'),(2,'Grado Superior');
/*!40000 ALTER TABLE `niveles` ENABLE KEYS */;
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
  `tipoPago` enum('mensual','trimestral','semestral','unico') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comprobante` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prorrogaHasta` date DEFAULT NULL,
  `estadoComprobante` enum('ninguno','verificando','aprobado','rechazado') COLLATE utf8mb4_unicode_ci DEFAULT 'ninguno',
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
  `token` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_usuario` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `tipoDocumento` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombreArchivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rutaArchivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `dni` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idCiclo` int NOT NULL,
  `curso` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '1ß',
  `nombreTutor` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dniTutor` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emailTutor` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefonoTutor` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parentescoTutor` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('pendiente','revisando','aceptada','rechazada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
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
  `numeroSerie` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fechaPrestamo` date NOT NULL,
  `fechaDevolucion` date DEFAULT NULL,
  `estadoPrestamo` enum('en curso','devuelto') COLLATE utf8mb4_unicode_ci DEFAULT 'en curso',
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
  `nombreProfesor` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emailProfesor` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu',
  `telefonoProfesor` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dniProfesor` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaNacimientoProfesor` date DEFAULT NULL,
  `fechaAltaProfesor` date DEFAULT NULL,
  `direccionProfesor` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudadProfesor` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigoPostalProfesor` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacionesProfesor` text COLLATE utf8mb4_unicode_ci,
  `fcm_token` text COLLATE utf8mb4_unicode_ci,
  `esTutor` tinyint(1) DEFAULT '0',
  `idCicloTutor` int DEFAULT NULL,
  PRIMARY KEY (`idProfesor`),
  UNIQUE KEY `uk_email_prof` (`emailProfesor`),
  KEY `idx_prof_dni` (`dniProfesor`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profesores`
--

LOCK TABLES `profesores` WRITE;
/*!40000 ALTER TABLE `profesores` DISABLE KEYS */;
INSERT INTO `profesores` VALUES (1,'Juan Garc√≠a Mart√≠nez','juan.garcia@aulpro.com','$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu','612345678','12345678A','1980-05-15','2023-09-01','Calle Principal 123','Madrid','28001',NULL,NULL,0,NULL),(2,'Mar√≠a L√≥pez Rodr√≠guez','maria.lopez@aulpro.com','$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu','623456789','87654321B','1985-03-22','2023-09-01','Avenida Principal 456','Barcelona','08002',NULL,NULL,0,NULL);
/*!40000 ALTER TABLE `profesores` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rate_limits`
--

LOCK TABLES `rate_limits` WRITE;
/*!40000 ALTER TABLE `rate_limits` DISABLE KEYS */;
INSERT INTO `rate_limits` VALUES (1,'contacto_centro','127.0.0.1',1,1783244556,NULL);
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
  `emisor_rol` enum('estudiante','profesor','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `asunto` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estadoReclamacion` enum('pendiente','atendido') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `leido` tinyint(1) DEFAULT '0',
  `respuesta` text COLLATE utf8mb4_unicode_ci,
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
  PRIMARY KEY (`idRA`),
  KEY `idModulo` (`idModulo`),
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
  `nombreReto` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fechaInicio` date NOT NULL,
  `fechaFin` date NOT NULL,
  `horasReto` int NOT NULL,
  PRIMARY KEY (`idReto`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `retos`
--

LOCK TABLES `retos` WRITE;
/*!40000 ALTER TABLE `retos` DISABLE KEYS */;
INSERT INTO `retos` VALUES (1,'Reto HTML y CSS','2026-02-01','2026-02-28',20),(2,'Reto JavaScript','2026-03-01','2026-03-31',25),(3,'Reto Base de Datos','2026-04-01','2026-04-30',30),(4,'Reto Backend','2026-05-01','2026-05-31',35),(5,'Reto Full Stack','2026-06-01','2026-06-30',50);
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
-- Table structure for table `secretarias`
--

DROP TABLE IF EXISTS `secretarias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `secretarias` (
  `idSecretaria` int NOT NULL AUTO_INCREMENT,
  `nombreSecretaria` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emailSecretaria` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activoSecretaria` tinyint(1) NOT NULL DEFAULT '1',
  `token_fcm` text COLLATE utf8mb4_unicode_ci,
  `must_change_password` tinyint(1) NOT NULL DEFAULT '1',
  `pwd_changed_at` datetime DEFAULT NULL,
  `fechaAltaSecretaria` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idSecretaria`),
  UNIQUE KEY `uq_email_sec` (`emailSecretaria`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `secretarias`
--

LOCK TABLES `secretarias` WRITE;
/*!40000 ALTER TABLE `secretarias` DISABLE KEYS */;
INSERT INTO `secretarias` VALUES (1,'Rosa PÇrez Mart°nez','secretaria@aulapro.com','$2y$12$vkJmVDiRp10Ayd76wF7wAOS5E65O833pCcn9KvCXAO0O.iTalc3z2',1,NULL,1,NULL,'2026-06-23 10:50:56');
/*!40000 ALTER TABLE `secretarias` ENABLE KEYS */;
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
  PRIMARY KEY (`idTutor`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tutores`
--

LOCK TABLES `tutores` WRITE;
/*!40000 ALTER TABLE `tutores` DISABLE KEYS */;
INSERT INTO `tutores` VALUES (1,'Mar°a Antonia S†nchez Ruiz','maria.sanchez.tutor@gmail.com','$2y$12$vVub1O6l31W.N01viOaFeeEujpwOFDGGQ9IoQ1vQEk0AFKHc05BCW','600111222','12345678T',NULL,1,NULL,NULL,'2026-06-23 10:50:56'),(2,'Fernando Garc°a L¢pez','fernando.garcia.tutor@gmail.com','$2y$12$vVub1O6l31W.N01viOaFeeEujpwOFDGGQ9IoQ1vQEk0AFKHc05BCW','600333444','23456789G',NULL,1,NULL,NULL,'2026-06-23 10:50:56'),(3,'Ana Torres JimÇnez','ana.torres.tutor@gmail.com','$2y$12$vVub1O6l31W.N01viOaFeeEujpwOFDGGQ9IoQ1vQEk0AFKHc05BCW','600555666','34567890A',NULL,1,NULL,NULL,'2026-06-23 10:50:56');
/*!40000 ALTER TABLE `tutores` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-08  3:04:55
