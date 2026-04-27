<?php
require_once("conectar.php");

// Ver todos los TFGs
function listarTodosLosTFGs() {
    $db = obtenerConexion();
    $sql = "SELECT estudiantes.idEstudiante, estudiantes.nombreEstudiante, estudiantes.archivoTFG, estudiantes.fechaSubidaTFG, ciclos.nombreCiclo, ciclos.idCiclo 
            FROM estudiantes 
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
            WHERE estudiantes.archivoTFG != '' 
            ORDER BY estudiantes.nombreEstudiante ASC";
            
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila; 
    }
    mysqli_close($db);
    return $lista;
}

// Filtrar por ciclo
function listarTFGsFiltrados($id) {
    $db = obtenerConexion();
    $sql = "SELECT estudiantes.idEstudiante, estudiantes.nombreEstudiante, estudiantes.archivoTFG, estudiantes.fechaSubidaTFG, ciclos.nombreCiclo, ciclos.idCiclo 
            FROM estudiantes 
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
            WHERE estudiantes.archivoTFG != '' AND estudiantes.idCiclo = $id 
            ORDER BY estudiantes.nombreEstudiante ASC";
            
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila; 
    }
    mysqli_close($db);
    return $lista;
}

// Ver por alumno
function obtenerTFGporEstudiante($id) {
    $db = obtenerConexion();
    $sql = "SELECT idEstudiante, nombreEstudiante, archivoTFG, fechaSubidaTFG 
            FROM estudiantes 
            WHERE idEstudiante = $id";
            
    $resultado = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($db);
    return $fila;
}

// Subir archivo
function actualizarTFG($id, $file) {
    $db = obtenerConexion();
    $now = date('Y-m-d H:i:s');
    $sql = "UPDATE estudiantes SET archivoTFG = '$file', fechaSubidaTFG = '$now' WHERE idEstudiante = $id";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Quitar archivo
function eliminarTFG($id) {
    $db = obtenerConexion();
    $sql = "UPDATE estudiantes SET archivoTFG = '', fechaSubidaTFG = NULL WHERE idEstudiante = $id";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Ver cuantos hay
function contarTFGsSubidos() {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM estudiantes WHERE archivoTFG != ''";
    $resultado = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    
    $total = 0;
    if (isset($fila)) {
        $total = $fila['total'];
    }
    mysqli_close($db);
    return $total;
}
// Eliminar archivo TFG de un alumno
function eliminarArchivoTFG($idEst) {
    $db = obtenerConexion();
    
    // Primero obtenemos el nombre del archivo para borrarlo del disco
    $sql = "SELECT archivoTFG FROM tfg WHERE idEstudiante = $idEst";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    
    if ($fila && !empty($fila['archivoTFG'])) {
        $ruta = "../../../public/uploads/pfc/" . $fila['archivoTFG'];
        if (file_exists($ruta)) {
            unlink($ruta);
        }
    }
    
    // Ahora limpiamos el registro en la base de datos
    $sqlDelete = "UPDATE tfg SET archivoTFG = NULL, fechaSubidaTFG = NULL WHERE idEstudiante = $idEst";
    $resultado = mysqli_query($db, $sqlDelete);
    
    mysqli_close($db);
    return $resultado;
}
?>