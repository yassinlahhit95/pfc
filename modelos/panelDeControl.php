<?php
require_once("conectar.php");

// Contadores simples
function contarEstudiantes() {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as t FROM estudiantes";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarProfesores() {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as t FROM profesores";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarDirectores() {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as t FROM directores";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarAnuncios() {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as t FROM anuncios";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarReclamaciones() {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as t FROM reclamaciones";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarCiclos() {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as t FROM ciclos";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarModulos() {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as t FROM modulos";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarRetos() {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as t FROM retos";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarAulas() {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as t FROM aulas";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarInventario() {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as t FROM dispositivos";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarPrestamosActivos() {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as t FROM prestamos WHERE estadoPrestamo = 'en curso'";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

// Dinero
function obtenerTotalRecaudado() {
    $db = obtenerConexion();
    $sql = "SELECT SUM(monto) as sum FROM pagos";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['sum']) ? $fila['sum'] : 0;
}

function obtenerPorcentajeAprobadosGlobal() {
    $db = obtenerConexion();
    $sql1 = "SELECT COUNT(*) as t FROM calificaciones_modulos";
    $fT = mysqli_fetch_assoc(mysqli_query($db, $sql1));
    $total = $fT['t'];

    if ($total == 0) { mysqli_close($db); return 0; }

    $sql2 = "SELECT COUNT(*) as a FROM calificaciones_modulos WHERE nota_1final >= 5 OR nota_2final >= 5";
    $fA = mysqli_fetch_assoc(mysqli_query($db, $sql2));
    $aprob = $fA['a'];

    $porc = ($aprob / $total) * 100;
    mysqli_close($db);
    return round($porc, 1);
}

function contarPagosRealizados() {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as t FROM pagos";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}

function contarPagos() {
    return contarPagosRealizados();
}
?>