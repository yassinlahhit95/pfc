<?php
function obtenerConexion() {
    $host = getenv('DB_HOST') ?: 'localhost';
    $user = getenv('DB_USER') ?: 'yassjjzw_adminpfc';
    $pass = getenv('DB_PASS') ?: 'Yassin1995**';
    $db   = getenv('DB_NAME') ?: 'yassjjzw_pfc';

    $conexion = mysqli_connect($host, $user, $pass, $db);

    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    mysqli_set_charset($conexion, "utf8mb4");
    mysqli_query($conexion, "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_spanish_ci'");
    return $conexion;
}
