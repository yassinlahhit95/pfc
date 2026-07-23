<?php
require_once __DIR__ . "/conectar.php";

const TOUR_ROLES_VALIDOS = ['admin', 'profesor', 'secretaria', 'estudiante', 'tutor'];

function tourEstaCompletado(int $idUsuario, string $tipoUsuario, string $tourKey): bool {
    if (!in_array($tipoUsuario, TOUR_ROLES_VALIDOS, true)) return true;
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT 1 FROM tours_completados WHERE idUsuario = ? AND tipoUsuario = ? AND tour_key = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'iss', $idUsuario, $tipoUsuario, $tourKey);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return (bool) mysqli_fetch_assoc($res);
}

function marcarTourCompletado(int $idUsuario, string $tipoUsuario, string $tourKey): bool {
    if (!in_array($tipoUsuario, TOUR_ROLES_VALIDOS, true)) return false;
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "INSERT IGNORE INTO tours_completados (idUsuario, tipoUsuario, tour_key) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'iss', $idUsuario, $tipoUsuario, $tourKey);
    return mysqli_stmt_execute($stmt);
}

// Reinicia (borra) el tour de un usuario para que vuelva a aparecer en su
// próximo login. $tourKey = null reinicia TODOS los tours de ese usuario.
function reiniciarTourUsuario(int $idUsuario, string $tipoUsuario, ?string $tourKey = null): bool {
    if (!in_array($tipoUsuario, TOUR_ROLES_VALIDOS, true)) return false;
    $con = obtenerConexion();
    if ($tourKey === null) {
        $stmt = mysqli_prepare($con, "DELETE FROM tours_completados WHERE idUsuario = ? AND tipoUsuario = ?");
        mysqli_stmt_bind_param($stmt, 'is', $idUsuario, $tipoUsuario);
    } else {
        $stmt = mysqli_prepare($con, "DELETE FROM tours_completados WHERE idUsuario = ? AND tipoUsuario = ? AND tour_key = ?");
        mysqli_stmt_bind_param($stmt, 'iss', $idUsuario, $tipoUsuario, $tourKey);
    }
    return mysqli_stmt_execute($stmt);
}
