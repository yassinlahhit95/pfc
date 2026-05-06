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

// Comprobar si ya existe un aula con el mismo nombre
function checkAulaExistente($nombreAula, $idExcluir = null) {
    $con = obtenerConexion();
    if ($idExcluir) {
        $stmt = mysqli_prepare($con, "SELECT idAula FROM aulas WHERE nombreAula = ? AND idAula != ?");
        mysqli_stmt_bind_param($stmt, "si", $nombreAula, $idExcluir);
    } else {
        $stmt = mysqli_prepare($con, "SELECT idAula FROM aulas WHERE nombreAula = ?");
        mysqli_stmt_bind_param($stmt, "s", $nombreAula);
    }
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    mysqli_close($con);
    return $existe;
}

// Insertar una nueva aula en el sistema
function insertarAula($nombreAula) {
    if (checkAulaExistente($nombreAula)) {
        return false;
    }
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "INSERT INTO aulas (nombreAula) VALUES (?)");
    mysqli_stmt_bind_param($stmt, "s", $nombreAula);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Eliminar un aula por su ID
function eliminarAula($idAula) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM aulas WHERE idAula = ?");
    mysqli_stmt_bind_param($stmt, "i", $idAula);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Actualizar el nombre de un aula existente
function actualizarAula($idAula, $nuevoNombreAula) {
    if (checkAulaExistente($nuevoNombreAula, $idAula)) {
        return false;
    }
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE aulas SET nombreAula = ? WHERE idAula = ?");
    mysqli_stmt_bind_param($stmt, "si", $nuevoNombreAula, $idAula);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener los datos de un aula específica por su ID
function obtenerAulaPorId($idAula) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM aulas WHERE idAula = ?");
    mysqli_stmt_bind_param($stmt, "i", $idAula);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosAula = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosAula;
}
