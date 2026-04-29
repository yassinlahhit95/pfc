-- --------------------------------------------------------
-- BASE DE DATOS LIMPIA Y CORREGIDA - CPS IBAIONDO
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- 1. Crear base de datos
CREATE DATABASE IF NOT EXISTS `pfc` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE `pfc`;

-- --------------------------------------------------------
-- 2. TABLAS DE CONFIGURACIÓN
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `niveles` (
  `idNivel` int(11) NOT NULL AUTO_INCREMENT,
  `nombreNivel` varchar(100) NOT NULL,
  PRIMARY KEY (`idNivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `niveles` (`idNivel`, `nombreNivel`) VALUES (1, 'Grado Medio'), (2, 'Grado Superior');

CREATE TABLE IF NOT EXISTS `aulas` (
  `idAula` int(11) NOT NULL AUTO_INCREMENT,
  `nombreAula` varchar(50) NOT NULL,
  PRIMARY KEY (`idAula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ciclos` (
  `idCiclo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreCiclo` varchar(150) NOT NULL,
  `abreviaturaCiclo` varchar(10) NOT NULL,
  `precioCiclo` decimal(10,2) DEFAULT 1000.00,
  `idNivel` int(11) DEFAULT 1,
  PRIMARY KEY (`idCiclo`),
  CONSTRAINT `fk_ciclos_niveles` FOREIGN KEY (`idNivel`) REFERENCES `niveles` (`idNivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `modulos` (
  `idModulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreModulo` varchar(150) NOT NULL,
  `horasMaximas` int(11) DEFAULT 100,
  `idCiclo` int(11) NOT NULL,
  PRIMARY KEY (`idModulo`),
  CONSTRAINT `fk_modulos_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 3. TABLAS DE USUARIOS
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `profesores` (
  `idProfesor` int(11) NOT NULL AUTO_INCREMENT,
  `nombreProfesor` varchar(150) NOT NULL,
  `emailProfesor` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '123456',
  `telefonoProfesor` varchar(20) DEFAULT '',
  `dniProfesor` varchar(20) DEFAULT '',
  `fechaNacimientoProfesor` date DEFAULT '1980-01-01',
  `fechaAltaProfesor` date DEFAULT '2026-01-01',
  `direccionProfesor` varchar(255) DEFAULT '',
  `ciudadProfesor` varchar(100) DEFAULT '',
  `codigoPostalProfesor` varchar(10) DEFAULT '',
  `observacionesProfesor` text DEFAULT NULL,
  `fcm_token` text DEFAULT NULL,
  PRIMARY KEY (`idProfesor`),
  UNIQUE KEY `uk_email_prof` (`emailProfesor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `estudiantes` (
  `idEstudiante` int(11) NOT NULL AUTO_INCREMENT,
  `nombreEstudiante` varchar(150) NOT NULL,
  `emailEstudiante` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '123456',
  `telefonoEstudiante` varchar(20) DEFAULT '',
  `dniEstudiante` varchar(20) NOT NULL,
  `fechaNacimientoEstudiante` date DEFAULT '2000-01-01',
  `fechaAltaEstudiante` date DEFAULT '2026-01-01',
  `direccionEstudiante` varchar(255) DEFAULT '',
  `ciudadEstudiante` varchar(100) DEFAULT '',
  `codigoPostalEstudiante` varchar(10) DEFAULT '',
  `observacionesEstudiante` text DEFAULT NULL,
  `idCiclo` int(11) DEFAULT NULL,
  `archivoTFG` varchar(255) DEFAULT '',
  `fechaSubidaTFG` datetime DEFAULT NULL,
  `fcm_token` text DEFAULT NULL,
  PRIMARY KEY (`idEstudiante`),
  UNIQUE KEY `uk_email_est` (`emailEstudiante`),
  CONSTRAINT `fk_estudiantes_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `directores` (
  `idDirector` int(11) NOT NULL AUTO_INCREMENT,
  `nombreDirector` varchar(150) NOT NULL,
  `emailDirector` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '123456',
  `telefonoDirector` varchar(20) DEFAULT '',
  `dniDirector` varchar(20) NOT NULL,
  `fechaNacimientoDirector` date DEFAULT '2000-01-01',
  `fechaAltaDirector` date DEFAULT '2026-01-01',
  `direccionDirector` varchar(255) DEFAULT '',
  `ciudadDirector` varchar(100) DEFAULT '',
  `codigoPostalDirector` varchar(10) DEFAULT '',
  `observacionesDirector` text DEFAULT NULL,
  `fcm_token` text DEFAULT NULL,
  PRIMARY KEY (`idDirector`),
  UNIQUE KEY `uk_email_dir` (`emailDirector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `directores` (`idDirector`, `nombreDirector`, `emailDirector`, `password`, `dniDirector`) VALUES 
(1, 'ADMINISTRADOR', 'admin@email.com', '123456', '00000000T');

-- --------------------------------------------------------
-- 4. ACADÉMICO Y NOTAS
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `retos` (
  `idReto` int(11) NOT NULL AUTO_INCREMENT,
  `nombreReto` varchar(150) NOT NULL,
  `fechaInicio` date NOT NULL,
  `fechaFin` date NOT NULL,
  `horasReto` int(11) NOT NULL,
  PRIMARY KEY (`idReto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `modulo_reto` (
  `idModulo` int(11) NOT NULL,
  `idReto` int(11) NOT NULL,
  PRIMARY KEY (`idModulo`, `idReto`),
  CONSTRAINT `fk_mr_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_mr_reto` FOREIGN KEY (`idReto`) REFERENCES `retos` (`idReto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `calificaciones_retos` (
  `idCalificacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `idReto` int(11) NOT NULL,
  `nota` decimal(4,2) NOT NULL,
  PRIMARY KEY (`idCalificacion`),
  CONSTRAINT `fk_cr_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_cr_reto` FOREIGN KEY (`idReto`) REFERENCES `retos` (`idReto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `calificaciones_modulos` (
  `idCalificacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL,
  `nota_1ev` decimal(4,2) DEFAULT 0.00,
  `nota_1final` decimal(4,2) DEFAULT 0.00,
  `nota_2ev` decimal(4,2) DEFAULT 0.00,
  `nota_2final` decimal(4,2) DEFAULT 0.00,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`idCalificacion`),
  CONSTRAINT `fk_cm_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_cm_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 5. INVENTARIO Y PRÉSTAMOS
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `dispositivos` (
  `idDispositivo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreDispositivo` varchar(100) NOT NULL,
  `numeroSerie` varchar(100) NOT NULL,
  `estadoDispositivo` enum('disponible','prestado') DEFAULT 'disponible',
  PRIMARY KEY (`idDispositivo`),
  UNIQUE KEY `uk_serie` (`numeroSerie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `prestamos` (
  `idPrestamo` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `numeroSerie` varchar(100) NOT NULL,
  `fechaPrestamo` date NOT NULL,
  `fechaDevolucion` date DEFAULT NULL,
  `estadoPrestamo` enum('en curso','devuelto') DEFAULT 'en curso',
  PRIMARY KEY (`idPrestamo`),
  CONSTRAINT `fk_pres_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 6. COMUNICACIÓN Y PAGOS
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `anuncios` (
  `idAnuncio` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `fechaAnuncio` datetime DEFAULT CURRENT_TIMESTAMP,
  `fechaExpiracion` date NOT NULL,
  `dirigidoA` enum('todos','estudiantes','profesores') DEFAULT 'todos',
  PRIMARY KEY (`idAnuncio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `reclamaciones` (
  `idReclamacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) DEFAULT NULL,
  `idProfesor` int(11) DEFAULT NULL,
  `emisor_rol` enum('estudiante','profesor','admin') DEFAULT 'estudiante',
  `asunto` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` date NOT NULL,
  `estadoReclamacion` enum('pendiente','atendido') DEFAULT 'pendiente',
  `leido` tinyint(1) DEFAULT 0,
  `respuesta` text DEFAULT NULL,
  PRIMARY KEY (`idReclamacion`),
  CONSTRAINT `fk_rec_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_rec_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pagos` (
  `idPago` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fechaPago` date NOT NULL,
  `fechaProximoPago` date NOT NULL,
  `tipoPago` enum('mensual','trimestral','semestral','unico') DEFAULT 'mensual',
  `comprobante` varchar(255) DEFAULT '',
  PRIMARY KEY (`idPago`),
  CONSTRAINT `fk_pag_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `eventos` (
  `idEvento` int(11) NOT NULL AUTO_INCREMENT,
  `tituloEvento` varchar(150) NOT NULL,
  `descripcionEvento` text DEFAULT NULL,
  `fechaEvento` date NOT NULL,
  `horaEvento` time DEFAULT '09:00:00',
  `ubicacionEvento` varchar(150) DEFAULT '',
  PRIMARY KEY (`idEvento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 7. TABLAS DE RELACIÓN (ASIGNACIONES)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `ciclo_profesor` (
  `idCiclo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  PRIMARY KEY (`idCiclo`, `idProfesor`),
  CONSTRAINT `fk_rel_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `fk_rel_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ciclo_aula` (
  `idCiclo` int(11) NOT NULL,
  `idAula` int(11) NOT NULL,
  PRIMARY KEY (`idCiclo`, `idAula`),
  CONSTRAINT `fk_rela_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `fk_rela_aula` FOREIGN KEY (`idAula`) REFERENCES `aulas` (`idAula`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `profesor_modulo` (
  `idProfesor` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL,
  PRIMARY KEY (`idProfesor`, `idModulo`),
  CONSTRAINT `fk_relm_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE,
  CONSTRAINT `fk_relm_mod` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 8. DATOS DE PRUEBA REALISTAS
-- --------------------------------------------------------

-- Aulas
INSERT INTO `aulas` (`nombreAula`) VALUES ('Aula 101'), ('Aula 102'), ('Laboratorio de Redes'), ('Taller de Montaje');

-- Ciclos
INSERT INTO `ciclos` (`nombreCiclo`, `abreviaturaCiclo`, `precioCiclo`, `idNivel`) VALUES 
('Desarrollo de Aplicaciones Web', 'DAW', 1200.00, 2),
('Desarrollo de Aplicaciones Multiplataforma', 'DAM', 1200.00, 2),
('Sistemas Microinformáticos y Redes', 'SMR', 800.00, 1);

-- Módulos DAW
INSERT INTO `modulos` (`nombreModulo`, `horasMaximas`, `idCiclo`) VALUES 
('Programación', 240, 1),
('Bases de Datos', 180, 1),
('Desarrollo Web en Entorno Cliente', 160, 1),
('Desarrollo Web en Entorno Servidor', 180, 1);

-- Módulos SMR
INSERT INTO `modulos` (`nombreModulo`, `horasMaximas`, `idCiclo`) VALUES 
('Montaje y Mantenimiento de Equipos', 200, 3),
('Redes Locales', 180, 3);

-- Profesores
INSERT INTO `profesores` (`nombreProfesor`, `emailProfesor`, `password`, `dniProfesor`, `telefonoProfesor`) VALUES 
('JUAN PÉREZ GARCÍA', 'juan.perez@email.com', '123456', '12345678A', '600111222'),
('MARÍA RODRÍGUEZ LÓPEZ', 'maria.rodriguez@email.com', '123456', '87654321B', '600333444');

-- Estudiantes
INSERT INTO `estudiantes` (`nombreEstudiante`, `emailEstudiante`, `password`, `dniEstudiante`, `idCiclo`, `telefonoEstudiante`) VALUES 
('CARLOS SÁNCHEZ MARTÍN', 'carlos.sanchez@email.com', '123456', '11223344C', 1, '611000111'),
('ANA BELÉN RUIZ', 'ana.ruiz@email.com', '123456', '44332211D', 1, '611000222'),
('DAVID GARCÍA FERNÁNDEZ', 'david.garcia@email.com', '123456', '55667788E', 3, '611000333'),
('ELENA MARTÍNEZ SOLER', 'elena.martinez@email.com', '123456', '99887766F', 1, '611000444');

-- Asignaciones Ciclo-Profesor
INSERT INTO `ciclo_profesor` (`idCiclo`, `idProfesor`) VALUES (1, 1), (1, 2), (3, 2);

-- Asignaciones Profesor-Módulo
INSERT INTO `profesor_modulo` (`idProfesor`, `idModulo`) VALUES 
(1, 1), (1, 4), -- Juan da Programación y Servidor
(2, 2), (2, 3), -- María da BD y Cliente
(2, 5), (2, 6); -- María también apoya en SMR

-- Retos
INSERT INTO `retos` (`nombreReto`, `fechaInicio`, `fechaFin`, `horasReto`) VALUES 
('PROYECTO E-COMMERCE PHP', '2026-05-01', '2026-06-15', 60),
('CONFIGURACIÓN RED CORPORATIVA', '2026-05-10', '2026-05-30', 40);

-- Relación Módulo-Reto
INSERT INTO `modulo_reto` (`idModulo`, `idReto`) VALUES 
(1, 1), (2, 1), (4, 1), -- El e-commerce involucra prog, bd y servidor
(6, 2); -- La red involucra Redes Locales

-- Anuncios
INSERT INTO `anuncios` (`titulo`, `mensaje`, `fechaExpiracion`, `dirigidoA`) VALUES 
('Mantenimiento del Servidor', 'El próximo viernes el servidor estará fuera de servicio de 15:00 a 17:00.', '2026-05-10', 'todos'),
('Entrega de Proyectos Finales', 'Recordad que la fecha límite es el 15 de junio.', '2026-06-15', 'estudiantes');

-- Eventos
INSERT INTO `eventos` (`tituloEvento`, `descripcionEvento`, `fechaEvento`, `horaEvento`, `ubicacionEvento`) VALUES 
('Charla Ciberseguridad', 'Ponencia a cargo de expertos en seguridad informática.', '2026-05-20', '10:30:00', 'Salón de Actos'),
('Graduación 2026', 'Ceremonia de entrega de diplomas.', '2026-06-25', '18:00:00', 'Patio Central');

COMMIT;
