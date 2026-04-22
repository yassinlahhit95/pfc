<?php
require_once("conectar.php");

function contarEstudiantes() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT COUNT(*) as total FROM estudiantes");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarProfesores() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT COUNT(*) as total FROM profesores");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarDirectores() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT COUNT(*) as total FROM directores");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarPagos() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT COUNT(*) as total FROM pagos");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarAnuncios() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT COUNT(*) as total FROM anuncios");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarReclamaciones() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT COUNT(*) as total FROM reclamaciones");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarCiclos() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT COUNT(*) as total FROM ciclos");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarModulos() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT COUNT(*) as total FROM modulos");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarRetos() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT COUNT(*) as total FROM retos");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarAulas() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT COUNT(*) as total FROM aulas");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarInventario() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT COUNT(*) as total FROM dispositivos");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarPrestamosActivos() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT COUNT(*) as total FROM prestamos WHERE estadoPrestamo = 'en curso'");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function obtenerTotalRecaudado() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT SUM(monto) as total FROM pagos");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    $total = 0;
    if ($fila['total'] > 0) {
        $total = $fila['total'];
    }
    return $total;
}

function contarPagosRealizados() {
    return contarPagos();
}
?>