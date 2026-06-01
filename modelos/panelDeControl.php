<?php
require_once __DIR__ . "/conectar.php";

function contarEstudiantes() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM estudiantes";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return intval($fila['total']);
}

function contarProfesores() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM profesores";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return intval($fila['total']);
}

function contarDirectores() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM directores";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return intval($fila['total']);
}

function contarAnuncios() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM anuncios";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return intval($fila['total']);
}

function contarCiclos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM ciclos";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return intval($fila['total']);
}

function contarEstudiantesDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(DISTINCT e.idEstudiante) as total
            FROM estudiantes e
            WHERE e.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
               OR e.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return intval($fila['total']);
}

function contarCiclosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(DISTINCT c.idCiclo) as total
            FROM ciclos c
            WHERE c.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
               OR c.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?)";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return intval($fila['total']);
}

function contarModulos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM modulos";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return intval($fila['total']);
}

function contarRetos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM retos";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return intval($fila['total']);
}

function contarInventario() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM dispositivos";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return intval($fila['total']);
}

function contarPrestamosActivos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM prestamos WHERE estadoPrestamo = 'en curso'";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return intval($fila['total']);
}

function obtenerTotalRecaudado() {
    $con = obtenerConexion();
    $sql = "SELECT SUM(monto) as acumulado FROM pagos";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return floatval($fila['acumulado']);
}

function contarPagosRealizados() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM pagos";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return intval($fila['total']);
}

function contarTFGsEntregados() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM estudiantes WHERE archivoTFG != '' AND archivoTFG IS NOT NULL";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    return intval($fila['total']);
}


