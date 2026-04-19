<?php
require_once("conectar.php");

function listarNiveles() {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM niveles ORDER BY nombreNivel ASC";
    $datos = [];
    if ($resultado = mysqli_query($conexion, $sql)) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $datos[] = $fila;
        }
    }
    mysqli_close($conexion);
    return $datos;
}
?>
