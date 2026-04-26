<?php
require_once("conectar.php");

// Ver retos
function listarRetos() {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT * FROM retos ORDER BY idReto ASC");
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Filtro por modulo
function listarRetosFiltrados($idMod) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT DISTINCT retos.* FROM retos JOIN modulo_reto ON retos.idReto = modulo_reto.idReto WHERE modulo_reto.idModulo = $idMod ORDER BY retos.idReto ASC");
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Retos de un profe
function obtenerRetosDeProfesor($idProf) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT DISTINCT retos.* FROM retos JOIN modulo_reto ON retos.idReto = modulo_reto.idReto JOIN profesor_modulo ON modulo_reto.idModulo = profesor_modulo.idModulo WHERE profesor_modulo.idProfesor = $idProf");
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Meter reto
function insertarReto($nom, $fecI, $fecF, $horas, $modulos = []) {
    $db = obtenerConexion();
    if (mysqli_query($db, "INSERT INTO retos (nombreReto, fechaInicio, fechaFin, horasReto) VALUES ('$nom', '$fecI', '$fecF', $horas)")) {
        $resId = mysqli_query($db, "SELECT MAX(idReto) as id FROM retos");
        $fId = mysqli_fetch_assoc($resId);
        $id = $fId['id'];
        foreach ($modulos as $m) { mysqli_query($db, "INSERT INTO modulo_reto (idModulo, idReto) VALUES ($m, $id)"); }
        mysqli_close($db);
        return $id;
    }
    mysqli_close($db);
    return false;
}

// Mirar horas
function comprobarHorasDisponiblesModulo($idMod, $horas, $excluir = 0) {
    $db = obtenerConexion();
    $fM = mysqli_fetch_assoc(mysqli_query($db, "SELECT horasMaximas FROM modulos WHERE idModulo = $idMod"));
    $max = $fM['horasMaximas'];

    $fS = mysqli_fetch_assoc(mysqli_query($db, "SELECT SUM(r.horasReto) as total FROM retos r JOIN modulo_reto mr ON r.idReto = mr.idReto WHERE mr.idModulo = $idMod AND r.idReto != $excluir"));
    $ocupadas = isset($fS['total']) ? $fS['total'] : 0;
    mysqli_close($db);
    return (($ocupadas + $horas) <= $max);
}

// Actualizar
function actualizarReto($id, $nom, $fecI, $fecF, $horas, $modulos = []) {
    $db = obtenerConexion();
    $ok = mysqli_query($db, "UPDATE retos SET nombreReto='$nom', fechaInicio='$fecI', fechaFin='$fecF', horasReto=$horas WHERE idReto=$id");
    if ($ok) {
        mysqli_query($db, "DELETE FROM modulo_reto WHERE idReto = $id");
        foreach ($modulos as $m) { mysqli_query($db, "INSERT INTO modulo_reto (idModulo, idReto) VALUES ($m, $id)"); }
    }
    mysqli_close($db);
    return $ok;
}

// Borrar
function eliminarReto($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "DELETE FROM retos WHERE idReto = $id");
    mysqli_close($db);
    return $res;
}

// Sacar por ID
function obtenerRetoPorId($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT * FROM retos WHERE idReto = $id");
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return $fila;
}

// Modulos de un reto
function obtenerModulosDeReto($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT modulos.*, ciclos.nombreCiclo FROM modulos JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo JOIN modulo_reto ON modulos.idModulo = modulo_reto.idModulo WHERE modulo_reto.idReto = $id");
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Calificar
function calificarReto($idEst, $idReto, $nota) {
    $db = obtenerConexion();
    $resC = mysqli_query($db, "SELECT idCalificacion FROM calificaciones_retos WHERE idEstudiante = $idEst AND idReto = $idReto");
    if (mysqli_num_rows($resC) > 0) {
        $sql = "UPDATE calificaciones_retos SET nota = $nota WHERE idEstudiante = $idEst AND idReto = $idReto";
    } else {
        $sql = "INSERT INTO calificaciones_retos (idEstudiante, idReto, nota) VALUES ($idEst, $idReto, $nota)";
    }
    $res = mysqli_query($db, $sql);
    mysqli_close($db);
    return $res;
}

// Ver nota
function obtenerCalificacion($idEst, $idReto) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT nota FROM calificaciones_retos WHERE idEstudiante = $idEst AND idReto = $idReto");
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['nota']) ? $fila['nota'] : "";
}

// Promedio para cada alumno en un modulo
function listarCalificacionesRetoPorModulo($idMod) {
    $db = obtenerConexion();
    $resR = mysqli_query($db, "SELECT idReto FROM modulo_reto WHERE idModulo = $idMod");
    $ids = [];
    while($f = mysqli_fetch_assoc($resR)) { $ids[] = $f['idReto']; }
    if (count($ids) == 0) { mysqli_close($db); return []; }
    $strIds = implode(",", $ids);
    $res = mysqli_query($db, "SELECT idEstudiante, AVG(nota) as prom FROM calificaciones_retos WHERE idReto IN ($strIds) GROUP BY idEstudiante");
    $map = [];
    while($fila = mysqli_fetch_assoc($res)) { $map[$fila['idEstudiante']] = $fila['prom']; }
    mysqli_close($db);
    return $map;
}

// Historial alumno
function listarCalificacionesRetoPorEstudiante($idEst) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT r.nombreReto, cr.nota, r.fechaInicio, r.fechaFin FROM calificaciones_retos cr JOIN retos r ON cr.idReto = r.idReto WHERE cr.idEstudiante = $idEst ORDER BY r.fechaInicio DESC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}
?>