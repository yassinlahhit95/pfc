<?php
require_once __DIR__ . '/../config/Config.php';

// Zona horaria de España
date_default_timezone_set('Europe/Madrid');

/**
 * Obtiene la conexión a la base de datos (reutiliza la conexión existente)
 */
function obtenerConexion() {
    static $conexion = null;

    if ($conexion !== null) {
        return $conexion;
    }

    $config = Config::getInstance();
    $host = $config->get('DB_HOST', 'localhost');
    $user = $config->get('DB_USER');
    $pass = $config->get('DB_PASS');
    $db   = $config->get('DB_NAME', 'aulapro');

    if (empty($user) || empty($pass)) {
        die("Error: Credenciales de base de datos no configuradas.");
    }

    $conexion = @mysqli_connect($host, $user, $pass, $db);

    if (!$conexion) {
        error_log("Database connection failed: " . mysqli_connect_error());
        die("Error de conexión a la base de datos. Intente más tarde.");
    }

    mysqli_set_charset($conexion, "utf8mb4");

    return $conexion;
}

/**
 * Execute a prepared statement and return the first result row, or null.
 * Usage: dbFetchOne("SELECT COUNT(*) as n FROM t")
 *        dbFetchOne("SELECT * FROM t WHERE id=?", "i", $id)
 */
function dbFetchOne(string $sql, string $types = '', ...$params): ?array {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, $sql);
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res) ?: null;
}

function actualizarTokenFCM($tabla, $campoId, $id, $token) {
    $con = obtenerConexion();
    $sql = "UPDATE $tabla SET fcm_token = ? WHERE $campoId = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $token, $id);
    return mysqli_stmt_execute($stmt);
}

function obtenerTokenFCM($tabla, $campoId, $id) {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM $tabla WHERE $campoId = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return ($fila && $fila['fcm_token']) ? $fila['fcm_token'] : null;
}
