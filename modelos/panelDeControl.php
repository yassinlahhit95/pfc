<?php
require_once __DIR__ . "/conectar.php";

function contarEstudiantes() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM estudiantes";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

function contarProfesores() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM profesores";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

function contarDirectores() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM directores";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

function contarAnuncios() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM anuncios";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

function contarReclamaciones() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

function contarCiclos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM ciclos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
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
    return (int)($fila['total'] ?? 0);
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
    return (int)($fila['total'] ?? 0);
}

function contarModulos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM modulos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

function contarRetos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM retos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

function contarAulas() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM aulas";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

function contarInventario() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM dispositivos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

function contarPrestamosActivos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM prestamos WHERE estadoPrestamo = 'en curso'";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

function obtenerTotalRecaudado() {
    $con = obtenerConexion();
    $sql = "SELECT SUM(monto) as acumulado FROM pagos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (float)($fila['acumulado'] ?? 0);
}

function obtenerPorcentajeAprobadosGlobal() {
    $con = obtenerConexion();

    $sql = "SELECT COUNT(*) as conteo FROM calificaciones_modulos";
    $resultado = mysqli_query($con, $sql);
    $filaTotal = mysqli_fetch_assoc($resultado);
    $totalRegistros = (int)$filaTotal['conteo'];

    if ($totalRegistros === 0) {
        mysqli_close($con);
        return 0;
    }

    $sql = "SELECT COUNT(*) as conteo FROM calificaciones_modulos WHERE nota_1final >= 5 OR nota_2final >= 5";
    $resultado = mysqli_query($con, $sql);
    $filaAprobados = mysqli_fetch_assoc($resultado);
    $totalAprobados = (int)$filaAprobados['conteo'];

    $porcentaje = ($totalAprobados / $totalRegistros) * 100;
    mysqli_close($con);
    return round($porcentaje, 1);
}

function contarPagosRealizados() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM pagos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Alias para compatibilidad
function contarPagos() {
    return contarPagosRealizados();
}
?>
