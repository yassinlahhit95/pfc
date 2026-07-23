-- ══════════════════════════════════════════════════════════════════════
-- `api_tokens.user_type` — añade 'secretaria' al enum de roles móviles
-- ══════════════════════════════════════════════════════════════════════
-- Aplica esto SOLO a una base de datos EXISTENTE (producción o un dev
-- previo a esta fecha). Una instalación nueva ya la recibe directamente
-- desde noDeploy/database.sql.
--
-- La API móvil v1 (api/v1/) empieza a soportar login de secretaría (junto
-- con estudiante/profesor/director/tutor, que ya existían). Sin este
-- cambio, `api/v1/auth.php` fallaría al intentar insertar un token con
-- user_type='secretaria' (valor fuera del enum existente).
--
-- Nunca se ejecuta automáticamente — se aplica manualmente, en orden, junto
-- con el resto de noDeploy/migrations/*.sql.
-- ══════════════════════════════════════════════════════════════════════

ALTER TABLE `api_tokens`
  MODIFY `user_type` enum('estudiante','profesor','director','tutor','secretaria')
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
