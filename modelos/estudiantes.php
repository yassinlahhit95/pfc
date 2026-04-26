<?php
require_once("conectar.php");

/**
 * Obtiene el listado completo de estudiantes registrados
 */
function listarEstudiantes() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes 
                     LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
                     ORDER BY estudiantes.idEstudiante ASC";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalEstudiantes = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalEstudiantes[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalEstudiantes;
}

/**
 * Registra un nuevo estudiante en el sistema
 */
function insertarEstudiante($nombreRecibido, $emailRecibido, $telefonoRecibido, $fechaNacimientoRecibida, $dniRecibido, $fechaAltaRecibida, $direccionRecibida, $ciudadRecibida, $codigoPostalRecibido, $observacionesRecibidas, $idCicloAsignado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo) 
                     VALUES ('$nombreRecibido', '$emailRecibido', '$telefonoRecibido', '$fechaNacimientoRecibida', '$dniRecibido', '$fechaAltaRecibida', '$direccionRecibida', '$ciudadRecibida', '$codigoPostalRecibido', '$observacionesRecibidas', $idCicloAsignado)";
    
    $resultadoInsercion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoInsercion;
}

/**
 * Actualiza la información de un estudiante existente
 */
function actualizarEstudiante($idEstudianteAEditar, $nombreNuevo, $emailNuevo, $telefonoNuevo, $fechaNacNuevo, $dniNuevo, $fechaAltaNueva, $direccionNueva, $ciudadNueva, $cpNuevo, $obsNuevas, $idCicloNuevo) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "UPDATE estudiantes SET 
                     nombreEstudiante = '$nombreNuevo', 
                     emailEstudiante = '$emailNuevo', 
                     telefonoEstudiante = '$telefonoNuevo', 
                     fechaNacimientoEstudiante = '$fechaNacNuevo', 
                     dniEstudiante = '$dniNuevo', 
                     fechaAltaEstudiante = '$fechaAltaNueva', 
                     direccionEstudiante = '$direccionNueva', 
                     ciudadEstudiante = '$ciudadNueva', 
                     codigoPostalEstudiante = '$cpNuevo', 
                     observacionesEstudiante = '$obsNuevas', 
                     idCiclo = $idCicloNuevo 
                     WHERE idEstudiante = $idEstudianteAEditar";
    
    $resultadoActualizacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoActualizacion;
}

/**
 * Lista estudiantes que pertenecen a ciclos impartidos por un profesor específico
 */
function listarEstudiantesPorProfesor($idProfesorRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes 
                     JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
                     WHERE estudiantes.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesorRecibido) 
                     ORDER BY estudiantes.nombreEstudiante ASC";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaEstudiantesEncontrados = array();
    
    while($datosEstudiante = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaEstudiantesEncontrados[] = $datosEstudiante;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaEstudiantesEncontrados;
}

/**
 * Lista los estudiantes matriculados en un ciclo específico
 */
function listarEstudiantesPorCiclo($idDelCicloConsultado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes 
                     LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
                     WHERE estudiantes.idCiclo = $idDelCicloConsultado 
                     ORDER BY estudiantes.idEstudiante ASC";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaEstudiantesDelCiclo = array();
    
    while($estudianteFila = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaEstudiantesDelCiclo[] = $estudianteFila;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaEstudiantesDelCiclo;
}

/**
 * Elimina a un estudiante del sistema
 */
function eliminarEstudiante($idEstudianteABorrar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM estudiantes WHERE idEstudiante = $idEstudianteABorrar";
    
    $resultadoEliminacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoEliminacion;
}

/**
 * Obtiene la información detallada de un estudiante por su ID
 */
function obtenerEstudiantePorId($idDelEstudianteBuscado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes 
                     LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
                     WHERE estudiantes.idEstudiante = $idDelEstudianteBuscado";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosEncontrados = mysqli_fetch_assoc($resultadoConsulta);
    
    mysqli_close($conexionBaseDatos);
    return $datosEncontrados;
}

function actualizarPasswordEstudiante($idEstudianteRecibido, $passwordNueva) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "UPDATE estudiantes SET password = '$passwordNueva' WHERE idEstudiante = $idEstudianteRecibido";
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Actualiza la información básica del perfil del propio estudiante
 */
function actualizarPerfilEstudiante($idEstudianteAActualizar, $nombrePerfil, $emailPerfil, $telefonoPerfil) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "UPDATE estudiantes SET 
                     nombreEstudiante = '$nombrePerfil', 
                     emailEstudiante = '$emailPerfil', 
                     telefonoEstudiante = '$telefonoPerfil' 
                     WHERE idEstudiante = $idEstudianteAActualizar";
    
    $resultadoActualizacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoActualizacion;
}
?>
