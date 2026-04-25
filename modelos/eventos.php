<?php
require_once("conectar.php");

function listarEventosProximos() {
    $conexion = obtenerConexion();
    $hoy = date('Y-m-d');
    $sql = "SELECT * FROM eventos WHERE fechaEvento >= '$hoy' ORDER BY fechaEvento ASC, horaEvento ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = array();
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarEvento($titulo, $descripcion, $fecha, $hora, $ubicacion) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO eventos (tituloEvento, descripcionEvento, fechaEvento, horaEvento, ubicacionEvento) 
            VALUES ('$titulo', '$descripcion', '$fecha', '$hora', '$ubicacion')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarEvento($id) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM eventos WHERE idEvento = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerEventoPorId($id) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM eventos WHERE idEvento = $id";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function actualizarEvento($id, $titulo, $descripcion, $fecha, $hora, $ubicacion) {
    $conexion = obtenerConexion();
    $sql = "UPDATE eventos SET tituloEvento = '$titulo', descripcionEvento = '$descripcion', 
            fechaEvento = '$fecha', horaEvento = '$hora', ubicacionEvento = '$ubicacion' 
            WHERE idEvento = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}
?>