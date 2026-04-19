<?php
require_once "conectar.php";

function contarEstudiantes() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(idEstudiante) AS total FROM estudiantes";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}

function contarProfesores() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(idProfesor) AS total FROM profesores";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}

function contarDirectores() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(idDirector) AS total FROM directores";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}

function obtenerTotalRecaudado() {
    $conexion = obtenerConexion();
    $sql = "SELECT SUM(monto) AS total FROM pagos WHERE estadoPago = 'pagado'";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}

function contarPagosPendientes() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(idPago) AS total FROM pagos WHERE estadoPago = 'pendiente'";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}

function contarAulas() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(idAula) AS total FROM aulas";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}

function contarCiclos() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(idCiclo) AS total FROM ciclos";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}

function contarModulos() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(idModulo) AS total FROM modulos";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}

function contarRetos() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(idReto) AS total FROM retos";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}

function contarInventario() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM dispositivos";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}

function contarPagos() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM pagos";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}

function contarAnuncios() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM anuncios";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}

function contarReclamaciones() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}

function contarPrestamosActivos() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM prestamos WHERE estadoPrestamo = 'en curso' OR estadoPrestamo = 'activo'";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}
?>
