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
    $dniEscapado = mysqli_real_escape_string($con, $dni);
    $emailEscapado = mysqli_real_escape_string($con, $email);
    
    $sql = "SELECT idDirector FROM directores WHERE (dniDirector = '$dniEscapado' OR emailDirector = '$emailEscapado')";
    if ($idExcluir) {
        $sql .= " AND idDirector != $idExcluir";
    }
    
    $resultado = mysqli_query($con, $sql);
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
    $sql = "INSERT INTO directores (nombreDirector, emailDirector, dniDirector, telefonoDirector, fechaAltaDirector, fechaNacimientoDirector, direccionDirector, ciudadDirector, codigoPostalDirector, observacionesDirector) 
            VALUES ('$nombre', '$email', '$dni', '$telefono', '$fechaAlta', '$fechaNacimiento', '$direccion', '$ciudad', '$codigoPostal', '$observaciones')";
            
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Actualizar los datos completos de un director existente
function actualizarDirector($idDirector, $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento = '2000-01-01', $direccion = '', $ciudad = '', $codigoPostal = '', $observaciones = '') {
    if (checkDirectorExistente($dni, $email, $idDirector)) {
        return false;
    }
    $con = obtenerConexion();
    $sql = "UPDATE directores 
            SET nombreDirector='$nombre', emailDirector='$email', dniDirector='$dni', 
                telefonoDirector='$telefono', fechaAltaDirector='$fechaAlta', 
                fechaNacimientoDirector='$fechaNacimiento', direccionDirector='$direccion', 
                ciudadDirector='$ciudad', codigoPostalDirector='$codigoPostal', 
                observacionesDirector='$observaciones' 
            WHERE idDirector=$idDirector";
            
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Eliminar un director por su ID
function eliminarDirector($idDirector) {
    $con = obtenerConexion();
    $sql = "DELETE FROM directores WHERE idDirector = $idDirector";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Actualizar los datos básicos de contacto de un director
function actualizarPerfilDirector($idDirector, $nombre, $email, $telefono) {
    $con = obtenerConexion();
    $sql = "UPDATE directores 
            SET nombreDirector='$nombre', emailDirector='$email', telefonoDirector='$telefono' 
            WHERE idDirector=$idDirector";
            
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Obtener la información de un director específico por su ID
function obtenerDirectorPorId($idDirector) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM directores WHERE idDirector = $idDirector";
    $resultado = mysqli_query($con, $sql);
    $datosDirector = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosDirector;
}

// Actualizar la contraseña de acceso de un director
function actualizarPasswordDirector($idDirector, $nuevaPassword) {
    $con = obtenerConexion();
    $sql = "UPDATE directores SET password = '$nuevaPassword' WHERE idDirector = $idDirector";
    $resultado = mysqli_query($con, $sql);
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
    $emailEscapado = mysqli_real_escape_string($con, $email);
    $passEscapada = mysqli_real_escape_string($con, $password);
    $sql = "SELECT * FROM directores 
            WHERE emailDirector = '$emailEscapado' AND password = '$passEscapada'";
            
    $resultado = mysqli_query($con, $sql);
    $datosUsuario = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosUsuario;
}

// Guardar o actualizar el token FCM de un director
function actualizarTokenFCMDirector($idDirector, $nuevoToken) {
    $con = obtenerConexion();
    $sql = "UPDATE directores SET fcm_token = '$nuevoToken' WHERE idDirector = $idDirector";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Obtener el token FCM actual de un director específico
function obtenerTokenFCMDirector($idDirector) {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM directores WHERE idDirector = $idDirector";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    $token = $fila['fcm_token'] ?? null;
    mysqli_close($con);
    return $token;
}
