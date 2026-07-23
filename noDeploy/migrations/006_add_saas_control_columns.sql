-- ══════════════════════════════════════════════════════════════════════
-- Columnas de control SaaS en `configuracion_centro` — faltaban en el
-- esquema para una base de datos existente
-- ══════════════════════════════════════════════════════════════════════
-- api/admin.php (API interna de control SaaS: activar/suspender instancia,
-- forzar features, licencia) lee y escribe estas columnas, y ya tenía
-- código defensivo anticipando que pudieran no existir todavía ("license_token
-- column not yet created — migration 003 not run"). Esa referencia a
-- "migration 003" es de un esquema de numeración antiguo (de antes de
-- consolidar todo bajo noDeploy/migrations/) y no tiene relación con el
-- 003 actual (cifrado PII) — el comentario se corrige en el propio
-- api/admin.php aparte de este archivo.
--
-- Aplica esto SOLO a una base de datos EXISTENTE (producción o un dev
-- previo a esta fecha). Una instalación nueva ya las recibe directamente
-- desde noDeploy/database.sql.
--
-- IMPORTANTE — `ADD COLUMN IF NOT EXISTS` es una extensión de MariaDB, NO
-- sintaxis estándar de MySQL: en MySQL 8.x real (que este proyecto declara
-- soportar, README.md) esa cláusula da un error de sintaxis y el ALTER
-- entero falla, incluso las columnas que sí no existen. Este archivo usaba
-- esa sintaxis y por eso nunca llegó a ejecutarse correctamente contra un
-- MySQL real — las columnas de producción se añadieron alguna vez por otra
-- vía. Se sustituye por el patrón portable de siempre (information_schema +
-- sentencia preparada), que funciona igual en MySQL 8.x y en MariaDB.
--
-- Nunca se ejecuta automáticamente — se aplica manualmente, en orden, junto
-- con el resto de noDeploy/migrations/*.sql.
-- ══════════════════════════════════════════════════════════════════════

SET @db := DATABASE();

SET @s := (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='configuracion_centro' AND COLUMN_NAME='instance_status') > 0,
  'SELECT 1', "ALTER TABLE `configuracion_centro` ADD COLUMN `instance_status` enum('active','suspended','pending') NOT NULL DEFAULT 'active'"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s := (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='configuracion_centro' AND COLUMN_NAME='suspension_message') > 0,
  'SELECT 1', 'ALTER TABLE `configuracion_centro` ADD COLUMN `suspension_message` text'
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s := (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='configuracion_centro' AND COLUMN_NAME='saas_lock_features') > 0,
  'SELECT 1', "ALTER TABLE `configuracion_centro` ADD COLUMN `saas_lock_features` tinyint(1) NOT NULL DEFAULT '0'"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s := (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='configuracion_centro' AND COLUMN_NAME='saas_message') > 0,
  'SELECT 1', 'ALTER TABLE `configuracion_centro` ADD COLUMN `saas_message` text'
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s := (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='configuracion_centro' AND COLUMN_NAME='saas_message_type') > 0,
  'SELECT 1', "ALTER TABLE `configuracion_centro` ADD COLUMN `saas_message_type` varchar(20) NOT NULL DEFAULT 'info'"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s := (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='configuracion_centro' AND COLUMN_NAME='saas_last_sync') > 0,
  'SELECT 1', 'ALTER TABLE `configuracion_centro` ADD COLUMN `saas_last_sync` datetime DEFAULT NULL'
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s := (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='configuracion_centro' AND COLUMN_NAME='license_token') > 0,
  'SELECT 1', 'ALTER TABLE `configuracion_centro` ADD COLUMN `license_token` text'
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s := (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='configuracion_centro' AND COLUMN_NAME='license_token_exp') > 0,
  'SELECT 1', 'ALTER TABLE `configuracion_centro` ADD COLUMN `license_token_exp` datetime DEFAULT NULL'
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
