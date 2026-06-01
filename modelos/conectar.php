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
