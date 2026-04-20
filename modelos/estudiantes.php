<?php
require_once("conectar.php");

function listarEstudiantes() {
    $conexion = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo 
            FROM estudiantes 
            LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
            ORDER BY idEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarEstudiante($nombre, $email, $telefono, $fNac, $dni, $fAlta, $dir, $ciu, $cp, $obs, $idCiclo) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo) 
            VALUES ('$nombre', '$email', '$telefono', '$fNac', '$dni', '$fAlta', '$dir', '$ciu', '$cp', '$obs', $idCiclo)";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarEstudiante($id, $nombre, $email, $telefono, $fNac, $dni, $fAlta, $dir, $ciu, $cp, $obs, $idCiclo) {
    $conexion = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante = '$nombre', emailEstudiante = '$email', 
            telefonoEstudiante = '$telefono', fechaNacimientoEstudiante = '$fNac', dniEstudiante = '$dni', 
            fechaAltaEstudiante = '$fAlta', direccionEstudiante = '$dir', ciudadEstudiante = '$ciu', 
            codigoPostalEstudiante = '$cp', observacionesEstudiante = '$obs', idCiclo = $idCiclo 
            WHERE idEstudiante = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function listarEstudiantesPorProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo 
            FROM estudiantes 
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            WHERE estudiantes.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesor)
            ORDER BY nombreEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function eliminarEstudiante($id) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM estudiantes WHERE idEstudiante = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerEstudiantePorId($id) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM estudiantes WHERE idEstudiante = $id";
    $resultado = mysqli_query($conexion, $sql);
    $estudiante = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $estudiante;
}

function actualizarPerfilEstudiante($id, $nombre, $email, $telefono) {
    $conexion = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante = '$nombre', emailEstudiante = '$email', telefonoEstudiante = '$telefono' WHERE idEstudiante = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarTFG($idEstudiante, $nombreArchivo) {
    $conexion = obtenerConexion();
    $sql = "UPDATE estudiantes SET archivoTFG = '$nombreArchivo' WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}
?>