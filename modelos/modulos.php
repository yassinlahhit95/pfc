<?php
require_once __DIR__ . "/conectar.php";

// Obtener la lista de todos los módulos registrados
function listarModulos() {
    $con = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo 
            FROM modulos 
            LEFT JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo 
            ORDER BY idModulo ASC";
            
    $resultado = mysqli_query($con, $sql);
    $listaModulos = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaModulos[] = $fila; 
    }
    mysqli_close($con);
    return $listaModulos;
}

// Obtener los módulos que imparte un profesor específico
function obtenerModulosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo 
            FROM modulos 
            JOIN profesor_modulo ON modulos.idModulo = profesor_modulo.idModulo 
            JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo 
            WHERE profesor_modulo.idProfesor = $idProfesor";
            
    $resultado = mysqli_query($con, $sql);
    $listaModulos = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaModulos[] = $fila; 
    }
    mysqli_close($con);
    return $listaModulos;
}

// Obtener módulos de un profesor dentro de un ciclo formativo concreto
function obtenerModulosDeProfesorPorCiclo($idProfesor, $idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo 
            FROM modulos 
            JOIN profesor_modulo ON modulos.idModulo = profesor_modulo.idModulo 
            JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo 
            WHERE profesor_modulo.idProfesor = $idProfesor AND modulos.idCiclo = $idCiclo";
            
    $resultado = mysqli_query($con, $sql);
    $listaModulos = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaModulos[] = $fila; 
    }
    mysqli_close($con);
    return $listaModulos;
}

// Obtener todos los módulos pertenecientes a un ciclo formativo
function obtenerModulosPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM modulos WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($con, $sql);
    $listaModulos = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaModulos[] = $fila; 
    }
    mysqli_close($con);
    return $listaModulos;
}

// Insertar un nuevo módulo en la base de datos
function insertarModulo($nombreModulo, $idCiclo, $horasMaximas) {
    $con = obtenerConexion();
    $sql = "INSERT INTO modulos (nombreModulo, idCiclo, horasMaximas) 
            VALUES ('$nombreModulo', $idCiclo, $horasMaximas)";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Actualizar los datos de un módulo existente
function actualizarModulo($idModulo, $nombreModulo, $idCiclo, $horasMaximas) {
    $con = obtenerConexion();
    $sql = "UPDATE modulos 
            SET nombreModulo='$nombreModulo', idCiclo=$idCiclo, horasMaximas=$horasMaximas 
            WHERE idModulo=$idModulo";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Eliminar un módulo por su ID
function eliminarModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "DELETE FROM modulos WHERE idModulo = $idModulo";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Obtener los datos de un módulo específico
function obtenerModuloPorId($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM modulos WHERE idModulo = $idModulo";
    $resultado = mysqli_query($con, $sql);
    $datosModulo = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosModulo;
}

// Obtener los IDs de los profesores que imparten un módulo
function obtenerProfesoresDeModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT idProfesor FROM profesor_modulo WHERE idModulo = $idModulo";
    $resultado = mysqli_query($con, $sql);
    $listaIdsProfesores = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaIdsProfesores[] = $fila['idProfesor']; 
    }
    mysqli_close($con);
    return $listaIdsProfesores;
}

// Eliminar todas las asociaciones de profesores de un módulo
function limpiarProfesoresModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "DELETE FROM profesor_modulo WHERE idModulo = $idModulo";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Calcular la suma de horas de todos los retos asociados a un módulo
function obtenerHorasTotalesRetosModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT SUM(r.horasReto) as total 
            FROM retos r 
            JOIN modulo_reto mr ON r.idReto = mr.idReto 
            WHERE mr.idModulo = $idModulo";
            
    $resultado = mysqli_query($con, $sql);
    $datosSuma = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($datosSuma['total'] ?? 0);
}
?>