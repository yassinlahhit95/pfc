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

function indiceExiste($con, $tabla, $indice) {
    $stmt = mysqli_prepare($con,
        "SELECT COUNT(*) c FROM information_schema.STATISTICS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $tabla, $indice);
    mysqli_stmt_execute($stmt);
    return (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['c'] > 0;
}

function agregarIndice($con, $tabla, $indice, $columnas) {
    if (indiceExiste($con, $tabla, $indice)) {
        echo "[ya aplicado] $tabla.$indice\n";
        return;
    }
    if (mysqli_query($con, "ALTER TABLE `$tabla` ADD INDEX `$indice` ($columnas)")) {
        echo "[OK]         índice $tabla.$indice añadido\n";
    } else {
        echo "[ERROR]      $tabla.$indice: " . mysqli_error($con) . "\n";
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

// Estas columnas ya existen en instalaciones antiguas (venían en el dump base,
// noDeploy/database.sql) pero nunca se registraron aquí — una instancia nueva
// creada solo a partir de migrate_db.php se quedaría sin ellas y los toggles
// del panel de configuración fallarían al hacer UPDATE sobre una columna inexistente.
agregarColumna($con, 'configuracion_centro', 'feature_prematricula',   "TINYINT(1) NOT NULL DEFAULT 1");
agregarColumna($con, 'configuracion_centro', 'feature_chat',           "TINYINT(1) NOT NULL DEFAULT 1");
agregarColumna($con, 'configuracion_centro', 'feature_inventario',     "TINYINT(1) NOT NULL DEFAULT 1");
agregarColumna($con, 'configuracion_centro', 'feature_subida_tfg',     "TINYINT(1) NOT NULL DEFAULT 1");
agregarColumna($con, 'configuracion_centro', 'feature_horario',        "TINYINT(1) DEFAULT 1");
agregarColumna($con, 'configuracion_centro', 'feature_anuncios',       "TINYINT(1) DEFAULT 1");
agregarColumna($con, 'configuracion_centro', 'feature_eventos',        "TINYINT(1) DEFAULT 1");
agregarColumna($con, 'configuracion_centro', 'feature_retos',          "TINYINT(1) DEFAULT 1");
agregarColumna($con, 'configuracion_centro', 'feature_mensajes',       "TINYINT(1) DEFAULT 1");
agregarColumna($con, 'configuracion_centro', 'feature_pagos',          "TINYINT(1) DEFAULT 1");
agregarColumna($con, 'configuracion_centro', 'feature_gastos',         "TINYINT(1) DEFAULT 1");
agregarColumna($con, 'configuracion_centro', 'feature_informes',       "TINYINT(1) DEFAULT 1");
agregarColumna($con, 'configuracion_centro', 'feature_geoblock_admin', "TINYINT(1) NOT NULL DEFAULT 1");

// prematricula_filtrar_niveles nunca se había registrado aquí — sin esta línea, una
// instancia nueva no tiene la columna y el toggle del panel de configuración falla
// al hacer UPDATE, y vistas/admisiones/pre-matricula.php siempre lee 0 en silencio.
agregarColumna($con, 'configuracion_centro', 'prematricula_filtrar_niveles', "TINYINT(1) NOT NULL DEFAULT 0");

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
// 8. Módulos: código oficial (RD) de cada módulo profesional
// ══════════════════════════════════════════════════════════════════════
agregarColumna($con, 'modulos', 'codigoModulo', "VARCHAR(20) NULL AFTER `nombreModulo`");

// ══════════════════════════════════════════════════════════════════════
// 9. Estudiantes: año de estudio (1º/2º). Una migración anterior sobrescribió
//    por error la columna `curso` (Grado Medio/Superior) con el enum 1º/2º
//    en lugar de crear esta columna nueva, dejando a todos los estudiantes
//    con curso='1º' y rompiendo el guardado de ficha (columna anioEstudio
//    inexistente). Aquí se añade la columna que faltaba y, si se detecta la
//    corrupción, se restaura `curso` a partir del nivel del ciclo del alumno.
// ══════════════════════════════════════════════════════════════════════
agregarColumna($con, 'estudiantes', 'anioEstudio', "ENUM('1º','2º') NULL AFTER `curso`");

$cursoTipo = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COLUMN_TYPE ct FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'estudiantes' AND COLUMN_NAME = 'curso'"))['ct'] ?? '';
if (strpos($cursoTipo, "'Grado Medio'") === false) {
    if (mysqli_query($con, "ALTER TABLE estudiantes MODIFY curso VARCHAR(20) NULL")) {
        mysqli_query($con, "UPDATE estudiantes e
            JOIN ciclos c ON e.idCiclo = c.idCiclo
            JOIN niveles n ON c.idNivel = n.idNivel
            SET e.curso = n.nombreNivel");
        if (mysqli_query($con, "ALTER TABLE estudiantes MODIFY curso ENUM('Grado Medio','Grado Superior') NULL")) {
            echo "[OK]         estudiantes.curso restaurado desde el nivel del ciclo\n";
        } else {
            echo "[ERROR]      estudiantes.curso (enum final): " . mysqli_error($con) . "\n";
        }
    } else {
        echo "[ERROR]      estudiantes.curso (ampliar): " . mysqli_error($con) . "\n";
    }
} else {
    echo "[ya aplicado] estudiantes.curso ya tiene el enum correcto\n";
}

// ══════════════════════════════════════════════════════════════════════
// 10. Índice compuesto en chat_mensajes: las consultas del contador de no
//     leídos y del último mensaje por conversación (chatContarNoLeidos,
//     chatConversacionesDe — usadas en el sondeo del chat en cada página,
//     los 5 roles) solo tenían índices de una columna (conversacion_id,
//     leido, fecha por separado). Con un índice compuesto MySQL puede
//     resolver el filtro conversacion_id + leido en una sola búsqueda.
// ══════════════════════════════════════════════════════════════════════
agregarIndice($con, 'chat_mensajes', 'idx_msg_conv_leido', '`conversacion_id`, `leido`');

// ══════════════════════════════════════════════════════════════════════
// 11. Blog / noticias del centro. Antes se creaba con CREATE TABLE IF NOT
//     EXISTS en cada request (modelos/blog.php), una sentencia DDL costosa
//     y sin sentido después de la primera vez. Se crea aquí, una sola vez.
// ══════════════════════════════════════════════════════════════════════
crearTabla($con, 'blog_posts', "CREATE TABLE IF NOT EXISTS blog_posts (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// ══════════════════════════════════════════════════════════════════════
// 12. Motor académico configurable — sustituye reglas antes hardcodeadas
//     (peso examen/reto 0.75/0.25, nota de aprobado = 5, exactamente 2
//     evaluaciones, etc. — ver modelos/academico_config.php) por
//     configuración en BD. Aditivo: no se toca ni se borra ninguna tabla
//     ni columna existente; calificaciones_modulos sigue intacta como red
//     de seguridad. feature_academico_config controla si el motor nuevo
//     está activo (por defecto 0: nada cambia hasta que se ejecute el
//     asistente de configuración, ver seedConfiguracionPorDefecto() más abajo).
// ══════════════════════════════════════════════════════════════════════
agregarColumna($con, 'configuracion_centro', 'feature_academico_config', "TINYINT(1) NOT NULL DEFAULT 0");

crearTabla($con, 'academic_config', "CREATE TABLE IF NOT EXISTS academic_config (
    idConfig INT AUTO_INCREMENT PRIMARY KEY,
    idCentro INT NULL COMMENT 'reservado para multi-centro futuro; hoy siempre NULL',
    nombre VARCHAR(150) NOT NULL DEFAULT 'Configuración académica',
    anioAcademico VARCHAR(9) NULL COMMENT 'p.ej. 2026-2027',
    tipoEducacion ENUM('grado_medio','grado_superior','otro') NOT NULL DEFAULT 'otro',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creadoEn TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizadoEn TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_ac_centro_activo (idCentro, activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

crearTabla($con, 'cursos_academicos', "CREATE TABLE IF NOT EXISTS cursos_academicos (
    idCurso INT AUTO_INCREMENT PRIMARY KEY,
    idCiclo INT NOT NULL,
    nombre VARCHAR(40) NOT NULL COMMENT 'p.ej. 1º, 2º, o nombre libre',
    orden INT NOT NULL DEFAULT 1,
    FOREIGN KEY (idCiclo) REFERENCES ciclos(idCiclo) ON DELETE CASCADE,
    UNIQUE KEY uk_curso_ciclo_orden (idCiclo, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// modulos.cursoAnio y modulos.creditosECTS ya se usan en modelos/modulos.php
// (insertarModulo/actualizarModulo) pero no tenían ALTER registrado en este
// migrador — deuda de esquema detectada durante el análisis. Se añaden aquí
// de forma idempotente (si ya existen en producción, agregarColumna lo detecta
// y no hace nada).
agregarColumna($con, 'modulos', 'cursoAnio',     "VARCHAR(10) NULL AFTER `nombreModulo`");
agregarColumna($con, 'modulos', 'creditosECTS',  "INT NULL AFTER `horasMaximas`");
agregarColumna($con, 'modulos', 'idCurso',       "INT NULL AFTER `idCiclo`");
agregarColumna($con, 'estudiantes', 'idCurso',   "INT NULL AFTER `anioEstudio`");
agregarIndice($con, 'modulos', 'idx_modulo_curso', '`idCurso`');
agregarIndice($con, 'estudiantes', 'idx_est_curso', '`idCurso`');

// Ciclos creados antes de que insertarNuevoCiclo() sembrara cursos_academicos
// por defecto se quedarían sin opciones de año en los formularios de módulos/
// estudiantes (que ahora leen esta tabla en vez de un "1º"/"2º" hardcodeado).
// Backfill idempotente: solo toca ciclos que hoy tienen 0 filas en cursos_academicos.
$resCiclosSinCurso = mysqli_query($con, "SELECT idCiclo FROM ciclos WHERE idCiclo NOT IN (SELECT DISTINCT idCiclo FROM cursos_academicos)");
if ($resCiclosSinCurso) {
    $stmtCurso = mysqli_prepare($con, "INSERT INTO cursos_academicos (idCiclo, nombre, orden) VALUES (?, ?, ?)");
    while ($filaCiclo = mysqli_fetch_assoc($resCiclosSinCurso)) {
        $idCicloBackfill = (int)$filaCiclo['idCiclo'];
        foreach ([['1º', 1], ['2º', 2]] as [$nombreCurso, $orden]) {
            mysqli_stmt_bind_param($stmtCurso, "isi", $idCicloBackfill, $nombreCurso, $orden);
            mysqli_stmt_execute($stmtCurso);
        }
    }
    echo "Backfill de cursos_academicos para ciclos existentes: OK\n";
}

crearTabla($con, 'academic_periods', "CREATE TABLE IF NOT EXISTS academic_periods (
    idPeriodo INT AUTO_INCREMENT PRIMARY KEY,
    idConfig INT NOT NULL,
    nombre VARCHAR(80) NOT NULL,
    tipo ENUM('evaluacion','recuperacion','ordinaria','extraordinaria','final','proyecto','practicas','certificacion','otro') NOT NULL DEFAULT 'evaluacion',
    fechaInicio DATE NULL,
    fechaFin DATE NULL,
    orden INT NOT NULL DEFAULT 1,
    visible TINYINT(1) NOT NULL DEFAULT 1,
    bloqueado TINYINT(1) NOT NULL DEFAULT 0,
    peso DECIMAL(5,2) NOT NULL DEFAULT 100.00,
    idPeriodoRecuperaDe INT NULL COMMENT 'si tipo=recuperacion, a qué período ordinario sustituye (solo si la nota de recuperación es mayor)',
    FOREIGN KEY (idConfig) REFERENCES academic_config(idConfig) ON DELETE CASCADE,
    FOREIGN KEY (idPeriodoRecuperaDe) REFERENCES academic_periods(idPeriodo) ON DELETE SET NULL,
    KEY idx_periodo_config_orden (idConfig, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

crearTabla($con, 'assessment_types', "CREATE TABLE IF NOT EXISTS assessment_types (
    idTipo INT AUTO_INCREMENT PRIMARY KEY,
    idConfig INT NOT NULL,
    nombre VARCHAR(80) NOT NULL,
    notaMaxima DECIMAL(4,2) NOT NULL DEFAULT 10.00,
    peso DECIMAL(6,2) NOT NULL DEFAULT 1.00 COMMENT 'peso relativo dentro de la media ponderada del módulo',
    aprobadoMinimo DECIMAL(4,2) NULL,
    obligatorio TINYINT(1) NOT NULL DEFAULT 0,
    recuperable TINYINT(1) NOT NULL DEFAULT 1,
    visible TINYINT(1) NOT NULL DEFAULT 1,
    editableProfesor TINYINT(1) NOT NULL DEFAULT 1,
    editableDirector TINYINT(1) NOT NULL DEFAULT 1,
    incluirEnMedia TINYINT(1) NOT NULL DEFAULT 1,
    origen ENUM('examen','reto','ra_ce','fct','tfg','otro') NOT NULL DEFAULT 'otro' COMMENT 'de qué tabla de datos se alimenta este tipo',
    orden INT NOT NULL DEFAULT 1,
    FOREIGN KEY (idConfig) REFERENCES academic_config(idConfig) ON DELETE CASCADE,
    KEY idx_tipo_config (idConfig)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

crearTabla($con, 'calificaciones_periodo', "CREATE TABLE IF NOT EXISTS calificaciones_periodo (
    idCalificacion INT AUTO_INCREMENT PRIMARY KEY,
    idEstudiante INT NOT NULL,
    idModulo INT NOT NULL,
    idPeriodo INT NOT NULL,
    idTipo INT NOT NULL,
    nota DECIMAL(4,2) NULL,
    estado VARCHAR(2) NULL COMMENT 'NP/EX/CO u otro código configurable',
    observaciones TEXT NULL,
    actualizadoEn TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (idEstudiante) REFERENCES estudiantes(idEstudiante) ON DELETE CASCADE,
    FOREIGN KEY (idModulo) REFERENCES modulos(idModulo) ON DELETE CASCADE,
    FOREIGN KEY (idPeriodo) REFERENCES academic_periods(idPeriodo) ON DELETE CASCADE,
    FOREIGN KEY (idTipo) REFERENCES assessment_types(idTipo) ON DELETE CASCADE,
    UNIQUE KEY uk_cp_est_mod_periodo_tipo (idEstudiante, idModulo, idPeriodo, idTipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

crearTabla($con, 'grading_policies', "CREATE TABLE IF NOT EXISTS grading_policies (
    idPolitica INT AUTO_INCREMENT PRIMARY KEY,
    idConfig INT NOT NULL,
    escalaMin DECIMAL(4,2) NOT NULL DEFAULT 0.00,
    escalaMax DECIMAL(4,2) NOT NULL DEFAULT 10.00,
    notaAprobado DECIMAL(4,2) NOT NULL DEFAULT 5.00,
    decimales TINYINT NOT NULL DEFAULT 0, -- normativa de FP española: nota final del módulo en entero (1-10)
    pesoTfgEnMedia DECIMAL(6,2) NOT NULL DEFAULT 1.00 COMMENT 'peso del TFG frente a 1 módulo en la media global',
    FOREIGN KEY (idConfig) REFERENCES academic_config(idConfig) ON DELETE CASCADE,
    UNIQUE KEY uk_gp_config (idConfig)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

crearTabla($con, 'promotion_rules', "CREATE TABLE IF NOT EXISTS promotion_rules (
    idRegla INT AUTO_INCREMENT PRIMARY KEY,
    idConfig INT NOT NULL,
    requiereTodosModulos TINYINT(1) NOT NULL DEFAULT 1,
    notaMinimaGlobal DECIMAL(4,2) NOT NULL DEFAULT 5.00,
    permiteModulosPendientes INT NOT NULL DEFAULT 0,
    FOREIGN KEY (idConfig) REFERENCES academic_config(idConfig) ON DELETE CASCADE,
    UNIQUE KEY uk_pr_config (idConfig)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

crearTabla($con, 'internship_config', "CREATE TABLE IF NOT EXISTS internship_config (
    idConfigFCT INT AUTO_INCREMENT PRIMARY KEY,
    idConfig INT NOT NULL,
    habilitado TINYINT(1) NOT NULL DEFAULT 0,
    horasRequeridasDefecto INT NOT NULL DEFAULT 0,
    metodoEvaluacion ENUM('nota','apto_no_apto','ambos') NOT NULL DEFAULT 'ambos',
    pesoEnMedia DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    requiereAprobarParaTitular TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (idConfig) REFERENCES academic_config(idConfig) ON DELETE CASCADE,
    UNIQUE KEY uk_ic_config (idConfig)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// La tabla `fct` existía en instalaciones antiguas (creada fuera de este
// migrador) pero nunca se registró aquí — una instancia nueva se quedaría
// sin ella. crearTabla() con IF NOT EXISTS es un no-op en las que ya la
// tienen; agregarColumna()/agregarIndice() de abajo cubren la que sí la
// tenían pero sin idEmpresa todavía.
crearTabla($con, 'fct', "CREATE TABLE IF NOT EXISTS fct (
    idFCT INT AUTO_INCREMENT PRIMARY KEY,
    idEstudiante INT NOT NULL,
    idCiclo INT NOT NULL,
    empresa VARCHAR(200) NOT NULL,
    idEmpresa INT NULL,
    tutorEmpresa VARCHAR(150) NULL,
    emailTutorEmpresa VARCHAR(150) NULL,
    telefonoEmpresa VARCHAR(20) NULL,
    ciudadEmpresa VARCHAR(100) NULL,
    fechaInicio DATE NULL,
    fechaFin DATE NULL,
    horasTotales INT NULL,
    horasRealizadas INT NULL,
    nota DECIMAL(4,2) NULL,
    apto TINYINT(1) NULL,
    observaciones TEXT NULL,
    idProfesorTutor INT NULL,
    fase INT NOT NULL DEFAULT 1,
    creado_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_fct_est_ciclo_fase (idEstudiante, idCiclo, fase),
    KEY idx_fct_ciclo (idCiclo),
    KEY idx_fct_profesor (idProfesorTutor),
    KEY idx_fct_empresa (idEmpresa),
    FOREIGN KEY (idCiclo) REFERENCES ciclos(idCiclo) ON DELETE CASCADE,
    FOREIGN KEY (idEstudiante) REFERENCES estudiantes(idEstudiante) ON DELETE CASCADE,
    FOREIGN KEY (idProfesorTutor) REFERENCES profesores(idProfesor) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// La tabla `fct` de instalaciones antiguas enlazaba la empresa solo por texto
// libre. Se añade idEmpresa como FK opcional hacia fp_empresas sin tocar la
// columna `empresa` existente, para no romper filas ya guardadas.
agregarColumna($con, 'fct', 'idEmpresa', "INT NULL AFTER `empresa`");
agregarIndice($con, 'fct', 'idx_fct_empresa', '`idEmpresa`');

// Feature flag: gestión de FCT (alta de prácticas + seguimiento). Activada
// por defecto porque la FCT es obligatoria por normativa para poder titular
// en ambos grados — no es un módulo "opcional" como chat/inventario.
agregarColumna($con, 'configuracion_centro', 'feature_fct', "TINYINT(1) NOT NULL DEFAULT 1");

crearTabla($con, 'tfg_config', "CREATE TABLE IF NOT EXISTS tfg_config (
    idConfigTFG INT AUTO_INCREMENT PRIMARY KEY,
    idConfig INT NOT NULL,
    habilitado TINYINT(1) NOT NULL DEFAULT 1,
    requiereComite TINYINT(1) NOT NULL DEFAULT 0,
    requiereDefensa TINYINT(1) NOT NULL DEFAULT 0,
    notaMinima DECIMAL(4,2) NOT NULL DEFAULT 5.00,
    pesoEnMedia DECIMAL(6,2) NOT NULL DEFAULT 1.00,
    permiteRecuperacion TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (idConfig) REFERENCES academic_config(idConfig) ON DELETE CASCADE,
    UNIQUE KEY uk_tc_config (idConfig)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// calificaciones_tfg: añadir convocatoria y ampliar la unicidad de
// (idEstudiante) a (idEstudiante, convocatoria) para que una recuperación
// no sobrescriba la nota anterior. Bloque idempotente a medida porque
// agregarIndice() no sustituye una UNIQUE KEY existente.
agregarColumna($con, 'calificaciones_tfg', 'convocatoria',
    "ENUM('ordinaria','extraordinaria') NOT NULL DEFAULT 'ordinaria' AFTER `idEstudiante`");
$tfgUniqueActual = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COUNT(*) c FROM information_schema.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'calificaciones_tfg'
       AND INDEX_NAME = 'uk_est_tfg' AND COLUMN_NAME = 'convocatoria'"))['c'] ?? 0;
if ((int)$tfgUniqueActual === 0) {
    // DROP + ADD deben ir en la MISMA sentencia ALTER TABLE: idEstudiante
    // tiene una FK hacia estudiantes(idEstudiante) que necesita un índice de
    // apoyo en todo momento. Si se hacen en dos ALTER separados, al llegar el
    // DROP ya no quedaría ningún índice sobre idEstudiante y MySQL lo rechaza
    // (error 1553) aunque el ADD de la misma transacción fuera a cubrirlo.
    if (mysqli_query($con, "ALTER TABLE calificaciones_tfg
            DROP INDEX uk_est_tfg,
            ADD UNIQUE KEY uk_est_tfg (idEstudiante, convocatoria)")) {
        echo "[OK]         calificaciones_tfg.uk_est_tfg ampliada a (idEstudiante, convocatoria)\n";
    } else {
        echo "[ERROR]      calificaciones_tfg.uk_est_tfg: " . mysqli_error($con) . "\n";
    }
} else {
    echo "[ya aplicado] calificaciones_tfg.uk_est_tfg ya incluye convocatoria\n";
}

crearTabla($con, 'challenge_config', "CREATE TABLE IF NOT EXISTS challenge_config (
    idConfigReto INT AUTO_INCREMENT PRIMARY KEY,
    idConfig INT NOT NULL,
    pesoDefecto DECIMAL(6,2) NOT NULL DEFAULT 1.00,
    permiteGrupal TINYINT(1) NOT NULL DEFAULT 0,
    permiteFases TINYINT(1) NOT NULL DEFAULT 0,
    requiereRubrica TINYINT(1) NOT NULL DEFAULT 0,
    evaluacionPares TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (idConfig) REFERENCES academic_config(idConfig) ON DELETE CASCADE,
    UNIQUE KEY uk_cc_config (idConfig)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

crearTabla($con, 'rubrics', "CREATE TABLE IF NOT EXISTS rubrics (
    idRubrica INT AUTO_INCREMENT PRIMARY KEY,
    ambito ENUM('reto','tfg','fct') NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

crearTabla($con, 'rubric_criteria', "CREATE TABLE IF NOT EXISTS rubric_criteria (
    idCriterio INT AUTO_INCREMENT PRIMARY KEY,
    idRubrica INT NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    pesoCriterio DECIMAL(6,2) NOT NULL DEFAULT 1.00,
    notaMaxima DECIMAL(4,2) NOT NULL DEFAULT 10.00,
    orden INT NOT NULL DEFAULT 1,
    FOREIGN KEY (idRubrica) REFERENCES rubrics(idRubrica) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

crearTabla($con, 'academic_templates', "CREATE TABLE IF NOT EXISTS academic_templates (
    idPlantilla INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion VARCHAR(500) NULL,
    configuracionJson JSON NOT NULL,
    editable TINYINT(1) NOT NULL DEFAULT 1,
    creadoEn TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// RA/CE → motor de medias ponderadas: un RA puede enlazarse a un
// assessment_type ('ra_ce') para que su `porcentaje` alimente la media del
// módulo igual que exámenes o retos. Nullable: si no se enlaza, RA/CE sigue
// funcionando exactamente igual que hoy (documentación sin efecto en la nota).
agregarColumna($con, 'resultados_aprendizaje', 'idTipo', "INT NULL AFTER `porcentaje`");
agregarIndice($con, 'resultados_aprendizaje', 'idx_ra_tipo', '`idTipo`');

// ── Configuración por defecto: reproduce EXACTAMENTE el comportamiento
//    hardcodeado actual (peso examen 3 : reto 1 = 75%/25%, aprobado = 5,
//    2 decimales, TFG cuenta como 1 módulo, recuperación siempre permitida,
//    retos individuales sin fases). feature_academico_config sigue en 0
//    tras esto: nada cambia para nadie hasta que se active explícitamente
//    desde el asistente. Solo se siembra si no existe ya ninguna config.
$yaHayConfig = (int)(mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COUNT(*) c FROM academic_config"))['c'] ?? 0) > 0;
if (!$yaHayConfig) {
    mysqli_query($con, "INSERT INTO academic_config (nombre, tipoEducacion, activo)
        VALUES ('Configuración heredada (auto-generada)', 'otro', 1)");
    $idConfig = (int)mysqli_insert_id($con);

    mysqli_query($con, "INSERT INTO grading_policies
        (idConfig, escalaMin, escalaMax, notaAprobado, decimales, pesoTfgEnMedia)
        VALUES ($idConfig, 0.00, 10.00, 5.00, 2, 1.00)");

    mysqli_query($con, "INSERT INTO promotion_rules
        (idConfig, requiereTodosModulos, notaMinimaGlobal, permiteModulosPendientes)
        VALUES ($idConfig, 1, 5.00, 0)");

    mysqli_query($con, "INSERT INTO assessment_types
        (idConfig, nombre, notaMaxima, peso, obligatorio, recuperable, incluirEnMedia, origen, orden)
        VALUES
        ($idConfig, 'Examen', 10.00, 3.00, 1, 1, 1, 'examen', 1),
        ($idConfig, 'Reto',   10.00, 1.00, 0, 1, 1, 'reto',   2)");

    // 4 períodos que reproducen las 4 columnas actuales (1ev/1final/2ev/2final):
    // 2 evaluaciones ordinarias + su recuperación respectiva, enlazadas vía
    // idPeriodoRecuperaDe (recuperación cuenta solo si su nota es mayor —
    // misma regla que calcularNotaDefinitiva() hoy).
    mysqli_query($con, "INSERT INTO academic_periods (idConfig, nombre, tipo, orden)
        VALUES ($idConfig, '1ª Evaluación', 'evaluacion', 1), ($idConfig, '2ª Evaluación', 'evaluacion', 3)");
    $idPeriodo1Ev = (int)mysqli_insert_id($con);
    $idPeriodo2Ev = $idPeriodo1Ev + 1;
    mysqli_query($con, "INSERT INTO academic_periods (idConfig, nombre, tipo, orden, idPeriodoRecuperaDe)
        VALUES ($idConfig, 'Recuperación 1ª Evaluación', 'recuperacion', 2, $idPeriodo1Ev),
               ($idConfig, 'Recuperación 2ª Evaluación', 'recuperacion', 4, $idPeriodo2Ev)");

    mysqli_query($con, "INSERT INTO internship_config
        (idConfig, habilitado, metodoEvaluacion, pesoEnMedia, requiereAprobarParaTitular)
        VALUES ($idConfig, 0, 'ambos', 0.00, 1)");

    mysqli_query($con, "INSERT INTO tfg_config
        (idConfig, habilitado, requiereComite, requiereDefensa, notaMinima, pesoEnMedia, permiteRecuperacion)
        VALUES ($idConfig, 1, 0, 0, 5.00, 1.00, 1)");

    mysqli_query($con, "INSERT INTO challenge_config
        (idConfig, pesoDefecto, permiteGrupal, permiteFases, requiereRubrica, evaluacionPares)
        VALUES ($idConfig, 1.00, 0, 0, 0, 0)");

    echo "[OK]         configuración académica por defecto sembrada (idConfig=$idConfig)\n";
} else {
    echo "[ya aplicado] ya existe una configuración académica\n";
}

// ── Plantillas de arranque para el asistente (STEP 10). Se generan a partir
//    de la primera configuración que exista (la heredada o la que se acabe
//    de sembrar arriba), con el mismo esquema de exportación que usa
//    "guardar como plantilla" — ver modelos/plantillas_academicas.php.
$yaHayPlantillas = (int)(mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COUNT(*) c FROM academic_templates"))['c'] ?? 0) > 0;
if (!$yaHayPlantillas) {
    require_once __DIR__ . '/modelos/plantillas_academicas.php';
    $configBase = obtenerConfigAcademicaActiva();
    if ($configBase) {
        $snapshot = exportarConfigComoArray((int)$configBase['idConfig']);

        $snapshotMedio = $snapshot;
        $snapshotMedio['config']['tipoEducacion'] = 'grado_medio';
        guardarPlantillaAcademica(
            'Estándar FP Grado Medio',
            'Configuración de partida para ciclos de Grado Medio: 2 evaluaciones + recuperación, examen 75% / reto 25%, aprobado 5.',
            $snapshotMedio, true
        );

        $snapshotSuperior = $snapshot;
        $snapshotSuperior['config']['tipoEducacion'] = 'grado_superior';
        guardarPlantillaAcademica(
            'Estándar FP Grado Superior',
            'Configuración de partida para ciclos de Grado Superior: misma estructura que Grado Medio, totalmente editable tras aplicarla.',
            $snapshotSuperior, true
        );

        echo "[OK]         2 plantillas académicas de arranque sembradas\n";
    } else {
        echo "[omitido]    sin configuración base para generar plantillas\n";
    }
} else {
    echo "[ya aplicado] ya existen plantillas académicas\n";
}

// ══════════════════════════════════════════════════════════════════════
// 13. Catálogo público de ciclos con ficha propia (landing). Independiente
//     de la tabla académica `ciclos` (gestión de alumnos/notas/profesores):
//     esta es contenido de marketing gestionado desde admin/secretaría,
//     igual que blog_posts. La sección "Oferta formativa" del constructor
//     sigue funcionando igual; sus tarjetas pueden enlazar opcionalmente
//     a una ficha de este catálogo (campo cicloSlug).
// ══════════════════════════════════════════════════════════════════════
crearTabla($con, 'landing_ciclos', "CREATE TABLE IF NOT EXISTS landing_ciclos (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// ══════════════════════════════════════════════════════════════════════
// 14. grading_policies.decimales: la nota final de un módulo de FP se
//     expresa en entero (1-10) en la normativa de evaluación española —
//     ver sección 12. Las filas nuevas ya nacen con DEFAULT 0; esto
//     corrige el DEFAULT de instalaciones ya creadas y las filas que
//     nadie ha tocado todavía (siguen en el valor de fábrica antiguo, 2).
// ══════════════════════════════════════════════════════════════════════
if (mysqli_query($con, "ALTER TABLE grading_policies MODIFY decimales TINYINT NOT NULL DEFAULT 0")) {
    echo "[OK]         grading_policies.decimales: DEFAULT -> 0\n";
} else {
    echo "[ERROR]      grading_policies.decimales: " . mysqli_error($con) . "\n";
}
mysqli_query($con, "UPDATE grading_policies SET decimales = 0 WHERE decimales = 2");
echo "[OK]         grading_policies: " . mysqli_affected_rows($con) . " fila(s) sin personalizar reseteadas a 0 decimales\n";

// ══════════════════════════════════════════════════════════════════════
// 15. MFA (2FA) — extendido a los 5 roles.
//     directores ya usaba estas columnas en el código (obtenerMfaDirector,
//     activarMfaDirector) pero nunca se habían registrado aquí — una
//     instancia nueva creada solo desde migrate_db.php no las tenía y el
//     2FA de admin habría fallado silenciosamente al activarse.
// ══════════════════════════════════════════════════════════════════════
foreach (['directores', 'profesores', 'secretarias', 'estudiantes', 'tutores'] as $tablaMfa) {
    agregarColumna($con, $tablaMfa, 'mfa_enabled',      "TINYINT(1) NOT NULL DEFAULT 0");
    agregarColumna($con, $tablaMfa, 'mfa_secret',       "VARCHAR(64) NULL DEFAULT NULL");
    agregarColumna($con, $tablaMfa, 'mfa_backup_codes', "TEXT NULL DEFAULT NULL");
}

// ══════════════════════════════════════════════════════════════════════
// 16. RGPD — rgpd_eliminaciones (Art. 17) ya se usaba en modelos/rgpd.php
//     pero, igual que las columnas MFA de la sección 15, nunca se había
//     registrado aquí. Se añade también rgpd_solicitudes: autoservicio
//     de "solicitar eliminación de mis datos" para los 5 roles — el propio
//     usuario pide la baja, el admin la revisa y la resuelve manualmente
//     (nunca hay auto-borrado real, ver vistas/admin/rgpd/index.php).
// ══════════════════════════════════════════════════════════════════════
crearTabla($con, 'rgpd_eliminaciones', "CREATE TABLE IF NOT EXISTS rgpd_eliminaciones (
    idRgpdEliminacion INT AUTO_INCREMENT PRIMARY KEY,
    idAdmin INT NOT NULL,
    entidad VARCHAR(60) NOT NULL,
    idRegistro INT NOT NULL,
    descripcion VARCHAR(255) NOT NULL DEFAULT '',
    motivo TEXT NOT NULL,
    datos_backup LONGTEXT NULL,
    ip VARCHAR(64) NULL,
    fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_entidad (entidad, idRegistro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

crearTabla($con, 'rgpd_solicitudes', "CREATE TABLE IF NOT EXISTS rgpd_solicitudes (
    idSolicitud INT AUTO_INCREMENT PRIMARY KEY,
    rolSesion VARCHAR(20) NOT NULL,
    idUsuario INT NOT NULL,
    nombreUsuario VARCHAR(255) NOT NULL DEFAULT '',
    emailUsuario VARCHAR(255) NOT NULL DEFAULT '',
    motivo TEXT NOT NULL,
    estado ENUM('pendiente','resuelta') NOT NULL DEFAULT 'pendiente',
    resueltaPorAdmin INT NULL,
    fechaSolicitud TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaResolucion TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// ══════════════════════════════════════════════════════════════════════
// 17. Justificación de faltas — el estudiante sube un justificante para una
//     falta ya registrada, el profesor del módulo lo aprueba o rechaza.
//     asistencias.estado ya admitía 'justificado' como valor (usado hasta
//     ahora solo manualmente por el profesor al pasar lista) — al aprobar
//     una justificación se actualiza esa misma fila a 'justificado'.
//     Sin FK a asistencias: la tabla base no está gestionada por este
//     migrador (viene del dump inicial) y no se puede asumir su motor.
// ══════════════════════════════════════════════════════════════════════
crearTabla($con, 'justificaciones_falta', "CREATE TABLE IF NOT EXISTS justificaciones_falta (
    idJustificacion INT AUTO_INCREMENT PRIMARY KEY,
    idAsistencia INT NOT NULL,
    idEstudiante INT NOT NULL,
    motivo TEXT NOT NULL,
    archivo VARCHAR(255) NULL,
    estado ENUM('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
    idProfesorResuelve INT NULL,
    motivoRechazo VARCHAR(500) NULL,
    fechaSolicitud TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaResolucion TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_asistencia (idAsistencia),
    INDEX idx_estudiante (idEstudiante),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// ══════════════════════════════════════════════════════════════════════
// FIN — invalidar caché de feature flags para ver los cambios al momento
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/include/FeatureGuard.php';
FeatureGuard::clearCache();

echo "\nMigración completada.\n";
echo "Recuerda BORRAR este archivo del servidor cuando termines.\n";
