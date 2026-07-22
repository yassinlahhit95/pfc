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
-- Nunca se ejecuta automáticamente — se aplica manualmente, en orden, junto
-- con el resto de noDeploy/migrations/*.sql.
-- ══════════════════════════════════════════════════════════════════════

ALTER TABLE `configuracion_centro`
  ADD COLUMN IF NOT EXISTS `instance_status` enum('active','suspended','pending') NOT NULL DEFAULT 'active',
  ADD COLUMN IF NOT EXISTS `suspension_message` text,
  ADD COLUMN IF NOT EXISTS `saas_lock_features` tinyint(1) NOT NULL DEFAULT '0',
  ADD COLUMN IF NOT EXISTS `saas_message` text,
  ADD COLUMN IF NOT EXISTS `saas_message_type` varchar(20) NOT NULL DEFAULT 'info',
  ADD COLUMN IF NOT EXISTS `saas_last_sync` datetime DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `license_token` text,
  ADD COLUMN IF NOT EXISTS `license_token_exp` datetime DEFAULT NULL;
