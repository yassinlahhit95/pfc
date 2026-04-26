<?php
require_once("conectar.php");

/**
 * Obtiene el listado completo de profesores
 */
function listarProfesores() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM profesores ORDER BY idProfesor ASC";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    $listaFinalProfesores = array();
    while ($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalProfesores[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalProfesores;
}

/**
 * Registra un nuevo profesor en el sistema
 */
function insertarProfesor($nombreRecibido, $emailRecibido, $telefonoRecibido, $dniRecibido, $direccionRecibida, $especialidadRecibida = "", $fechaNacimientoRecibida = '1980-01-01', $fechaAltaRecibida = '2026-01-01', $ciudadRecibida = '', $codigoPostalRecibido = '', $observacionesRecibidas = '') {
    $conexionBaseDatos = obtenerConexion();
    
    $sentenciaSQL = "INSERT INTO profesores (nombreProfesor, emailProfesor, telefonoProfesor, dniProfesor, direccionProfesor, especialidad, fechaNacimientoProfesor, fechaAltaProfesor, ciudadProfesor, codigoPostalProfesor, observacionesProfesor)
                     VALUES ('$nombreRecibido', '$emailRecibido', '$telefonoRecibido', '$dniRecibido', '$direccionRecibida', '$especialidadRecibida', '$fechaNacimientoRecibida', '$fechaAltaRecibida', '$ciudadRecibida', '$codigoPostalRecibido', '$observacionesRecibidas')";
    
    if (mysqli_query($conexionBaseDatos, $sentenciaSQL)) {
        $idDelProfesorCreado = mysqli_insert_id($conexionBaseDatos);
        mysqli_close($conexionBaseDatos);
        return $idDelProfesorCreado;
    }
    
    mysqli_close($conexionBaseDatos);
    return false;
}

/**
 * Actualiza la información de un profesor existente
 */
function actualizarProfesor($idProfesorAEditar, $nombreNuevo, $emailNuevo, $telefonoNuevo, $dniNuevo, $direccionNueva, $especialidadNueva = "", $fechaNacNuevo = '1980-01-01', $fechaAltaNueva = '2026-01-01', $ciudadNueva = '', $cpNuevo = '', $obsNuevas = '') {
    $conexionBaseDatos = obtenerConexion();
    
    $sentenciaSQL = "UPDATE profesores SET 
                     nombreProfesor = '$nombreNuevo', 
                     emailProfesor = '$emailNuevo',
                     telefonoProfesor = '$telefonoNuevo', 
                     dniProfesor = '$dniNuevo',
                     direccionProfesor = '$direccionNueva', 
                     especialidad = '$especialidadNueva',
                     fechaNacimientoProfesor = '$fechaNacNuevo', 
                     fechaAltaProfesor = '$fechaAltaNueva',
                     ciudadProfesor = '$ciudadNueva', 
                     codigoPostalProfesor = '$cpNuevo', 
                     observacionesProfesor = '$obsNuevas'
                     WHERE idProfesor = $idProfesorAEditar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Vincula un profesor con un ciclo formativo
 */
function asociarCicloProfesor($idDelCiclo, $idDelProfesor) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($idDelCiclo, $idDelProfesor)";
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Vincula un profesor con un módulo profesional
 */
function asociarModuloProfesor($idDelModulo, $idDelProfesor) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "INSERT INTO profesor_modulo (idModulo, idProfesor) VALUES ($idDelModulo, $idDelProfesor)";
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Elimina un profesor del sistema
 */
function eliminarProfesor($idProfesorABorrar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM profesores WHERE idProfesor = $idProfesorABorrar";
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Obtiene los datos de un profesor por su ID
 */
function obtenerProfesorPorId($idDelProfesorBuscado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM profesores WHERE idProfesor = $idDelProfesorBuscado";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosDelProfesor = mysqli_fetch_assoc($resultadoConsulta);
    
    mysqli_close($conexionBaseDatos);
    return $datosDelProfesor;
}

/**
 * Lista los profesores asignados a un ciclo específico
 */
function listarProfesoresPorCiclo($idDelCicloConsultado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT p.* FROM profesores p 
                     JOIN ciclo_profesor cp ON p.idProfesor = cp.idProfesor 
                     WHERE cp.idCiclo = $idDelCicloConsultado 
                     ORDER BY p.nombreProfesor ASC";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalProfesores = array();
    
    while ($datosFila = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalProfesores[] = $datosFila;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalProfesores;
}

/**
 * Obtiene solo los IDs de los módulos que tiene asignados un profesor
 */
function obtenerIdsModulosDeProfesor($idDelProfesorConsultado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT idModulo FROM profesor_modulo WHERE idProfesor = $idDelProfesorConsultado";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    $listaDeIdsEncontrados = array();
    while($datosFila = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaDeIdsEncontrados[] = $datosFila['idModulo'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaDeIdsEncontrados;
}

/**
 * Elimina todas las asignaciones de módulos de un profesor
 */
function limpiarModulosProfesor($idDelProfesorALimpiar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM profesor_modulo WHERE idProfesor = $idDelProfesorALimpiar";
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

function actualizarPasswordProfesor($idProfesorRecibido, $passwordNueva) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "UPDATE profesores SET password = '$passwordNueva' WHERE idProfesor = $idProfesorRecibido";
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Actualiza la información básica del perfil del propio profesor
 */
function actualizarPerfilProfesor($idProfesorAActualizar, $nombrePerfil, $emailPerfil, $telefonoPerfil) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "UPDATE profesores SET 
                     nombreProfesor = '$nombrePerfil', 
                     emailProfesor = '$emailPerfil', 
                     telefonoProfesor = '$telefonoPerfil' 
                     WHERE idProfesor = $idProfesorAActualizar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}
?>