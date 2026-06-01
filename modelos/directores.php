<?php
require_once __DIR__ . "/conectar.php";

// Listado rápido de todos los directores para las tablas del admin
function listarDirectores() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM directores ORDER BY idDirector ASC";
    $res = mysqli_query($con, $sql);
    $todos = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $todos[] = $fila;
    }
    mysqli_close($con);
    return $todos;
}

// Función para no duplicar DNIs o Emails. 
// El $idExcluir es para cuando editamos, que no salte error con el propio usuario.
function checkDirectorExistente($dni, $email, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idDirector FROM directores WHERE (dniDirector = ? OR emailDirector = ?) AND idDirector != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $dni, $email, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    mysqli_close($con);
    return $existe;
}

// Registro de nuevos directores. 
// OJO: La fecha de nacimiento por defecto es 2000-01-01 si no viene nada.
function insertarDirector($nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento = '2000-01-01', $direccion = '', $ciudad = '', $codigoPostal = '', $observaciones = '') {
    $con = obtenerConexion();
    $pass = password_hash('123456', PASSWORD_DEFAULT);
    $sql = "INSERT INTO directores (nombreDirector, emailDirector, password, dniDirector, telefonoDirector, fechaAltaDirector, fechaNacimientoDirector, direccionDirector, ciudadDirector, codigoPostalDirector, observacionesDirector) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssssss", $nombre, $email, $pass, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function actualizarDirector($idDirector, $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento = '2000-01-01', $direccion = '', $ciudad = '', $codigoPostal = '', $observaciones = '') {
    $con = obtenerConexion();
    $sql = "UPDATE directores SET nombreDirector=?, emailDirector=?, dniDirector=?, telefonoDirector=?, fechaAltaDirector=?, fechaNacimientoDirector=?, direccionDirector=?, ciudadDirector=?, codigoPostalDirector=?, observacionesDirector=? WHERE idDirector=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssi", $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones, $idDirector);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function eliminarDirector($idDirector) {
    $con = obtenerConexion();
    $sql = "DELETE FROM directores WHERE idDirector = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idDirector);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerDirectorPorId($idDirector) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM directores WHERE idDirector = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idDirector);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosDirector = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosDirector;
}

function obtenerTokensDirectores() {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM directores WHERE fcm_token IS NOT NULL AND fcm_token != ''";
    $resultado = mysqli_query($con, $sql);

    $listaTokens = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaTokens[] = $fila['fcm_token'];
    }
    mysqli_close($con);
    return $listaTokens;
}

function validarLoginDirector($email, $password) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM directores WHERE emailDirector = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosUsuario = mysqli_fetch_assoc($resultado);
    mysqli_close($con);

    if ($datosUsuario && password_verify($password, $datosUsuario['password'])) {
        return $datosUsuario;
    }
    return null;
}

function actualizarTokenFCMDirector($idDirector, $nuevoToken) {
    $con = obtenerConexion();
    $sql = "UPDATE directores SET fcm_token = ? WHERE idDirector = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nuevoToken, $idDirector);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerTokenFCMDirector($idDirector) {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM directores WHERE idDirector = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idDirector);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    $token = null;
    if ($fila && $fila['fcm_token']) {
        $token = $fila['fcm_token'];
    }
    mysqli_close($con);
    return $token;
}
