<?php
// ══════════════════════════════════════════════════════════════════════
// MIGRADOR DE BASE DE DATOS — ejecutar una vez tras subir una versión nueva
// ══════════════════════════════════════════════════════════════════════
// Seguro de ejecutar varias veces: cada paso comprueba si ya está aplicado.
// Acceso: solo CLI o sesión de administrador (nunca público).
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/include/Security.php';
require_once __DIR__ . '/modelos/conectar.php';

$esCli = php_sapi_name() === 'cli';
if (!$esCli && empty($_SESSION['idAdmin'])) {
    http_response_code(403);
    exit("403 — Solo un administrador con sesión iniciada puede ejecutar las migraciones.\n");
}

$con = obtenerConexion();

// ── Utilidades idempotentes ─────────────────────────────────────────────
function columnaExiste($con, $tabla, $columna) {
    $stmt = mysqli_prepare($con,
        "SELECT COUNT(*) c FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $tabla, $columna);
    mysqli_stmt_execute($stmt);
    return (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['c'] > 0;
}

function agregarColumna($con, $tabla, $columna, $definicion) {
    if (columnaExiste($con, $tabla, $columna)) {
        echo "[ya aplicado] $tabla.$columna\n";
        return;
    }
    if (mysqli_query($con, "ALTER TABLE `$tabla` ADD COLUMN `$columna` $definicion")) {
        echo "[OK]         $tabla.$columna añadida\n";
    } else {
        echo "[ERROR]      $tabla.$columna: " . mysqli_error($con) . "\n";
    }
}

function crearTabla($con, $nombre, $sql) {
    if (mysqli_query($con, $sql)) {
        echo "[OK]         tabla $nombre\n";
    } else {
        echo "[ERROR]      tabla $nombre: " . mysqli_error($con) . "\n";
    }
}

echo "═══ Migración AulaPro — " . date('Y-m-d H:i') . " ═══\n\n";

// ══════════════════════════════════════════════════════════════════════
// 1. Feature flags en configuracion_centro
// ══════════════════════════════════════════════════════════════════════
agregarColumna($con, 'configuracion_centro', 'feature_ra_ce',   "TINYINT(1) DEFAULT 0");
agregarColumna($con, 'configuracion_centro', 'feature_fp_dual', "TINYINT(1) DEFAULT 0");
agregarColumna($con, 'configuracion_centro', 'feature_landing', "TINYINT(1) NOT NULL DEFAULT 1 AFTER `feature_fp_dual`");

// ══════════════════════════════════════════════════════════════════════
// 2. Módulos transversales
// ══════════════════════════════════════════════════════════════════════
agregarColumna($con, 'modulos', 'tipoModulo',
    "ENUM('Específico', 'Transversal', 'Proyecto', 'Empresa') DEFAULT 'Específico'");

// ══════════════════════════════════════════════════════════════════════
// 3. FP Dual
// ══════════════════════════════════════════════════════════════════════
crearTabla($con, 'fp_empresas', "CREATE TABLE IF NOT EXISTS fp_empresas (
    idEmpresa INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    cif VARCHAR(50),
    direccion VARCHAR(255),
    persona_contacto VARCHAR(255),
    telefono VARCHAR(50),
    email VARCHAR(255),
    activo TINYINT(1) DEFAULT 1
)");

crearTabla($con, 'fp_dual_asignaciones', "CREATE TABLE IF NOT EXISTS fp_dual_asignaciones (
    idAsignacion INT AUTO_INCREMENT PRIMARY KEY,
    idEstudiante INT NOT NULL,
    idEmpresa INT NOT NULL,
    fecha_inicio DATE,
    fecha_fin DATE,
    horas_asignadas INT DEFAULT 0,
    estado ENUM('Pendiente', 'En curso', 'Finalizado', 'Cancelado') DEFAULT 'Pendiente',
    FOREIGN KEY (idEstudiante) REFERENCES estudiantes(idEstudiante) ON DELETE CASCADE,
    FOREIGN KEY (idEmpresa) REFERENCES fp_empresas(idEmpresa) ON DELETE CASCADE
)");

// ══════════════════════════════════════════════════════════════════════
// 4. Evaluación RA / CE
// ══════════════════════════════════════════════════════════════════════
crearTabla($con, 'resultados_aprendizaje', "CREATE TABLE IF NOT EXISTS resultados_aprendizaje (
    idRA INT AUTO_INCREMENT PRIMARY KEY,
    idModulo INT NOT NULL,
    codigo VARCHAR(20) NOT NULL,
    descripcion TEXT,
    porcentaje INT DEFAULT 0,
    FOREIGN KEY (idModulo) REFERENCES modulos(idModulo) ON DELETE CASCADE
)");

crearTabla($con, 'criterios_evaluacion', "CREATE TABLE IF NOT EXISTS criterios_evaluacion (
    idCE INT AUTO_INCREMENT PRIMARY KEY,
    idRA INT NOT NULL,
    codigo VARCHAR(20) NOT NULL,
    descripcion TEXT,
    FOREIGN KEY (idRA) REFERENCES resultados_aprendizaje(idRA) ON DELETE CASCADE
)");

crearTabla($con, 'calificaciones_ce', "CREATE TABLE IF NOT EXISTS calificaciones_ce (
    idCalificacionCE INT AUTO_INCREMENT PRIMARY KEY,
    idEstudiante INT NOT NULL,
    idCE INT NOT NULL,
    nota DECIMAL(4,2),
    FOREIGN KEY (idEstudiante) REFERENCES estudiantes(idEstudiante) ON DELETE CASCADE,
    FOREIGN KEY (idCE) REFERENCES criterios_evaluacion(idCE) ON DELETE CASCADE,
    UNIQUE KEY idx_estudiante_ce (idEstudiante, idCE)
)");

// ══════════════════════════════════════════════════════════════════════
// 5. Landing page personalizable (plantillas + constructor)
// ══════════════════════════════════════════════════════════════════════
crearTabla($con, 'landing_config', "CREATE TABLE IF NOT EXISTS landing_config (
    idLanding     INT NOT NULL DEFAULT 1,
    plantilla     VARCHAR(30) NOT NULL DEFAULT 'institucional',
    ajustes       JSON NULL,
    plantilla_pub VARCHAR(30) DEFAULT NULL,
    ajustes_pub   JSON NULL,
    publicadoEn   DATETIME DEFAULT NULL,
    actualizadoEn TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idLanding)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

if (mysqli_query($con, "INSERT IGNORE INTO landing_config (idLanding) VALUES (1)")) {
    echo "[OK]         fila inicial landing_config\n";
}

crearTabla($con, 'landing_secciones', "CREATE TABLE IF NOT EXISTS landing_secciones (
    idSeccion INT NOT NULL AUTO_INCREMENT,
    version   ENUM('draft','live') NOT NULL DEFAULT 'draft',
    tipo      VARCHAR(40) NOT NULL,
    orden     INT NOT NULL DEFAULT 0,
    visible   TINYINT(1) NOT NULL DEFAULT 1,
    contenido JSON NULL,
    PRIMARY KEY (idSeccion),
    KEY idx_landing_version_orden (version, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// ══════════════════════════════════════════════════════════════════════
// 6. Anuncios para familias — ampliar el enum dirigidoA con 'tutores'
// ══════════════════════════════════════════════════════════════════════
$tipoDirigidoA = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COLUMN_TYPE ct FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'anuncios' AND COLUMN_NAME = 'dirigidoA'"))['ct'] ?? '';
if (strpos($tipoDirigidoA, 'tutores') === false) {
    if (mysqli_query($con, "ALTER TABLE anuncios MODIFY dirigidoA
        ENUM('todos','estudiantes','profesores','tutores') DEFAULT 'todos'")) {
        echo "[OK]         anuncios.dirigidoA ampliado con 'tutores'\n";
    } else {
        echo "[ERROR]      anuncios.dirigidoA: " . mysqli_error($con) . "\n";
    }
} else {
    echo "[ya aplicado] anuncios.dirigidoA incluye 'tutores'\n";
}

// ══════════════════════════════════════════════════════════════════════
// 7. Boletines: el serial generado (BLT-YYYY-XXXX-XXXX-XXXX-XXXX) ocupa 28
//    caracteres y la columna era VARCHAR(25) → el registro fallaba y el QR
//    de verificación nunca encontraba el documento.
// ══════════════════════════════════════════════════════════════════════
$lenSerial = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT CHARACTER_MAXIMUM_LENGTH len FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'boletines_log' AND COLUMN_NAME = 'serial'"))['len'] ?? 0;
if ((int)$lenSerial < 40) {
    if (mysqli_query($con, "ALTER TABLE boletines_log MODIFY serial VARCHAR(40) NOT NULL")) {
        echo "[OK]         boletines_log.serial ampliado a VARCHAR(40)\n";
    } else {
        echo "[ERROR]      boletines_log.serial: " . mysqli_error($con) . "\n";
    }
} else {
    echo "[ya aplicado] boletines_log.serial >= 40\n";
}

// Columnas de auditoría de escaneos que usa verificarBoletinPorSerial()
agregarColumna($con, 'boletines_log', 'scan_count',   "INT UNSIGNED NOT NULL DEFAULT 0");
agregarColumna($con, 'boletines_log', 'last_scan_at', "DATETIME NULL DEFAULT NULL");
agregarColumna($con, 'boletines_log', 'last_scan_ip', "VARCHAR(45) NULL DEFAULT NULL");

// Registro de intentos de verificación (auditoría + límite de tasa por IP)
crearTabla($con, 'verificaciones_log', "CREATE TABLE IF NOT EXISTS verificaciones_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    serial_buscado VARCHAR(40) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    resultado TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_verif_ip_fecha (ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// ══════════════════════════════════════════════════════════════════════
// FIN — invalidar caché de feature flags para ver los cambios al momento
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/include/FeatureGuard.php';
FeatureGuard::clearCache();

echo "\nMigración completada.\n";
echo "Recuerda BORRAR este archivo del servidor cuando termines.\n";
