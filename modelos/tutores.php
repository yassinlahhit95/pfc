<?php
require_once __DIR__ . "/conectar.php";
require_once __DIR__ . "/../include/Cache.php";

// ══════════════════════════════════════════════════════════════════════
//  AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════

function validarLoginTutor($email, $password) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM tutores WHERE emailTutor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $tutor = mysqli_fetch_assoc($res);

    if ($tutor && password_verify($password, $tutor['password'])) {
        if (class_exists('Security')) Security::rehashOnLogin($con, 'tutores', 'idTutor', $tutor['idTutor'], $password, $tutor['password']);
        return $tutor;
    }
    return null;
}

// ══════════════════════════════════════════════════════════════════════
//  CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function obtenerTutorPorId($idTutor) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM tutores WHERE idTutor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idTutor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res) ?: null;
}

function listarEstudiantesPorTutor($idTutor) {
    $con = obtenerConexion();
    $sql = "SELECT e.*, c.nombreCiclo, et.parentesco
            FROM estudiantes e
            JOIN estudiante_tutor et ON e.idEstudiante = et.idEstudiante
            JOIN ciclos c ON e.idCiclo = c.idCiclo
            WHERE et.idTutor = ? AND e.deleted_at IS NULL";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idTutor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

// Hijos de varios tutores a la vez, agrupados por idTutor => [['idEstudiante','nombreEstudiante'], ...].
// Evita el patrón N+1 de llamar listarEstudiantesPorTutor() una vez por tutor en las
// vistas de listado (verTutores.php de admin y secretaría).
function listarHijosPorTutores(array $idsTutores): array {
    if (!$idsTutores) return [];
    $con = obtenerConexion();
    $ph = implode(',', array_fill(0, count($idsTutores), '?'));
    $types = str_repeat('i', count($idsTutores));
    $sql = "SELECT et.idTutor, e.idEstudiante, e.nombreEstudiante
            FROM estudiantes e
            JOIN estudiante_tutor et ON e.idEstudiante = et.idEstudiante
            WHERE et.idTutor IN ($ph) AND e.deleted_at IS NULL
            ORDER BY e.nombreEstudiante ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$idsTutores);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $porTutor = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $porTutor[$fila['idTutor']][] = ['idEstudiante' => (int)$fila['idEstudiante'], 'nombreEstudiante' => $fila['nombreEstudiante']];
    }
    return $porTutor;
}

function obtenerTokensTutores() {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM tutores WHERE fcm_token IS NOT NULL AND fcm_token != ''";
    $resultado = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila['fcm_token'];
    }
    return $lista;
}

function listarIdsTutores(): array {
    $con = obtenerConexion();
    $resultado = mysqli_query($con, "SELECT idTutor FROM tutores");
    $ids = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $ids[] = (int)$fila['idTutor'];
    }
    return $ids;
}

/**
 * Actualiza el token FCM de un tutor
 */
function actualizarTokenFCMTutor($idTutor, $nuevoToken) {
    $con = obtenerConexion();
    $sql = "UPDATE tutores SET fcm_token = ? WHERE idTutor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nuevoToken, $idTutor);
    return mysqli_stmt_execute($stmt);
}

function insertarTutor($nombre, $email, $dni, $telefono) {
    $con = obtenerConexion();
    require_once __DIR__ . '/../include/credenciales.php';
    [$pass] = generarCredencialesTemporales($email, $nombre, 'Tutor');
    $sql = "INSERT INTO tutores (nombreTutor, emailTutor, password, dniTutor, telefonoTutor) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $nombre, $email, $pass, $dni, $telefono);
    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($con);
}

