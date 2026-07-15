<?php
// ══════════════════════════════════════════════════════════════════════
// MOTOR ACADÉMICO CONFIGURABLE — capa de acceso a la configuración
// ══════════════════════════════════════════════════════════════════════
// Lee la configuración académica (períodos, tipos de evaluación, políticas
// de calificación, reglas de promoción, FCT, TFG, retos) desde las tablas
// creadas en migrate_db.php sección 12. Mientras feature_academico_config
// esté desactivado (por defecto), estas funciones no se usan en el cálculo
// de notas — ver modelos/calificaciones.php.
require_once __DIR__ . "/conectar.php";
require_once __DIR__ . "/../include/FeatureGuard.php";

// Indica si el motor académico configurable está activo para este centro.
// Mientras esté desactivado, calificaciones.php sigue usando las reglas
// hardcodeadas de siempre (comportamiento idéntico al actual).
function motorAcademicoActivo(): bool {
    return FeatureGuard::check('feature_academico_config');
}

// Devuelve la configuración académica activa (hoy siempre hay como mucho una
// fila con activo=1; idCentro queda reservado para multi-centro futuro).
function obtenerConfigAcademicaActiva(): ?array {
    return dbFetchOne("SELECT * FROM academic_config WHERE activo = 1 ORDER BY idConfig DESC LIMIT 1");
}

function obtenerPoliticaCalificacion(int $idConfig): ?array {
    return dbFetchOne("SELECT * FROM grading_policies WHERE idConfig = ?", "i", $idConfig);
}

function obtenerReglasPromocion(int $idConfig): ?array {
    return dbFetchOne("SELECT * FROM promotion_rules WHERE idConfig = ?", "i", $idConfig);
}

function obtenerConfigFCT(int $idConfig): ?array {
    return dbFetchOne("SELECT * FROM internship_config WHERE idConfig = ?", "i", $idConfig);
}

function obtenerConfigTFG(int $idConfig): ?array {
    return dbFetchOne("SELECT * FROM tfg_config WHERE idConfig = ?", "i", $idConfig);
}

function obtenerConfigRetos(int $idConfig): ?array {
    return dbFetchOne("SELECT * FROM challenge_config WHERE idConfig = ?", "i", $idConfig);
}

// Tipos de evaluación configurados (Examen, Reto, RA/CE, FCT, TFG, u otros
// definidos por el centro), en el orden en que deben mostrarse/aplicarse.
function listarTiposEvaluacion(int $idConfig): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM assessment_types WHERE idConfig = ? ORDER BY orden ASC, idTipo ASC");
    mysqli_stmt_bind_param($stmt, "i", $idConfig);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $out = [];
    while ($fila = mysqli_fetch_assoc($res)) $out[] = $fila;
    return $out;
}

// Períodos académicos configurados (evaluación, recuperación, extraordinaria...),
// en orden de visualización.
function listarPeriodosAcademicos(int $idConfig): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM academic_periods WHERE idConfig = ? ORDER BY orden ASC, idPeriodo ASC");
    mysqli_stmt_bind_param($stmt, "i", $idConfig);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $out = [];
    while ($fila = mysqli_fetch_assoc($res)) $out[] = $fila;
    return $out;
}

// Cursos académicos (1º, 2º, o los que el centro defina) de un ciclo.
function listarCursosDeCiclo(int $idCiclo): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM cursos_academicos WHERE idCiclo = ? ORDER BY orden ASC");
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $out = [];
    while ($fila = mysqli_fetch_assoc($res)) $out[] = $fila;
    return $out;
}

// Cursos académicos de todos los ciclos, para embeber en formularios (mismo
// patrón que listarTodosLosCiclos()) y poblar el <select> de año en JS
// filtrando por idCiclo, en vez de hardcodear "1º"/"2º".
function listarTodosLosCursosAcademicos(): array {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT idCurso, idCiclo, nombre, orden FROM cursos_academicos ORDER BY idCiclo ASC, orden ASC");
    $out = [];
    while ($fila = mysqli_fetch_assoc($res)) $out[] = $fila;
    return $out;
}

