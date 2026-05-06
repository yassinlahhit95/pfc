<?php
require_once __DIR__ . "/conectar.php";

// Obtener la lista de todos los directores registrados
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

// Comprobar si ya existe un director con el mismo DNI o Email
function checkDirectorExistente($dni, $email, $idExcluir = null) {
    $con = obtenerConexion();
    if ($idExcluir) {
        $stmt = mysqli_prepare($con, "SELECT idDirector FROM directores WHERE (dniDirector = ? OR emailDirector = ?) AND idDirector != ?");
        mysqli_stmt_bind_param($stmt, "ssi", $dni, $email, $idExcluir);
    } else {
        $stmt = mysqli_prepare($con, "SELECT idDirector FROM directores WHERE (dniDirector = ? OR emailDirector = ?)");
        mysqli_stmt_bind_param($stmt, "ss", $dni, $email);
    }
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    mysqli_close($con);
    return $existe;
}

// Registrar un nuevo director en el sistema
function insertarDirector($nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento = '2000-01-01', $direccion = '', $ciudad = '', $codigoPostal = '', $observaciones = '') {
    if (checkDirectorExistente($dni, $email)) {
        return false;
    }
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "INSERT INTO directores (nombreDirector, emailDirector, dniDirector, telefonoDirector, fechaAltaDirector, fechaNacimientoDirector, direccionDirector, ciudadDirector, codigoPostalDirector, observacionesDirector) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssssssss", $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Actualizar los datos completos de un director existente
function actualizarDirector($idDirector, $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento = '2000-01-01', $direccion = '', $ciudad = '', $codigoPostal = '', $observaciones = '') {
    if (checkDirectorExistente($dni, $email, $idDirector)) {
        return false;
    }
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE directores SET nombreDirector=?, emailDirector=?, dniDirector=?, telefonoDirector=?, fechaAltaDirector=?, fechaNacimientoDirector=?, direccionDirector=?, ciudadDirector=?, codigoPostalDirector=?, observacionesDirector=? WHERE idDirector=?");
    mysqli_stmt_bind_param($stmt, "ssssssssssi", $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones, $idDirector);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Eliminar un director por su ID
function eliminarDirector($idDirector) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM directores WHERE idDirector = ?");
    mysqli_stmt_bind_param($stmt, "i", $idDirector);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Actualizar los datos básicos de contacto de un director
function actualizarPerfilDirector($idDirector, $nombre, $email, $telefono) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE directores SET nombreDirector=?, emailDirector=?, telefonoDirector=? WHERE idDirector=?");
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $telefono, $idDirector);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener la información de un director específico por su ID
function obtenerDirectorPorId($idDirector) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM directores WHERE idDirector = ?");
    mysqli_stmt_bind_param($stmt, "i", $idDirector);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosDirector = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosDirector;
}

// Actualizar la contraseña de acceso de un director
function actualizarPasswordDirector($idDirector, $nuevaPassword) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE directores SET password = ? WHERE idDirector = ?");
    mysqli_stmt_bind_param($stmt, "si", $nuevaPassword, $idDirector);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener los tokens FCM de todos los directores para notificaciones push
function obtenerTokensDirectores() {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM directores WHERE fcm_token IS NOT NULL AND fcm_token != ''";
    $resultado = mysqli_query($con, $sql);

    $listaTokens = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        if (!empty($fila['fcm_token'])) {
            $listaTokens[] = $fila['fcm_token'];
        }
    }
    mysqli_close($con);
    return $listaTokens;
}

// Validar las credenciales de acceso de un director (login)
function validarLoginDirector($email, $password) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM directores WHERE emailDirector = ? AND password = ?");
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosUsuario = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosUsuario;
}

// Guardar o actualizar el token FCM de un director
function actualizarTokenFCMDirector($idDirector, $nuevoToken) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE directores SET fcm_token = ? WHERE idDirector = ?");
    mysqli_stmt_bind_param($stmt, "si", $nuevoToken, $idDirector);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener el token FCM actual de un director específico
function obtenerTokenFCMDirector($idDirector) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT fcm_token FROM directores WHERE idDirector = ?");
    mysqli_stmt_bind_param($stmt, "i", $idDirector);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    $token = $fila['fcm_token'] ?? null;
    mysqli_close($con);
    return $token;
}
