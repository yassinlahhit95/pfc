<?php
require_once __DIR__ . "/conectar.php";

function listarDirectores() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM directores ORDER BY idDirector ASC";
    $resultado = mysqli_query($con, $sql);

    $listaDirectores = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaDirectores[] = $fila;
    }
    mysqli_close($con);
    return $listaDirectores;
}

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

function insertarDirector($nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento = '2000-01-01', $direccion = '', $ciudad = '', $codigoPostal = '', $observaciones = '') {
    if (checkDirectorExistente($dni, $email)) {
        return false;
    }
    $con = obtenerConexion();
    $sql = "INSERT INTO directores (nombreDirector, emailDirector, dniDirector, telefonoDirector, fechaAltaDirector, fechaNacimientoDirector, direccionDirector, ciudadDirector, codigoPostalDirector, observacionesDirector) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssss", $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function actualizarDirector($idDirector, $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento = '2000-01-01', $direccion = '', $ciudad = '', $codigoPostal = '', $observaciones = '') {
    if (checkDirectorExistente($dni, $email, $idDirector)) {
        return false;
    }
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

function actualizarPerfilDirector($idDirector, $nombre, $email, $telefono) {
    $con = obtenerConexion();
    $sql = "UPDATE directores SET nombreDirector=?, emailDirector=?, telefonoDirector=? WHERE idDirector=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $telefono, $idDirector);
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

function actualizarPasswordDirector($idDirector, $nuevaPassword) {
    $con = obtenerConexion();
    $sql = "UPDATE directores SET password = ? WHERE idDirector = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nuevaPassword, $idDirector);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerTokensDirectores() {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM directores WHERE fcm_token IS NOT NULL AND fcm_token != ''";
    $resultado = mysqli_query($con, $sql);

    $listaTokens = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        if ($fila['fcm_token'] != null) {
            $listaTokens[] = $fila['fcm_token'];
        }
    }
    mysqli_close($con);
    return $listaTokens;
}

function validarLoginDirector($email, $password) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM directores WHERE emailDirector = ? AND password = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosUsuario = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosUsuario;
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
    if ($fila != null && $fila['fcm_token'] != null) {
        $token = $fila['fcm_token'];
    }
    mysqli_close($con);
    return $token;
}
?>