// ¿El nombre dado corresponde a un curso académico configurado para este ciclo?
// Sustituye la validación contra whitelist fija ['1º','2º'] en los controladores.
function existeNombreCursoEnCiclo(int $idCiclo, string $nombre): bool {
    if ($nombre === '') return true; // "sin especificar" sigue siendo válido
    foreach (listarCursosDeCiclo($idCiclo) as $curso) {
        if ($curso['nombre'] === $nombre) return true;
    }
    return false;
}

// ══════════════════════════════════════════════════════════════════════
// ESCRITURA — usadas por el asistente de configuración (wizard, STEP 1-9)
// ══════════════════════════════════════════════════════════════════════

// Crea una configuración nueva (vacía, sin activar) y sus 6 tablas hijas de
// 1 fila con los valores por defecto que hoy reproducen el comportamiento
// hardcodeado (igual que el sembrado inicial de migrate_db.php sección 12).
function crearConfigAcademicaVacia(string $nombre, string $tipoEducacion, ?string $anioAcademico = null): int|false {
    $con = obtenerConexion();
    mysqli_begin_transaction($con);
    try {
        $stmt = mysqli_prepare($con, "INSERT INTO academic_config (nombre, tipoEducacion, anioAcademico, activo) VALUES (?, ?, ?, 0)");
        mysqli_stmt_bind_param($stmt, "sss", $nombre, $tipoEducacion, $anioAcademico);
        mysqli_stmt_execute($stmt);
        $idConfig = (int)mysqli_insert_id($con);

        mysqli_query($con, "INSERT INTO grading_policies (idConfig) VALUES ($idConfig)");
        mysqli_query($con, "INSERT INTO promotion_rules (idConfig) VALUES ($idConfig)");
        mysqli_query($con, "INSERT INTO internship_config (idConfig) VALUES ($idConfig)");
        mysqli_query($con, "INSERT INTO tfg_config (idConfig) VALUES ($idConfig)");
        mysqli_query($con, "INSERT INTO challenge_config (idConfig) VALUES ($idConfig)");
        mysqli_query($con, "INSERT INTO assessment_types (idConfig, nombre, peso, obligatorio, origen, orden) VALUES
            ($idConfig, 'Examen', 3.00, 1, 'examen', 1), ($idConfig, 'Reto', 1.00, 0, 'reto', 2)");

        mysqli_commit($con);
        return $idConfig;
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        error_log('[AulaPro] crearConfigAcademicaVacia: ' . $e->getMessage());
        return false;
    }
}

// Activa una configuración (y desactiva cualquier otra) — "aplicar" desde
// el asistente. Con multi-centro futuro, esto se filtraría por idCentro.
function activarConfigAcademica(int $idConfig): bool {
    $con = obtenerConexion();
    // Comprobación explícita de existencia: un UPDATE ... WHERE idConfig=X que
    // no encuentra ninguna fila devuelve éxito igualmente (mysqli_query() solo
    // informa de si la sentencia se ejecutó, no de si afectó a alguna fila).
    // Sin esto, activar un idConfig inexistente desactivaba todas las
    // configuraciones (incluida la real) y devolvía true — se detectó
    // probando este caso a propósito antes de darlo por terminado.
    if (!dbFetchOne("SELECT idConfig FROM academic_config WHERE idConfig = ?", "i", $idConfig)) {
        return false;
    }
    mysqli_begin_transaction($con);
    try {
        if (!mysqli_query($con, "UPDATE academic_config SET activo = 0")) throw new \RuntimeException(mysqli_error($con));
        $stmt = mysqli_prepare($con, "UPDATE academic_config SET activo = 1 WHERE idConfig = ?");
        mysqli_stmt_bind_param($stmt, "i", $idConfig);
        if (!mysqli_stmt_execute($stmt) || mysqli_stmt_affected_rows($stmt) !== 1) {
            throw new \RuntimeException('activación no aplicada');
        }
        mysqli_commit($con);
        return true;
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        error_log('[AulaPro] activarConfigAcademica: ' . $e->getMessage());
        return false;
    }
}

function actualizarInfoGeneralConfig(int $idConfig, string $nombre, string $tipoEducacion, ?string $anioAcademico): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE academic_config SET nombre = ?, tipoEducacion = ?, anioAcademico = ? WHERE idConfig = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $tipoEducacion, $anioAcademico, $idConfig);
    return mysqli_stmt_execute($stmt);
}

