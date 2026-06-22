-- ============================================================
-- AulaPro — Base de datos completa
-- Importar en phpMyAdmin: selecciona la BD y usa la pestaña SQL
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ── Limpiar tablas existentes ────────────────────────────────
DROP TABLE IF EXISTS `chat_mensajes`;
DROP TABLE IF EXISTS `chat_conversaciones`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `verificaciones_log`;
DROP TABLE IF EXISTS `boletines_log`;
DROP TABLE IF EXISTS `horario_franjas`;
DROP TABLE IF EXISTS `horarios`;
DROP TABLE IF EXISTS `aulas`;
DROP TABLE IF EXISTS `account_lockout`;
DROP TABLE IF EXISTS `rate_limits`;
DROP TABLE IF EXISTS `login_intentos`;
DROP TABLE IF EXISTS `auditoria`;
DROP TABLE IF EXISTS `entregas_ejercicios`;
DROP TABLE IF EXISTS `ejercicios`;
DROP TABLE IF EXISTS `carpetas_ejercicios`;
DROP TABLE IF EXISTS `aula_almacenamiento_ciclo`;
DROP TABLE IF EXISTS `aula_archivo_accesos`;
DROP TABLE IF EXISTS `aula_favoritos`;
DROP TABLE IF EXISTS `aula_archivo_versiones`;
DROP TABLE IF EXISTS `aula_asistencia_sesion`;
DROP TABLE IF EXISTS `aula_sesiones_vivas`;
DROP TABLE IF EXISTS `aula_analytics`;
DROP TABLE IF EXISTS `aula_notificaciones`;
DROP TABLE IF EXISTS `aula_comentarios`;
DROP TABLE IF EXISTS `aula_versiones_entrega`;
DROP TABLE IF EXISTS `aula_entregas`;
DROP TABLE IF EXISTS `aula_tareas`;
DROP TABLE IF EXISTS `aula_archivos`;
DROP TABLE IF EXISTS `aula_carpetas`;
DROP TABLE IF EXISTS `modulo_profesor`;
DROP TABLE IF EXISTS `ciclo_profesor`;
DROP TABLE IF EXISTS `eventos`;
DROP TABLE IF EXISTS `pagos`;
DROP TABLE IF EXISTS `reclamaciones`;
DROP TABLE IF EXISTS `anuncios`;
DROP TABLE IF EXISTS `prestamos`;
DROP TABLE IF EXISTS `dispositivos`;
DROP TABLE IF EXISTS `calificaciones_tfg`;
DROP TABLE IF EXISTS `calificaciones_modulos`;
DROP TABLE IF EXISTS `calificaciones_retos`;
DROP TABLE IF EXISTS `modulo_reto`;
DROP TABLE IF EXISTS `reto_archivos`;
DROP TABLE IF EXISTS `retos`;
DROP TABLE IF EXISTS `pre_matricula_archivos`;
DROP TABLE IF EXISTS `pre_matriculas`;
DROP TABLE IF EXISTS `estudiante_tutor`;
DROP TABLE IF EXISTS `tutores`;
DROP TABLE IF EXISTS `estudiantes`;
DROP TABLE IF EXISTS `profesores`;
DROP TABLE IF EXISTS `directores`;
DROP TABLE IF EXISTS `modulos`;
DROP TABLE IF EXISTS `ciclos`;
DROP TABLE IF EXISTS `niveles`;
DROP TABLE IF EXISTS `configuracion_centro`;

-- ── Tablas ───────────────────────────────────────────────────

