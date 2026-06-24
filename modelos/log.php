<?php
require_once __DIR__ . "/conectar.php";

function registrarAccion(string $accion, string $tabla, ?int $idRegistro = null, string $descripcion = ''): void {
    try {
        $con     = obtenerConexion();
        $idAdmin = isset($_SESSION['idAdmin']) ? (int)$_SESSION['idAdmin'] : null;
        $ip      = $_SERVER['REMOTE_ADDR'] ?? null;

        $stmt = mysqli_prepare($con,
            "INSERT INTO log_acciones (idAdmin, accion, tabla, idRegistro, descripcion, ip)
             VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ississ", $idAdmin, $accion, $tabla, $idRegistro, $descripcion, $ip);
        mysqli_stmt_execute($stmt);
    } catch (\Throwable $e) {
        // Never let logging failure break a request
    }
}

// Requires log_secretaria_acciones table (see database.sql).
function registrarAccionSecretaria(string $accion, string $tabla, ?int $idRegistro = null, string $descripcion = ''): void {
    try {
        $con          = obtenerConexion();
        $idSecretaria = isset($_SESSION['idSecretaria']) ? (int)$_SESSION['idSecretaria'] : null;
        $ip           = $_SERVER['REMOTE_ADDR'] ?? null;
        $stmt = mysqli_prepare($con,
            "INSERT INTO log_secretaria_acciones (idSecretaria, accion, tabla, idRegistro, descripcion, ip)
             VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ississ", $idSecretaria, $accion, $tabla, $idRegistro, $descripcion, $ip);
        mysqli_stmt_execute($stmt);
    } catch (\Throwable $e) {}
}

function listarHistorialSecretarias(?int $idSecretaria = null, int $limite = 300): array {
    $con = obtenerConexion();
    if ($idSecretaria) {
        $stmt = mysqli_prepare($con,
            "SELECT l.*, s.nombreSecretaria
             FROM log_secretaria_acciones l
             LEFT JOIN secretarias s ON l.idSecretaria = s.idSecretaria
             WHERE l.idSecretaria = ?
             ORDER BY l.fecha DESC LIMIT ?");
        mysqli_stmt_bind_param($stmt, "ii", $idSecretaria, $limite);
    } else {
        $stmt = mysqli_prepare($con,
            "SELECT l.*, s.nombreSecretaria
             FROM log_secretaria_acciones l
             LEFT JOIN secretarias s ON l.idSecretaria = s.idSecretaria
             ORDER BY l.fecha DESC LIMIT ?");
        mysqli_stmt_bind_param($stmt, "i", $limite);
    }
    mysqli_stmt_execute($stmt);
    $res  = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}
