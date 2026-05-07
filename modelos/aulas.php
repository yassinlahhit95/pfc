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
        $sql = "SELECT idAula FROM aulas WHERE nombreAula = ? AND idAula != ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $nombreAula, $idExcluir);
    } else {
        $sql = "SELECT idAula FROM aulas WHERE nombreAula = ?";
        $stmt = mysqli_prepare($con, $sql);
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
    $sql = "INSERT INTO aulas (nombreAula) VALUES (?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $nombreAula);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Eliminar un aula por su ID
function eliminarAula($idAula) {
    $con = obtenerConexion();
    $sql = "DELETE FROM aulas WHERE idAula = ?";
    $stmt = mysqli_prepare($con, $sql);
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
    $sql = "UPDATE aulas SET nombreAula = ? WHERE idAula = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nuevoNombreAula, $idAula);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener los datos de un aula específica por su ID
function obtenerAulaPorId($idAula) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM aulas WHERE idAula = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idAula);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosAula = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosAula;
}
?>
