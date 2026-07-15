<?php
// ══════════════════════════════════════════════════════════════════════
// FCT (Formación en Centros de Trabajo) — capa de acceso a datos.
// La tabla `fct` ya existía en el esquema pero sin código de aplicación
// (tabla huérfana). Esto es la capa de datos + su integración en el motor
// de notas configurable (ver modelos/motor_calificaciones.php); no incluye
// vistas de gestión (formularios de alta/edición) — eso es una funcionalidad
// de UI más amplia (a la altura de lo que ya existe para TFG/retos), fuera
// del alcance de "hacer el motor de notas configurable".
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/conectar.php";

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

function listarFCTPorEstudiante(int $idEstudiante): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM fct WHERE idEstudiante = ? ORDER BY fase ASC");
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
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
                          telefonoEmpresa, ciudadEmpresa, fechaInicio, fechaFin, horasTotales, fase)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $idEmpresa = $datos['idEmpresa'] ?? null;
    $fase = $datos['fase'] ?? 1;
    mysqli_stmt_bind_param($stmt, "iisisssssssi",
        $datos['idEstudiante'], $datos['idCiclo'], $datos['empresa'], $idEmpresa,
        $datos['tutorEmpresa'], $datos['emailTutorEmpresa'], $datos['telefonoEmpresa'], $datos['ciudadEmpresa'],
        $datos['fechaInicio'], $datos['fechaFin'], $datos['horasTotales'], $fase);
    if (!mysqli_stmt_execute($stmt)) return false;
    return mysqli_insert_id($con);
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
