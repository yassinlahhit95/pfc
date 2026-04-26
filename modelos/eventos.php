<?php
require_once("conectar.php");

/**
 * Lista todos los eventos programados desde hoy en adelante
 */
function listarEventosProximos() {
    $conexionBaseDatos = obtenerConexion();
    $fechaDeHoy = date('Y-m-d');
    
    $sentenciaSQL = "SELECT * FROM eventos 
                     WHERE fechaEvento >= '$fechaDeHoy' 
                     ORDER BY fechaEvento ASC, horaEvento ASC";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalEventos = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalEventos[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalEventos;
}

/**
 * Registra un nuevo evento en el calendario
 */
function insertarEvento($tituloRecibido, $descripcionRecibida, $fechaRecibida, $horaRecibida, $ubicacionRecibida) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "INSERT INTO eventos (tituloEvento, descripcionEvento, fechaEvento, horaEvento, ubicacionEvento) 
                     VALUES ('$tituloRecibido', '$descripcionRecibida', '$fechaRecibida', '$horaRecibida', '$ubicacionRecibida')";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Elimina un evento por su ID
 */
function eliminarEvento($idEventoABorrar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM eventos WHERE idEvento = $idEventoABorrar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Obtiene los datos de un evento específico por su ID
 */
function obtenerEventoPorId($idEventoBuscado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM eventos WHERE idEvento = $idEventoBuscado";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosEncontrados = mysqli_fetch_assoc($resultadoConsulta);
    
    mysqli_close($conexionBaseDatos);
    return $datosEncontrados;
}

/**
 * Actualiza la información de un evento existente
 */
function actualizarEvento($idEventoAEditar, $tituloNuevo, $descripcionNueva, $fechaNueva, $horaNueva, $ubicacionNueva) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "UPDATE eventos SET 
                     tituloEvento = '$tituloNuevo', 
                     descripcionEvento = '$descripcionNueva', 
                     fechaEvento = '$fechaNueva', 
                     horaEvento = '$horaNueva', 
                     ubicacionEvento = '$ubicacionNueva' 
                     WHERE idEvento = $idEventoAEditar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}
?>