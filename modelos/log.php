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

function registrarAccionSecretaria(string $accion, string $tabla, ?int $idRegistro = null, string $descripcion = ''): void {
    try {
        $con          = obtenerConexion();
        $idSecretaria = isset($_SESSION['idSecretaria']) ? (int)$_SESSION['idSecretaria'] : null;

        $detalles = $descripcion;
        if ($idRegistro) $detalles = "ID: $idRegistro " . $detalles;
        
        $stmt = mysqli_prepare($con, "INSERT INTO historial_secretarias (idSecretaria, accion, entidad, detalles) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isss", $idSecretaria, $accion, $tabla, $detalles);
        mysqli_stmt_execute($stmt);
    } catch (\Throwable $e) {}
}

function listarHistorialSecretarias(?int $idSecretaria = null, int $limite = 300, int $offset = 0): array {
    $con = obtenerConexion();
    if ($idSecretaria) {
        $stmt = mysqli_prepare($con,
            "SELECT l.*, s.nombreSecretaria
             FROM historial_secretarias l
             LEFT JOIN secretarias s ON l.idSecretaria = s.idSecretaria
             WHERE l.idSecretaria = ?
             ORDER BY l.fecha DESC LIMIT ? OFFSET ?");
        mysqli_stmt_bind_param($stmt, "iii", $idSecretaria, $limite, $offset);
    } else {
        $stmt = mysqli_prepare($con,
            "SELECT l.*, s.nombreSecretaria
             FROM historial_secretarias l
             LEFT JOIN secretarias s ON l.idSecretaria = s.idSecretaria
             ORDER BY l.fecha DESC LIMIT ? OFFSET ?");
        mysqli_stmt_bind_param($stmt, "ii", $limite, $offset);
    }
    mysqli_stmt_execute($stmt);
    $res  = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}

function contarHistorialSecretarias(?int $idSecretaria = null): int {
    $con = obtenerConexion();
    if ($idSecretaria) {
        $stmt = mysqli_prepare($con, "SELECT COUNT(*) AS n FROM historial_secretarias WHERE idSecretaria = ?");
        mysqli_stmt_bind_param($stmt, "i", $idSecretaria);
    } else {
        $stmt = mysqli_prepare($con, "SELECT COUNT(*) AS n FROM historial_secretarias");
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return (int)(mysqli_fetch_assoc($res)['n'] ?? 0);
}
