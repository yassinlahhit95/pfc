<?php
// ══════════════════════════════════════════════════════════════════════
// FCT (Formación en Centros de Trabajo) — capa de acceso a datos.
// Gestión (alta/seguimiento) en vistas/{admin,profesores}/fct/ — feature_fct.
// Integrada en el motor de notas (ver modelos/calificaciones.php y
// modelos/motor_calificaciones.php): con o sin el motor configurable activo,
// aprobar la FCT es obligatorio por normativa para poder titular.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/conectar.php";

function _sqlCiclosFCTDeProfesor(): string {
    return "(f.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
          OR f.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?))";
}

function listarFCTPorCiclo(int $idCiclo): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT f.*, e.nombreEstudiante FROM fct f
         JOIN estudiantes e ON e.idEstudiante = f.idEstudiante
         WHERE f.idCiclo = ? ORDER BY e.nombreEstudiante ASC, f.fase ASC");
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $out = [];
    while ($fila = mysqli_fetch_assoc($res)) $out[] = $fila;
    return $out;
}

function obtenerFCTPorId(int $idFCT): ?array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT f.*, e.nombreEstudiante, e.idCiclo AS idCicloEstudiante
         FROM fct f JOIN estudiantes e ON e.idEstudiante = f.idEstudiante
         WHERE f.idFCT = ?");
    mysqli_stmt_bind_param($stmt, "i", $idFCT);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
}

// FCT de los estudiantes de los ciclos que imparte este profesor (tutor de
// aula, no necesariamente el tutor de empresa asignado en idProfesorTutor)
// — mismo criterio que listarTFGsPorProfesor().
function listarFCTPorProfesor(int $idProfesor): array {
    $con = obtenerConexion();
    $sql = "SELECT DISTINCT f.*, e.nombreEstudiante, c.nombreCiclo
            FROM fct f
            JOIN estudiantes e ON e.idEstudiante = f.idEstudiante
            JOIN ciclos c ON c.idCiclo = f.idCiclo
            WHERE " . _sqlCiclosFCTDeProfesor() . "
            ORDER BY e.nombreEstudiante ASC, f.fase ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $out = [];
    while ($fila = mysqli_fetch_assoc($res)) $out[] = $fila;
    return $out;
}

function insertarFCT(array $datos): int|false {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "INSERT INTO fct (idEstudiante, idCiclo, empresa, idEmpresa, tutorEmpresa, emailTutorEmpresa,
                          telefonoEmpresa, ciudadEmpresa, fechaInicio, fechaFin, horasTotales, fase, idProfesorTutor)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $idEmpresa = $datos['idEmpresa'] ?? null;
    $fase = $datos['fase'] ?? 1;
    $idProfesorTutor = $datos['idProfesorTutor'] ?? null;
    mysqli_stmt_bind_param($stmt, "iisisssssssii",
        $datos['idEstudiante'], $datos['idCiclo'], $datos['empresa'], $idEmpresa,
        $datos['tutorEmpresa'], $datos['emailTutorEmpresa'], $datos['telefonoEmpresa'], $datos['ciudadEmpresa'],
        $datos['fechaInicio'], $datos['fechaFin'], $datos['horasTotales'], $fase, $idProfesorTutor);
    if (!mysqli_stmt_execute($stmt)) return false;
    return mysqli_insert_id($con);
}

// Actualiza los datos de alta (empresa/tutor/fechas/horas requeridas) — la
// parte que normalmente solo se toca una vez, al dar de alta la FCT.
// Separada de actualizarSeguimientoFCT() (horas realizadas/nota/apto/obs,
// que sí cambia con frecuencia) para no forzar a enviar todos los campos
// juntos cuando el formulario de edición solo necesita tocar una parte.
function actualizarFCT(int $idFCT, array $datos): bool {
    $con = obtenerConexion();
    $idEmpresa = $datos['idEmpresa'] ?? null;
    // idProfesorTutor solo lo edita el admin (el formulario de profesor no
    // lo incluye) — se mantiene el valor ya guardado si no llega en $datos.
    $idProfesorTutor = array_key_exists('idProfesorTutor', $datos)
        ? $datos['idProfesorTutor']
        : (obtenerFCTPorId($idFCT)['idProfesorTutor'] ?? null);
    $stmt = mysqli_prepare($con,
        "UPDATE fct SET empresa=?, idEmpresa=?, tutorEmpresa=?, emailTutorEmpresa=?,
                telefonoEmpresa=?, ciudadEmpresa=?, fechaInicio=?, fechaFin=?, horasTotales=?, idProfesorTutor=?
         WHERE idFCT = ?");
    mysqli_stmt_bind_param($stmt, "sisssssssii",
        $datos['empresa'], $idEmpresa, $datos['tutorEmpresa'], $datos['emailTutorEmpresa'],
        $datos['telefonoEmpresa'], $datos['ciudadEmpresa'], $datos['fechaInicio'], $datos['fechaFin'],
        $datos['horasTotales'], $idProfesorTutor, $idFCT);
    return mysqli_stmt_execute($stmt);
}

// Actualiza horas realizadas / nota / apto / observaciones (la parte de
// seguimiento y evaluación, lo que más cambia tras el alta inicial).
function actualizarSeguimientoFCT(int $idFCT, ?int $horasRealizadas, ?float $nota, ?bool $apto, ?string $observaciones): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "UPDATE fct SET horasRealizadas = ?, nota = ?, apto = ?, observaciones = ? WHERE idFCT = ?");
    $aptoInt = $apto === null ? null : (int)$apto;
    mysqli_stmt_bind_param($stmt, "idisi", $horasRealizadas, $nota, $aptoInt, $observaciones, $idFCT);
    return mysqli_stmt_execute($stmt);
}

function eliminarFCT(int $idFCT): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM fct WHERE idFCT = ?");
    mysqli_stmt_bind_param($stmt, "i", $idFCT);
    return mysqli_stmt_execute($stmt);
}

// Nota de FCT de un estudiante en un ciclo, en escala 0-10, según el método
// de evaluación configurado (nota / apto_no_apto / ambos). Usada por el
// motor de notas configurable (origen='fct'). Si hay varias fases, se
// promedian las que tengan nota/apto registrado.
function obtenerNotaFCTEscala10(int $idEstudiante, int $idCiclo, string $metodoEvaluacion = 'ambos'): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT nota, apto FROM fct WHERE idEstudiante = ? AND idCiclo = ?");
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $valores = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        if ($metodoEvaluacion === 'nota' && $fila['nota'] !== null) {
            $valores[] = (float)$fila['nota'];
        } elseif ($metodoEvaluacion === 'apto_no_apto' && $fila['apto'] !== null) {
            $valores[] = ((int)$fila['apto'] === 1) ? 10.0 : 0.0; // apto/no apto expresado en escala 0-10 para poder ponderarlo igual que el resto
        } elseif ($metodoEvaluacion === 'ambos') {
            if ($fila['nota'] !== null) {
                $valores[] = (float)$fila['nota'];
            } elseif ($fila['apto'] !== null) {
                $valores[] = ((int)$fila['apto'] === 1) ? 10.0 : 0.0;
            }
        }
    }

    if (empty($valores)) return ['media' => 0.0, 'huboNota' => false];
    return ['media' => array_sum($valores) / count($valores), 'huboNota' => true];
}
