<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarFCTPorCiclo(int $idCiclo): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT f.*, e.nombreEstudiante, e.emailEstudiante,
                p.nombreProfesor AS nombreTutorCentro
         FROM fct f
         JOIN estudiantes e ON e.idEstudiante = f.idEstudiante
         LEFT JOIN profesores p ON p.idProfesor = f.idProfesorTutor
         WHERE f.idCiclo = ?
         ORDER BY e.nombreEstudiante ASC, f.fase ASC");
    if (!$stmt) return [];
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($res)) $rows[] = $row;
    mysqli_stmt_close($stmt);
    return $rows;
}

function obtenerFCTPorId(int $idFCT): ?array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT f.*, e.nombreEstudiante, c.nombreCiclo, c.abreviaturaCiclo
         FROM fct f
         JOIN estudiantes e ON e.idEstudiante = f.idEstudiante
         JOIN ciclos c      ON c.idCiclo      = f.idCiclo
         WHERE f.idFCT = ?");
    if (!$stmt) return null;
    mysqli_stmt_bind_param($stmt, "i", $idFCT);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    return $row ?: null;
}

function obtenerFCTPorEstudianteCiclo(int $idEstudiante, int $idCiclo, int $fase = 1): ?array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT * FROM fct WHERE idEstudiante = ? AND idCiclo = ? AND fase = ?");
    if (!$stmt) return null;
    mysqli_stmt_bind_param($stmt, "iii", $idEstudiante, $idCiclo, $fase);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    return $row ?: null;
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES / ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function guardarFCT(
    int $idEstudiante, int $idCiclo, string $empresa,
    ?string $tutorEmpresa, ?string $emailTutorEmpresa, ?string $telefonoEmpresa,
    ?string $ciudadEmpresa, ?string $fechaInicio, ?string $fechaFin,
    ?int $horasTotales, ?int $horasRealizadas, ?float $nota,
    ?bool $apto, ?string $observaciones, ?int $idProfesorTutor, int $fase = 1
): bool {
    $con = obtenerConexion();
    $aptoInt = $apto !== null ? (int)$apto : null;
    $sql = "INSERT INTO fct
            (idEstudiante, idCiclo, empresa, tutorEmpresa, emailTutorEmpresa, telefonoEmpresa,
             ciudadEmpresa, fechaInicio, fechaFin, horasTotales, horasRealizadas, nota, apto,
             observaciones, idProfesorTutor, fase)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
              empresa            = VALUES(empresa),
              tutorEmpresa       = VALUES(tutorEmpresa),
              emailTutorEmpresa  = VALUES(emailTutorEmpresa),
              telefonoEmpresa    = VALUES(telefonoEmpresa),
              ciudadEmpresa      = VALUES(ciudadEmpresa),
              fechaInicio        = VALUES(fechaInicio),
              fechaFin           = VALUES(fechaFin),
              horasTotales       = VALUES(horasTotales),
              horasRealizadas    = VALUES(horasRealizadas),
              nota               = VALUES(nota),
              apto               = VALUES(apto),
              observaciones      = VALUES(observaciones),
              idProfesorTutor    = VALUES(idProfesorTutor)";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) return false;
    mysqli_stmt_bind_param($stmt, "iisssssssiiidisi",
        $idEstudiante, $idCiclo, $empresa, $tutorEmpresa, $emailTutorEmpresa, $telefonoEmpresa,
        $ciudadEmpresa, $fechaInicio, $fechaFin,
        $horasTotales, $horasRealizadas, $nota,
        $aptoInt, $observaciones, $idProfesorTutor, $fase
    );
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function eliminarFCT(int $idFCT): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM fct WHERE idFCT = ?");
    if (!$stmt) return false;
    mysqli_stmt_bind_param($stmt, "i", $idFCT);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

function fctEstaCompleta(array $fct): bool {
    return !empty($fct['empresa'])
        && !empty($fct['fechaInicio'])
        && !empty($fct['fechaFin'])
        && ($fct['nota'] !== null || $fct['apto'] !== null);
}

function fctEtiquetaNota(array $fct): string {
    if ($fct['apto'] !== null) {
        return $fct['apto'] ? 'APTO' : 'NO APTO';
    }
    if ($fct['nota'] !== null) {
        return number_format((float)$fct['nota'], 1);
    }
    return 'Pendiente';
}
