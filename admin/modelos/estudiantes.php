<?php
require_once("conectar.php");

function listarEstudiantes() {
    $conexion = obtenerConexion();
    $sentenciaSql = "SELECT *, 
            (SELECT nombreCiclo FROM ciclos WHERE ciclos.idCiclo = estudiantes.idCiclo) as nombreCiclo
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

function insertarEstudiante($nombre, $email, $telefono, $fNac, $dni, $fAlta, $dir, $ciu, $cp, $obs, $idCiclo) {
    $conexion = obtenerConexion();
    $sentenciaSql = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo) 
            VALUES ('$nombre', '$email', '$telefono', '$fNac', '$dni', '$fAlta', '$dir', '$ciu', '$cp', '$obs', $idCiclo)";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarEstudiante($id, $nombre, $email, $telefono, $fNac, $dni, $fAlta, $dir, $ciu, $cp, $obs, $idCiclo) {
    $conexion = obtenerConexion();
    $sentenciaSql = "UPDATE estudiantes SET nombreEstudiante = '$nombre', emailEstudiante = '$email', 
            telefonoEstudiante = '$telefono', fechaNacimientoEstudiante = '$fNac', dniEstudiante = '$dni', 
            fechaAltaEstudiante = '$fAlta', direccionEstudiante = '$dir', ciudadEstudiante = '$ciu', 
            codigoPostalEstudiante = '$cp', observacionesEstudiante = '$obs', idCiclo = $idCiclo 
            WHERE idEstudiante = $id";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarEstudiante($id) {
    $conexion = obtenerConexion();
    $sentenciaSql = "DELETE FROM estudiantes WHERE idEstudiante = $id";
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
