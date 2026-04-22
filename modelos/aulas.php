<?php
require_once("conectar.php");

function listarAulas() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT * FROM aulas ORDER BY idAula ASC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarAula($nombre) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "INSERT INTO aulas (nombreAula) VALUES ('$nombre')");
    mysqli_close($conexion);
    return $resultado;
}

function eliminarAula($idAula) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "DELETE FROM aulas WHERE idAula = $idAula");
    mysqli_close($conexion);
    return $resultado;
}
?>
