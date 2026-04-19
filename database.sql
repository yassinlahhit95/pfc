-- ========================================================
-- Archivo SQL Final - Sistema de Gestión PFC
-- Autor: Yassin Lahhit (TFG Student)
-- Centro: CPS Ibaiondo
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
  `nombreEstado` varchar(50) NOT NULL,
  PRIMARY KEY (`idEstado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `estados` (`idEstado`, `nombreEstado`) VALUES (1, 'activo'), (2, 'inactivo');

-- --------------------------------------------------------
-- 2. TABLA DE NIVELES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `niveles` (
  `idNivel` int(11) NOT NULL AUTO_INCREMENT,
  `nombreNivel` varchar(100) NOT NULL,
  PRIMARY KEY (`idNivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `niveles` (`idNivel`, `nombreNivel`) VALUES (1, 'Grado Medio'), (2, 'Grado Superior'), (3, 'Bachillerato');

-- --------------------------------------------------------
-- 3. TABLA DE AULAS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `aulas` (
  `idAula` int(11) NOT NULL AUTO_INCREMENT,
  `nombreAula` varchar(50) NOT NULL,
  PRIMARY KEY (`idAula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `aulas` (`nombreAula`) VALUES ('Aula 101'), ('Aula 202'), ('Laboratorio 1');

-- --------------------------------------------------------
-- 4. TABLA DE CICLOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ciclos` (
  `idCiclo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreCiclo` varchar(150) NOT NULL,
  `descripcionCiclo` text,
  `idNivel` int(11) DEFAULT NULL,
  `idEstado` int(11) DEFAULT 1,
  PRIMARY KEY (`idCiclo`),
  CONSTRAINT `fk_ciclos_niveles` FOREIGN KEY (`idNivel`) REFERENCES `niveles` (`idNivel`),
  CONSTRAINT `fk_ciclos_estados` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `ciclos` (`nombreCiclo`, `idNivel`, `idEstado`) VALUES 
('Desarrollo de Aplicaciones Web', 2, 1),
('Sistemas Microinformáticos y Redes', 1, 1);

-- --------------------------------------------------------
-- 5. TABLA DE MODULOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `modulos` (
  `idModulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreModulo` varchar(150) NOT NULL,
  `horasMaximas` int(11) DEFAULT 100,
  `idCiclo` int(11) NOT NULL,
  PRIMARY KEY (`idModulo`),
  CONSTRAINT `fk_modulos_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `modulos` (`nombreModulo`, `idCiclo`) VALUES ('Programación', 1), ('Bases de Datos', 1), ('Sistemas Operativos', 2);

-- --------------------------------------------------------
-- 6. TABLA DE PROFESORES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `profesores` (
  `idProfesor` int(11) NOT NULL AUTO_INCREMENT,
  `nombreProfesor` varchar(150) NOT NULL,
  `emailProfesor` varchar(150) NOT NULL,
  `telefonoProfesor` varchar(20) DEFAULT NULL,
  `dniProfesor` varchar(20) DEFAULT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `direccionProfesor` varchar(255) DEFAULT NULL,
  `idEstado` int(11) DEFAULT 1,
  PRIMARY KEY (`idProfesor`),
  CONSTRAINT `fk_profesores_estados` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `profesores` (`nombreProfesor`, `emailProfesor`, `idEstado`) VALUES 
('Juan Pérez', 'juan.perez@email.com', 1),
('María García', 'maria.garcia@email.com', 1);

-- --------------------------------------------------------
-- 7. TABLA DE ESTUDIANTES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `estudiantes` (
  `idEstudiante` int(11) NOT NULL AUTO_INCREMENT,
  `nombreEstudiante` varchar(150) NOT NULL,
  `emailEstudiante` varchar(150) NOT NULL,
  `telefonoEstudiante` varchar(20) DEFAULT NULL,
  `dniEstudiante` varchar(20) NOT NULL,
  `fechaNacimientoEstudiante` date DEFAULT NULL,
  `fechaAltaEstudiante` date DEFAULT NULL,
  `direccionEstudiante` varchar(255) DEFAULT NULL,
  `ciudadEstudiante` varchar(100) DEFAULT NULL,
  `codigoPostalEstudiante` varchar(10) DEFAULT NULL,
  `observacionesEstudiante` text,
  `idCiclo` int(11) DEFAULT NULL,
  `idEstado` int(11) DEFAULT 1,
  `tituloTFG` varchar(255) DEFAULT NULL,
  `archivoTFG` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idEstudiante`),
  CONSTRAINT `fk_estudiantes_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL,
  CONSTRAINT `fk_estudiantes_estados` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `estudiantes` (`nombreEstudiante`, `emailEstudiante`, `dniEstudiante`, `idCiclo`, `idEstado`) VALUES 
('Ana Martínez', 'ana.mtz@email.com', '12345678A', 1, 1),
('Roberto Solís', 'rober.solis@email.com', '87654321B', 1, 1);

-- --------------------------------------------------------
-- 8. TABLA DE RETOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `retos` (
  `idReto` int(11) NOT NULL AUTO_INCREMENT,
  `nombreReto` varchar(150) NOT NULL,
  `fechaInicio` date NOT NULL,
  `fechaFin` date NOT NULL,
  `horasReto` int(11) NOT NULL,
  PRIMARY KEY (`idReto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 9. RELACIÓN MÓDULOS-RETOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `modulo_reto` (
  `idModulo` int(11) NOT NULL,
  `idReto` int(11) NOT NULL,
  PRIMARY KEY (`idModulo`, `idReto`),
  CONSTRAINT `fk_mr_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_mr_reto` FOREIGN KEY (`idReto`) REFERENCES `retos` (`idReto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 10. TABLA DE CALIFICACIONES (RETOS)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `calificaciones_retos` (
  `idCalificacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `idReto` int(11) NOT NULL,
  `nota` decimal(4,2) NOT NULL,
  PRIMARY KEY (`idCalificacion`),
  CONSTRAINT `fk_nota_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_nota_reto` FOREIGN KEY (`idReto`) REFERENCES `retos` (`idReto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 11. TABLA DE CALIFICACIONES (MODULOS)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `calificaciones_modulos` (
  `idCalificacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL,
  `nota_1ev` decimal(4,2) DEFAULT NULL,
  `nota_1final` decimal(4,2) DEFAULT NULL,
  `nota_2ev` decimal(4,2) DEFAULT NULL,
  `nota_2final` decimal(4,2) DEFAULT NULL,
  PRIMARY KEY (`idCalificacion`),
  CONSTRAINT `fk_notamod_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_notamod_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 12. TABLA DE PAGOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pagos` (
  `idPago` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fechaPago` date DEFAULT NULL,
  `estadoPago` enum('pendiente','pagado') DEFAULT 'pendiente',
  `tipoPago` enum('mensual','trimestral','semestral','unico') DEFAULT 'mensual',
  `comprobante` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idPago`),
  CONSTRAINT `fk_pagos_estudiantes` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 13. TABLA DE ANUNCIOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `anuncios` (
  `idAnuncio` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `fechaExpiracion` date NOT NULL,
  PRIMARY KEY (`idAnuncio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 14. TABLA DE DISPOSITIVOS (INVENTARIO)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `dispositivos` (
  `idDispositivo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreDispositivo` varchar(100) NOT NULL,
  `numeroSerie` varchar(100) NOT NULL UNIQUE,
  `estadoDispositivo` enum('disponible','prestado') DEFAULT 'disponible',
  PRIMARY KEY (`idDispositivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 15. TABLA DE PRÉSTAMOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `prestamos` (
  `idPrestamo` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `numeroSerie` varchar(100) NOT NULL,
  `fechaPrestamo` date NOT NULL,
  `fechaDevolucion` date DEFAULT NULL,
  `estadoPrestamo` enum('en curso','devuelto') DEFAULT 'en curso',
  PRIMARY KEY (`idPrestamo`),
  CONSTRAINT `fk_prestamo_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 16. TABLA DE RECLAMACIONES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reclamaciones` (
  `idReclamacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `asunto` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `gravedad` enum('leve','grave','muy grave') DEFAULT 'leve',
  `fecha` date NOT NULL,
  `estadoReclamacion` enum('pendiente','atendido') DEFAULT 'pendiente',
  PRIMARY KEY (`idReclamacion`),
  CONSTRAINT `fk_recl_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_recl_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 17. TABLA DE DIRECTORES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `directores` (
  `idDirector` int(11) NOT NULL AUTO_INCREMENT,
  `nombreDirector` varchar(150) NOT NULL,
  `emailDirector` varchar(150) NOT NULL,
  `dniDirector` varchar(20) NOT NULL,
  `fechaAltaDirector` date DEFAULT NULL,
  `idEstado` int(11) DEFAULT 1,
  PRIMARY KEY (`idDirector`),
  CONSTRAINT `fk_dir_estado` FOREIGN KEY (`idEstado`) REFERENCES `estados` (`idEstado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 18. TABLAS DE RELACIÓN CICLOS (PROFESORES Y AULAS)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ciclo_profesor` (
  `idCiclo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  PRIMARY KEY (`idCiclo`, `idProfesor`),
  CONSTRAINT `fk_cp_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `fk_cp_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ciclo_aula` (
  `idCiclo` int(11) NOT NULL,
  `idAula` int(11) NOT NULL,
  PRIMARY KEY (`idCiclo`, `idAula`),
  CONSTRAINT `fk_ca_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `fk_ca_aula` FOREIGN KEY (`idAula`) REFERENCES `aulas` (`idAula`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
