-- ============================================================
-- AulaPro — Migration 2026-06-22
-- Adds feature_horario column to configuracion_centro.
-- Run in phpMyAdmin → SQL tab on the production database.
-- Safe to run multiple times (column check prevents duplicates).
-- ============================================================

-- Add feature_horario if it does not already exist
SET @col_exists = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'configuracion_centro'
      AND COLUMN_NAME  = 'feature_horario'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `configuracion_centro` ADD COLUMN `feature_horario` tinyint(1) NOT NULL DEFAULT 1 AFTER `feature_informes`',
    'SELECT ''feature_horario already exists'' AS info'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
