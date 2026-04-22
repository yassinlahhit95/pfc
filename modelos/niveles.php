<?php
require_once("conectar.php");

function listarNiveles() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT * FROM niveles ORDER BY idNivel ASC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}
?>
