SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `niveles` (
  `idNivel` int(11) NOT NULL AUTO_INCREMENT,
  `nombreNivel` varchar(50) NOT NULL,
  PRIMARY KEY (`idNivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ciclos` (
  `idCiclo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreCiclo` varchar(100) NOT NULL,
  `abreviaturaCiclo` varchar(10) NOT NULL,
  `precioCiclo` decimal(10,2),
  `idNivel` int(11),
  PRIMARY KEY (`idCiclo`),
  CONSTRAINT `fk_ciclos_niveles` FOREIGN KEY (`idNivel`) REFERENCES `niveles` (`idNivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `modulos` (
  `idModulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreModulo` varchar(120) NOT NULL,
  `horasMaximas` int(11),
  `idCiclo` int(11) NOT NULL,
  PRIMARY KEY (`idModulo`),
  CONSTRAINT `fk_modulos_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `profesores` (
  `idProfesor` int(11) NOT NULL AUTO_INCREMENT,
  `nombreProfesor` varchar(100) NOT NULL,
  `emailProfesor` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '$2y$10$E0xJ3K6X8iO8I6U.L0R.7.L.7.L.7.L.7.L.7.L.7.L.7.L.7.',
  `telefonoProfesor` varchar(15),
  `dniProfesor` varchar(12),
  `fechaNacimientoProfesor` date,
  `fechaAltaProfesor` date,
  `direccionProfesor` varchar(200),
  `ciudadProfesor` varchar(80),
  `codigoPostalProfesor` varchar(10),
  `observacionesProfesor` text,
  `fcm_token` text,
  PRIMARY KEY (`idProfesor`),
  UNIQUE KEY `uk_email_prof` (`emailProfesor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `estudiantes` (
  `idEstudiante` int(11) NOT NULL AUTO_INCREMENT,
  `nombreEstudiante` varchar(100) NOT NULL,
  `emailEstudiante` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '$2y$10$V0vS8U8U8U8U8U8U8U8U8eR.v.H6p.6p.eR4p7v.v.H6p.6p.',
  `telefonoEstudiante` varchar(15),
  `dniEstudiante` varchar(12) NOT NULL,
  `fechaNacimientoEstudiante` date,
  `fechaAltaEstudiante` date,
  `direccionEstudiante` varchar(200),
  `ciudadEstudiante` varchar(80),
  `codigoPostalEstudiante` varchar(10),
  `observacionesEstudiante` text,
  `idCiclo` int(11),
  `curso` enum('Grado Medio','Grado Superior'),
  `archivoTFG` varchar(255),
  `tituloTFG` varchar(255),
  `fechaSubidaTFG` datetime,
  `fcm_token` text,
  PRIMARY KEY (`idEstudiante`),
  UNIQUE KEY `uk_email_est` (`emailEstudiante`),
  CONSTRAINT `fk_estudiantes_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `directores` (
  `idDirector` int(11) NOT NULL AUTO_INCREMENT,
  `nombreDirector` varchar(150) NOT NULL,
  `emailDirector` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '$2y$10$V0vS8U8U8U8U8U8U8U8U8eR.v.H6p.6p.eR4p7v.v.H6p.6p.',
  `telefonoDirector` varchar(20),
  `dniDirector` varchar(20) NOT NULL,
  `fechaNacimientoDirector` date,
  `fechaAltaDirector` date,
  `direccionDirector` varchar(255),
  `ciudadDirector` varchar(100),
  `codigoPostalDirector` varchar(10),
  `observacionesDirector` text,
  `fcm_token` text,
  PRIMARY KEY (`idDirector`),
  UNIQUE KEY `uk_email_dir` (`emailDirector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `retos` (
  `idReto` int(11) NOT NULL AUTO_INCREMENT,
  `nombreReto` varchar(150) NOT NULL,
  `fechaInicio` date NOT NULL,
  `fechaFin` date NOT NULL,
  `horasReto` int(11) NOT NULL,
  PRIMARY KEY (`idReto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `modulo_reto` (
  `idModulo` int(11) NOT NULL,
  `idReto` int(11) NOT NULL,
  PRIMARY KEY (`idModulo`, `idReto`),
  CONSTRAINT `fk_mr_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_mr_reto` FOREIGN KEY (`idReto`) REFERENCES `retos` (`idReto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `calificaciones_retos` (
  `idCalificacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `idReto` int(11) NOT NULL,
  `nota` decimal(4,2) NOT NULL,
  PRIMARY KEY (`idCalificacion`),
  CONSTRAINT `fk_cr_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_cr_reto` FOREIGN KEY (`idReto`) REFERENCES `retos` (`idReto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `calificaciones_modulos` (
  `idCalificacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL,
  `nota_1ev` decimal(4,2),
  `nota_1final` decimal(4,2),
  `nota_2ev` decimal(4,2),
  `nota_2final` decimal(4,2),
  `observaciones` text,
  PRIMARY KEY (`idCalificacion`),
  CONSTRAINT `fk_cm_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_cm_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `calificaciones_tfg` (
  `idCalificacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `nota` decimal(4,2) NOT NULL,
  `observaciones` text,
  PRIMARY KEY (`idCalificacion`),
  UNIQUE KEY `uk_est_tfg` (`idEstudiante`),
  CONSTRAINT `fk_ctfg_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `dispositivos` (
  `idDispositivo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreDispositivo` varchar(100) NOT NULL,
  `numeroSerie` varchar(100) NOT NULL,
  `estadoDispositivo` enum('disponible','prestado'),
  PRIMARY KEY (`idDispositivo`),
  UNIQUE KEY `uk_serie` (`numeroSerie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `prestamos` (
  `idPrestamo` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `numeroSerie` varchar(100) NOT NULL,
  `fechaPrestamo` date NOT NULL,
  `fechaDevolucion` date,
  `estadoPrestamo` enum('en curso','devuelto'),
  PRIMARY KEY (`idPrestamo`),
  CONSTRAINT `fk_pres_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `anuncios` (
  `idAnuncio` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `fechaAnuncio` datetime DEFAULT CURRENT_TIMESTAMP,
  `fechaExpiracion` date NOT NULL,
  `dirigidoA` enum('todos','estudiantes','profesores'),
  PRIMARY KEY (`idAnuncio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `reclamaciones` (
  `idReclamacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11),
  `idProfesor` int(11),
  `emisor_rol` enum('estudiante','profesor','admin'),
  `asunto` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` datetime NOT NULL,
  `estadoReclamacion` enum('pendiente','atendido'),
  `leido` tinyint(1),
  `respuesta` text,
  PRIMARY KEY (`idReclamacion`),
  CONSTRAINT `fk_rec_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_rec_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pagos` (
  `idPago` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fechaPago` date NOT NULL,
  `fechaProximoPago` date NOT NULL,
  `tipoPago` enum('mensual','trimestral','semestral','unico'),
  `comprobante` varchar(255),
  PRIMARY KEY (`idPago`),
  CONSTRAINT `fk_pag_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `eventos` (
  `idEvento` int(11) NOT NULL AUTO_INCREMENT,
  `tituloEvento` varchar(150) NOT NULL,
  `descripcionEvento` text,
  `fechaEvento` date NOT NULL,
  `horaEvento` time,
  `ubicacionEvento` varchar(150),
  PRIMARY KEY (`idEvento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ciclo_profesor` (
  `idCiclo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  PRIMARY KEY (`idCiclo`, `idProfesor`),
  CONSTRAINT `fk_rel_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE,
  CONSTRAINT `fk_rel_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `modulo_profesor` (
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  PRIMARY KEY (`idModulo`, `idProfesor`),
  CONSTRAINT `fk_relm_mod` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_relm_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- MÓDULO AULA DIGITAL
-- ============================================================

CREATE TABLE `aula_carpetas` (
  `idCarpeta` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#0ea5e9',
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idCarpeta`),
  CONSTRAINT `fk_aulacarp_mod`  FOREIGN KEY (`idModulo`)   REFERENCES `modulos`    (`idModulo`)   ON DELETE CASCADE,
  CONSTRAINT `fk_aulacarp_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `aula_archivos` (
  `idArchivo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreArchivo` varchar(255) NOT NULL,
  `nombreOriginal` varchar(255) NOT NULL,
  `extension` enum('pdf','docx','txt') NOT NULL,
  `tamanio` int(11) DEFAULT 0,
  `descripcion` varchar(500) DEFAULT NULL,
  `idCarpeta` int(11) DEFAULT NULL,
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `fechaSubida` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idArchivo`),
  CONSTRAINT `fk_aulaarch_carp` FOREIGN KEY (`idCarpeta`)  REFERENCES `aula_carpetas` (`idCarpeta`) ON DELETE SET NULL,
  CONSTRAINT `fk_aulaarch_mod`  FOREIGN KEY (`idModulo`)   REFERENCES `modulos`    (`idModulo`)   ON DELETE CASCADE,
  CONSTRAINT `fk_aulaarch_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `aula_tareas` (
  `idTarea` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `archivoAdjunto` varchar(255) DEFAULT NULL,
  `publicado` tinyint(1) NOT NULL DEFAULT 1,
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idTarea`),
  CONSTRAINT `fk_aulatar_mod`  FOREIGN KEY (`idModulo`)   REFERENCES `modulos`    (`idModulo`)   ON DELETE CASCADE,
  CONSTRAINT `fk_aulatar_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `aula_entregas` (
  `idEntrega` int(11) NOT NULL AUTO_INCREMENT,
  `idTarea` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `archivoEntrega` varchar(255) DEFAULT NULL,
  `respuesta` text DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `fechaEntrega` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nota` decimal(4,2) DEFAULT NULL,
  `estado` enum('enviada','corregida') NOT NULL DEFAULT 'enviada',
  PRIMARY KEY (`idEntrega`),
  UNIQUE KEY `uk_aula_entrega` (`idTarea`,`idEstudiante`),
  CONSTRAINT `fk_aulaentr_tar` FOREIGN KEY (`idTarea`)      REFERENCES `aula_tareas`    (`idTarea`)      ON DELETE CASCADE,
  CONSTRAINT `fk_aulaentr_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes`    (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `aula_versiones_entrega` (
  `idVersion` int(11) NOT NULL AUTO_INCREMENT,
  `idTarea` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `archivoEntrega` varchar(255) DEFAULT NULL,
  `respuesta` text DEFAULT NULL,
  `version` int(11) NOT NULL,
  `fechaVersion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idVersion`),
  CONSTRAINT `fk_aulavers_tar` FOREIGN KEY (`idTarea`)      REFERENCES `aula_tareas` (`idTarea`)      ON DELETE CASCADE,
  CONSTRAINT `fk_aulavers_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `aula_comentarios` (
  `idComentario` int(11) NOT NULL AUTO_INCREMENT,
  `idEntrega` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `tipoUsuario` enum('profesor','estudiante') NOT NULL,
  `mensaje` text NOT NULL,
  `archivoCorreccion` varchar(255) DEFAULT NULL,
  `fechaComentario` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idComentario`),
  CONSTRAINT `fk_aulacomen_entr` FOREIGN KEY (`idEntrega`) REFERENCES `aula_entregas` (`idEntrega`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `aula_notificaciones` (
  `idNotificacion` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuario` int(11) NOT NULL,
  `tipoUsuario` enum('profesor','estudiante','admin') NOT NULL,
  `tipo` enum('archivo_subido','entrega_enviada','correccion','comentario') NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text DEFAULT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `idReferencia` int(11) DEFAULT NULL,
  `tipoReferencia` varchar(50) DEFAULT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idNotificacion`),
  KEY `idx_aulanotif` (`idUsuario`,`tipoUsuario`,`leida`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `carpetas_ejercicios` (
  `idCarpeta` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#0ea5e9',
  `icono` varchar(30) NOT NULL DEFAULT 'fa-folder',
  `idProfesor` int(11) NOT NULL,
  `idCiclo` int(11) NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idCarpeta`),
  CONSTRAINT `fk_carp_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE,
  CONSTRAINT `fk_carp_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ejercicios` (
  `idEjercicio` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `idCarpeta` int(11) DEFAULT NULL,
  `idProfesor` int(11) NOT NULL,
  `idCiclo` int(11) NOT NULL,
  `fechaLimite` datetime DEFAULT NULL,
  `archivoAdjunto` varchar(255) DEFAULT NULL,
  `publicado` tinyint(1) NOT NULL DEFAULT 1,
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idEjercicio`),
  CONSTRAINT `fk_ej_carp` FOREIGN KEY (`idCarpeta`) REFERENCES `carpetas_ejercicios` (`idCarpeta`) ON DELETE SET NULL,
  CONSTRAINT `fk_ej_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE,
  CONSTRAINT `fk_ej_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `entregas_ejercicios` (
  `idEntrega` int(11) NOT NULL AUTO_INCREMENT,
  `idEjercicio` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `respuesta` text DEFAULT NULL,
  `archivoEntrega` varchar(255) DEFAULT NULL,
  `fechaEntrega` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nota` decimal(4,2) DEFAULT NULL,
  `comentarioProfesor` text DEFAULT NULL,
  `estado` enum('entregado','calificado') NOT NULL DEFAULT 'entregado',
  PRIMARY KEY (`idEntrega`),
  UNIQUE KEY `uk_entrega_unica` (`idEjercicio`,`idEstudiante`),
  CONSTRAINT `fk_entr_ej` FOREIGN KEY (`idEjercicio`) REFERENCES `ejercicios` (`idEjercicio`) ON DELETE CASCADE,
  CONSTRAINT `fk_entr_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `auditoria` (
  `idAuditoria` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuario` int(11) NOT NULL,
  `tipoUsuario` enum('admin','profesor','estudiante') NOT NULL,
  `accion` varchar(80) NOT NULL,
  `tabla` varchar(60) DEFAULT NULL,
  `detalles` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAuditoria`),
  KEY `idx_auditoria_usuario` (`idUsuario`, `tipoUsuario`),
  KEY `idx_auditoria_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `login_intentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `intentos` tinyint(3) NOT NULL DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL,
  `ultimo_intento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `aula_analytics` (
  `idAnalytics` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuario` int(11) NOT NULL,
  `tipoUsuario` enum('estudiante','profesor') NOT NULL,
  `accion` varchar(50) NOT NULL,
  `idModulo` int(11) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `userAgent` text,
  `metadatos` json DEFAULT NULL,
  `fechaCreacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAnalytics`),
  KEY `idx_usuario` (`idUsuario`),
  KEY `idx_modulo` (`idModulo`),
  KEY `idx_accion` (`accion`),
  KEY `idx_fecha` (`fechaCreacion`),
  KEY `idx_usuario_modulo` (`idUsuario`,`idModulo`),
  KEY `idx_modulo_accion_fecha` (`idModulo`, `accion`, `fechaCreacion`),
  KEY `idx_usuario_tipo_fecha` (`idUsuario`, `tipoUsuario`, `fechaCreacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `aula_sesiones_vivas` (
  `idSesion` int(11) NOT NULL AUTO_INCREMENT,
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text,
  `fechaSesion` date NOT NULL,
  `horaSesion` time NOT NULL,
  `enlaceReunion` varchar(500),
  `plataforma` varchar(100),
  `estado` enum('programada','en_vivo','finalizada') NOT NULL DEFAULT 'programada',
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idSesion`),
  KEY `idx_modulo` (`idModulo`),
  KEY `idx_profesor` (`idProfesor`),
  KEY `idx_fecha` (`fechaSesion`),
  CONSTRAINT `fk_aulasesion_mod` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulasesion_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `aula_asistencia_sesion` (
  `idAsistencia` int(11) NOT NULL AUTO_INCREMENT,
  `idSesion` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `horaUnion` time,
  `horaSalida` time,
  `duracion` int(11) COMMENT 'Duration in minutes',
  `presente` tinyint(1) NOT NULL DEFAULT 1,
  `fechaRegistro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAsistencia`),
  UNIQUE KEY `uk_sesion_estudiante` (`idSesion`,`idEstudiante`),
  KEY `idx_sesion` (`idSesion`),
  KEY `idx_estudiante` (`idEstudiante`),
  CONSTRAINT `fk_aulasis_sesion` FOREIGN KEY (`idSesion`) REFERENCES `aula_sesiones_vivas` (`idSesion`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulasis_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- Niveles formativos
INSERT INTO `niveles` (`idNivel`, `nombreNivel`) VALUES
(1, 'Grado Medio'),
(2, 'Grado Superior');

-- admin: admin@aulapro.com | 123456
INSERT INTO `directores` (`idDirector`, `nombreDirector`, `emailDirector`, `password`, `dniDirector`) VALUES
(1, 'Administrador', 'admin@aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '00000000T');

-- ============================================================
-- DATOS DE PRUEBA - CICLOS, PROFESORES, ESTUDIANTES, MÓDULOS
-- ============================================================

-- CICLOS (3 ciclos: DAW, DAM, ASIR - todos Grado Superior)
INSERT INTO `ciclos` (`idCiclo`, `nombreCiclo`, `abreviaturaCiclo`, `precioCiclo`, `idNivel`) VALUES
(1, 'Desarrollo de Aplicaciones Web', 'DAW', 1200.00, 2),
(2, 'Desarrollo de Aplicaciones Multiplataforma', 'DAM', 1200.00, 2),
(3, 'Administración de Sistemas Informáticos en Red', 'ASIR', 1200.00, 2);

-- PROFESORES (2 profesores con contraseña: 123456)
-- Password hash: $2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu
INSERT INTO `profesores` (`idProfesor`, `nombreProfesor`, `emailProfesor`, `password`, `telefonoProfesor`, `dniProfesor`, `fechaNacimientoProfesor`, `fechaAltaProfesor`, `direccionProfesor`, `ciudadProfesor`, `codigoPostalProfesor`) VALUES
(1, 'Juan García Martínez', 'juan.garcia@aulpro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '612345678', '12345678A', '1980-05-15', '2023-09-01', 'Calle Principal 123', 'Madrid', '28001'),
(2, 'María López Rodríguez', 'maria.lopez@aulpro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '623456789', '87654321B', '1985-03-22', '2023-09-01', 'Avenida Principal 456', 'Barcelona', '08002');

-- MÓDULOS PARA DAW (idCiclo=1)
INSERT INTO `modulos` (`idModulo`, `nombreModulo`, `horasMaximas`, `idCiclo`) VALUES
(1, 'Lenguajes de Marcas', 42, 1),
(2, 'Programación del Lado del Cliente', 126, 1),
(3, 'Bases de Datos', 84, 1),
(4, 'Programación del Lado del Servidor', 126, 1),
(5, 'Despliegue de Aplicaciones Web', 63, 1);

-- MÓDULOS PARA DAM (idCiclo=2)
INSERT INTO `modulos` (`idModulo`, `nombreModulo`, `horasMaximas`, `idCiclo`) VALUES
(6, 'Lenguajes de Programación', 105, 2),
(7, 'Fundamentos de Bases de Datos', 84, 2),
(8, 'Programación Multimedia', 105, 2),
(9, 'Acceso a Datos', 84, 2),
(10, 'Interfaces', 84, 2);

-- MÓDULOS PARA ASIR (idCiclo=3)
INSERT INTO `modulos` (`idModulo`, `nombreModulo`, `horasMaximas`, `idCiclo`) VALUES
(11, 'Planificación y Administración de Redes', 84, 3),
(12, 'Gestión e Instalación de Sistemas Operativos', 105, 3),
(13, 'Servicios en Red', 105, 3),
(14, 'Administración de Sistemas Gestores de Bases de Datos', 84, 3),
(15, 'Seguridad Informática', 105, 3);

-- ESTUDIANTES PARA DAW (idCiclo=1) - 3 estudiantes
-- Password: 123456
INSERT INTO `estudiantes` (`idEstudiante`, `nombreEstudiante`, `emailEstudiante`, `password`, `dniEstudiante`, `fechaNacimientoEstudiante`, `fechaAltaEstudiante`, `idCiclo`, `curso`) VALUES
(1, 'Carlos Sánchez López', 'carlos.sanchez@aulpro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '11111111C', '2004-06-10', '2023-09-01', 1, 'Grado Superior'),
(2, 'Laura Fernández García', 'laura.fernandez@aulpro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '22222222D', '2004-08-22', '2023-09-01', 1, 'Grado Superior'),
(3, 'Pablo Martínez Ruiz', 'pablo.martinez@aulpro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '33333333E', '2005-01-18', '2023-09-01', 1, 'Grado Superior');

-- ESTUDIANTES PARA DAM (idCiclo=2) - 3 estudiantes
INSERT INTO `estudiantes` (`idEstudiante`, `nombreEstudiante`, `emailEstudiante`, `password`, `dniEstudiante`, `fechaNacimientoEstudiante`, `fechaAltaEstudiante`, `idCiclo`, `curso`) VALUES
(4, 'Andrea Jiménez Torres', 'andrea.jimenez@aulpro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '44444444F', '2004-07-14', '2023-09-01', 2, 'Grado Superior'),
(5, 'David Moreno Pérez', 'david.moreno@aulpro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '55555555G', '2005-02-28', '2023-09-01', 2, 'Grado Superior'),
(6, 'Sofía González Blanco', 'sofia.gonzalez@aulpro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '66666666H', '2004-11-05', '2023-09-01', 2, 'Grado Superior');

-- ESTUDIANTES PARA ASIR (idCiclo=3) - 3 estudiantes
INSERT INTO `estudiantes` (`idEstudiante`, `nombreEstudiante`, `emailEstudiante`, `password`, `dniEstudiante`, `fechaNacimientoEstudiante`, `fechaAltaEstudiante`, `idCiclo`, `curso`) VALUES
(7, 'Alejandro Ramírez Santos', 'alejandro.ramirez@aulpro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '77777777I', '2004-09-12', '2023-09-01', 3, 'Grado Superior'),
(8, 'Cristina Díaz Muñoz', 'cristina.diaz@aulpro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '88888888J', '2005-03-30', '2023-09-01', 3, 'Grado Superior'),
(9, 'Roberto Vega Herrera', 'roberto.vega@aulpro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '99999999K', '2004-12-08', '2023-09-01', 3, 'Grado Superior');

-- RETOS (5 retos para toda la plataforma)
INSERT INTO `retos` (`idReto`, `nombreReto`, `fechaInicio`, `fechaFin`, `horasReto`) VALUES
(1, 'Reto HTML y CSS - Crear una página web responsiva', '2026-02-01', '2026-02-28', 20),
(2, 'Reto JavaScript - Validación de formularios', '2026-03-01', '2026-03-31', 25),
(3, 'Reto Base de Datos - Diseño de modelo relacional', '2026-04-01', '2026-04-30', 30),
(4, 'Reto Backend - API RESTful con PHP', '2026-05-01', '2026-05-31', 35),
(5, 'Reto Full Stack - Aplicación web completa', '2026-06-01', '2026-06-30', 50);

-- RELACIÓN MÓDULOS-RETOS (modulo_reto)
INSERT INTO `modulo_reto` (`idModulo`, `idReto`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(1, 5), (2, 5), (3, 5), (4, 5);

-- TABLA: Relación Profesor-Módulo
CREATE TABLE IF NOT EXISTS `profesor_modulo` (
  `idProfesor` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL,
  PRIMARY KEY (`idProfesor`, `idModulo`),
  CONSTRAINT `fk_profesor_modulo_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE,
  CONSTRAINT `fk_profesor_modulo_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ASIGNACIÓN DE PROFESORES A MÓDULOS
INSERT INTO `profesor_modulo` (`idProfesor`, `idModulo`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5),
(2, 6), (2, 7), (2, 8), (2, 9), (2, 10);

-- ============================================================
-- NOTAS SOBRE LOS DATOS DE PRUEBA
-- ============================================================
-- Profesores:
--   Juan García: juan.garcia@aulpro.com | 123456
--   María López: maria.lopez@aulpro.com | 123456
--
-- Ciclos: DAW, DAM, ASIR (todos Grado Superior)
--
-- Estudiantes (3 por ciclo, 9 total):
--   DAW (1-3):
--     - carlos.sanchez@aulpro.com | 123456
--     - laura.fernandez@aulpro.com | 123456
--     - pablo.martinez@aulpro.com | 123456
--   DAM (4-6):
--     - andrea.jimenez@aulpro.com | 123456
--     - david.moreno@aulpro.com | 123456
--     - sofia.gonzalez@aulpro.com | 123456
--   ASIR (7-9):
--     - alejandro.ramirez@aulpro.com | 123456
--     - cristina.diaz@aulpro.com | 123456
--     - roberto.vega@aulpro.com | 123456
--
-- Módulos: 5 por ciclo (15 total)
-- Retos: 5 retos generales asignados a módulos
-- ============================================================
