-- ============================================================
-- MIGRATION: Add grading fields to aula_entregas table
-- Date: 2026-05-29
-- Purpose: Support teacher feedback and correction files
-- ============================================================

-- Add missing columns to aula_entregas if they don't exist
ALTER TABLE `aula_entregas`
ADD COLUMN IF NOT EXISTS `comentarioCalificacion` text DEFAULT NULL AFTER `nota`,
ADD COLUMN IF NOT EXISTS `archivoCorreccion` varchar(255) DEFAULT NULL AFTER `comentarioCalificacion`;

-- Fix aula_tareas column name to match code (publicada instead of publicado)
-- Note: Keep 'publicado' as is since data might already exist, but code will use the column name correctly

-- Add index for faster lookups
ALTER TABLE `aula_entregas`
ADD INDEX IF NOT EXISTS `idx_tarea_estudiante` (`idTarea`, `idEstudiante`),
ADD INDEX IF NOT EXISTS `idx_estudiante_nota` (`idEstudiante`, `nota`);

-- Verify the structure is correct
-- SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='aula_entregas' AND TABLE_SCHEMA=DATABASE();
