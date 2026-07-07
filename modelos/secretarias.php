<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
//  AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════

function validarLoginSecretaria($email, $password) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM secretarias WHERE emailSecretaria = ? AND activoSecretaria = 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $secretaria = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($secretaria && password_verify($password, $secretaria['password'])) {
        if (class_exists('Security')) Security::rehashOnLogin($con, 'secretarias', 'idSecretaria', $secretaria['idSecretaria'], $password, $secretaria['password']);
        return $secretaria;
    }
    return null;
}

// ══════════════════════════════════════════════════════════════════════
//  CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function obtenerSecretariaPorId($id) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM secretarias WHERE idSecretaria = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
}


// ══════════════════════════════════════════════════════════════════════
//  ESCRITURA
// ══════════════════════════════════════════════════════════════════════


function actualizarSecretaria($id, $nombre, $email) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE secretarias SET nombreSecretaria=?, emailSecretaria=? WHERE idSecretaria=?");
    mysqli_stmt_bind_param($stmt, "ssi", $nombre, $email, $id);
    return mysqli_stmt_execute($stmt);
}

function actualizarPasswordSecretaria($id, $password) {
    $con  = obtenerConexion();
    $hash = Security::hashPassword($password);
    $stmt = mysqli_prepare($con, "UPDATE secretarias SET password=?, pwd_changed_at=NOW(), must_change_password=0 WHERE idSecretaria=?");
    mysqli_stmt_bind_param($stmt, "si", $hash, $id);
    return mysqli_stmt_execute($stmt);
}

function listarTodasLasSecretarias(): array {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT * FROM secretarias ORDER BY nombreSecretaria ASC");
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}

function insertarSecretaria(string $nombre, string $email): int|false {
    $con = obtenerConexion();
    require_once __DIR__ . '/../include/credenciales.php';
    [$hash] = generarCredencialesTemporales($email, $nombre, 'Secretaria');
    $stmt = mysqli_prepare($con, "INSERT INTO secretarias (nombreSecretaria, emailSecretaria, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $nombre, $email, $hash);
    if (mysqli_stmt_execute($stmt)) return mysqli_insert_id($con);
    return false;
}

function eliminarSecretaria(int $id): bool {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM secretarias WHERE idSecretaria = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    return mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0;
}

function toggleActivoSecretaria(int $id, int $activo): bool {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE secretarias SET activoSecretaria = ? WHERE idSecretaria = ?");
    mysqli_stmt_bind_param($stmt, "ii", $activo, $id);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
//  HISTORIAL / LOGS
// ══════════════════════════════════════════════════════════════════════

if (!function_exists('registrarAccionSecretaria')) {
    function registrarAccionSecretaria($idSecretaria, $accion, $entidad, $detalles = '') {
        $con = obtenerConexion();
        // Auto-create table if missing
        mysqli_query($con, "CREATE TABLE IF NOT EXISTS `historial_secretarias` (
          `idHistorial` int(11) NOT NULL AUTO_INCREMENT,
          `idSecretaria` int(11) NOT NULL,
          `accion` varchar(255) NOT NULL,
          `entidad` varchar(100) NOT NULL,
          `detalles` text,
          `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`idHistorial`),
          KEY `idx_secretaria` (`idSecretaria`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $stmt = mysqli_prepare($con, "INSERT INTO historial_secretarias (idSecretaria, accion, entidad, detalles) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isss", $idSecretaria, $accion, $entidad, $detalles);
        return mysqli_stmt_execute($stmt);
    }
}

if (!function_exists('listarHistorialSecretarias')) {
    function listarHistorialSecretarias($limite = 100) {
        $con = obtenerConexion();
        $sql = "SELECT h.*, s.nombreSecretaria 
                FROM historial_secretarias h 
                LEFT JOIN secretarias s ON h.idSecretaria = s.idSecretaria 
                ORDER BY h.fecha DESC LIMIT ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $limite);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $lista = [];
        while ($fila = mysqli_fetch_assoc($res)) {
            $lista[] = $fila;
        }
        return $lista;
    }
}

