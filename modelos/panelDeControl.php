<?php
require_once __DIR__ . "/conectar.php";

function contarEstudiantes() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM estudiantes";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarProfesores() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM profesores";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarDirectores() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM directores";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarAnuncios() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM anuncios";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarReclamaciones() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarCiclos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM ciclos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarEstudiantesDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(DISTINCT e.idEstudiante) as total FROM estudiantes e JOIN ciclos c ON e.idCiclo = c.idCiclo JOIN ciclo_profesor cp ON c.idCiclo = cp.idCiclo WHERE cp.idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarCiclosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM ciclo_profesor WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarModulos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM modulos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarRetos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM retos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarAulas() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM aulas";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarInventario() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM dispositivos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarPrestamosActivos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM prestamos WHERE estadoPrestamo = 'en curso'";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

function obtenerTotalRecaudado() {
    $con = obtenerConexion();
    $sql = "SELECT SUM(monto) as acumulado FROM pagos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return floatval($fila['acumulado']);
}

function obtenerPorcentajeAprobadosGlobal() {
    $con = obtenerConexion();
    $sql = "SELECT
                (SELECT COUNT(*) FROM calificaciones_modulos) AS total,
                (SELECT COUNT(*) FROM calificaciones_modulos WHERE nota_1final >= 5 OR nota_2final >= 5) AS aprobados";
    $resultado = mysqli_query($con, $sql);
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
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return intval($fila['total']);
}

?>
