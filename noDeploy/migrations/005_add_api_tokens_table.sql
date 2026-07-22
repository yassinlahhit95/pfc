-- ══════════════════════════════════════════════════════════════════════
-- Tabla `api_tokens` — faltaba en el esquema (anti-patrón detectado)
-- ══════════════════════════════════════════════════════════════════════
-- api/v1/_api.php la creaba con un `CREATE TABLE IF NOT EXISTS` en tiempo
-- de ejecución (v1EnsureTokenTable(), llamada en cada login y en cada
-- petición autenticada) — exactamente el mismo anti-patrón ya corregido
-- una vez para `historial_secretarias` en modelos/log.php: paga el coste
-- de una consulta de metadatos en cada request para nada, porque la tabla
-- ya debería estar garantizada por el esquema. Se retira ese CREATE TABLE
-- del código y se añade aquí como migración estándar.
--
-- Aplica esto SOLO a una base de datos EXISTENTE (producción o un dev
-- previo a esta fecha). Una instalación nueva ya la recibe directamente
-- desde noDeploy/database.sql.
--
-- Nunca se ejecuta automáticamente — se aplica manualmente, en orden, junto
-- con el resto de noDeploy/migrations/*.sql.
-- ══════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `api_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_type` enum('estudiante','profesor','director','tutor') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `token` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_info` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL,
  `last_used_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_token` (`token`),
  KEY `idx_user` (`user_type`,`user_id`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
