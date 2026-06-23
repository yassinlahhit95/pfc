-- ============================================================
-- AulaPro — Migration 2026-06-21
-- Run this in phpMyAdmin → SQL tab on the production database.
-- Safe to run multiple times (uses IF NOT EXISTS / column checks).
-- ============================================================

-- 1. Add activo + fechaArchivado to ciclos
ALTER TABLE `ciclos`
  ADD COLUMN `activo` tinyint(1) NOT NULL DEFAULT 1 AFTER `idNivel`,
  ADD COLUMN `fechaArchivado` datetime DEFAULT NULL AFTER `activo`;

-- 2. Add anioEstudio to estudiantes
ALTER TABLE `estudiantes`
  ADD COLUMN `anioEstudio` varchar(20) DEFAULT NULL AFTER `curso`;

-- 3. Add cursoAnio + creditosECTS to modulos
ALTER TABLE `modulos`
  ADD COLUMN `cursoAnio` varchar(10) DEFAULT NULL AFTER `horasMaximas`,
  ADD COLUMN `creditosECTS` int(3) DEFAULT NULL AFTER `cursoAnio`;

-- 4. Asistencias table (attendance tracking)
CREATE TABLE IF NOT EXISTS `asistencias` (
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

-- 5. Log de acciones de administrador
CREATE TABLE IF NOT EXISTS `log_acciones` (
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

-- 6. RGPD — registro de eliminaciones
CREATE TABLE IF NOT EXISTS `rgpd_eliminaciones` (
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

-- 7. Consentimientos RGPD
CREATE TABLE IF NOT EXISTS `consentimientos` (
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

-- 8. Categorías de gasto
CREATE TABLE IF NOT EXISTS `categorias_gasto` (
  `idCategoria`     int(11) NOT NULL AUTO_INCREMENT,
  `nombre`          varchar(100) NOT NULL,
  `presupuestoAnual` decimal(10,2) DEFAULT 0.00,
  `color`           varchar(20) DEFAULT '#6366f1',
  `activo`          tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idCategoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Gastos del centro
CREATE TABLE IF NOT EXISTS `gastos` (
  `idGasto`           int(11) NOT NULL AUTO_INCREMENT,
  `idCategoria`       int(11) NOT NULL,
  `idCiclo`           int(11) DEFAULT NULL,
  `concepto`          varchar(255) NOT NULL,
  `importe`           decimal(10,2) NOT NULL,
  `fecha`             date NOT NULL,
  `tipoJustificante`  varchar(50) DEFAULT NULL,
  `numeroReferencia`  varchar(100) DEFAULT NULL,
  `archivoJustificante` varchar(255) DEFAULT NULL,
  `observaciones`     text DEFAULT NULL,
  `fechaRegistro`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idGasto`),
  KEY `idx_gasto_fecha` (`fecha`),
  KEY `idx_gasto_categoria` (`idCategoria`),
  CONSTRAINT `fk_gasto_cat` FOREIGN KEY (`idCategoria`) REFERENCES `categorias_gasto` (`idCategoria`),
  CONSTRAINT `fk_gasto_ciclo` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`idCiclo`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default expense categories
INSERT IGNORE INTO `categorias_gasto` (`idCategoria`, `nombre`, `presupuestoAnual`, `color`) VALUES
(1, 'Material Didáctico', 2000.00, '#6366f1'),
(2, 'Mantenimiento',       1500.00, '#f59e0b'),
(3, 'Software y Licencias', 3000.00, '#10b981'),
(4, 'Personal',            5000.00, '#3b82f6'),
(5, 'Otros',               1000.00, '#8b5cf6');
