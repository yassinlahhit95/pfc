<?php
require_once __DIR__ . "/conectar.php";

function contarEstudiantes() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM estudiantes";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarProfesores() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM profesores";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarDirectores() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM directores";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarAnuncios() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM anuncios";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarReclamaciones() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarCiclos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM ciclos";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarEstudiantesDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(DISTINCT e.idEstudiante) as total
            FROM estudiantes e
            JOIN ciclos c ON e.idCiclo = c.idCiclo
            LEFT JOIN ciclo_profesor cp ON c.idCiclo = cp.idCiclo
            LEFT JOIN modulos m ON c.idCiclo = m.idCiclo
            LEFT JOIN profesor_modulo pm ON m.idModulo = pm.idModulo
            WHERE (cp.idProfesor = ? OR pm.idProfesor = ?)";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarCiclosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(DISTINCT c.idCiclo) as total
            FROM ciclos c
            LEFT JOIN ciclo_profesor cp ON c.idCiclo = cp.idCiclo
            LEFT JOIN modulos m ON c.idCiclo = m.idCiclo
            LEFT JOIN profesor_modulo pm ON m.idModulo = pm.idModulo
            WHERE (cp.idProfesor = ? OR pm.idProfesor = ?)";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarModulos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM modulos";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarRetos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM retos";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarInventario() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM dispositivos";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarPrestamosActivos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM prestamos WHERE estadoPrestamo = 'en curso'";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function obtenerTotalRecaudado() {
    $con = obtenerConexion();
    $sql = "SELECT SUM(monto) as acumulado FROM pagos";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return floatval($fila['acumulado']);
}

function obtenerPorcentajeAprobadosGlobal() {
    $con = obtenerConexion();
    $sql = "SELECT
                (SELECT COUNT(*) FROM calificaciones_modulos) AS total,
                (SELECT COUNT(*) FROM calificaciones_modulos WHERE nota_1final >= 5 OR nota_2final >= 5) AS aprobados";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    $total = intval($fila['total']);
    $aprobados = intval($fila['aprobados']);
    if ($total == 0) {
        return 0;
    }
    return round(($aprobados / $total) * 100, 1);
}

function contarPagosRealizados() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM pagos";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarTFGsEntregados() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM estudiantes WHERE archivoTFG != '' AND archivoTFG IS NOT NULL";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarTFGsCalificados() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM calificaciones_tfg";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

?>
