<?php
require_once("conectar.php");

// Ver todos los modulos
function listarModulos() {
    $db = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo FROM modulos LEFT JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo ORDER BY idModulo ASC";
    $res = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Coger modulos de un profesor
function obtenerModulosDeProfesor($idProf) {
    $db = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo FROM modulos JOIN profesor_modulo ON modulos.idModulo = profesor_modulo.idModulo JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo WHERE profesor_modulo.idProfesor = $idProf";
    $res = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

function obtenerModulosDeProfesorPorCiclo($idProf, $idCic) {
    $db = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo 
            FROM modulos 
            JOIN profesor_modulo ON modulos.idModulo = profesor_modulo.idModulo 
            JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo 
            WHERE profesor_modulo.idProfesor = $idProf AND modulos.idCiclo = $idCic";
    $res = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Listar modulos de un ciclo
function obtenerModulosPorCiclo($idCic) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT * FROM modulos WHERE idCiclo = $idCic");
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Meter modulo
function insertarModulo($nombre, $idCic, $horas) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "INSERT INTO modulos (nombreModulo, idCiclo, horasMaximas) VALUES ('$nombre', $idCic, $horas)");
    mysqli_close($db);
    return $res;
}

// Actualizar modulo
function actualizarModulo($id, $nombre, $idCic, $horas) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "UPDATE modulos SET nombreModulo='$nombre', idCiclo=$idCic, horasMaximas=$horas WHERE idModulo=$id");
    mysqli_close($db);
    return $res;
}

// Borrar
function eliminarModulo($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "DELETE FROM modulos WHERE idModulo = $id");
    mysqli_close($db);
    return $res;
}

// Coger por ID
function obtenerModuloPorId($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT * FROM modulos WHERE idModulo = $id");
    $datos = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return $datos;
}

// Mirar que profes dan un modulo
function obtenerProfesoresDeModulo($idMod) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT idProfesor FROM profesor_modulo WHERE idModulo = $idMod");
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila['idProfesor']; }
    mysqli_close($db);
    return $lista;
}

function limpiarProfesoresModulo($idMod) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "DELETE FROM profesor_modulo WHERE idModulo = $idMod");
    mysqli_close($db);
    return $res;
}

// Sumar horas de retos
function obtenerHorasTotalesRetosModulo($idMod) {
    $db = obtenerConexion();
    $sql = "SELECT SUM(r.horasReto) as total FROM retos r JOIN modulo_reto mr ON r.idReto = mr.idReto WHERE mr.idModulo = $idMod";
    $res = mysqli_query($db, $sql);
    $datos = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($datos['total']) ? $datos['total'] : 0;
}
?>