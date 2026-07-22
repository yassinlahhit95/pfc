-- ══════════════════════════════════════════════════════════════════════
-- Tablas de auditoría/consentimiento referenciadas en código pero nunca
-- creadas en el esquema — se descubrieron al depurar dos fallos reales:
--
-- 1. `log_acciones` — registrarAccion() (modelos/log.php) inserta aquí en
--    cada acción de admin, pero el INSERT está envuelto en un try/catch que
--    ignora el fallo a propósito ("nunca romper un request por un fallo de
--    log"), así que la tabla ausente no daba ningún error visible: el log
--    de auditoría de admin llevaba fallando en silencio desde siempre.
-- 2. `consentimientos` — exportarDatosEstudiante() (RGPD Art. 20) y
--    eliminarEstudianteRGPD() (RGPD Art. 17) referencian esta tabla. El
--    export crasheaba (mysqli_sql_exception sin capturar) y el borrado
--    fallaba silenciosamente (capturado por su propio try/catch, pero
--    haciendo rollback de TODA la eliminación) — es decir, el borrado RGPD
--    de un estudiante estaba completamente roto.
--
-- Aplica esto SOLO a una base de datos EXISTENTE (producción o un dev
-- previo a esta fecha). Una instalación nueva ya las recibe directamente
-- desde noDeploy/database.sql.
--
-- Nunca se ejecuta automáticamente — se aplica manualmente, en orden, junto
-- con el resto de noDeploy/migrations/*.sql.
-- ══════════════════════════════════════════════════════════════════════

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
