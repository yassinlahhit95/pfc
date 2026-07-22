-- ══════════════════════════════════════════════════════════════════════
-- APLICAR TODAS LAS MIGRACIONES PENDIENTES A UNA BASE DE DATOS EXISTENTE
-- (producción, o cualquier dev anterior a 2026-07-10)
-- ══════════════════════════════════════════════════════════════════════
-- Concatenación, EN ORDEN, de:
--   001_blog_posts.sql
--   002_landing_ciclos.sql
--   003_encrypt_pii_columns.sql
--   004_add_missing_audit_tables.sql
--   005_add_api_tokens_table.sql
--   006_add_saas_control_columns.sql
--
-- Este archivo es solo una comodidad para aplicar todas de una vez con
-- un único comando. Los archivos numerados individuales siguen siendo la
-- fuente de referencia (por si hace falta aplicar solo una); no se borran.
--
-- IMPORTANTE — LEER ANTES DE EJECUTAR:
-- 1. Haz backup completo antes de ejecutar (mysqldump). MySQL NO soporta
--    rollback transaccional para DDL (CREATE TABLE/ALTER TABLE hacen commit
--    implícito) — si algo falla a mitad, no hay vuelta atrás automática.
-- 2. Ejecuta esto UNA sola vez por base de datos. Es mayormente idempotente
--    (CREATE TABLE IF NOT EXISTS, MODIFY COLUMN al mismo tipo no rompe nada
--    si se re-ejecuta), pero no está pensado para correr en cada deploy.
-- 3. TRAS ejecutar este archivo, hace falta un paso adicional en PHP (no en
--    SQL): ejecutar `php migrar_cifrado_pii.php` (raíz del proyecto) para
--    cifrar los valores de DNI/teléfono/dirección/etc. que ya existan en
--    la base de datos. El ALTER de aquí solo amplía las columnas para que
--    quepa el texto cifrado — no cifra nada por sí solo. Es idempotente,
--    se puede re-ejecutar sin riesgo.
--
--   mysql -u usuario -p nombre_bd < noDeploy/migrations/aplicar_todas_produccion.sql
--   php migrar_cifrado_pii.php
-- ══════════════════════════════════════════════════════════════════════


-- ── 001_blog_posts.sql ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS blog_posts (
    idPost INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    resumen VARCHAR(500) NOT NULL DEFAULT '',
    contenido MEDIUMTEXT NULL,
    imagen VARCHAR(255) NOT NULL DEFAULT '',
    categoria VARCHAR(80) NOT NULL DEFAULT '',
    autor VARCHAR(120) NOT NULL DEFAULT '',
    publicado TINYINT(1) NOT NULL DEFAULT 0,
    destacado TINYINT(1) NOT NULL DEFAULT 0,
    fechaPublicacion DATETIME NULL,
    creadoEn TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizadoEn TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_publicado (publicado, fechaPublicacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ── 002_landing_ciclos.sql ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS landing_ciclos (
    idLandingCiclo INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    etiqueta VARCHAR(60) NOT NULL DEFAULT '',
    resumen VARCHAR(300) NOT NULL DEFAULT '',
    descripcion MEDIUMTEXT NULL,
    imagen VARCHAR(255) NOT NULL DEFAULT '',
    precio VARCHAR(60) NOT NULL DEFAULT '',
    duracion VARCHAR(60) NOT NULL DEFAULT '',
    modalidad VARCHAR(60) NOT NULL DEFAULT '',
    publicado TINYINT(1) NOT NULL DEFAULT 0,
    destacado TINYINT(1) NOT NULL DEFAULT 0,
    orden INT NOT NULL DEFAULT 0,
    creadoEn TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizadoEn TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_publicado (publicado, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ── 003_encrypt_pii_columns.sql ─────────────────────────────────────────
-- Ampliación de columnas para poder guardar valores cifrados (RGPD Art. 32).
-- Recuerda: esto NO cifra datos existentes — eso lo hace migrar_cifrado_pii.php
-- (ver instrucciones arriba), que debe ejecutarse justo después de este archivo.
ALTER TABLE `directores`
  MODIFY COLUMN `dniDirector` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY COLUMN `fechaNacimientoDirector` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  MODIFY COLUMN `direccionDirector` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `telefonoDirector` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `mfa_secret` text COLLATE utf8mb4_unicode_ci;

ALTER TABLE `estudiantes`
  MODIFY COLUMN `dniEstudiante` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY COLUMN `fechaNacimientoEstudiante` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  MODIFY COLUMN `direccionEstudiante` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `telefonoEstudiante` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `mfa_secret` text COLLATE utf8mb4_unicode_ci;

-- profesores / secretarias / tutores: solo mfa_secret entra en el alcance
-- (el resto de sus columnas no contienen datos personales cifrables aquí).
ALTER TABLE `profesores`  MODIFY COLUMN `mfa_secret` text COLLATE utf8mb4_unicode_ci;
ALTER TABLE `secretarias` MODIFY COLUMN `mfa_secret` text COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tutores`     MODIFY COLUMN `mfa_secret` text;

ALTER TABLE `fct`
  MODIFY COLUMN `tutorEmpresa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `emailTutorEmpresa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `telefonoEmpresa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- observacionesEstudiante, observacionesDirector, fct.observaciones y
-- mfa_backup_codes ya eran TEXT — no necesitan ALTER.


-- ── 004_add_missing_audit_tables.sql ────────────────────────────────────
CREATE TABLE IF NOT EXISTS `log_acciones` (
  `idLog` int NOT NULL AUTO_INCREMENT,
  `idAdmin` int DEFAULT NULL,
  `accion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabla` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idRegistro` int DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idLog`),
  KEY `idx_log_admin` (`idAdmin`),
  KEY `idx_log_fecha` (`fecha`),
  CONSTRAINT `fk_log_admin` FOREIGN KEY (`idAdmin`) REFERENCES `directores` (`idDirector`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `consentimientos` (
  `idConsentimiento` int NOT NULL AUTO_INCREMENT,
  `idEstudiante` int NOT NULL,
  `tipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idConsentimiento`),
  KEY `idx_consentimiento_estudiante` (`idEstudiante`),
  CONSTRAINT `fk_consentimiento_estudiante` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ── 005_add_api_tokens_table.sql ────────────────────────────────────────
-- Faltaba en el esquema — api/v1/_api.php la creaba con un CREATE TABLE
-- IF NOT EXISTS en tiempo de ejecución en cada request (anti-patrón ya
-- corregido una vez para historial_secretarias). Se retira del código y
-- se añade aquí como migración estándar.
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


-- ── 006_add_saas_control_columns.sql ────────────────────────────────────
-- api/admin.php (control SaaS: activar/suspender instancia, licencia) ya
-- tenía código defensivo anticipando que estas columnas pudieran no existir.
ALTER TABLE `configuracion_centro`
  ADD COLUMN IF NOT EXISTS `instance_status` enum('active','suspended','pending') NOT NULL DEFAULT 'active',
  ADD COLUMN IF NOT EXISTS `suspension_message` text,
  ADD COLUMN IF NOT EXISTS `saas_lock_features` tinyint(1) NOT NULL DEFAULT '0',
  ADD COLUMN IF NOT EXISTS `saas_message` text,
  ADD COLUMN IF NOT EXISTS `saas_message_type` varchar(20) NOT NULL DEFAULT 'info',
  ADD COLUMN IF NOT EXISTS `saas_last_sync` datetime DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `license_token` text,
  ADD COLUMN IF NOT EXISTS `license_token_exp` datetime DEFAULT NULL;