// Restablece la contraseña del tutor: genera una temporal (se envía por email
// y se muestra una única vez en el mensaje de éxito) y obliga a cambiarla al entrar.
function restablecerPasswordTutor(int $idTutor): bool {
    $tutor = obtenerTutorPorId($idTutor);
    if (!$tutor) return false;

    require_once __DIR__ . '/../include/credenciales.php';
    [$hash] = generarCredencialesTemporales($tutor['emailTutor'], $tutor['nombreTutor'], 'Tutor');

    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "UPDATE tutores SET password = ?, must_change_password = 1, pwd_changed_at = NULL WHERE idTutor = ?");
    mysqli_stmt_bind_param($stmt, "si", $hash, $idTutor);
    return mysqli_stmt_execute($stmt);
}

function actualizarTutor(int $idTutor, string $nombre, string $email, string $dni, string $telefono): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "UPDATE tutores SET nombreTutor=?, emailTutor=?, dniTutor=?, telefonoTutor=? WHERE idTutor=?");
    mysqli_stmt_bind_param($stmt, "ssssi", $nombre, $email, $dni, $telefono, $idTutor);
    return mysqli_stmt_execute($stmt);
}

function eliminarTutor(int $idTutor): bool {
    $con = obtenerConexion();
    mysqli_begin_transaction($con);
    try {
        $delVinc = mysqli_prepare($con, "DELETE FROM estudiante_tutor WHERE idTutor = ?");
        mysqli_stmt_bind_param($delVinc, "i", $idTutor);
        if (!mysqli_stmt_execute($delVinc)) throw new \RuntimeException('delete estudiante_tutor');

        $delTutor = mysqli_prepare($con, "DELETE FROM tutores WHERE idTutor = ?");
        mysqli_stmt_bind_param($delTutor, "i", $idTutor);
        if (!mysqli_stmt_execute($delTutor)) throw new \RuntimeException('delete tutores');
        $borrado = mysqli_stmt_affected_rows($delTutor) > 0;

        mysqli_commit($con);
        return $borrado;
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        return false;
    }
}

function actualizarVinculacionesTutor(int $idTutor, array $idsEstudiantes, string $parentesco): void {
    $con = obtenerConexion();
    mysqli_begin_transaction($con);
    try {
        $del = mysqli_prepare($con, "DELETE FROM estudiante_tutor WHERE idTutor = ?");
        mysqli_stmt_bind_param($del, "i", $idTutor);
        if (!mysqli_stmt_execute($del)) throw new \RuntimeException('delete estudiante_tutor');
        if (!empty($idsEstudiantes)) {
            $ins = mysqli_prepare($con, "INSERT IGNORE INTO estudiante_tutor (idEstudiante, idTutor, parentesco) VALUES (?, ?, ?)");
            foreach ($idsEstudiantes as $idEst) {
                $idEst = (int)$idEst;
                if ($idEst > 0) {
                    mysqli_stmt_bind_param($ins, "iis", $idEst, $idTutor, $parentesco);
                    if (!mysqli_stmt_execute($ins)) throw new \RuntimeException('insert estudiante_tutor');
                }
            }
        }
        mysqli_commit($con);
    } catch (\Throwable $e) {
        mysqli_rollback($con);
    }
}

function vincularEstudianteTutor($idEstudiante, $idTutor, $parentesco) {
    $con = obtenerConexion();
    $sql = "INSERT IGNORE INTO estudiante_tutor (idEstudiante, idTutor, parentesco) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $idEstudiante, $idTutor, $parentesco);
    return mysqli_stmt_execute($stmt);
}

function obtenerTutorPorDni(string $dni): ?array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM tutores WHERE dniTutor = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $dni);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res) ?: null;
}


/**
 * Cuenta el total de tutores en el sistema
 */
function contarTutores() {
    return Cache::remember('panel_total_tutores', 60, function () {
        $con = obtenerConexion();
        $res = mysqli_query($con, "SELECT COUNT(*) as total FROM tutores");
        $fila = mysqli_fetch_assoc($res);
        return intval($fila['total']);
    });
}

/**
 * Lista todos los tutores con información básica
 */
function listarTodosLosTutores() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM tutores ORDER BY nombreTutor ASC";
    $res = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}
