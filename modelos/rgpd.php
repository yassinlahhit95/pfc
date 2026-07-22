<?php
require_once __DIR__ . "/conectar.php";
require_once __DIR__ . "/estudiantes.php";
require_once __DIR__ . "/directores.php";

/**
 * Collects all personal data for a student (RGPD Art. 20 – portability).
 * Returns an associative array ready for JSON export.
 */
function exportarDatosEstudiante(int $idEstudiante): array {
    $con = obtenerConexion();

    // Profile
    $stmt = mysqli_prepare($con, "SELECT * FROM estudiantes WHERE idEstudiante = ?");
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $perfil = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$perfil) return [];
    unset($perfil['password']); // never export password hash
    $perfil = descifrarFilaEstudiante($perfil);

    // Grades – modules
    $stmt = mysqli_prepare($con,
        "SELECT cm.*, m.nombreModulo FROM calificaciones_modulos cm
         JOIN modulos m ON m.idModulo = cm.idModulo
         WHERE cm.idEstudiante = ?");
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $notasModulos = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    // Grades – retos
    $stmt = mysqli_prepare($con,
        "SELECT cr.*, r.nombreReto FROM calificaciones_retos cr
         JOIN retos r ON r.idReto = cr.idReto
         WHERE cr.idEstudiante = ?");
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $notasRetos = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    // Payments
    $stmt = mysqli_prepare($con, "SELECT * FROM pagos WHERE idEstudiante = ?");
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $pagos = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    // Messages (sent/received)
    $stmt = mysqli_prepare($con,
        "SELECT * FROM reclamaciones WHERE idEstudiante = ? ORDER BY fecha ASC");
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $mensajes = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    // Attendance
    $stmt = mysqli_prepare($con,
        "SELECT a.fecha, m.nombreModulo, a.estado, a.observacion, a.fechaRegistro
         FROM asistencias a
         JOIN modulos m ON m.idModulo = a.idModulo
         WHERE a.idEstudiante = ? ORDER BY a.fecha ASC");
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $asistencias = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    // Consent log
    $stmt = mysqli_prepare($con,
        "SELECT tipo, ip, fecha FROM consentimientos WHERE idEstudiante = ? ORDER BY fecha ASC");
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $consentimientos = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    return [
        'exportado_en'   => date('c'),
        'base_legal'     => 'RGPD Art. 20 – Derecho a la portabilidad de datos',
        'perfil'         => $perfil,
        'notas_modulos'   => $notasModulos,
        'notas_retos'     => $notasRetos,
        'asistencias'     => $asistencias,
        'pagos'           => $pagos,
        'mensajes'        => $mensajes,
        'consentimientos' => $consentimientos,
    ];
}

/**
 * Hard-deletes a student and all related data (RGPD Art. 17 – right to erasure).
 * Saves a JSON backup and logs the deletion in rgpd_eliminaciones.
 * Requires the admin's plaintext password for confirmation.
 *
 * @return array ['ok' => bool, 'msg' => string]
 */
function eliminarEstudianteRGPD(int $idEstudiante, string $motivo, int $idAdmin, string $adminPassword): array {
    $con = obtenerConexion();

    // Verify admin password
    $stmt = mysqli_prepare($con, "SELECT password FROM directores WHERE idDirector = ?");
    mysqli_stmt_bind_param($stmt, "i", $idAdmin);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$row || !password_verify($adminPassword, $row['password'])) {
        return ['ok' => false, 'msg' => 'Contraseña de administrador incorrecta.'];
    }

    // Collect backup data
    $backup = exportarDatosEstudiante($idEstudiante);
    if (empty($backup)) {
        return ['ok' => false, 'msg' => 'Estudiante no encontrado.'];
    }
    $descripcion = $backup['perfil']['nombreEstudiante'] ?? "ID $idEstudiante";

    mysqli_begin_transaction($con);
    try {
        $tablasRelacionadas = [
            ['calificaciones_retos',   'idEstudiante'],
            ['calificaciones_modulos', 'idEstudiante'],
            ['pagos',                  'idEstudiante'],
            ['reclamaciones',          'idEstudiante'],
            ['consentimientos',        'idEstudiante'],
            ['estudiante_tutor',       'idEstudiante'],
            // log_acciones no tiene columna idEstudiante (solo idAdmin) — se conserva como registro de auditoría.
            ['log_acciones',           null],
        ];

        foreach ($tablasRelacionadas as [$tabla, $columna]) {
            if ($columna === null) continue;
            $stmt = mysqli_prepare($con, "DELETE FROM $tabla WHERE $columna = ?");
            mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
            if (!mysqli_stmt_execute($stmt)) throw new \RuntimeException("delete $tabla");
        }

        // Delete physical TFG file if present (local disk and R2, whichever holds it)
        if (!empty($backup['perfil']['archivoTFG'])) {
            $tfgPath = __DIR__ . "/../public/uploads/pfc/" . $backup['perfil']['archivoTFG'];
            if (file_exists($tfgPath)) { @unlink($tfgPath); }
            require_once __DIR__ . '/../include/R2Client.php';
            R2Client::deleteObject('pfc/' . $backup['perfil']['archivoTFG']);
        }

        // Delete main student record
        $stmt = mysqli_prepare($con, "DELETE FROM estudiantes WHERE idEstudiante = ?");
        mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
        if (!mysqli_stmt_execute($stmt)) throw new \RuntimeException("delete estudiantes");

        // Record evidence
        $ip          = $_SERVER['REMOTE_ADDR'] ?? null;
        $backupJson  = json_encode($backup, JSON_UNESCAPED_UNICODE);
        $stmt = mysqli_prepare($con,
            "INSERT INTO rgpd_eliminaciones (idAdmin, entidad, idRegistro, descripcion, motivo, datos_backup, ip)
             VALUES (?, 'estudiantes', ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iissss",
            $idAdmin, $idEstudiante, $descripcion, $motivo, $backupJson, $ip);
        if (!mysqli_stmt_execute($stmt)) throw new \RuntimeException("insert rgpd_eliminaciones");

        mysqli_commit($con);
        return ['ok' => true, 'msg' => "Estudiante '$descripcion' eliminado permanentemente (RGPD Art. 17)."];
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        error_log("RGPD borrar error: " . $e->getMessage());
        return ['ok' => false, 'msg' => 'Error interno al procesar la eliminación. Inténtalo de nuevo.'];
    }
}

/**
 * Purges log_acciones entries older than $years years (LOPDGDD minimum: 3 years).
 * Returns the number of rows deleted.
 */
function purgarLogsAntiguos(int $years = 3): int {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "DELETE FROM log_acciones WHERE fecha < DATE_SUB(NOW(), INTERVAL ? YEAR)");
    mysqli_stmt_bind_param($stmt, "i", $years);
    mysqli_stmt_execute($stmt);
    return (int)mysqli_affected_rows($con);
}

/**
 * Returns RGPD deletion log entries (for audit display).
 */
function listarEliminacionesRGPD(int $limit = 50): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT re.*, d.nombreDirector
         FROM rgpd_eliminaciones re
         LEFT JOIN directores d ON d.idDirector = re.idAdmin
         ORDER BY re.fecha DESC
         LIMIT ?");
    mysqli_stmt_bind_param($stmt, "i", $limit);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

// ══════════════════════════════════════════════════════════════════════
// AUTOSERVICIO — exportar mis propios datos (Art. 20) y solicitar baja (Art. 17)
// Disponible para los 5 roles. Nunca hay auto-borrado real: la solicitud
// la resuelve un admin manualmente (ver eliminarEstudianteRGPD arriba,
// que sigue siendo la única vía de borrado efectivo, y solo para estudiantes).
// ══════════════════════════════════════════════════════════════════════

/**
 * Exporta la propia fila de un usuario de rol no-estudiante (admin, profesor,
 * secretaría, tutor) — no tienen historial académico propio como el estudiante,
 * así que basta con su registro de cuenta, sin la contraseña.
 */
function exportarDatosPropios(string $tabla, string $idCol, int $id): array {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM `$tabla` WHERE `$idCol` = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $perfil = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$perfil) return [];
    unset($perfil['password'], $perfil['mfa_secret'], $perfil['mfa_backup_codes']);
    if ($tabla === 'directores') $perfil = descifrarFilaDirector($perfil);

    return [
        'exportado_en' => date('c'),
        'base_legal'   => 'RGPD Art. 20 – Derecho a la portabilidad de datos',
        'perfil'       => $perfil,
    ];
}

function crearSolicitudRGPD(string $rolSesion, int $idUsuario, string $nombreUsuario, string $emailUsuario, string $motivo): bool {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "INSERT INTO rgpd_solicitudes (rolSesion, idUsuario, nombreUsuario, emailUsuario, motivo)
         VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sisss", $rolSesion, $idUsuario, $nombreUsuario, $emailUsuario, $motivo);
    return mysqli_stmt_execute($stmt);
}

function listarSolicitudesRGPD(string $estado = 'pendiente', int $limit = 50): array {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT * FROM rgpd_solicitudes WHERE estado = ? ORDER BY fechaSolicitud DESC LIMIT ?");
    mysqli_stmt_bind_param($stmt, "si", $estado, $limit);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

function resolverSolicitudRGPD(int $idSolicitud, int $idAdmin): bool {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "UPDATE rgpd_solicitudes SET estado = 'resuelta', resueltaPorAdmin = ?, fechaResolucion = NOW()
         WHERE idSolicitud = ? AND estado = 'pendiente'");
    mysqli_stmt_bind_param($stmt, "ii", $idAdmin, $idSolicitud);
    return mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0;
}
