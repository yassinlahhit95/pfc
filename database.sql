-- ========================================================
-- Archivo SQL Completo para la Base de Datos "pfc"
-- INCLUYE: Ciclos, Módulos, Retos, Alumnos, Profesores, Pagos, Anuncios, Inventario y Reclamaciones
-- ========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- 0. CREACIÓN DE LA BASE DE DATOS
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `pfc` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE `pfc`;

-- --------------------------------------------------------
-- 1. TABLA DE ESTADOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `estados` (
  `idEstado` int(11) NOT NULL AUTO_INCREMENT,
  `nombreEstado` varchar(50) COLLATE utf8mb4_spanish_ci NOT NULL,
  PRIMARY KEY (`idEstado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO `estados` (`idEstado`, `nombreEstado`) VALUES (1, 'activo'), (2, 'inactivo');

-- --------------------------------------------------------
-- 2. TABLA DE NIVELES EDUCATIVOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `niveles` (
  `idNivel` int(11) NOT NULL AUTO_INCREMENT,
  `nombreNivel` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  PRIMARY KEY (`idNivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO `niveles` (`idNivel`, `nombreNivel`) VALUES (1, 'Grado Medio'), (2, 'Grado Superior'), (3, 'Bachillerato');

-- --------------------------------------------------------
-- 3. TABLA DE PROFESORES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `profesores` (
  `idProfesor` int(11) NOT NULL AUTO_INCREMENT,
  `nombreProfesor` varchar(150) COLLATE utf8mb4_spanish_ci NOT NULL,
  `emailProfesor` varchar(150) COLLATE utf8mb4_spanish_ci NOT NULL,
  `telefonoProfesor` varchar(20) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `dniProfesor` varchar(20) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `especialidad` varchar(100) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `direccionProfesor` varchar(255) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `idEstado` int(11) DEFAULT 1,
  PRIMARY KEY (`idProfesor`),
  CONSTRAINT `profesores_fk_estado` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO `profesores` (`idProfesor`, `nombreProfesor`, `emailProfesor`, `idEstado`) VALUES 
(1, 'Juan Pérez', 'juan.perez@email.com', 1),
(2, 'María García', 'maria.garcia@email.com', 1);

-- --------------------------------------------------------
-- 4. TABLA DE AULAS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `aulas` (
  `idAula` int(11) NOT NULL AUTO_INCREMENT,
  `nombreAula` varchar(50) COLLATE utf8mb4_spanish_ci NOT NULL,
  PRIMARY KEY (`idAula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO `aulas` (`idAula`, `nombreAula`) VALUES (1, 'Aula 101'), (2, 'Aula 202'), (3, 'Laboratorio 1');

-- --------------------------------------------------------
-- 5. TABLA DE CICLOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ciclos` (
  `idCiclo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreCiclo` varchar(150) COLLATE utf8mb4_spanish_ci NOT NULL,
  `descripcionCiclo` text COLLATE utf8mb4_spanish_ci,
  `idNivel` int(11) DEFAULT NULL,
  `idEstado` int(11) DEFAULT 1,
  PRIMARY KEY (`idCiclo`),
  CONSTRAINT `ciclos_fk_nivel` FOREIGN KEY (`idNivel`) REFERENCES `niveles` (`idNivel`),
  CONSTRAINT `ciclos_fk_estado` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO `ciclos` (`idCiclo`, `nombreCiclo`, `idNivel`, `idEstado`) VALUES 
(1, 'Desarrollo de Aplicaciones Web', 2, 1),
(2, 'Sistemas Microinformáticos y Redes', 1, 1);

-- --------------------------------------------------------
-- 6. TABLAS DE RELACIÓN (MULTIPLE PROFESOR/AULA POR CICLO)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ciclo_profesor` (
  `idCiclo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  PRIMARY KEY (`idCiclo`, `idProfesor`),
  CONSTRAINT `ciclo_prof_fk_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `ciclo_prof_fk_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

CREATE TABLE IF NOT EXISTS `ciclo_aula` (
  `idCiclo` int(11) NOT NULL,
  `idAula` int(11) NOT NULL,
  PRIMARY KEY (`idCiclo`, `idAula`),
  CONSTRAINT `ciclo_aula_fk_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `ciclo_aula_fk_aula` FOREIGN KEY (`idAula`) REFERENCES `aulas` (`idAula`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO `ciclo_profesor` (`idCiclo`, `idProfesor`) VALUES (1, 1), (2, 2);
INSERT INTO `ciclo_aula` (`idCiclo`, `idAula`) VALUES (1, 1), (2, 2);

-- --------------------------------------------------------
-- 7. TABLA DE MODULOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `modulos` (
  `idModulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreModulo` varchar(150) COLLATE utf8mb4_spanish_ci NOT NULL,
  `horasMaximas` int(11) DEFAULT 100,
  `idCiclo` int(11) NOT NULL,
  PRIMARY KEY (`idModulo`),
  CONSTRAINT `modulos_fk_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------
-- 8. TABLA DE RETOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `retos` (
  `idReto` int(11) NOT NULL AUTO_INCREMENT,
  `nombreReto` varchar(150) COLLATE utf8mb4_spanish_ci NOT NULL,
  `fechaInicio` date NOT NULL,
  `fechaFin` date NOT NULL,
  `horasReto` int(11) NOT NULL,
  PRIMARY KEY (`idReto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------
-- 9. TABLA DE RELACIÓN MÓDULOS-RETOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `modulo_reto` (
  `idModulo` int(11) NOT NULL,
  `idReto` int(11) NOT NULL,
  PRIMARY KEY (`idModulo`, `idReto`),
  CONSTRAINT `modulo_reto_fk_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `modulo_reto_fk_reto` FOREIGN KEY (`idReto`) REFERENCES `retos` (`idReto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------
-- 10. TABLA DE ESTUDIANTES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `estudiantes` (
  `idEstudiante` int(11) NOT NULL AUTO_INCREMENT,
  `nombreEstudiante` varchar(150) COLLATE utf8mb4_spanish_ci NOT NULL,
  `emailEstudiante` varchar(150) COLLATE utf8mb4_spanish_ci NOT NULL,
  `telefonoEstudiante` varchar(20) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `dniEstudiante` varchar(20) COLLATE utf8mb4_spanish_ci NOT NULL,
  `fechaNacimientoEstudiante` date DEFAULT NULL,
  `fechaAltaEstudiante` date DEFAULT NULL,
  `direccionEstudiante` varchar(255) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `ciudadEstudiante` varchar(100) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `codigoPostalEstudiante` varchar(10) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `nivelEstudiante` varchar(50) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `observacionesEstudiante` text COLLATE utf8mb4_spanish_ci,
  `idCiclo` int(11) DEFAULT NULL,
  `idEstado` int(11) DEFAULT 1,
  PRIMARY KEY (`idEstudiante`),
  CONSTRAINT `estudiantes_fk_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL,
  CONSTRAINT `estudiantes_fk_estado` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO `estudiantes` (`idEstudiante`, `nombreEstudiante`, `emailEstudiante`, `dniEstudiante`, `idCiclo`, `idEstado`) VALUES 
(1, 'Ana Martínez', 'ana.mtz@email.com', '12345678A', 1, 1),
(2, 'Roberto Solís', 'rober.solis@email.com', '87654321B', 1, 1);

-- --------------------------------------------------------
-- 11. TABLA DE CALIFICACIONES DE RETOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `calificaciones_retos` (
  `idCalificacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `idReto` int(11) NOT NULL,
  `nota` decimal(4,2) NOT NULL,
  PRIMARY KEY (`idCalificacion`),
  CONSTRAINT `calificaciones_fk_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `calificaciones_fk_reto` FOREIGN KEY (`idReto`) REFERENCES `retos` (`idReto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------
-- 12. TABLA DE PAGOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pagos` (
  `idPago` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `concepto` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fechaPago` date DEFAULT NULL,
  `estadoPago` enum('pendiente','pagado') COLLATE utf8mb4_spanish_ci DEFAULT 'pendiente',
  `tipoPago` enum('mensual','trimestral','semestral','unico') COLLATE utf8mb4_spanish_ci DEFAULT 'mensual',
  `comprobante` varchar(255) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idPago`),
  CONSTRAINT `pagos_fk_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------
-- 13. TABLA DE ANUNCIOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `anuncios` (
  `idAnuncio` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) COLLATE utf8mb4_spanish_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `fechaExpiracion` date NOT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAnuncio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------
-- 14. TABLA DE DISPOSITIVOS (INVENTARIO)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `dispositivos` (
  `idDispositivo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreDispositivo` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `numeroSerie` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL UNIQUE,
  `estadoDispositivo` enum('disponible','prestado') COLLATE utf8mb4_spanish_ci DEFAULT 'disponible',
  PRIMARY KEY (`idDispositivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------
-- 15. TABLA DE PRÉSTAMOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `prestamos` (
  `idPrestamo` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `numeroSerie` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `fechaPrestamo` date NOT NULL,
  `fechaDevolucion` date DEFAULT NULL,
  `estadoPrestamo` enum('en curso','devuelto') COLLATE utf8mb4_spanish_ci DEFAULT 'en curso',
  PRIMARY KEY (`idPrestamo`),
  CONSTRAINT `prestamos_fk_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------
-- 16. TABLA DE RECLAMACIONES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reclamaciones` (
  `idReclamacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `asunto` varchar(150) COLLATE utf8mb4_spanish_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `gravedad` enum('leve','grave','muy grave') COLLATE utf8mb4_spanish_ci DEFAULT 'leve',
  `fecha` date NOT NULL,
  `estadoReclamacion` enum('pendiente','atendido') COLLATE utf8mb4_spanish_ci DEFAULT 'pendiente',
  `fechaRegistro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idReclamacion`),
  CONSTRAINT `reclamaciones_fk_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `reclamaciones_fk_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------
-- 17. TABLA DE DIRECTORES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `directores` (
  `idDirector` int(11) NOT NULL AUTO_INCREMENT,
  `nombreDirector` varchar(150) COLLATE utf8mb4_spanish_ci NOT NULL,
  `emailDirector` varchar(150) COLLATE utf8mb4_spanish_ci NOT NULL,
  `dniDirector` varchar(20) COLLATE utf8mb4_spanish_ci NOT NULL,
  `fechaAltaDirector` date DEFAULT NULL,
  `idEstado` int(11) DEFAULT 1,
  PRIMARY KEY (`idDirector`),
  CONSTRAINT `directores_fk_estado` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

COMMIT;