function actualizarPoliticaCalificacion(int $idConfig, float $escalaMin, float $escalaMax, float $notaAprobado, int $decimales, float $pesoTfgEnMedia): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "UPDATE grading_policies SET escalaMin=?, escalaMax=?, notaAprobado=?, decimales=?, pesoTfgEnMedia=? WHERE idConfig=?");
    mysqli_stmt_bind_param($stmt, "dddidi", $escalaMin, $escalaMax, $notaAprobado, $decimales, $pesoTfgEnMedia, $idConfig);
    return mysqli_stmt_execute($stmt);
}

function actualizarReglasPromocion(int $idConfig, bool $requiereTodosModulos, float $notaMinimaGlobal, int $permiteModulosPendientes): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "UPDATE promotion_rules SET requiereTodosModulos=?, notaMinimaGlobal=?, permiteModulosPendientes=? WHERE idConfig=?");
    $req = (int)$requiereTodosModulos;
    mysqli_stmt_bind_param($stmt, "idii", $req, $notaMinimaGlobal, $permiteModulosPendientes, $idConfig);
    return mysqli_stmt_execute($stmt);
}

function actualizarConfigFCT(int $idConfig, bool $habilitado, int $horasRequeridasDefecto, string $metodoEvaluacion, float $pesoEnMedia, bool $requiereAprobar): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "UPDATE internship_config SET habilitado=?, horasRequeridasDefecto=?, metodoEvaluacion=?, pesoEnMedia=?, requiereAprobarParaTitular=? WHERE idConfig=?");
    $hab = (int)$habilitado; $req = (int)$requiereAprobar;
    mysqli_stmt_bind_param($stmt, "iisdii", $hab, $horasRequeridasDefecto, $metodoEvaluacion, $pesoEnMedia, $req, $idConfig);
    return mysqli_stmt_execute($stmt);
}

function actualizarConfigTFG(int $idConfig, bool $habilitado, bool $requiereComite, bool $requiereDefensa, float $notaMinima, float $pesoEnMedia, bool $permiteRecuperacion): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "UPDATE tfg_config SET habilitado=?, requiereComite=?, requiereDefensa=?, notaMinima=?, pesoEnMedia=?, permiteRecuperacion=? WHERE idConfig=?");
    $hab = (int)$habilitado; $com = (int)$requiereComite; $def = (int)$requiereDefensa; $rec = (int)$permiteRecuperacion;
    mysqli_stmt_bind_param($stmt, "iiiddii", $hab, $com, $def, $notaMinima, $pesoEnMedia, $rec, $idConfig);
    return mysqli_stmt_execute($stmt);
}

function actualizarConfigRetos(int $idConfig, float $pesoDefecto, bool $permiteGrupal, bool $permiteFases, bool $requiereRubrica, bool $evaluacionPares): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "UPDATE challenge_config SET pesoDefecto=?, permiteGrupal=?, permiteFases=?, requiereRubrica=?, evaluacionPares=? WHERE idConfig=?");
    $grp = (int)$permiteGrupal; $fas = (int)$permiteFases; $rub = (int)$requiereRubrica; $par = (int)$evaluacionPares;
    mysqli_stmt_bind_param($stmt, "diiiii", $pesoDefecto, $grp, $fas, $rub, $par, $idConfig);
    return mysqli_stmt_execute($stmt);
}

