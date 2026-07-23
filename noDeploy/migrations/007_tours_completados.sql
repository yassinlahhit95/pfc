-- ══════════════════════════════════════════════════════════════════════
-- Tabla `tours_completados` — seguimiento del tour guiado de primer login
-- ══════════════════════════════════════════════════════════════════════
-- Aplica esto SOLO a una base de datos EXISTENTE (producción o un dev
-- previo a esta fecha). Una instalación nueva ya la recibe directamente
-- desde noDeploy/database.sql.
--
-- Una única fila por (usuario, rol, tour) marca ese tour como completado
-- (terminado o saltado — ambos cuentan como "no volver a mostrar"). El
-- admin puede reiniciar el tour de un usuario borrando su fila.
--
-- Nunca se ejecuta automáticamente — se aplica manualmente, en orden, junto
-- con el resto de noDeploy/migrations/*.sql.
-- ══════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `tours_completados` (
  `idTourCompletado` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `tipoUsuario` enum('admin','profesor','secretaria','estudiante','tutor') NOT NULL,
  `tour_key` varchar(50) NOT NULL,
  `completado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idTourCompletado`),
  UNIQUE KEY `uniq_usuario_tour` (`idUsuario`,`tipoUsuario`,`tour_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
