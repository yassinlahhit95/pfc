-- ============================================================
-- AulaPro — Migration 2026-07-05
-- Sistema de landing page personalizable (plantillas + constructor).
-- Adds feature_landing column + landing_config + landing_secciones.
-- Run in phpMyAdmin → SQL tab on the production database.
-- Safe to run multiple times (column check prevents duplicates).
-- ============================================================

-- Add feature_landing if it does not already exist
SET @col_exists = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'configuracion_centro'
      AND COLUMN_NAME  = 'feature_landing'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `configuracion_centro` ADD COLUMN `feature_landing` tinyint(1) NOT NULL DEFAULT 1 AFTER `feature_fp_dual`',
    'SELECT ''feature_landing already exists'' AS info'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Configuración global de la landing (fila única, patrón configuracion_centro)
CREATE TABLE IF NOT EXISTS `landing_config` (
  `idLanding`     INT NOT NULL DEFAULT 1,
  `plantilla`     VARCHAR(30) NOT NULL DEFAULT 'institucional',
  `ajustes`       JSON NULL,
  `plantilla_pub` VARCHAR(30) DEFAULT NULL,
  `ajustes_pub`   JSON NULL,
  `publicadoEn`   DATETIME DEFAULT NULL,
  `actualizadoEn` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idLanding`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `landing_config` (`idLanding`) VALUES (1);

-- Secciones de la landing: version='draft' (editable) / version='live' (pública)
CREATE TABLE IF NOT EXISTS `landing_secciones` (
  `idSeccion` INT NOT NULL AUTO_INCREMENT,
  `version`   ENUM('draft','live') NOT NULL DEFAULT 'draft',
  `tipo`      VARCHAR(40) NOT NULL,
  `orden`     INT NOT NULL DEFAULT 0,
  `visible`   TINYINT(1) NOT NULL DEFAULT 1,
  `contenido` JSON NULL,
  PRIMARY KEY (`idSeccion`),
  KEY `idx_landing_version_orden` (`version`, `orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
