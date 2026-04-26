<?php
require_once("conectar.php");

/**
 * Cuenta el número total de estudiantes registrados
 */
function contarEstudiantes() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT COUNT(*) as total_estudiantes FROM estudiantes";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $filaDeDatos = mysqli_fetch_assoc($resultadoConsulta);
    
    $totalFinal = 0;
    if (!empty($filaDeDatos)) {
        $totalFinal = $filaDeDatos['total_estudiantes'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $totalFinal;
}

/**
 * Cuenta el número total de profesores registrados
 */
function contarProfesores() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT COUNT(*) as total_profesores FROM profesores";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $filaDeDatos = mysqli_fetch_assoc($resultadoConsulta);
    
    $totalFinal = 0;
    if (!empty($filaDeDatos)) {
        $totalFinal = $filaDeDatos['total_profesores'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $totalFinal;
}

/**
 * Cuenta el número total de directores registrados
 */
function contarDirectores() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT COUNT(*) as total_directores FROM directores";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $filaDeDatos = mysqli_fetch_assoc($resultadoConsulta);
    
    $totalFinal = 0;
    if (!empty($filaDeDatos)) {
        $totalFinal = $filaDeDatos['total_directores'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $totalFinal;
}

/**
 * Cuenta el número total de anuncios publicados
 */
function contarAnuncios() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT COUNT(*) as total_anuncios FROM anuncios";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $filaDeDatos = mysqli_fetch_assoc($resultadoConsulta);
    
    $totalFinal = 0;
    if (!empty($filaDeDatos)) {
        $totalFinal = $filaDeDatos['total_anuncios'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $totalFinal;
}

/**
 * Cuenta el número total de mensajes o reclamaciones recibidas
 */
function contarReclamaciones() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT COUNT(*) as total_reclamaciones FROM reclamaciones";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $filaDeDatos = mysqli_fetch_assoc($resultadoConsulta);
    
    $totalFinal = 0;
    if (!empty($filaDeDatos)) {
        $totalFinal = $filaDeDatos['total_reclamaciones'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $totalFinal;
}

/**
 * Cuenta el número total de ciclos formativos
 */
function contarCiclos() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT COUNT(*) as total_ciclos FROM ciclos";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $filaDeDatos = mysqli_fetch_assoc($resultadoConsulta);
    
    $totalFinal = 0;
    if (!empty($filaDeDatos)) {
        $totalFinal = $filaDeDatos['total_ciclos'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $totalFinal;
}

/**
 * Cuenta el número total de módulos profesionales
 */
function contarModulos() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT COUNT(*) as total_modulos FROM modulos";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $filaDeDatos = mysqli_fetch_assoc($resultadoConsulta);
    
    $totalFinal = 0;
    if (!empty($filaDeDatos)) {
        $totalFinal = $filaDeDatos['total_modulos'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $totalFinal;
}

/**
 * Cuenta el número total de retos académicos
 */
function contarRetos() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT COUNT(*) as total_retos FROM retos";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $filaDeDatos = mysqli_fetch_assoc($resultadoConsulta);
    
    $totalFinal = 0;
    if (!empty($filaDeDatos)) {
        $totalFinal = $filaDeDatos['total_retos'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $totalFinal;
}

/**
 * Cuenta el número total de aulas registradas
 */
function contarAulas() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT COUNT(*) as total_aulas FROM aulas";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $filaDeDatos = mysqli_fetch_assoc($resultadoConsulta);
    
    $totalFinal = 0;
    if (!empty($filaDeDatos)) {
        $totalFinal = $filaDeDatos['total_aulas'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $totalFinal;
}

/**
 * Cuenta cuántos dispositivos hay en el inventario
 */
function contarInventario() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT COUNT(*) as total_dispositivos FROM dispositivos";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $filaDeDatos = mysqli_fetch_assoc($resultadoConsulta);
    
    $totalFinal = 0;
    if (!empty($filaDeDatos)) {
        $totalFinal = $filaDeDatos['total_dispositivos'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $totalFinal;
}

/**
 * Cuenta cuántos préstamos de dispositivos están actualmente en curso
 */
function contarPrestamosActivos() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT COUNT(*) as total_prestamos FROM prestamos WHERE estadoPrestamo = 'en curso'";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $filaDeDatos = mysqli_fetch_assoc($resultadoConsulta);
    
    $totalFinal = 0;
    if (!empty($filaDeDatos)) {
        $totalFinal = $filaDeDatos['total_prestamos'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $totalFinal;
}

/**
 * Calcula la suma total de dinero recaudado por todos los pagos
 */
function obtenerTotalRecaudado() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT SUM(monto) as suma_recaudada FROM pagos";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $filaDeDatos = mysqli_fetch_assoc($resultadoConsulta);
    
    $totalDinero = 0;
    if (!empty($filaDeDatos)) {
        if ($filaDeDatos['suma_recaudada'] > 0) {
            $totalDinero = $filaDeDatos['suma_recaudada'];
        }
    }
    
    mysqli_close($conexionBaseDatos);
    return $totalDinero;
}
?>