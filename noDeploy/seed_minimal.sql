-- ══════════════════════════════════════════════════════════════════════
-- SEED MÍNIMO para una instalación nueva SIN usar el asistente (/install/).
-- Distinto de noDeploy/demo_data.sql (que son datos ficticios de desarrollo
-- local: alumnos, profesores, ciclos... para trastear). Esto es solo lo
-- mínimo indispensable para poder entrar por primera vez: una cuenta de
-- administrador y la fila de configuración del centro.
--
-- Uso: tras importar noDeploy/database.sql en una base de datos nueva,
--   mysql -u usuario -p nombre_bd < noDeploy/seed_minimal.sql
--
-- ⚠ CAMBIA el email y la contraseña antes de exponer esto a producción.
-- La contraseña de abajo es literalmente "CambiaEstaContrasena123!" — el
-- hash bcrypt correspondiente. Inicia sesión y cámbiala inmediatamente,
-- o mejor aún, usa el asistente (/install/) en vez de este fichero, que
-- te pide una contraseña propia desde el principio.
-- ══════════════════════════════════════════════════════════════════════

INSERT INTO `directores` (`nombreDirector`, `emailDirector`, `password`, `dniDirector`, `fechaAltaDirector`)
VALUES (
  'Administrador',
  'admin@tucentro.es',
  '$2y$12$fUQGw.Y9A1aCsstZYXYEWOUM5gABE/Dcou0btV3oxPq0pqGsnI55q', -- "CambiaEstaContrasena123!" (hash verificado con password_verify())
  '',
  CURDATE()
)
ON DUPLICATE KEY UPDATE nombreDirector = nombreDirector; -- no-op si ya existe: evita fallar en un reintento

INSERT INTO `configuracion_centro` (`idConfig`, `nombreCentro`, `cursoEscolar`)
VALUES (1, 'Mi Centro', CONCAT(YEAR(CURDATE()), '-', YEAR(CURDATE()) + 1))
ON DUPLICATE KEY UPDATE nombreCentro = nombreCentro;
