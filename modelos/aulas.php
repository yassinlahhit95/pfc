<?php
require_once __DIR__ . "/conectar.php";

// Obtener la lista de todas las aulas registradas
function listarAulas() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM aulas ORDER BY idAula ASC";
    $resultado = mysqli_query($con, $sql);
    
    $listaAulas = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaAulas[] = $fila;
    }
    
    mysqli_close($con);
    return $listaAulas;
}

// Insertar una nueva aula en el sistema
function insertarAula($nombreAula) {
    $con = obtenerConexion();
    $sql = "INSERT INTO aulas (nombreAula) VALUES ('$nombreAula')";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Eliminar un aula por su ID
function eliminarAula($idAula) {
    $con = obtenerConexion();
    $sql = "DELETE FROM aulas WHERE idAula = $idAula";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Actualizar el nombre de un aula existente
function actualizarAula($idAula, $nuevoNombreAula) {
    $con = obtenerConexion();
    $sql = "UPDATE aulas SET nombreAula = '$nuevoNombreAula' WHERE idAula = $idAula";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Obtener los datos de un aula específica por su ID
function obtenerAulaPorId($idAula) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM aulas WHERE idAula = $idAula";
    $resultado = mysqli_query($con, $sql);
    $datosAula = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosAula;
}
