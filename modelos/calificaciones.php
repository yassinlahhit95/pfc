<?php
require_once("conectar.php");

// Ver notas de un alumno en un modulo
function obtenerNotasModulo($idEst, $idMod) {
    $db = obtenerConexion();
    $sql = "SELECT * FROM calificaciones_modulos WHERE idEstudiante = $idEst AND idModulo = $idMod";
    $resultado = mysqli_query($db, $sql);
    $datos = mysqli_fetch_assoc($resultado);
    mysqli_close($db);
    return $datos;
}

// Sacar todas las notas para admin
function listarCalificacionesGeneral() {
    $db = obtenerConexion();
    $sql = "SELECT calificaciones_modulos.*, estudiantes.nombreEstudiante, modulos.nombreModulo FROM calificaciones_modulos JOIN estudiantes ON calificaciones_modulos.idEstudiante = estudiantes.idEstudiante JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo ORDER BY estudiantes.idEstudiante ASC";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Sacar nota por ID
function obtenerCalificacionPorId($id) {
    $db = obtenerConexion();
    $sql = "SELECT * FROM calificaciones_modulos WHERE idCalificacion = $id";
    $resultado = mysqli_query($db, $sql);
    $datos = mysqli_fetch_assoc($resultado);
    mysqli_close($db);
    return $datos;
}

// Borrar nota
function eliminarCalificacion($id) {
    $db = obtenerConexion();
    $resultado = mysqli_query($db, "DELETE FROM calificaciones_modulos WHERE idCalificacion = $id");
    mysqli_close($db);
    return $resultado;
}

// Notas de un alumno
function listarCalificacionesPorEstudiante($idEst) {
    $db = obtenerConexion();
    $sql = "SELECT calificaciones_modulos.*, modulos.nombreModulo FROM calificaciones_modulos JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo WHERE idEstudiante = $idEst";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Notas para profes con filtro
function listarCalificacionesPorProfesorFiltrado($idProf, $idCiclo = 0, $idMod = 0) {
    $db = obtenerConexion();
    $where = "WHERE modulos.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProf)";
    if ($idCiclo > 0) { $where = $where . " AND modulos.idCiclo = $idCiclo"; }
    if ($idMod > 0) { $where = $where . " AND modulos.idModulo = $idMod"; }
    
    $sql = "SELECT calificaciones_modulos.*, estudiantes.nombreEstudiante, modulos.nombreModulo FROM calificaciones_modulos JOIN estudiantes ON calificaciones_modulos.idEstudiante = estudiantes.idEstudiante JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo $where ORDER BY estudiantes.nombreEstudiante ASC";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Guardar o actualizar
function actualizarOCrearNotaCompleta($idEst, $idMod, $n1, $n1f, $n2, $n2f, $obs) {
    $db = obtenerConexion();
    $sqlCheck = "SELECT idCalificacion FROM calificaciones_modulos WHERE idEstudiante = $idEst AND idModulo = $idMod";
    $resCheck = mysqli_query($db, $sqlCheck);
    
    if(mysqli_num_rows($resCheck) > 0) {
        $sql = "UPDATE calificaciones_modulos SET nota_1ev='$n1', nota_1final='$n1f', nota_2ev='$n2', nota_2final='$n2f', observaciones='$obs' WHERE idEstudiante=$idEst AND idModulo=$idMod";
    } else {
        $sql = "INSERT INTO calificaciones_modulos (idEstudiante, idModulo, nota_1ev, nota_1final, nota_2ev, nota_2final, observaciones) VALUES ($idEst, $idMod, '$n1', '$n1f', '$n2', '$n2f', '$obs')";
    }
    
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Lista de un modulo para el formulario masivo
function listarCalificacionesPorModulo($idMod) {
    $db = obtenerConexion();
    $resMod = mysqli_query($db, "SELECT idCiclo FROM modulos WHERE idModulo = $idMod");
    $datosMod = mysqli_fetch_assoc($resMod);
    $idCiclo = isset($datosMod['idCiclo']) ? $datosMod['idCiclo'] : 0;
    
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, cm.nota_1ev as calificacion, cm.observaciones FROM estudiantes e LEFT JOIN calificaciones_modulos cm ON e.idEstudiante = cm.idEstudiante AND cm.idModulo = $idMod WHERE e.idCiclo = $idCiclo ORDER BY e.nombreEstudiante ASC";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}
?>