-- 1. Niveles
CREATE TABLE `niveles` (
  `idNivel` int(11) NOT NULL AUTO_INCREMENT,
  `nombreNivel` varchar(50) NOT NULL,
  PRIMARY KEY (`idNivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Ciclos
CREATE TABLE `ciclos` (
  `idCiclo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreCiclo` varchar(100) NOT NULL,
  `abreviaturaCiclo` varchar(10) NOT NULL,
  `precioCiclo` decimal(10,2),
  `idNivel` int(11),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fechaArchivado` datetime DEFAULT NULL,
  PRIMARY KEY (`idCiclo`),
  KEY `idx_ciclo_nivel` (`idNivel`),
  CONSTRAINT `fk_ciclos_niveles` FOREIGN KEY (`idNivel`) REFERENCES `niveles` (`idNivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Módulos
CREATE TABLE `modulos` (
  `idModulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreModulo` varchar(120) NOT NULL,
  `horasMaximas` int(11),
  `idCiclo` int(11) NOT NULL,
  `cursoAnio` varchar(10) DEFAULT NULL,
  `creditosECTS` int(3) DEFAULT NULL,
  PRIMARY KEY (`idModulo`),
  KEY `idx_modulo_ciclo` (`idCiclo`),
  CONSTRAINT `fk_modulos_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Profesores
CREATE TABLE `profesores` (
  `idProfesor` int(11) NOT NULL AUTO_INCREMENT,
  `nombreProfesor` varchar(100) NOT NULL,
  `emailProfesor` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1,
  `pwd_changed_at` datetime DEFAULT NULL,
  `telefonoProfesor` varchar(15),
  `dniProfesor` varchar(12),
  `fechaNacimientoProfesor` date,
  `fechaAltaProfesor` date,
  `direccionProfesor` varchar(200),
  `ciudadProfesor` varchar(80),
  `codigoPostalProfesor` varchar(10),
  `observacionesProfesor` text,
  `fcm_token` text,
  `esTutor` tinyint(1) NOT NULL DEFAULT 0,
  `idCicloTutor` int(11) DEFAULT NULL,
  PRIMARY KEY (`idProfesor`),
  UNIQUE KEY `uk_email_prof` (`emailProfesor`),
  KEY `idx_prof_dni` (`dniProfesor`),
  CONSTRAINT `fk_prof_ciclo_tutor` FOREIGN KEY (`idCicloTutor`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Estudiantes
CREATE TABLE `estudiantes` (
  `idEstudiante` int(11) NOT NULL AUTO_INCREMENT,
  `nombreEstudiante` varchar(100) NOT NULL,
  `emailEstudiante` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1,
  `pwd_changed_at` datetime DEFAULT NULL,
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
  `anioEstudio` varchar(20) DEFAULT NULL,
  `archivoTFG` varchar(255),
  `tituloTFG` varchar(255),
  `fechaSubidaTFG` datetime,
  `fcm_token` text,
  PRIMARY KEY (`idEstudiante`),
  UNIQUE KEY `uk_email_est` (`emailEstudiante`),
  UNIQUE KEY `uk_dni_est` (`dniEstudiante`),
  KEY `idx_est_ciclo` (`idCiclo`),
  CONSTRAINT `fk_estudiantes_ciclos` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Directores (administradores)
CREATE TABLE `directores` (
  `idDirector` int(11) NOT NULL AUTO_INCREMENT,
  `nombreDirector` varchar(150) NOT NULL,
  `emailDirector` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1,
  `pwd_changed_at` datetime DEFAULT NULL,
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `mfa_secret` varchar(64) DEFAULT NULL,
  `mfa_backup_codes` text DEFAULT NULL,
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
  UNIQUE KEY `uk_email_dir` (`emailDirector`),
  UNIQUE KEY `uk_dni_dir` (`dniDirector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tutores (familias)
CREATE TABLE `tutores` (
  `idTutor` int(11) NOT NULL AUTO_INCREMENT,
  `nombreTutor` varchar(100) NOT NULL,
  `emailTutor` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1,
  `pwd_changed_at` datetime DEFAULT NULL,
  `telefonoTutor` varchar(20),
  `dniTutor` varchar(20) NOT NULL,
  `fcm_token` text,
  PRIMARY KEY (`idTutor`),
  UNIQUE KEY `uk_email_tutor` (`emailTutor`),
  UNIQUE KEY `uk_dni_tutor` (`dniTutor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Relación Estudiante–Tutor
CREATE TABLE `estudiante_tutor` (
  `idEstudiante` int(11) NOT NULL,
  `idTutor` int(11) NOT NULL,
  `parentesco` enum('Padre','Madre','Tutor Legal') NOT NULL DEFAULT 'Padre',
  PRIMARY KEY (`idEstudiante`,`idTutor`),
  CONSTRAINT `fk_et_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_et_tutor`      FOREIGN KEY (`idTutor`)      REFERENCES `tutores`     (`idTutor`)      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Retos
CREATE TABLE `retos` (
  `idReto` int(11) NOT NULL AUTO_INCREMENT,
  `nombreReto` varchar(150) NOT NULL,
  `fechaInicio` date NOT NULL,
  `fechaFin` date NOT NULL,
  `horasReto` int(11) NOT NULL,
  PRIMARY KEY (`idReto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Módulo–Reto
CREATE TABLE `modulo_reto` (
  `idModulo` int(11) NOT NULL,
  `idReto` int(11) NOT NULL,
  PRIMARY KEY (`idModulo`, `idReto`),
  CONSTRAINT `fk_mr_modulo` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_mr_reto`   FOREIGN KEY (`idReto`)   REFERENCES `retos`   (`idReto`)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Archivos adjuntos a retos
CREATE TABLE `reto_archivos` (
  `idArchivo` int(11) NOT NULL AUTO_INCREMENT,
  `idReto` int(11) NOT NULL,
  `nombreArchivo` varchar(255) NOT NULL,
  `rutaArchivo` varchar(255) NOT NULL,
  `tipoArchivo` varchar(50),
  `fechaSubida` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idArchivo`),
  CONSTRAINT `fk_retoarch_reto` FOREIGN KEY (`idReto`) REFERENCES `retos` (`idReto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Calificaciones retos
CREATE TABLE `calificaciones_retos` (
  `idCalificacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `idReto` int(11) NOT NULL,
  `nota` decimal(4,2) NOT NULL,
  PRIMARY KEY (`idCalificacion`),
  KEY `idx_cal_reto_est`  (`idEstudiante`),
  KEY `idx_cal_reto_reto` (`idReto`),
  CONSTRAINT `fk_cr_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_cr_reto`       FOREIGN KEY (`idReto`)       REFERENCES `retos`       (`idReto`)       ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Calificaciones módulos
CREATE TABLE `calificaciones_modulos` (
  `idCalificacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL,
  `nota_1ev`    decimal(4,2) DEFAULT NULL,
  `nota_1final` decimal(4,2) DEFAULT NULL,
  `nota_2ev`    decimal(4,2) DEFAULT NULL,
  `nota_2final` decimal(4,2) DEFAULT NULL,
  `estado_1ev`    varchar(2) DEFAULT NULL,
  `estado_1final` varchar(2) DEFAULT NULL,
  `estado_2ev`    varchar(2) DEFAULT NULL,
  `estado_2final` varchar(2) DEFAULT NULL,
  `observaciones` text,
  PRIMARY KEY (`idCalificacion`),
  UNIQUE KEY `uk_est_mod` (`idEstudiante`, `idModulo`),
  KEY `idx_cm_est` (`idEstudiante`),
  KEY `idx_cm_mod` (`idModulo`),
  CONSTRAINT `fk_cm_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_cm_modulo`     FOREIGN KEY (`idModulo`)     REFERENCES `modulos`     (`idModulo`)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Calificaciones TFG
CREATE TABLE `calificaciones_tfg` (
  `idCalificacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `nota` decimal(4,2) NOT NULL,
  `observaciones` text,
  PRIMARY KEY (`idCalificacion`),
  UNIQUE KEY `uk_est_tfg` (`idEstudiante`),
  CONSTRAINT `fk_ctfg_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Dispositivos (inventario)
CREATE TABLE `dispositivos` (
  `idDispositivo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreDispositivo` varchar(100) NOT NULL,
  `numeroSerie` varchar(100) NOT NULL,
  `estadoDispositivo` enum('disponible','prestado') DEFAULT 'disponible',
  PRIMARY KEY (`idDispositivo`),
  UNIQUE KEY `uk_serie` (`numeroSerie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. Préstamos
CREATE TABLE `prestamos` (
  `idPrestamo` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `numeroSerie` varchar(100) NOT NULL,
  `fechaPrestamo` date NOT NULL,
  `fechaDevolucion` date,
  `estadoPrestamo` enum('en curso','devuelto') DEFAULT 'en curso',
  PRIMARY KEY (`idPrestamo`),
  KEY `idx_pres_est`   (`idEstudiante`),
  KEY `idx_pres_serie` (`numeroSerie`),
  CONSTRAINT `fk_pres_estudiante`  FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes`  (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_pres_dispositivo` FOREIGN KEY (`numeroSerie`)  REFERENCES `dispositivos` (`numeroSerie`)  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. Anuncios
CREATE TABLE `anuncios` (
  `idAnuncio` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `fechaAnuncio` datetime DEFAULT CURRENT_TIMESTAMP,
  `fechaExpiracion` date NOT NULL,
  `dirigidoA` enum('todos','estudiantes','profesores','tutores') DEFAULT 'todos',
  PRIMARY KEY (`idAnuncio`),
  KEY `idx_anuncio_fecha` (`fechaAnuncio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. Reclamaciones (mensajería formal)
CREATE TABLE `reclamaciones` (
  `idReclamacion` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11),
  `idProfesor` int(11),
  `emisor_rol` enum('estudiante','profesor','admin') NOT NULL,
  `asunto` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estadoReclamacion` enum('pendiente','atendido') DEFAULT 'pendiente',
  `leido` tinyint(1) DEFAULT 0,
  `respuesta` text,
  `id_parent` int(11) DEFAULT NULL,
  PRIMARY KEY (`idReclamacion`),
  KEY `idx_rec_est`         (`idEstudiante`),
  KEY `idx_rec_prof`        (`idProfesor`),
  KEY `idx_recl_prof_leido` (`idProfesor`, `leido`),
  KEY `idx_recl_est_leido`  (`idEstudiante`, `leido`),
  KEY `idx_recl_parent`     (`id_parent`),
  CONSTRAINT `fk_rec_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_rec_profesor`   FOREIGN KEY (`idProfesor`)   REFERENCES `profesores`  (`idProfesor`)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. Pagos
CREATE TABLE `pagos` (
  `idPago` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fechaPago` date NOT NULL,
  `fechaProximoPago` date NOT NULL,
  `tipoPago` enum('mensual','trimestral','semestral','unico'),
  `comprobante` varchar(255),
  PRIMARY KEY (`idPago`),
  KEY `idx_pago_est` (`idEstudiante`),
  CONSTRAINT `fk_pag_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20. Eventos
CREATE TABLE `eventos` (
  `idEvento` int(11) NOT NULL AUTO_INCREMENT,
  `tituloEvento` varchar(150) NOT NULL,
  `descripcionEvento` text,
  `fechaEvento` date NOT NULL,
  `horaEvento` time,
  `ubicacionEvento` varchar(150),
  PRIMARY KEY (`idEvento`),
  KEY `idx_evento_fecha` (`fechaEvento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 21. Ciclo–Profesor
CREATE TABLE `ciclo_profesor` (
  `idCiclo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  PRIMARY KEY (`idCiclo`, `idProfesor`),
  CONSTRAINT `fk_rel_ciclo` FOREIGN KEY (`idCiclo`)    REFERENCES `ciclos`     (`idCiclo`)    ON DELETE CASCADE,
  CONSTRAINT `fk_rel_prof`  FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 22. Módulo–Profesor
CREATE TABLE `modulo_profesor` (
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  PRIMARY KEY (`idModulo`, `idProfesor`),
  CONSTRAINT `fk_relm_mod`  FOREIGN KEY (`idModulo`)   REFERENCES `modulos`    (`idModulo`)   ON DELETE CASCADE,
  CONSTRAINT `fk_relm_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Pre-matrícula ─────────────────────────────────────────────

-- 23. Solicitudes de pre-matriculación
CREATE TABLE `pre_matriculas` (
  `idPreMatricula` int(11) NOT NULL AUTO_INCREMENT,
  `dni` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20),
  `idCiclo` int(11) NOT NULL,
  `curso` varchar(50) DEFAULT '1º',
  `nombreTutor` varchar(100),
  `dniTutor` varchar(20),
  `emailTutor` varchar(100),
  `telefonoTutor` varchar(20),
  `parentescoTutor` varchar(50),
  `estado` enum('PENDIENTE','EN_REVISION','SUBSANACION','ADMITIDO','RECHAZADO') DEFAULT 'PENDIENTE',
  `observaciones` text,
  `fechaSolicitud` datetime DEFAULT CURRENT_TIMESTAMP,
  `fechaActualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idPreMatricula`),
  UNIQUE KEY `uk_pm_dni` (`dni`),
  CONSTRAINT `fk_pm_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 24. Archivos adjuntos a pre-matrículas
CREATE TABLE `pre_matricula_archivos` (
  `idArchivo` int(11) NOT NULL AUTO_INCREMENT,
  `idPreMatricula` int(11) NOT NULL,
  `tipoDocumento` varchar(50) NOT NULL,
  `nombreArchivo` varchar(255) NOT NULL,
  `rutaArchivo` varchar(255) NOT NULL,
  `fechaSubida` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idArchivo`),
  CONSTRAINT `fk_pma_pm` FOREIGN KEY (`idPreMatricula`) REFERENCES `pre_matriculas` (`idPreMatricula`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Aula Digital ─────────────────────────────────────────────

CREATE TABLE `aula_carpetas` (
  `idCarpeta` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `idPadre` int(11) DEFAULT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#0ea5e9',
  `icono` varchar(30) NOT NULL DEFAULT 'fa-folder',
  `fijado` tinyint(1) NOT NULL DEFAULT 0,
  `eliminado` tinyint(1) NOT NULL DEFAULT 0,
  `fechaEliminacion` datetime DEFAULT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idCarpeta`),
  KEY `idx_aula_carp_mod`   (`idModulo`),
  KEY `idx_aula_carp_padre` (`idPadre`),
  CONSTRAINT `fk_aulacarp_mod`   FOREIGN KEY (`idModulo`)   REFERENCES `modulos`       (`idModulo`)   ON DELETE CASCADE,
  CONSTRAINT `fk_aulacarp_prof`  FOREIGN KEY (`idProfesor`) REFERENCES `profesores`    (`idProfesor`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulacarp_padre` FOREIGN KEY (`idPadre`)    REFERENCES `aula_carpetas` (`idCarpeta`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `aula_archivos` (
  `idArchivo` int(11) NOT NULL AUTO_INCREMENT,
  `nombreArchivo` varchar(255) NOT NULL,
  `nombreOriginal` varchar(255) NOT NULL,
  `extension` varchar(10) NOT NULL,
  `tamanio` int(11) DEFAULT 0,
  `descripcion` varchar(500) DEFAULT NULL,
  `idCarpeta` int(11) DEFAULT NULL,
  `idModulo` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `fijado` tinyint(1) NOT NULL DEFAULT 0,
  `eliminado` tinyint(1) NOT NULL DEFAULT 0,
  `fechaEliminacion` datetime DEFAULT NULL,
  `fechaSubida` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idArchivo`),
  KEY `idx_aula_arch_mod`  (`idModulo`),
  KEY `idx_aula_arch_carp` (`idCarpeta`),
  KEY `idx_aula_arch_elim` (`eliminado`),
  CONSTRAINT `fk_aulaarch_carp` FOREIGN KEY (`idCarpeta`)  REFERENCES `aula_carpetas` (`idCarpeta`)  ON DELETE SET NULL,
  CONSTRAINT `fk_aulaarch_mod`  FOREIGN KEY (`idModulo`)   REFERENCES `modulos`       (`idModulo`)   ON DELETE CASCADE,
  CONSTRAINT `fk_aulaarch_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores`    (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY `idx_aula_tarea_mod` (`idModulo`),
  CONSTRAINT `fk_aulatar_mod`  FOREIGN KEY (`idModulo`)   REFERENCES `modulos`    (`idModulo`)   ON DELETE CASCADE,
  CONSTRAINT `fk_aulatar_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `comentarioCalificacion` text DEFAULT NULL,
  `archivoCorreccion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idEntrega`),
  UNIQUE KEY `uk_aula_entrega` (`idTarea`,`idEstudiante`),
  KEY `idx_aula_entr_est` (`idEstudiante`),
  CONSTRAINT `fk_aulaentr_tar` FOREIGN KEY (`idTarea`)      REFERENCES `aula_tareas`  (`idTarea`)      ON DELETE CASCADE,
  CONSTRAINT `fk_aulaentr_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes`  (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `aula_versiones_entrega` (
  `idVersion` int(11) NOT NULL AUTO_INCREMENT,
  `idTarea` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `archivoEntrega` varchar(255) DEFAULT NULL,
  `respuesta` text DEFAULT NULL,
  `version` int(11) NOT NULL,
  `fechaVersion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idVersion`),
  CONSTRAINT `fk_aulavers_tar` FOREIGN KEY (`idTarea`)      REFERENCES `aula_tareas`  (`idTarea`)      ON DELETE CASCADE,
  CONSTRAINT `fk_aulavers_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes`  (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY `idx_aula_notif_usr` (`idUsuario`, `tipoUsuario`, `leida`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY `idx_analytics_usr`   (`idUsuario`),
  KEY `idx_analytics_mod`   (`idModulo`),
  KEY `idx_analytics_fecha` (`fechaCreacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY `idx_sesion_mod`  (`idModulo`),
  KEY `idx_sesion_prof` (`idProfesor`),
  CONSTRAINT `fk_aulasesion_mod`  FOREIGN KEY (`idModulo`)   REFERENCES `modulos`    (`idModulo`)   ON DELETE CASCADE,
  CONSTRAINT `fk_aulasesion_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `aula_asistencia_sesion` (
  `idAsistencia` int(11) NOT NULL AUTO_INCREMENT,
  `idSesion` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `horaUnion` time,
  `horaSalida` time,
  `duracion` int(11),
  `presente` tinyint(1) NOT NULL DEFAULT 1,
  `fechaRegistro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAsistencia`),
  UNIQUE KEY `uk_sesion_estudiante` (`idSesion`,`idEstudiante`),
  CONSTRAINT `fk_aulasis_sesion` FOREIGN KEY (`idSesion`)     REFERENCES `aula_sesiones_vivas` (`idSesion`)     ON DELETE CASCADE,
  CONSTRAINT `fk_aulasis_est`    FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes`         (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `aula_archivo_versiones` (
  `idVersion` int(11) NOT NULL AUTO_INCREMENT,
  `idArchivo` int(11) NOT NULL,
  `nombreArchivo` varchar(255) NOT NULL,
  `nombreOriginal` varchar(255) NOT NULL,
  `extension` varchar(10) NOT NULL,
  `tamanio` int(11) DEFAULT 0,
  `version` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL,
  `fechaVersion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idVersion`),
  CONSTRAINT `fk_aulaver_arch` FOREIGN KEY (`idArchivo`)  REFERENCES `aula_archivos` (`idArchivo`)  ON DELETE CASCADE,
  CONSTRAINT `fk_aulaver_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores`    (`idProfesor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `aula_favoritos` (
  `idFavorito` int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) NOT NULL,
  `idArchivo` int(11) NOT NULL,
  `fechaMarcado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idFavorito`),
  UNIQUE KEY `uk_aulafav` (`idEstudiante`,`idArchivo`),
  CONSTRAINT `fk_aulafav_est`  FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes`   (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_aulafav_arch` FOREIGN KEY (`idArchivo`)    REFERENCES `aula_archivos` (`idArchivo`)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `aula_archivo_accesos` (
  `idAcceso` int(11) NOT NULL AUTO_INCREMENT,
  `idArchivo` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `tipo` enum('vista','descarga') NOT NULL DEFAULT 'vista',
  `fechaAcceso` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAcceso`),
  CONSTRAINT `fk_aulaacc_arch` FOREIGN KEY (`idArchivo`)    REFERENCES `aula_archivos` (`idArchivo`)    ON DELETE CASCADE,
  CONSTRAINT `fk_aulaacc_est`  FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes`   (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `aula_almacenamiento_ciclo` (
  `idCiclo` int(11) NOT NULL,
  `limiteBytes` bigint(20) NOT NULL DEFAULT 5368709120,
  PRIMARY KEY (`idCiclo`),
  CONSTRAINT `fk_aulaalm_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Ejercicios ───────────────────────────────────────────────

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
  CONSTRAINT `fk_carp_prof_ej`  FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE CASCADE,
  CONSTRAINT `fk_carp_ciclo_ej` FOREIGN KEY (`idCiclo`)    REFERENCES `ciclos`     (`idCiclo`)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY `idx_ej_ciclo_pub` (`idCiclo`, `publicado`),
  CONSTRAINT `fk_ej_carp_ej`  FOREIGN KEY (`idCarpeta`)  REFERENCES `carpetas_ejercicios` (`idCarpeta`)  ON DELETE SET NULL,
  CONSTRAINT `fk_ej_prof_ej`  FOREIGN KEY (`idProfesor`) REFERENCES `profesores`           (`idProfesor`) ON DELETE CASCADE,
  CONSTRAINT `fk_ej_ciclo_ej` FOREIGN KEY (`idCiclo`)    REFERENCES `ciclos`               (`idCiclo`)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  UNIQUE KEY `uk_entrega_unica_ej` (`idEjercicio`,`idEstudiante`),
  CONSTRAINT `fk_entr_ej_ej`  FOREIGN KEY (`idEjercicio`)  REFERENCES `ejercicios`  (`idEjercicio`)  ON DELETE CASCADE,
  CONSTRAINT `fk_entr_est_ej` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Seguridad ────────────────────────────────────────────────

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
  KEY `idx_auditoria_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `login_intentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `intentos` tinyint(3) NOT NULL DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL,
  `ultimo_intento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_resets` (
  `token` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tipo_usuario` enum('admin','profesor','estudiante','tutor') NOT NULL,
  `expires_at` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0,
  `creado_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`token`),
  KEY `idx_pr_email_tipo` (`email`, `tipo_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rate_limits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(64) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT 0,
  `window_start` int(10) unsigned NOT NULL,
  `blocked_until` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_scope_ip` (`scope`, `ip`),
  KEY `idx_blocked_until` (`blocked_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `account_lockout` (
  `email` varchar(190) NOT NULL,
  `intentos` int(10) unsigned NOT NULL DEFAULT 0,
  `window_start` int(10) unsigned NOT NULL,
  `locked_until` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Aulas físicas y horarios ─────────────────────────────────

CREATE TABLE `aulas` (
  `idAula` int(11) NOT NULL AUTO_INCREMENT,
  `planta` tinyint(4) NOT NULL,
  `numero` int(11) NOT NULL,
  `codigoAula` varchar(10) AS (CONCAT(`planta`, LPAD(`numero`, 2, '0'))) STORED,
  `nombreAula` varchar(60) DEFAULT NULL,
  `tipoAula` enum('teoria','laboratorio','taller','otro') NOT NULL DEFAULT 'teoria',
  `capacidad` int(11) DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idAula`),
  UNIQUE KEY `uk_aula_planta_numero` (`planta`,`numero`),
  UNIQUE KEY `uk_aula_codigo` (`codigoAula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `horarios` (
  `idHorario` int(11) NOT NULL AUTO_INCREMENT,
  `idCiclo` int(11) NOT NULL,
  `diaSemana` enum('Lunes','Martes','Miércoles','Jueves','Viernes') NOT NULL,
  `horaInicio` time NOT NULL,
  `horaFin` time NOT NULL,
  `idModulo` int(11) DEFAULT NULL,
  `idProfesor` int(11) DEFAULT NULL,
  `idAula` int(11) DEFAULT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idHorario`),
  UNIQUE KEY `uk_horario_celda`    (`idCiclo`,`diaSemana`,`horaInicio`),
  UNIQUE KEY `uk_horario_aula`     (`idAula`,`diaSemana`,`horaInicio`),
  UNIQUE KEY `uk_horario_profesor` (`idProfesor`,`diaSemana`,`horaInicio`),
  KEY `indice_horario_ciclo`   (`idCiclo`),
  KEY `indice_horario_modulo`  (`idModulo`),
  KEY `indice_horario_aula`    (`idAula`),
  CONSTRAINT `fk_horario_ciclo`    FOREIGN KEY (`idCiclo`)    REFERENCES `ciclos`     (`idCiclo`)    ON DELETE CASCADE,
  CONSTRAINT `fk_horario_modulo`   FOREIGN KEY (`idModulo`)   REFERENCES `modulos`    (`idModulo`)   ON DELETE SET NULL,
  CONSTRAINT `fk_horario_profesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE SET NULL,
  CONSTRAINT `fk_horario_aula`     FOREIGN KEY (`idAula`)     REFERENCES `aulas`      (`idAula`)     ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `horario_franjas` (
  `idFranja` int(11) NOT NULL AUTO_INCREMENT,
  `idCiclo` int(11) NOT NULL,
  `horaInicio` time NOT NULL,
  `horaFin` time NOT NULL,
  `esReceso` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idFranja`),
  UNIQUE KEY `uk_franja_ciclo_inicio` (`idCiclo`,`horaInicio`),
  CONSTRAINT `fk_franja_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Configuración del centro ─────────────────────────────────

CREATE TABLE `configuracion_centro` (
  `idConfig` int(11) NOT NULL DEFAULT 1,
  `nombreCentro` varchar(200) NOT NULL DEFAULT 'Centro de Formación Profesional',
  `codigoCentro` varchar(20) DEFAULT NULL,
  `direccionCentro` varchar(255) DEFAULT NULL,
  `ciudadCentro` varchar(100) DEFAULT NULL,
  `cpCentro` varchar(10) DEFAULT NULL,
  `telefonoCentro` varchar(20) DEFAULT NULL,
  `emailCentro` varchar(100) DEFAULT NULL,
  `cursoEscolar` varchar(20) DEFAULT NULL,
  `logoCentro` varchar(255) DEFAULT NULL,
  `logoGobierno1` varchar(255) DEFAULT NULL,
  `logoGobierno2` varchar(255) DEFAULT NULL,
  `textoLegal` text DEFAULT NULL,
  `nombreDirectorFirmante` varchar(150) DEFAULT NULL,
  `feature_prematricula` tinyint(1) NOT NULL DEFAULT 1,
  `feature_chat` tinyint(1) NOT NULL DEFAULT 1,
  `feature_inventario` tinyint(1) NOT NULL DEFAULT 1,
  `feature_subida_tfg` tinyint(1) NOT NULL DEFAULT 1,
  `feature_anuncios` tinyint(1) NOT NULL DEFAULT 1,
  `feature_eventos` tinyint(1) NOT NULL DEFAULT 1,
  `feature_retos` tinyint(1) NOT NULL DEFAULT 1,
  `feature_mensajes` tinyint(1) NOT NULL DEFAULT 1,
  `feature_pagos` tinyint(1) NOT NULL DEFAULT 1,
  `feature_gastos` tinyint(1) NOT NULL DEFAULT 1,
  `feature_informes` tinyint(1) NOT NULL DEFAULT 1,
  `feature_horario` tinyint(1) NOT NULL DEFAULT 1,
  `instance_status` enum('active','suspended') NOT NULL DEFAULT 'active',
  `suspension_message` text DEFAULT NULL,
  `saas_lock_features` tinyint(1) NOT NULL DEFAULT 0,
  `saas_message` text DEFAULT NULL,
  `saas_message_type` varchar(20) NOT NULL DEFAULT 'info',
  `saas_last_sync` datetime DEFAULT NULL,
  `license_token` text DEFAULT NULL,
  `license_token_exp` datetime DEFAULT NULL,
  PRIMARY KEY (`idConfig`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Boletines y verificaciones ───────────────────────────────

CREATE TABLE `boletines_log` (
  `serial` varchar(30) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `idCiclo` int(11) NOT NULL,
  `nombreEstudiante` varchar(255) NOT NULL,
  `nombreCiclo` varchar(255) NOT NULL,
  `cursoEscolar` varchar(20) NOT NULL,
  `fechaGeneracion` datetime DEFAULT CURRENT_TIMESTAMP,
  `scan_count` int(11) NOT NULL DEFAULT 0,
  `last_scan_at` datetime DEFAULT NULL,
  `last_scan_ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`serial`),
  KEY `idx_bl_estudiante` (`idEstudiante`),
  KEY `idx_bl_ciclo` (`idCiclo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `verificaciones_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serial_buscado` varchar(30) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `resultado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vl_ip_time` (`ip`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Chat ─────────────────────────────────────────────────────

CREATE TABLE `chat_conversaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_a_rol` enum('admin','profesor','estudiante','tutor') NOT NULL,
  `user_a_id`  int(11) NOT NULL,
  `user_b_rol` enum('admin','profesor','estudiante','tutor') NOT NULL,
  `user_b_id`  int(11) NOT NULL,
  `created_at`      datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_message_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_conv` (`user_a_rol`, `user_a_id`, `user_b_rol`, `user_b_id`),
  KEY `idx_user_a` (`user_a_rol`, `user_a_id`),
  KEY `idx_user_b` (`user_b_rol`, `user_b_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `chat_mensajes` (
  `id`              int(11) NOT NULL AUTO_INCREMENT,
  `conversacion_id` int(11) NOT NULL,
  `emisor_rol`      enum('admin','profesor','estudiante','tutor') NOT NULL,
  `emisor_id`       int(11) NOT NULL,
  `contenido`       text NOT NULL,
  `fecha`           datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `leido`           tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_conv_fecha` (`conversacion_id`, `fecha`),
  CONSTRAINT `fk_chat_conv` FOREIGN KEY (`conversacion_id`) REFERENCES `chat_conversaciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `asistencias` (
  `idAsistencia`  int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante`  int(11) NOT NULL,
  `idModulo`      int(11) NOT NULL,
  `idProfesor`    int(11) DEFAULT NULL,
  `fecha`         date NOT NULL,
  `estado`        enum('presente','ausente','retraso','justificado') NOT NULL DEFAULT 'presente',
  `observacion`   text DEFAULT NULL,
  `fechaRegistro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAsistencia`),
  UNIQUE KEY `uq_asistencia` (`idEstudiante`, `idModulo`, `fecha`),
  KEY `idx_asist_modulo_fecha` (`idModulo`, `fecha`),
  KEY `idx_asist_estudiante` (`idEstudiante`),
  CONSTRAINT `fk_asist_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE,
  CONSTRAINT `fk_asist_mod` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`idModulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_asist_prof` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`idProfesor`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `log_acciones` (
  `idLog`       int(11) NOT NULL AUTO_INCREMENT,
  `idAdmin`     int(11) DEFAULT NULL,
  `accion`      varchar(100) NOT NULL,
  `tabla`       varchar(80) NOT NULL,
  `idRegistro`  int(11) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `ip`          varchar(45) DEFAULT NULL,
  `fecha`       datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idLog`),
  KEY `idx_log_fecha` (`fecha`),
  KEY `idx_log_admin` (`idAdmin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rgpd_eliminaciones` (
  `id`           int(11) NOT NULL AUTO_INCREMENT,
  `idAdmin`      int(11) DEFAULT NULL,
  `entidad`      varchar(80) NOT NULL,
  `idRegistro`   int(11) NOT NULL,
  `descripcion`  varchar(255) DEFAULT NULL,
  `motivo`       text DEFAULT NULL,
  `datos_backup` longtext DEFAULT NULL,
  `ip`           varchar(45) DEFAULT NULL,
  `fecha`        datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rgpd_fecha` (`fecha`),
  CONSTRAINT `fk_rgpd_admin` FOREIGN KEY (`idAdmin`) REFERENCES `directores` (`idDirector`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `consentimientos` (
  `id`           int(11) NOT NULL AUTO_INCREMENT,
  `idEstudiante` int(11) DEFAULT NULL,
  `idTutor`      int(11) DEFAULT NULL,
  `tipo`         varchar(80) NOT NULL,
  `ip`           varchar(45) DEFAULT NULL,
  `userAgent`    varchar(255) DEFAULT NULL,
  `texto`        text DEFAULT NULL,
  `fecha`        datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_consentimiento_est` (`idEstudiante`),
  CONSTRAINT `fk_cons_est` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categorias_gasto` (
  `idCategoria`      int(11) NOT NULL AUTO_INCREMENT,
  `nombre`           varchar(100) NOT NULL,
  `presupuestoAnual` decimal(10,2) DEFAULT 0.00,
  `color`            varchar(20) DEFAULT '#6366f1',
  `activo`           tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idCategoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gastos` (
  `idGasto`             int(11) NOT NULL AUTO_INCREMENT,
  `idCategoria`         int(11) NOT NULL,
  `idCiclo`             int(11) DEFAULT NULL,
  `concepto`            varchar(255) NOT NULL,
  `importe`             decimal(10,2) NOT NULL,
  `fecha`               date NOT NULL,
  `tipoJustificante`    varchar(50) DEFAULT NULL,
  `numeroReferencia`    varchar(100) DEFAULT NULL,
  `archivoJustificante` varchar(255) DEFAULT NULL,
  `observaciones`       text DEFAULT NULL,
  `fechaRegistro`       datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idGasto`),
  KEY `idx_gasto_fecha` (`fecha`),
  KEY `idx_gasto_categoria` (`idCategoria`),
  CONSTRAINT `fk_gasto_cat` FOREIGN KEY (`idCategoria`) REFERENCES `categorias_gasto` (`idCategoria`),
  CONSTRAINT `fk_gasto_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ── Datos iniciales ──────────────────────────────────────────

INSERT INTO `configuracion_centro`
  (`idConfig`, `nombreCentro`, `codigoCentro`, `direccionCentro`, `ciudadCentro`, `cpCentro`,
   `telefonoCentro`, `emailCentro`, `cursoEscolar`, `textoLegal`, `nombreDirectorFirmante`,
   `feature_prematricula`, `feature_chat`, `feature_inventario`, `feature_subida_tfg`,
   `feature_anuncios`, `feature_eventos`, `feature_retos`, `feature_mensajes`,
   `feature_pagos`, `feature_gastos`, `feature_informes`, `feature_horario`,
   `instance_status`, `suspension_message`, `saas_lock_features`, `saas_message`,
   `saas_message_type`, `saas_last_sync`, `license_token`, `license_token_exp`)
VALUES
  (1, 'Centro de Formación Profesional', '', '', '', '', '', '', '2025-2026', '', '',
   1, 1, 1, 1,
   1, 1, 1, 1,
   1, 1, 1, 1,
   'active', NULL, 0, NULL, 'info', NULL, NULL, NULL);

INSERT INTO `niveles` (`idNivel`, `nombreNivel`) VALUES
(1, 'Grado Medio'),
(2, 'Grado Superior');

-- Cuenta de arranque — contraseña: 123456. CAMBIA email y contraseña tras el primer login.
INSERT INTO `directores` (`nombreDirector`, `emailDirector`, `password`, `dniDirector`)
VALUES ('Administrador', 'admin@aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '00000000T');

INSERT INTO `ciclos` (`idCiclo`, `nombreCiclo`, `abreviaturaCiclo`, `precioCiclo`, `idNivel`) VALUES
(1, 'Desarrollo de Aplicaciones Web',            'DAW',  1200.00, 2),
(2, 'Desarrollo de Aplicaciones Multiplataforma', 'DAM',  1200.00, 2),
(3, 'Sistemas Informáticos en Red',               'ASIR', 1200.00, 2);

-- Profesores de ejemplo (contraseña: 123456)
INSERT INTO `profesores` (`idProfesor`, `nombreProfesor`, `emailProfesor`, `password`, `telefonoProfesor`, `dniProfesor`, `fechaNacimientoProfesor`, `fechaAltaProfesor`, `direccionProfesor`, `ciudadProfesor`, `codigoPostalProfesor`) VALUES
(1, 'Juan García Martínez',  'juan.garcia@aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '612345678', '12345678A', '1980-05-15', '2023-09-01', 'Calle Principal 123',    'Madrid',    '28001'),
(2, 'María López Rodríguez', 'maria.lopez@aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '623456789', '87654321B', '1985-03-22', '2023-09-01', 'Avenida Principal 456', 'Barcelona', '08002');

INSERT INTO `modulos` (`idModulo`, `nombreModulo`, `horasMaximas`, `idCiclo`) VALUES
(1,  'Lenguajes de Marcas',                          42,  1),
(2,  'Programación del Lado del Cliente',            126, 1),
(3,  'Bases de Datos',                                84, 1),
(4,  'Programación del Lado del Servidor',           126, 1),
(5,  'Despliegue de Aplicaciones Web',                63, 1),
(6,  'Lenguajes de Programación',                    105, 2),
(7,  'Fundamentos de Bases de Datos',                 84, 2),
(8,  'Programación Multimedia',                      105, 2),
(9,  'Acceso a Datos',                                84, 2),
(10, 'Interfaces',                                    84, 2),
(11, 'Planificación y Administración de Redes',       84, 3),
(12, 'Gestión e Instalación de Sistemas Operativos', 105, 3),
(13, 'Servicios en Red',                             105, 3),
(14, 'Sistemas Gestores de Bases de Datos',           84, 3),
(15, 'Seguridad Informática',                        105, 3);

-- Estudiantes de ejemplo (contraseña: 123456)
INSERT INTO `estudiantes` (`idEstudiante`, `nombreEstudiante`, `emailEstudiante`, `password`, `dniEstudiante`, `fechaNacimientoEstudiante`, `fechaAltaEstudiante`, `idCiclo`, `curso`) VALUES
(1, 'Carlos Sánchez López',      'carlos.sanchez@aulapro.com',    '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '11111111C', '2004-06-10', '2023-09-01', 1, 'Grado Superior'),
(2, 'Laura Fernández García',    'laura.fernandez@aulapro.com',   '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '22222222D', '2004-08-22', '2023-09-01', 1, 'Grado Superior'),
(3, 'Pablo Martínez Ruiz',       'pablo.martinez@aulapro.com',    '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '33333333E', '2005-01-18', '2023-09-01', 1, 'Grado Superior'),
(4, 'Andrea Jiménez Torres',     'andrea.jimenez@aulapro.com',    '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '44444444F', '2004-07-14', '2023-09-01', 2, 'Grado Superior'),
(5, 'David Moreno Pérez',        'david.moreno@aulapro.com',      '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '55555555G', '2005-02-28', '2023-09-01', 2, 'Grado Superior'),
(6, 'Sofía González Blanco',     'sofia.gonzalez@aulapro.com',    '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '66666666H', '2004-11-05', '2023-09-01', 2, 'Grado Superior'),
(7, 'Alejandro Ramírez Santos',  'alejandro.ramirez@aulapro.com', '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '77777777I', '2004-09-12', '2023-09-01', 3, 'Grado Superior'),
(8, 'Cristina Díaz Muñoz',       'cristina.diaz@aulapro.com',     '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '88888888J', '2005-03-30', '2023-09-01', 3, 'Grado Superior'),
(9, 'Roberto Vega Herrera',      'roberto.vega@aulapro.com',      '$2y$10$9nwmzvOe4muwQ9jM5Ryc7.bmaA3Gipm7S4Wnj2S1oiDPnRo1JNcvu', '99999999K', '2004-12-08', '2023-09-01', 3, 'Grado Superior');

INSERT INTO `retos` (`idReto`, `nombreReto`, `fechaInicio`, `fechaFin`, `horasReto`) VALUES
(1, 'Reto HTML y CSS',    '2026-02-01', '2026-02-28', 20),
(2, 'Reto JavaScript',    '2026-03-01', '2026-03-31', 25),
(3, 'Reto Base de Datos', '2026-04-01', '2026-04-30', 30),
(4, 'Reto Backend',       '2026-05-01', '2026-05-31', 35),
(5, 'Reto Full Stack',    '2026-06-01', '2026-06-30', 50);

INSERT INTO `modulo_reto` (`idModulo`, `idReto`) VALUES
(1,1),(2,2),(3,3),(4,4),(1,5),(2,5),(3,5),(4,5);

INSERT INTO `modulo_profesor` (`idModulo`, `idProfesor`) VALUES
(1,1),(2,1),(3,1),(4,1),(5,1),(6,2),(7,2),(8,2),(9,2),(10,2);

INSERT INTO `ciclo_profesor` (`idCiclo`, `idProfesor`) VALUES
(1,1),(2,2);

INSERT INTO `aula_almacenamiento_ciclo` (`idCiclo`, `limiteBytes`) VALUES
(1,5368709120),(2,5368709120),(3,5368709120);

INSERT INTO `aulas` (`idAula`, `planta`, `numero`, `nombreAula`, `tipoAula`, `capacidad`) VALUES
(1,1,1,'Aula 101',         'teoria',      30),
(2,1,2,'Aula 102',         'teoria',      30),
(3,2,1,'Laboratorio 201',  'laboratorio', 24),
(4,2,2,'Laboratorio 202',  'laboratorio', 24),
(5,0,1,'Aula 001 (Taller)','taller',      20);

INSERT INTO `horarios` (`idCiclo`,`diaSemana`,`horaInicio`,`horaFin`,`idModulo`,`idProfesor`,`idAula`) VALUES
(1,'Lunes',    '08:00:00','09:00:00',1,1,1),
(1,'Lunes',    '09:00:00','10:00:00',2,1,1),
(1,'Lunes',    '10:00:00','11:00:00',3,1,1),
(1,'Martes',   '08:00:00','09:00:00',2,1,1),
(1,'Martes',   '11:30:00','12:30:00',4,1,3),
(1,'Miércoles','09:00:00','10:00:00',3,1,1),
(1,'Miércoles','12:30:00','13:30:00',5,1,3),
(1,'Jueves',   '10:00:00','11:00:00',4,1,3),
(1,'Viernes',  '13:30:00','14:30:00',5,1,3);
