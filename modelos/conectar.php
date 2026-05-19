<?php
function obtenerConexion() {
    $conexion = mysqli_connect("localhost", "yassjjzw_adminpfc", "Yassin1995**", "yassjjzw_pfc");
   /* $conexion = mysqli_connect("localhost", "cuhq4y87y_pfc", "123456", "cuhq4y87y_pfc");*/
    
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conexion, "utf8mb4");
    mysqli_query($conexion, "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_spanish_ci'");
    return $conexion;
}
