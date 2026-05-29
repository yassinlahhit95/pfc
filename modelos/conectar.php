<?php
// Cargar configuración
require_once __DIR__ . '/../config/Config.php';

function obtenerConexion() {
    $config = Config::getInstance();

    $host = $config->get('DB_HOST', 'localhost');
    $user = $config->get('DB_USER');
    $pass = $config->get('DB_PASS');
    $db = $config->get('DB_NAME', 'aulapro');

    // Validar que existan las credenciales
    if (empty($user) || empty($pass)) {
        die("Error: Credenciales de base de datos no configuradas. Revisar .env o config/Config.php");
    }

    $conexion = @mysqli_connect($host, $user, $pass, $db);

    if (!$conexion) {
        // Log del error sin exponer detalles
        error_log("Database connection failed: " . mysqli_connect_error());
        die("Error de conexión a la base de datos. Por favor, intenta más tarde.");
    }

    mysqli_set_charset($conexion, "utf8mb4");
    mysqli_query($conexion, "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_spanish_ci'");
    return $conexion;
}
