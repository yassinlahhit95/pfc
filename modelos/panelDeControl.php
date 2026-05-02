<?php
require_once __DIR__ . "/conectar.php";

// --- CONTADORES ESTADÍSTICOS DEL SISTEMA ---

// Contar el total de estudiantes registrados
function contarEstudiantes() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM estudiantes";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Contar el total de profesores registrados
function contarProfesores() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM profesores";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Contar el total de directores/administradores registrados
function contarDirectores() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM directores";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Contar el total de anuncios publicados
function contarAnuncios() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM anuncios";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Contar el total de reclamaciones o mensajes registrados
function contarReclamaciones() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Contar el total de ciclos formativos registrados
function contarCiclos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM ciclos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Contar cuántos estudiantes están vinculados a un profesor concreto
function contarEstudiantesDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(DISTINCT e.idEstudiante) as total 
            FROM estudiantes e 
            JOIN ciclos c ON e.idCiclo = c.idCiclo 
            JOIN ciclo_profesor cp ON c.idCiclo = cp.idCiclo 
            WHERE cp.idProfesor = $idProfesor";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Contar cuántos ciclos formativos tiene asignados un profesor
function contarCiclosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM ciclo_profesor WHERE idProfesor = $idProfesor";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Contar el total de módulos profesionales registrados
function contarModulos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM modulos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Contar el total de retos académicos registrados
function contarRetos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM retos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Contar el total de aulas físicas registradas
function contarAulas() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM aulas";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Contar el total de dispositivos registrados en el inventario
function contarInventario() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM dispositivos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Contar cuántos préstamos de dispositivos están actualmente 'en curso'
function contarPrestamosActivos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM prestamos WHERE estadoPrestamo = 'en curso'";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// --- LÓGICA FINANCIERA ---

// Calcular la cantidad total recaudada a través de todos los pagos realizados
function obtenerTotalRecaudado() {
    $con = obtenerConexion();
    $sql = "SELECT SUM(monto) as acumulado FROM pagos";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (float)($fila['acumulado'] ?? 0);
}

// --- ANÁLISIS ACADÉMICO ---

// Calcular el porcentaje global de módulos aprobados (nota final >= 5)
function obtenerPorcentajeAprobadosGlobal() {
    $con = obtenerConexion();
    
    // 1. Contamos el total de calificaciones registradas
    $sql = "SELECT COUNT(*) as conteo FROM calificaciones_modulos";
    $resultado = mysqli_query($con, $sql);
    $filaTotal = mysqli_fetch_assoc($resultado);
    $totalRegistros = (int)$filaTotal['conteo'];

    if ($totalRegistros === 0) { 
        mysqli_close($con); 
        return 0; 
    }

    // 2. Contamos cuántas de esas calificaciones son aprobadas (en 1ª o 2ª final)
    $sql = "SELECT COUNT(*) as conteo 
                     FROM calificaciones_modulos 
                     WHERE nota_1final >= 5 OR nota_2final >= 5";
    $resultado = mysqli_query($con, $sql);
    $filaAprobados = mysqli_fetch_assoc($resultado);
    $totalAprobados = (int)$filaAprobados['conteo'];

    // 3. Calculamos el porcentaje
    $porcentaje = ($totalAprobados / $totalRegistros) * 100;
    mysqli_close($con);
    return round($porcentaje, 1);
}

// Contar cuántas operaciones de pago se han realizado
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