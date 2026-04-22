-- ========================================================
-- Archivo SQL Final - Sistema de Gestión PFC (SIMULACIÓN COMPLETA)
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
-- 1. TABLA DE NIVELES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `niveles` (
  `idNivel` int(11) NOT NULL AUTO_INCREMENT,
  `nombreNivel` varchar(100) NOT NULL,
  PRIMARY KEY (`idNivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `niveles` (`idNivel`, `nombreNivel`) VALUES 
(1, 'Grado Medio'), 
(2, 'Grado Superior'), 
(3, 'Bachillerato');

-- --------------------------------------------------------
-- 2. TABLA DE AULAS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `aulas` (
  `idAula` int(11) NOT NULL AUTO_INCREMENT,
  `nombreAula` varchar(50) NOT NULL,
  PRIMARY KEY (`idAula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `aulas` (`idAula`, `nombreAula`) VALUES 
(1, 'Aula 101 - Informática'), 
(2, 'Aula 202 - Redes'), 
(3, 'Laboratorio 1'), 
(4, 'Taller Hardware'),
(5, 'Salón de Actos');

-- --------------------------------------------------------
-- 3. TABLA DE CICLOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ciclos` (
  `idCiclo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreCiclo` varchar(150) NOT NULL,
  `descripcionCiclo` text,
  `idNivel` int(11) DEFAULT NULL,
  PRIMARY KEY (`idCiclo`),
  CONSTRAINT `fk_ciclos_niveles` FOREIGN KEY (`idNivel`) REFERENCES `niveles` (`idNivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `ciclos` (`idCiclo`, `nombreCiclo`, `descripcionCiclo`, `idNivel`) VALUES 
(1, 'Desarrollo de Aplicaciones Web', 'Especialización en entornos web frontend y backend.', 2),
(2, 'Sistemas Microinformáticos y Redes', 'Configuración de equipos y redes de área local.', 1),
(3, 'Administración de Sistemas Informáticos', 'Gestión de servidores, bases de datos y seguridad.', 2),
(4, 'Desarrollo de Aplicaciones Multiplataforma', 'Creación de software para móviles y escritorio.', 2);

-- --------------------------------------------------------
-- 4. TABLA DE MODULOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `modulos` (
  `idModulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreModulo` varchar(150) NOT NULL,
  `horasMaximas` int(11) DEFAULT 100,
  `idCiclo` int(11) NOT NULL,
  PRIMARY KEY (`idModulo`),
  CONSTRAINT `fk_modulos_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `modulos` (`idModulo`, `nombreModulo`, `horasMaximas`, `idCiclo`) VALUES 
(1, 'Despliegue de Aplicaciones Web', 90, 1),
(2, 'Diseño de Interfaces Web', 120, 1),
(3, 'Programación PHP', 180, 1),
(4, 'Montaje y Mantenimiento', 140, 2),
(5, 'Redes Locales', 160, 2),
(6, 'Sistemas Operativos en Red', 130, 3),
(7, 'Gestión de Bases de Datos', 150, 3),
(8, 'Programación Multimedia', 110, 4);

-- --------------------------------------------------------
-- 5. TABLA DE PROFESORES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `profesores` (
  `idProfesor` int(11) NOT NULL AUTO_INCREMENT,
  `nombreProfesor` varchar(150) NOT NULL,
  `emailProfesor` varchar(150) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL DEFAULT '123456',
  `telefonoProfesor` varchar(20) DEFAULT NULL,
  `dniProfesor` varchar(20) DEFAULT NULL,
  `direccionProfesor` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idProfesor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `profesores` (`idProfesor`, `nombreProfesor`, `emailProfesor`, `password`, `telefonoProfesor`, `dniProfesor`, `direccionProfesor`) VALUES 
(1, 'Juan Pérez', 'juan.perez@email.com', '123456', '611223344', '11223344J', 'Calle Mayor 1'),
(2, 'María García', 'maria.garcia@email.com', '123456', '622334455', '22334455M', 'Avenida Principal 45'),
(3, 'Carlos Ruiz', 'carlos.ruiz@email.com', '123456', '633445566', '33445566C', 'Calle Estación 12'),
(4, 'Elena Sanz', 'elena.sanz@email.com', '123456', '644556677', '44556677E', 'Plaza Central 3'),
(5, 'Pedro Gómez', 'pedro.gomez@email.com', '123456', '655667788', '55667788P', 'Calle del Sol 9');

-- --------------------------------------------------------
-- 6. TABLA DE ESTUDIANTES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `estudiantes` (
  `idEstudiante` int(11) NOT NULL AUTO_INCREMENT,
  `nombreEstudiante` varchar(150) NOT NULL,
  `emailEstudiante` varchar(150) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL DEFAULT '123456',
  `telefonoEstudiante` varchar(20) DEFAULT NULL,
  `dniEstudiante` varchar(20) NOT NULL,
  `fechaNacimientoEstudiante` date DEFAULT NULL,
  `fechaAltaEstudiante` date DEFAULT NULL,
  `direccionEstudiante` varchar(255) DEFAULT NULL,
  `ciudadEstudiante` varchar(100) DEFAULT NULL,
  `codigoPostalEstudiante` varchar(10) DEFAULT NULL,
  `observacionesEstudiante` text,
  `idCiclo` int(11) DEFAULT NULL,
  `archivoTFG` varchar(255) DEFAULT NULL,
  `fechaSubidaTFG` datetime DEFAULT NULL,
  PRIMARY KEY (`idEstudiante`),
  CONSTRAINT `fk_estudiantes_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `estudiantes` (`idEstudiante`, `nombreEstudiante`, `emailEstudiante`, `password`, `dniEstudiante`, `idCiclo`, `fechaAltaEstudiante`) VALUES 
(1, 'Ana Martínez', 'ana.mtz@email.com', '123456', '12345678A', 1, '2025-09-01'),
(2, 'Roberto Solís', 'rober.solis@email.com', '123456', '87654321B', 1, '2025-09-01'),
(3, 'Lucía López', 'lucia.lopez@email.com', '123456', '11221122L', 2, '2025-09-05'),
(4, 'David Ferrín', 'david.ferrin@email.com', '123456', '33443344D', 2, '2025-09-05'),
(5, 'Sonia Valle', 'sonia.valle@email.com', '123456', '55665566S', 3, '2025-09-10'),
(6, 'Marcos Juez', 'marcos.juez@email.com', '123456', '77887788M', 3, '2025-09-10'),
(7, 'Beatriz Amo', 'beatriz.amo@email.com', '123456', '99009900B', 4, '2025-09-15'),
(8, 'Javier Cano', 'javier.cano@email.com', '123456', '10101010J', 4, '2025-09-15');

-- --------------------------------------------------------
-- 7. TABLA DE RETOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `retos` (
  `idReto` int(11) NOT NULL AUTO_INCREMENT,
  `nombreReto` varchar(150) NOT NULL,
  `fechaInicio` date NOT NULL,
  `fechaFin` date NOT NULL,
  `horasReto` int(11) NOT NULL,
  PRIMARY KEY (`idReto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `retos` (`idReto`, `nombreReto`, `fechaInicio`, `fechaFin`, `horasReto`) VALUES 
(1, 'E-commerce Solidario', '2026-01-10', '2026-02-15', 40),
(2, 'Infraestructura Segura', '2026-03-01', '2026-04-10', 50),
(3, 'App Ciudadana', '2026-04-20', '2026-06-01', 60);

-- --------------------------------------------------------
-- 8. RELACIÓN MÓDULOS-RETOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `modulo_reto` (
  `idModulo` int(11) NOT NULL,
  `idReto` int(11) NOT NULL,
  PRIMARY KEY (`idModulo`, `idReto`),
  CONSTRAINT `fk_mr_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_mr_reto` FOREIGN KEY (`idReto`) REFERENCES `retos` (`idReto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `modulo_reto` (`idModulo`, `idReto`) VALUES 
(3, 1), (2, 1), (5, 2), (6, 2), (8, 3);

-- --------------------------------------------------------
-- 9. TABLA DE CALIFICACIONES (RETOS)
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

INSERT INTO `calificaciones_retos` (`idEstudiante`, `idReto`, `nota`) VALUES 
(1, 1, 8.50), (2, 1, 7.00), (3, 2, 9.25), (4, 2, 6.50);

-- --------------------------------------------------------
-- 10. TABLA DE CALIFICACIONES (MODULOS)
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

INSERT INTO `calificaciones_modulos` (`idEstudiante`, `idModulo`, `nota_1ev`, `nota_1final`, `nota_2ev`, `nota_2final`) VALUES 
(1, 1, 7.5, 8.0, 8.0, 8.5),
(1, 3, 6.0, 7.0, 7.5, 8.0),
(2, 3, 5.0, 5.5, 6.0, 6.5);

-- --------------------------------------------------------
-- 11. TABLA DE PAGOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pagos` (
  `idPago` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fechaPago` date NOT NULL,
  `fechaProximoPago` date NOT NULL,
  `tipoPago` enum('mensual','trimestral','semestral','unico') DEFAULT 'mensual',
  `comprobante` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idPago`),
  CONSTRAINT `fk_pagos_estudiantes` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pagos` (`idEstudiante`, `monto`, `fechaPago`, `fechaProximoPago`, `tipoPago`) VALUES 
(1, 150.00, '2026-04-01', '2026-05-01', 'mensual'),
(3, 400.00, '2026-03-15', '2026-06-15', 'trimestral'),
(5, 750.00, '2026-01-10', '2026-07-10', 'semestral');

-- --------------------------------------------------------
-- 12. TABLA DE ANUNCIOS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `anuncios` (
  `idAnuncio` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `fechaExpiracion` date NOT NULL,
  PRIMARY KEY (`idAnuncio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `anuncios` (`titulo`, `mensaje`, `fechaExpiracion`) VALUES 
('Mantenimiento Servidor', 'El servidor estará fuera de servicio el sábado de 08:00 a 14:00.', '2026-05-30'),
('Entrega TFG', 'Recordatorio: La fecha límite para subir el PDF es el 15 de Junio.', '2026-06-15'),
('Vacaciones de Mayo', 'El centro permanecerá cerrado el día 1 de mayo.', '2026-05-02');

-- --------------------------------------------------------
-- 13. TABLA DE DISPOSITIVOS (INVENTARIO)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `dispositivos` (
  `idDispositivo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreDispositivo` varchar(100) NOT NULL,
  `numeroSerie` varchar(100) NOT NULL UNIQUE,
  `estadoDispositivo` enum('disponible','prestado') DEFAULT 'disponible',
  PRIMARY KEY (`idDispositivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `dispositivos` (`nombreDispositivo`, `numeroSerie`, `estadoDispositivo`) VALUES 
('Portátil Dell Latitude', 'DELL-9988', 'prestado'),
('Proyector Epson X2', 'EPSON-123', 'disponible'),
('Tablet Samsung Tab S', 'SAMI-4455', 'prestado'),
('Kit Raspberry Pi 4', 'RPI-001', 'disponible');

-- --------------------------------------------------------
-- 14. TABLA DE PRÉSTAMOS
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

INSERT INTO `prestamos` (`idEstudiante`, `numeroSerie`, `fechaPrestamo`, `estadoPrestamo`) VALUES 
(1, 'DELL-9988', '2026-04-10', 'en curso'),
(3, 'SAMI-4455', '2026-04-15', 'en curso');

-- --------------------------------------------------------
-- 15. TABLA DE RECLAMACIONES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reclamaciones` (
  `idReclamacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `idProfesor` int(11) DEFAULT NULL,
  `asunto` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` date NOT NULL,
  `estadoReclamacion` enum('pendiente','atendido') DEFAULT 'pendiente',
  PRIMARY KEY (`idReclamacion`),
  CONSTRAINT `fk_recl_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_recl_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `reclamaciones` (`idEstudiante`, `idProfesor`, `asunto`, `descripcion`, `fecha`, `estadoReclamacion`) VALUES 
(1, 2, 'Revisión de examen', 'Solicito revisión de la nota final de PHP.', '2026-04-18', 'pendiente'),
(4, NULL, 'Falta de material', 'No hay suficientes kits de Raspberry en el aula 202.', '2026-04-20', 'pendiente');

-- --------------------------------------------------------
-- 16. TABLA DE DIRECTORES (ADMINS)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `directores` (
  `idDirector` int(11) NOT NULL AUTO_INCREMENT,
  `nombreDirector` varchar(150) NOT NULL,
  `emailDirector` varchar(150) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL DEFAULT '123456',
  `telefonoDirector` varchar(20) DEFAULT NULL,
  `dniDirector` varchar(20) NOT NULL,
  `fechaAltaDirector` date DEFAULT NULL,
  PRIMARY KEY (`idDirector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `directores` (`idDirector`, `nombreDirector`, `emailDirector`, `password`, `dniDirector`) VALUES 
(1, 'Admin Principal', 'admin@email.com', '123456', '00000000T'),
(2, 'Marta Jefa', 'marta.jefa@email.com', '123456', '99999999M');

-- --------------------------------------------------------
-- 17. TABLAS DE RELACIÓN CICLOS (PROFESORES Y AULAS)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ciclo_profesor` (
  `idCiclo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  PRIMARY KEY (`idCiclo`, `idProfesor`),
  CONSTRAINT `fk_cp_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `fk_cp_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `ciclo_profesor` (`idCiclo`, `idProfesor`) VALUES 
(1, 1), (1, 2), (2, 3), (3, 4), (4, 5);

CREATE TABLE IF NOT EXISTS `ciclo_aula` (
  `idCiclo` int(11) NOT NULL,
  `idAula` int(11) NOT NULL,
  PRIMARY KEY (`idCiclo`, `idAula`),
  CONSTRAINT `fk_ca_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `fk_ca_aula` FOREIGN KEY (`idAula`) REFERENCES `aulas` (`idAula`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `ciclo_aula` (`idCiclo`, `idAula`) VALUES 
(1, 1), (2, 4), (3, 2), (4, 3);

-- --------------------------------------------------------
-- 18. RELACIÓN PROFESOR-MODULO
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `profesor_modulo` (
  `idProfesor` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL,
  PRIMARY KEY (`idProfesor`, `idModulo`),
  CONSTRAINT `fk_pm_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE,
  CONSTRAINT `fk_pm_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `profesor_modulo` (`idProfesor`, `idModulo`) VALUES 
(1, 1), (2, 3), (2, 2), (3, 4), (4, 6), (5, 8);

COMMIT;
