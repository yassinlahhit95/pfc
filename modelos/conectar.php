<?php
require_once __DIR__ . '/../config/Config.php';

// Zona horaria de España
date_default_timezone_set('Europe/Madrid');

// ══════════════════════════════════════════════════════════════════════
// CONEXIÓN
// ══════════════════════════════════════════════════════════════════════

// Singleton de conexión mysqli — reutiliza la instancia durante toda la petición.
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
        die("Error: credenciales de base de datos no configuradas.");
    }
    $conexion = @mysqli_connect($host, $user, $pass, $db);
    if (!$conexion) {
        error_log("Fallo al conectar a la BD: " . mysqli_connect_error());
        die("Error de conexión a la base de datos. Inténtelo más tarde.");
    }
    mysqli_set_charset($conexion, "utf8mb4");
    return $conexion;
}

// Ejecuta un SELECT preparado y devuelve la primera fila como array asociativo, o null.
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

// ══════════════════════════════════════════════════════════════════════
// TOKENS FCM
// ══════════════════════════════════════════════════════════════════════

// Lista blanca de tablas y su columna id para evitar inyección de nombre de columna.
const FCM_TABLAS = [
    'estudiantes' => 'idEstudiante',
    'profesores'  => 'idProfesor',
    'directores'  => 'idDirector',
    'tutores'     => 'idTutor',
];

function actualizarTokenFCM($tabla, $campoId, $id, $token) {
    if (!isset(FCM_TABLAS[$tabla]) || FCM_TABLAS[$tabla] !== $campoId) return false;
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE `$tabla` SET fcm_token = ? WHERE `$campoId` = ?");
    mysqli_stmt_bind_param($stmt, "si", $token, $id);
    return mysqli_stmt_execute($stmt);
}

function obtenerTokenFCM($tabla, $campoId, $id) {
    if (!isset(FCM_TABLAS[$tabla]) || FCM_TABLAS[$tabla] !== $campoId) return null;
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT fcm_token FROM `$tabla` WHERE `$campoId` = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return ($fila && $fila['fcm_token']) ? $fila['fcm_token'] : null;
}
