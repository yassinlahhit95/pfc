<?php
require_once __DIR__ . "/conectar.php";

// Obtener la lista de todos los Trabajos de Fin de Grado (TFG) subidos
function listarTodosLosTFGs() {
    $con = obtenerConexion();
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, e.archivoTFG, e.fechaSubidaTFG, c.nombreCiclo, c.idCiclo 
            FROM estudiantes e 
            JOIN ciclos c ON e.idCiclo = c.idCiclo 
            WHERE e.archivoTFG != '' 
            ORDER BY e.nombreEstudiante ASC";
            
    $resultado = mysqli_query($con, $sql);
    $listaTFGs = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $listaTFGs[] = $fila; 
    }
    mysqli_close($con);
    return $listaTFGs;
}

// Listar TFGs filtrados por un ciclo formativo específico
function listarTFGsFiltrados($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, e.archivoTFG, e.fechaSubidaTFG, c.nombreCiclo, c.idCiclo 
            FROM estudiantes e 
            JOIN ciclos c ON e.idCiclo = c.idCiclo 
            WHERE e.archivoTFG != '' AND e.idCiclo = $idCiclo 
            ORDER BY e.nombreEstudiante ASC";
            
    $resultado = mysqli_query($con, $sql);
    $listaFiltrada = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $listaFiltrada[] = $fila; 
    }
    mysqli_close($con);
    return $listaFiltrada;
}

// Obtener los datos del TFG de un estudiante concreto
function obtenerTFGporEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT idEstudiante, nombreEstudiante, archivoTFG, fechaSubidaTFG 
            FROM estudiantes 
            WHERE idEstudiante = $idEstudiante";
            
    $resultado = mysqli_query($con, $sql);
    $datosTFG = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosTFG;
}

// Subir o actualizar el archivo de un TFG
function actualizarTFG($idEstudiante, $nombreArchivo) {
    $con = obtenerConexion();
    $fechaHoraActual = date('Y-m-d H:i:s');
    $sql = "UPDATE estudiantes 
            SET archivoTFG = '$nombreArchivo', fechaSubidaTFG = '$fechaHoraActual' 
            WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Actualizar tanto el título como el archivo (opcional) de un TFG
function actualizarDatosTFG($idEstudiante, $tituloTFG, $nombreArchivo = null) {
    $con = obtenerConexion();
    $fechaHoraActual = date('Y-m-d H:i:s');
    
    if (!empty($nombreArchivo)) {
        $sql = "UPDATE estudiantes 
                SET tituloTFG = '$tituloTFG', archivoTFG = '$nombreArchivo', fechaSubidaTFG = '$fechaHoraActual' 
                WHERE idEstudiante = $idEstudiante";
    } else {
        $sql = "UPDATE estudiantes SET tituloTFG = '$tituloTFG' WHERE idEstudiante = $idEstudiante";
    }
    
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Eliminar el archivo del TFG de un estudiante (limpiar campos)
function eliminarTFG($idEstudiante) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes 
            SET archivoTFG = '', fechaSubidaTFG = NULL 
            WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Contar cuántos TFGs han sido subidos en total
function contarTFGsSubidos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM estudiantes WHERE archivoTFG != ''";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Contar los TFGs subidos por estudiantes de los ciclos de un profesor
function contarTFGsDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(DISTINCT e.idEstudiante) as total 
            FROM estudiantes e 
            JOIN ciclos c ON e.idCiclo = c.idCiclo 
            JOIN ciclo_profesor cp ON c.idCiclo = cp.idCiclo 
            WHERE cp.idProfesor = $idProfesor AND e.archivoTFG != ''";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

// Listar los TFGs de los estudiantes vinculados a un profesor
function listarTFGsPorProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, e.archivoTFG, e.fechaSubidaTFG, c.nombreCiclo, c.idCiclo 
            FROM estudiantes e 
            JOIN ciclos c ON e.idCiclo = c.idCiclo 
            JOIN ciclo_profesor cp ON c.idCiclo = cp.idCiclo 
            WHERE cp.idProfesor = $idProfesor AND e.archivoTFG != '' 
            ORDER BY e.nombreEstudiante ASC";
            
    $resultado = mysqli_query($con, $sql);
    $listaTFGs = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $listaTFGs[] = $fila; 
    }
    mysqli_close($con);
    return $listaTFGs;
}

// Eliminar el archivo físico y limpiar el registro del TFG
function eliminarArchivoTFG($idEstudiante) {
    $con = obtenerConexion();
    
    // 1. Localizamos el archivo en la base de datos
    $sqlBusqueda = "SELECT archivoTFG FROM estudiantes WHERE idEstudiante = $idEstudiante";
    $resultadoBusqueda = mysqli_query($con, $sqlBusqueda);
    $fila = mysqli_fetch_assoc($resultadoBusqueda);
    
    if ($fila && !empty($fila['archivoTFG'])) {
        $rutaFisica = __DIR__ . "/../public/uploads/pfc/" . $fila['archivoTFG'];
        if (file_exists($rutaFisica)) {
            unlink($rutaFisica); // Borramos el archivo real del disco
        }
    }
    
    // 2. Limpiamos los campos en la base de datos (está en la tabla estudiantes)
    $sql = "UPDATE estudiantes 
                    SET archivoTFG = '', fechaSubidaTFG = NULL 
                    WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($con, $sql);
    
    mysqli_close($con);
    return $resultado;
}

