-- ══════════════════════════════════════════════════════════════════════
-- Cifrado de datos personales (RGPD Art. 32) — ampliación de columnas
-- ══════════════════════════════════════════════════════════════════════
-- Aplica esto SOLO a una base de datos EXISTENTE con datos (producción o un
-- dev previo a esta fecha). Una instalación nueva ya recibe estos tipos de
-- columna directamente desde noDeploy/database.sql — no hace falta ejecutar
-- este archivo en ese caso.
--
-- Tras aplicar este ALTER, ejecutar migrar_cifrado_pii.php (raíz del
-- proyecto) para cifrar los valores existentes. Es idempotente — se puede
-- re-ejecutar sin riesgo.
--
-- Nunca se ejecuta automáticamente — se aplica manualmente, en orden, junto
-- con el resto de noDeploy/migrations/*.sql.
-- ══════════════════════════════════════════════════════════════════════

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
