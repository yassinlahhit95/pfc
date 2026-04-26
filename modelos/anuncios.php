<?php
require_once("conectar.php");

/**
 * Obtiene el listado completo de anuncios registrados
 */
function listarTodosLosAnuncios() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM anuncios ORDER BY idAnuncio DESC";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    $listaFinalAnuncios = array();
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        // Mantenemos compatibilidad con nombres de campos antiguos si fuera necesario
        $filaDeDatos['tituloAnuncio'] = $filaDeDatos['titulo'];
        $filaDeDatos['contenidoAnuncio'] = $filaDeDatos['mensaje'];
        $listaFinalAnuncios[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalAnuncios;
}

/**
 * Crea un nuevo anuncio en el sistema
 */
function insertarAnuncio($tituloRecibido, $mensajeRecibido, $dirigidoA = 'todos') {
    $conexionBaseDatos = obtenerConexion();
    
    $fechaDeHoy = date('Y-m-d H:i:s');
    $fechaDeExpiracionCalculada = date('Y-m-d', strtotime('+1 month'));
    
    $sentenciaSQL = "INSERT INTO anuncios (titulo, mensaje, fechaAnuncio, fechaExpiracion, dirigidoA) 
                     VALUES ('$tituloRecibido', '$mensajeRecibido', '$fechaDeHoy', '$fechaDeExpiracionCalculada', '$dirigidoA')";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Elimina un anuncio por su ID
 */
function eliminarAnuncio($idAnuncioABorrar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM anuncios WHERE idAnuncio = $idAnuncioABorrar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Obtiene los datos de un anuncio específico
 */
function obtenerAnuncioPorId($idAnuncioBuscado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM anuncios WHERE idAnuncio = $idAnuncioBuscado";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosAnuncio = mysqli_fetch_assoc($resultadoConsulta);
    
    if (!empty($datosAnuncio)) {
        $datosAnuncio['tituloAnuncio'] = $datosAnuncio['titulo'];
        $datosAnuncio['contenidoAnuncio'] = $datosAnuncio['mensaje'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $datosAnuncio;
}

/**
 * Actualiza los datos de un anuncio existente
 */
function actualizarAnuncio($idAnuncioAEditar, $tituloNuevo, $mensajeNuevo, $fechaExpiracionNueva, $dirigidoANuevo) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "UPDATE anuncios SET 
                     titulo = '$tituloNuevo', 
                     mensaje = '$mensajeNuevo', 
                     fechaExpiracion = '$fechaExpiracionNueva', 
                     dirigidoA = '$dirigidoANuevo' 
                     WHERE idAnuncio = $idAnuncioAEditar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Cuenta cuántos anuncios no han expirado aún
 */
function contarAnunciosQueEstanActivos() {
    $conexionBaseDatos = obtenerConexion();
    $fechaHoy = date('Y-m-d');
    
    $sentenciaSQL = "SELECT COUNT(*) as total FROM anuncios WHERE fechaExpiracion >= '$fechaHoy'";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $filaDeDatos = mysqli_fetch_assoc($resultadoConsulta);
    
    $totalActivos = 0;
    if (!empty($filaDeDatos)) {
        $totalActivos = $filaDeDatos['total'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $totalActivos;
}

/**
 * Lista los anuncios filtrados por el rol del usuario
 */
function listarAnunciosPorRol($rolDelUsuario) {
    $conexionBaseDatos = obtenerConexion();
    $fechaHoy = date('Y-m-d');
    
    $sentenciaSQL = "SELECT * FROM anuncios 
                     WHERE fechaExpiracion >= '$fechaHoy' 
                     AND (dirigidoA = '$rolDelUsuario' OR dirigidoA = 'todos') 
                     ORDER BY idAnuncio DESC";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalAnuncios = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalAnuncios[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalAnuncios;
}
/**
 * Lista los anuncios más recientes con un límite para la paginación
 */
function listarAnunciosConPaginas($limiteDeResultados) {
    $conexionBaseDatos = obtenerConexion();
    
    // Obtenemos la página desde la URL si existe
    $posicionInicio = 0;
    if (isset($_GET['p_anuncios'])) {
        $paginaActual = (int)$_GET['p_anuncios'];
        if ($paginaActual > 1) {
            $posicionInicio = ($paginaActual - 1) * $limiteDeResultados;
        }
    }

    $sentenciaSQL = "SELECT * FROM anuncios 
                     ORDER BY idAnuncio DESC 
                     LIMIT $posicionInicio, $limiteDeResultados";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalAnuncios = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalAnuncios[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalAnuncios;
}
?>