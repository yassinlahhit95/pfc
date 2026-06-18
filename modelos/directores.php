<?php
require_once __DIR__ . "/conectar.php";

function listarDirectores() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM directores ORDER BY idDirector ASC";
    $res = mysqli_query($con, $sql);
    $todos = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $todos[] = $fila;
    }
    
    return $todos;
}

// $idExcluir permite excluir el propio registro al editar (evita falso positivo de duplicado)
function checkDirectorExistente($dni, $email, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idDirector FROM directores WHERE (dniDirector = ? OR emailDirector = ?) AND idDirector != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $dni, $email, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    
    return $existe;
}

function insertarDirector($nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento = '2000-01-01', $direccion = '', $ciudad = '', $codigoPostal = '', $observaciones = '') {
    $con = obtenerConexion();
    require_once __DIR__ . '/../include/credenciales.php';
    [$pass] = generarCredencialesTemporales($email, $nombre, 'Director');
    $sql = "INSERT INTO directores (nombreDirector, emailDirector, password, dniDirector, telefonoDirector, fechaAltaDirector, fechaNacimientoDirector, direccionDirector, ciudadDirector, codigoPostalDirector, observacionesDirector) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssssss", $nombre, $email, $pass, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones);
    $resultado = mysqli_stmt_execute($stmt);
    
    return $resultado;
}

function actualizarDirector($idDirector, $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento = '2000-01-01', $direccion = '', $ciudad = '', $codigoPostal = '', $observaciones = '') {
    $con = obtenerConexion();
    $sql = "UPDATE directores SET nombreDirector=?, emailDirector=?, dniDirector=?, telefonoDirector=?, fechaAltaDirector=?, fechaNacimientoDirector=?, direccionDirector=?, ciudadDirector=?, codigoPostalDirector=?, observacionesDirector=? WHERE idDirector=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssi", $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones, $idDirector);
    $resultado = mysqli_stmt_execute($stmt);
    
    return $resultado;
}

function eliminarDirector($idDirector) {
    $con = obtenerConexion();
    $sql = "DELETE FROM directores WHERE idDirector = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idDirector);
    $resultado = mysqli_stmt_execute($stmt);
    
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
    

    if ($datosUsuario && password_verify($password, $datosUsuario['password'])) {
        if (class_exists('Security')) Security::rehashOnLogin($con, 'directores', 'idDirector', $datosUsuario['idDirector'], $password, $datosUsuario['password']);
        return $datosUsuario;
    }
    return null;
}

function actualizarTokenFCMDirector($idDirector, $nuevoToken) {
    return actualizarTokenFCM('directores', 'idDirector', $idDirector, $nuevoToken);
}

function obtenerTokenFCMDirector($idDirector) {
    return obtenerTokenFCM('directores', 'idDirector', $idDirector);
}

// ── MFA / 2FA (TOTP) ────────────────────────────────────────────────────────
function obtenerMfaDirector($idDirector) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT mfa_enabled, mfa_secret, mfa_backup_codes FROM directores WHERE idDirector = ?");
    mysqli_stmt_bind_param($stmt, "i", $idDirector);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
}

function activarMfaDirector($idDirector, $secret, $backupCodesJson) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE directores SET mfa_enabled = 1, mfa_secret = ?, mfa_backup_codes = ? WHERE idDirector = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $secret, $backupCodesJson, $idDirector);
    return mysqli_stmt_execute($stmt);
}

function actualizarBackupCodesDirector($idDirector, $backupCodesJson) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE directores SET mfa_backup_codes = ? WHERE idDirector = ?");
    mysqli_stmt_bind_param($stmt, "si", $backupCodesJson, $idDirector);
    return mysqli_stmt_execute($stmt);
}
