<?php
require_once("conectar.php");

// Contadores simples
function contarEstudiantes() {
    $db = obtenerConexion();
    $fila = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as t FROM estudiantes"));
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarProfesores() {
    $db = obtenerConexion();
    $fila = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as t FROM profesores"));
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarDirectores() {
    $db = obtenerConexion();
    $fila = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as t FROM directores"));
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarAnuncios() {
    $db = obtenerConexion();
    $fila = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as t FROM anuncios"));
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarReclamaciones() {
    $db = obtenerConexion();
    $fila = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as t FROM reclamaciones"));
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarCiclos() {
    $db = obtenerConexion();
    $fila = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as t FROM ciclos"));
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarModulos() {
    $db = obtenerConexion();
    $fila = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as t FROM modulos"));
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarRetos() {
    $db = obtenerConexion();
    $fila = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as t FROM retos"));
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarAulas() {
    $db = obtenerConexion();
    $fila = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as t FROM aulas"));
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarInventario() {
    $db = obtenerConexion();
    $fila = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as t FROM dispositivos"));
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarPrestamosActivos() {
    $db = obtenerConexion();
    $fila = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as t FROM prestamos WHERE estadoPrestamo = 'en curso'"));
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

// Dinero
function obtenerTotalRecaudado() {
    $db = obtenerConexion();
    $fila = mysqli_fetch_assoc(mysqli_query($db, "SELECT SUM(monto) as sum FROM pagos"));
    mysqli_close($db);
    return isset($fila['sum']) ? $fila['sum'] : 0;
}

function obtenerPorcentajeAprobadosGlobal() {
    $db = obtenerConexion();
    $fT = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as t FROM calificaciones_modulos"));
    $total = $fT['t'];
    if ($total == 0) { mysqli_close($db); return 0; }
    $fA = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as a FROM calificaciones_modulos WHERE nota_1final >= 5 OR nota_2final >= 5"));
    $porc = ($fA['a'] / $total) * 100;
    mysqli_close($db);
    return round($porc, 1);
}

function contarPagosRealizados() {
    $db = obtenerConexion();
    $fila = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as t FROM pagos"));
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarPagos() {
    return contarPagosRealizados();
}
?>