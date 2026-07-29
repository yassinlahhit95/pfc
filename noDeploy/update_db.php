<?php
require_once __DIR__ . '/modelos/conectar.php';

$con = obtenerConexion();

echo "Starting database updates...<br>";

// Helper function
function runQuery($con, $sql, $successMsg) {
    try {
        if (mysqli_query($con, $sql)) {
            echo "<span style='color:green'>SUCCESS</span>: $successMsg<br>";
        }
    } catch (Exception $e) {
        $err = $e->getMessage();
        if (strpos($err, 'Duplicate column name') !== false || strpos($err, 'already exists') !== false) {
            echo "<span style='color:gray'>SKIPPED (already exists)</span>: $successMsg<br>";
        } else {
            echo "<span style='color:red'>ERROR</span>: $successMsg - " . htmlspecialchars($err) . "<br>";
        }
    }
}

// 1. Configuracion centro features
runQuery($con, "ALTER TABLE configuracion_centro ADD COLUMN feature_inventario TINYINT(1) DEFAULT 1", "Add feature_inventario to configuracion_centro");
runQuery($con, "ALTER TABLE configuracion_centro ADD COLUMN feature_modulos TINYINT(1) DEFAULT 1", "Add feature_modulos to configuracion_centro");
runQuery($con, "ALTER TABLE configuracion_centro ADD COLUMN feature_gastos TINYINT(1) DEFAULT 1", "Add feature_gastos to configuracion_centro");

// 2. Estudiantes eliminado
runQuery($con, "ALTER TABLE estudiantes ADD COLUMN eliminado TINYINT(1) DEFAULT 0", "Add eliminado column to estudiantes");

// 3. Gastos Categorias
$sqlCategorias = "CREATE TABLE IF NOT EXISTS `gastos_categorias` (
  `idCategoria` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `color` varchar(20) DEFAULT '#808080',
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`idCategoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
runQuery($con, $sqlCategorias, "Create gastos_categorias table");

// 4. Gastos
$sqlGastos = "CREATE TABLE IF NOT EXISTS `gastos` (
  `idGasto` int NOT NULL AUTO_INCREMENT,
  `idCategoria` int NOT NULL,
  `idCiclo` int DEFAULT NULL,
  `concepto` varchar(255) NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `tipoJustificante` varchar(50) DEFAULT 'Ninguno',
  `numeroReferencia` varchar(100) DEFAULT NULL,
  `archivoJustificante` text,
  `observaciones` text,
  `creadoEn` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `creadoPorId` int DEFAULT NULL,
  `creadoPorRol` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idGasto`),
  KEY `fk_gastos_categoria` (`idCategoria`),
  KEY `fk_gastos_ciclo` (`idCiclo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
runQuery($con, $sqlGastos, "Create gastos table");

// Insert default categories if not exists
runQuery($con, "INSERT IGNORE INTO gastos_categorias (idCategoria, nombre, color) VALUES (1, 'Material de Oficina', '#4CAF50'), (2, 'Mantenimiento', '#FF9800'), (3, 'Eventos', '#9C27B0'), (4, 'Equipamiento', '#2196F3'), (5, 'Otros', '#607D8B')", "Insert default gastos_categorias");

// 5. Eventos y recordatorios (nuevos cambios)
runQuery($con, "ALTER TABLE eventos ADD COLUMN activo TINYINT DEFAULT 1", "Add activo column to eventos");

$sqlRecordatorios = "CREATE TABLE IF NOT EXISTS `recordatorios` (
  `idRecordatorio` int NOT NULL AUTO_INCREMENT,
  `idEvento` int NOT NULL,
  `tipo` enum('24h_antes','1h_antes','15m_antes','exacta') NOT NULL,
  `fecha_programada` datetime NOT NULL,
  PRIMARY KEY (`idRecordatorio`),
  KEY `idx_recordatorio_evento` (`idEvento`),
  KEY `idx_recordatorio_fecha` (`fecha_programada`),
  CONSTRAINT `fk_recordatorio_evento` FOREIGN KEY (`idEvento`) REFERENCES `eventos` (`idEvento`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
runQuery($con, $sqlRecordatorios, "Create recordatorios table");

$sqlNotifRec = "CREATE TABLE IF NOT EXISTS `notificaciones_recordatorios` (
  `idNotificacionRecordatorio` int NOT NULL AUTO_INCREMENT,
  `idEvento` int NOT NULL,
  `idUsuario` int NOT NULL,
  `tipoUsuario` enum('director','profesor','secretaria','estudiante','tutor') NOT NULL,
  `idRecordatorio` int DEFAULT NULL,
  `fecha_programada` datetime NOT NULL,
  `leido` tinyint DEFAULT '0',
  `estado` enum('pendiente','enviado','fallido') DEFAULT 'pendiente',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idNotificacionRecordatorio`),
  KEY `idx_notifrec_usuario` (`idUsuario`),
  KEY `idx_notifrec_evento` (`idEvento`),
  KEY `idx_notifrec_programada` (`fecha_programada`),
  KEY `idx_notifrec_recordatorio` (`idRecordatorio`),
  CONSTRAINT `fk_notifrec_evento` FOREIGN KEY (`idEvento`) REFERENCES `eventos` (`idEvento`) ON DELETE CASCADE,
  CONSTRAINT `fk_notifrec_recordatorio` FOREIGN KEY (`idRecordatorio`) REFERENCES `recordatorios` (`idRecordatorio`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
runQuery($con, $sqlNotifRec, "Create notificaciones_recordatorios table");

echo "<br><b>Database updates completed!</b> You can now delete this file.";
?>
