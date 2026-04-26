<?php
require_once("conectar.php");

/**
 * Obtiene el listado completo de directores del sistema
 */
function listarDirectores() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM directores ORDER BY idDirector ASC";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    $listaFinalDirectores = array();
    while ($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalDirectores[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalDirectores;
}

/**
 * Registra un nuevo director en el sistema
 */
function insertarDirector($nombreRecibido, $emailRecibido, $dniRecibido, $telefonoRecibido, $fechaAltaRecibida, $fechaNacimientoRecibida = '2000-01-01', $direccionRecibida = '', $ciudadRecibida = '', $codigoPostalRecibido = '', $observacionesRecibidas = '') {
    $conexionBaseDatos = obtenerConexion();
    
    $sentenciaSQL = "INSERT INTO directores (nombreDirector, emailDirector, dniDirector, telefonoDirector, fechaAltaDirector, fechaNacimientoDirector, direccionDirector, ciudadDirector, codigoPostalDirector, observacionesDirector) 
                     VALUES ('$nombreRecibido', '$emailRecibido', '$dniRecibido', '$telefonoRecibido', '$fechaAltaRecibida', '$fechaNacimientoRecibida', '$direccionRecibida', '$ciudadRecibida', '$codigoPostalRecibido', '$observacionesRecibidas')";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Actualiza la información de un director existente
 */
function actualizarDirector($idDirectorAEditar, $nombreNuevo, $emailNuevo, $dniNuevo, $telefonoNuevo, $fechaAltaNueva, $fechaNacNuevo = '2000-01-01', $direccionNueva = '', $ciudadNueva = '', $cpNuevo = '', $obsNuevas = '') {
    $conexionBaseDatos = obtenerConexion();
    
    $sentenciaSQL = "UPDATE directores SET 
                     nombreDirector = '$nombreNuevo', 
                     emailDirector = '$emailNuevo', 
                     dniDirector = '$dniNuevo', 
                     telefonoDirector = '$telefonoNuevo', 
                     fechaAltaDirector = '$fechaAltaNueva',
                     fechaNacimientoDirector = '$fechaNacNuevo', 
                     direccionDirector = '$direccionNueva',
                     ciudadDirector = '$ciudadNueva', 
                     codigoPostalDirector = '$cpNuevo', 
                     observacionesDirector = '$obsNuevas'
                     WHERE idDirector = $idDirectorAEditar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Elimina un director por su ID
 */
function eliminarDirector($idDirectorABorrar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM directores WHERE idDirector = $idDirectorABorrar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

function actualizarPasswordDirector($idDirectorRecibido, $passwordNueva) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "UPDATE directores SET password = '$passwordNueva' WHERE idDirector = $idDirectorRecibido";
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

function actualizarPerfilDirector($idDirectorAEditar, $nombreNuevo, $emailNuevo, $telefonoNuevo) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "UPDATE directores SET 
                     nombreDirector = '$nombreNuevo', 
                     emailDirector = '$emailNuevo', 
                     telefonoDirector = '$telefonoNuevo' 
                     WHERE idDirector = $idDirectorAEditar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Obtiene los datos de un director específico por su ID
 */
function obtenerDirectorPorId($idDirectorBuscado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM directores WHERE idDirector = $idDirectorBuscado";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosEncontrados = mysqli_fetch_assoc($resultadoConsulta);
    
    mysqli_close($conexionBaseDatos);
    return $datosEncontrados;
}
?>