// ── Períodos académicos: alta/edición/baja individual (STEP 4) ──
function guardarPeriodoAcademico(int $idConfig, ?int $idPeriodo, string $nombre, string $tipo, ?string $fechaInicio, ?string $fechaFin, int $orden, bool $visible, bool $bloqueado, ?int $idPeriodoRecuperaDe): int|false {
    $con = obtenerConexion();
    $vis = (int)$visible; $bloq = (int)$bloqueado;
    if ($idPeriodo) {
        $stmt = mysqli_prepare($con,
            "UPDATE academic_periods SET nombre=?, tipo=?, fechaInicio=?, fechaFin=?, orden=?, visible=?, bloqueado=?, idPeriodoRecuperaDe=?
             WHERE idPeriodo=? AND idConfig=?");
        mysqli_stmt_bind_param($stmt, "ssssiiiiii", $nombre, $tipo, $fechaInicio, $fechaFin, $orden, $vis, $bloq, $idPeriodoRecuperaDe, $idPeriodo, $idConfig);
        return mysqli_stmt_execute($stmt) ? $idPeriodo : false;
    }
    $stmt = mysqli_prepare($con,
        "INSERT INTO academic_periods (idConfig, nombre, tipo, fechaInicio, fechaFin, orden, visible, bloqueado, idPeriodoRecuperaDe)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issssiiii", $idConfig, $nombre, $tipo, $fechaInicio, $fechaFin, $orden, $vis, $bloq, $idPeriodoRecuperaDe);
    if (!mysqli_stmt_execute($stmt)) return false;
    return (int)mysqli_insert_id($con);
}

function eliminarPeriodoAcademico(int $idConfig, int $idPeriodo): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM academic_periods WHERE idPeriodo = ? AND idConfig = ?");
    mysqli_stmt_bind_param($stmt, "ii", $idPeriodo, $idConfig);
    return mysqli_stmt_execute($stmt);
}

// ── Tipos de evaluación: alta/edición/baja individual (STEP 5) ──
function guardarTipoEvaluacion(int $idConfig, ?int $idTipo, string $nombre, float $notaMaxima, float $peso, bool $obligatorio, bool $recuperable, bool $incluirEnMedia, string $origen, int $orden): int|false {
    $con = obtenerConexion();
    $obl = (int)$obligatorio; $rec = (int)$recuperable; $inc = (int)$incluirEnMedia;
    if ($idTipo) {
        $stmt = mysqli_prepare($con,
            "UPDATE assessment_types SET nombre=?, notaMaxima=?, peso=?, obligatorio=?, recuperable=?, incluirEnMedia=?, origen=?, orden=?
             WHERE idTipo=? AND idConfig=?");
        mysqli_stmt_bind_param($stmt, "sddiiisiii", $nombre, $notaMaxima, $peso, $obl, $rec, $inc, $origen, $orden, $idTipo, $idConfig);
        return mysqli_stmt_execute($stmt) ? $idTipo : false;
    }
    $stmt = mysqli_prepare($con,
        "INSERT INTO assessment_types (idConfig, nombre, notaMaxima, peso, obligatorio, recuperable, incluirEnMedia, origen, orden)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isddiiisi", $idConfig, $nombre, $notaMaxima, $peso, $obl, $rec, $inc, $origen, $orden);
    if (!mysqli_stmt_execute($stmt)) return false;
    return (int)mysqli_insert_id($con);
}

function eliminarTipoEvaluacion(int $idConfig, int $idTipo): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM assessment_types WHERE idTipo = ? AND idConfig = ?");
    mysqli_stmt_bind_param($stmt, "ii", $idTipo, $idConfig);
    return mysqli_stmt_execute($stmt);
}

// ── Cursos académicos: alta/edición/baja individual (STEP 2) ──
function guardarCursoAcademico(int $idCiclo, ?int $idCurso, string $nombre, int $orden): int|false {
    $con = obtenerConexion();
    if ($idCurso) {
        $stmt = mysqli_prepare($con, "UPDATE cursos_academicos SET nombre=?, orden=? WHERE idCurso=? AND idCiclo=?");
        mysqli_stmt_bind_param($stmt, "siii", $nombre, $orden, $idCurso, $idCiclo);
        return mysqli_stmt_execute($stmt) ? $idCurso : false;
    }
    $stmt = mysqli_prepare($con, "INSERT INTO cursos_academicos (idCiclo, nombre, orden) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isi", $idCiclo, $nombre, $orden);
    if (!mysqli_stmt_execute($stmt)) return false;
    return (int)mysqli_insert_id($con);
}

function eliminarCursoAcademico(int $idCiclo, int $idCurso): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM cursos_academicos WHERE idCurso = ? AND idCiclo = ?");
    mysqli_stmt_bind_param($stmt, "ii", $idCurso, $idCiclo);
    return mysqli_stmt_execute($stmt);
}
