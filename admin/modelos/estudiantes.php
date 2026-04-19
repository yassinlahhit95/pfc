<?php
require_once("conectar.php");

function listarEstudiantes() {
    $conexion = obtenerConexion();
    $sentenciaSql = "SELECT *, 
            (SELECT nombreCiclo FROM ciclos WHERE ciclos.idCiclo = estudiantes.idCiclo) as nombreCiclo,
            (SELECT nombreEstado FROM estados WHERE estados.idEstado = estudiantes.idEstado) as nombreEstado
            FROM estudiantes 
            ORDER BY idEstudiante ASC";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    $listaDeEstudiantes = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaDeEstudiantes[] = $fila;
    }
    mysqli_close($conexion);
    return $listaDeEstudiantes;
}

function insertarEstudiante($nombre, $correo, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $idEstado) {
    $conexion = obtenerConexion();
    $sentenciaSql = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo, idEstado) 
            VALUES ('$nombre', '$correo', '$telefono', '$fechaNacimiento', '$dni', '$fechaAlta', '$direccion', '$ciudad', '$codigoPostal', '$observaciones', $idCiclo, $idEstado)";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarEstudiante($idDelEstudiante, $nombre, $correo, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $idEstado) {
    $conexion = obtenerConexion();
    $sentenciaSql = "UPDATE estudiantes SET nombreEstudiante = '$nombre', emailEstudiante = '$correo', 
            telefonoEstudiante = '$telefono', fechaNacimientoEstudiante = '$fechaNacimiento', dniEstudiante = '$dni', 
            fechaAltaEstudiante = '$fechaAlta', direccionEstudiante = '$direccion', ciudadEstudiante = '$ciudad', 
            codigoPostalEstudiante = '$codigoPostal', observacionesEstudiante = '$observaciones', idCiclo = $idCiclo, 
            idEstado = $idEstado WHERE idEstudiante = $idDelEstudiante";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarEstudiante($idDelEstudiante) {
    $conexion = obtenerConexion();
    $sentenciaSql = "DELETE FROM estudiantes WHERE idEstudiante = $idDelEstudiante";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerEstudiantePorId($idDelEstudiante) {
    $conexion = obtenerConexion();
    $sentenciaSql = "SELECT * FROM estudiantes WHERE idEstudiante = $idDelEstudiante";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    $estudiante = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $estudiante;
}

function actualizarTFG($idEstudiante, $nombreArchivo) {
    $conexion = obtenerConexion();
    $sentenciaSql = "UPDATE estudiantes SET archivoTFG = '$nombreArchivo' WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}
?>
