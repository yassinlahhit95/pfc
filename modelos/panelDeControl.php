<?php
require_once("conectar.php");

function contarEstudiantes() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM estudiantes";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarProfesores() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM profesores";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarDirectores() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM directores";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarPagos() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM pagos";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarAnuncios() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM anuncios";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarReclamaciones() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarCiclos() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM ciclos";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarModulos() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM modulos";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarRetos() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM retos";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarAulas() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM aulas";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarInventario() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM dispositivos";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function contarPrestamosActivos() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM prestamos WHERE estadoPrestamo = 'en curso'";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function obtenerTotalRecaudado() {
    $conexion = obtenerConexion();
    $sql = "SELECT SUM(monto) as total FROM pagos";
    $resultado = mysqli_query($conexion, $sql);
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