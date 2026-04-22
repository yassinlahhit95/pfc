<?php
require_once("conectar.php");

function listarEstudiantes() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo ORDER BY idEstudiante ASC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo) VALUES ('$nombre', '$email', '$telefono', '$fechaNacimiento', '$dni', '$fechaAlta', '$direccion', '$ciudad', '$codigoPostal', '$observaciones', $idCiclo)");
    mysqli_close($conexion);
    return $resultado;
}

function actualizarEstudiante($idEstudiante, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "UPDATE estudiantes SET nombreEstudiante = '$nombre', emailEstudiante = '$email', telefonoEstudiante = '$telefono', fechaNacimientoEstudiante = '$fechaNacimiento', dniEstudiante = '$dni', fechaAltaEstudiante = '$fechaAlta', direccionEstudiante = '$direccion', ciudadEstudiante = '$ciudad', codigoPostalEstudiante = '$codigoPostal', observacionesEstudiante = '$observaciones', idCiclo = $idCiclo WHERE idEstudiante = $idEstudiante");
    mysqli_close($conexion);
    return $resultado;
}

function listarEstudiantesPorProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesor) ORDER BY nombreEstudiante ASC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function eliminarEstudiante($idEstudiante) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "DELETE FROM estudiantes WHERE idEstudiante = $idEstudiante");
    mysqli_close($conexion);
    return $resultado;
}

function obtenerEstudiantePorId($idEstudiante) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE idEstudiante = $idEstudiante");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function actualizarPerfilEstudiante($idEstudiante, $nombre, $email, $telefono) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "UPDATE estudiantes SET nombreEstudiante = '$nombre', emailEstudiante = '$email', telefonoEstudiante = '$telefono' WHERE idEstudiante = $idEstudiante");
    mysqli_close($conexion);
    return $resultado;
}
?>