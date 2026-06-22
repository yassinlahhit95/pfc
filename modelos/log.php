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
