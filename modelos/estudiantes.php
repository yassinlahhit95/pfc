<?php
require_once("conectar.php");

// Ver lista de todos los alumnos
function listarEstudiantes() {
    $db = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo ORDER BY estudiantes.idEstudiante ASC";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila; 
    }
    mysqli_close($db);
    return $lista;
}

// Meter un alumno nuevo
function insertarEstudiante($nombre, $email, $tel, $f_nac, $dni, $f_alta, $dir, $ciudad, $cp, $obs, $idCiclo) {
    $db = obtenerConexion();
    $sql = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo) 
            VALUES ('$nombre', '$email', '$tel', '$f_nac', '$dni', '$f_alta', '$dir', '$ciudad', '$cp', '$obs', $idCiclo)";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Cambiar los datos de un alumno
function actualizarEstudiante($id, $nombre, $email, $tel, $f_nac, $dni, $f_alta, $dir, $ciudad, $cp, $obs, $idCiclo) {
    $db = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante='$nombre', emailEstudiante='$email', telefonoEstudiante='$tel', fechaNacimientoEstudiante='$f_nac', dniEstudiante='$dni', fechaAltaEstudiante='$f_alta', direccionEstudiante='$dir', ciudadEstudiante='$ciudad', codigoPostalEstudiante='$cp', observacionesEstudiante='$obs', idCiclo=$idCiclo WHERE idEstudiante=$id";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Alumnos de los ciclos que imparte un profesor
function listarEstudiantesPorProfesor($idProfesor) {
    $db = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesor) ORDER BY estudiantes.nombreEstudiante ASC";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila; 
    }
    mysqli_close($db);
    return $lista;
}

// Alumnos que estan en un ciclo concreto
function listarEstudiantesPorCiclo($idCiclo) {
    $db = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idCiclo = $idCiclo ORDER BY estudiantes.idEstudiante ASC";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila; 
    }
    mysqli_close($db);
    return $lista;
}

// Quitar un alumno
function eliminarEstudiante($id) {
    $db = obtenerConexion();
    $sql = "DELETE FROM estudiantes WHERE idEstudiante = $id";
    $resultado = mysqli_query($db, $resultado);
    mysqli_close($db);
    return $resultado;
}

// Datos de un alumno por ID
function obtenerEstudiantePorId($id) {
    $db = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idEstudiante = $id";
    $resultado = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($db);
    return $fila;
}

// Cambiar la clave
function actualizarPasswordEstudiante($id, $pass) {
    $db = obtenerConexion();
    $sql = "UPDATE estudiantes SET password = '$pass' WHERE idEstudiante = $id";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Datos basicos del perfil propio
function actualizarPerfilEstudiante($id, $nombre, $email, $tel) {
    $db = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante='$nombre', emailEstudiante='$email', telefonoEstudiante='$tel' WHERE idEstudiante=$id";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}
?>
