<?php
require_once __DIR__ . "/conectar.php";
require_once __DIR__ . "/../include/Crypto.php";

// ── Cifrado de datos personales (RGPD Art. 32) ──────────────────────────
// dniDirector usa cifrado determinista (mismo texto → mismo cifrado) para
// que UNIQUE KEY y checkDirectorExistente sigan funcionando. El resto usa
// cifrado aleatorio.
function _descifrarFilaDirector(?array $fila): ?array {
    if (!$fila) return $fila;
    foreach (['dniDirector', 'telefonoDirector', 'fechaNacimientoDirector', 'direccionDirector', 'observacionesDirector'] as $c) {
        if (array_key_exists($c, $fila)) $fila[$c] = Crypto::decrypt($fila[$c]);
    }
    return $fila;
}

// Público (con "descifrar" en vez de "_") para que modelos/rgpd.php pueda reutilizarlo.
function descifrarFilaDirector(?array $fila): ?array {
    return _descifrarFilaDirector($fila);
}

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarDirectores() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM directores ORDER BY idDirector ASC";
    $res = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = _descifrarFilaDirector($fila);
    }
    return $lista;
}

function obtenerDirectorPorId($idDirector) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM directores WHERE idDirector = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idDirector);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return _descifrarFilaDirector(mysqli_fetch_assoc($resultado));
}

function obtenerTokensDirectores() {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM directores WHERE fcm_token IS NOT NULL AND fcm_token != ''";
    $resultado = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila['fcm_token'];
    }
    return $lista;
}

function obtenerTokenFCMDirector($idDirector) {
    return obtenerTokenFCM('directores', 'idDirector', $idDirector);
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function insertarDirector($nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento = '2000-01-01', $direccion = '', $ciudad = '', $codigoPostal = '', $observaciones = '') {
    $con = obtenerConexion();
    require_once __DIR__ . '/../include/credenciales.php';
    [$pass] = generarCredencialesTemporales($email, $nombre, 'Director');
    $dniC             = Crypto::encryptDeterministic($dni);
    $telefonoC        = Crypto::encrypt($telefono);
    $fechaNacimientoC = Crypto::encrypt($fechaNacimiento);
    $direccionC       = Crypto::encrypt($direccion);
    $observacionesC   = Crypto::encrypt($observaciones);
    $sql = "INSERT INTO directores (nombreDirector, emailDirector, password, dniDirector, telefonoDirector, fechaAltaDirector, fechaNacimientoDirector, direccionDirector, ciudadDirector, codigoPostalDirector, observacionesDirector) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssssss", $nombre, $email, $pass, $dniC, $telefonoC, $fechaAlta, $fechaNacimientoC, $direccionC, $ciudad, $codigoPostal, $observacionesC);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarDirector($idDirector, $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento = '2000-01-01', $direccion = '', $ciudad = '', $codigoPostal = '', $observaciones = '') {
    $con = obtenerConexion();
    $dniC             = Crypto::encryptDeterministic($dni);
    $telefonoC        = Crypto::encrypt($telefono);
    $fechaNacimientoC = Crypto::encrypt($fechaNacimiento);
    $direccionC       = Crypto::encrypt($direccion);
    $observacionesC   = Crypto::encrypt($observaciones);
    $sql = "UPDATE directores SET nombreDirector=?, emailDirector=?, dniDirector=?, telefonoDirector=?, fechaAltaDirector=?, fechaNacimientoDirector=?, direccionDirector=?, ciudadDirector=?, codigoPostalDirector=?, observacionesDirector=? WHERE idDirector=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssi", $nombre, $email, $dniC, $telefonoC, $fechaAlta, $fechaNacimientoC, $direccionC, $ciudad, $codigoPostal, $observacionesC, $idDirector);
    return mysqli_stmt_execute($stmt);
}


function actualizarTokenFCMDirector($idDirector, $nuevoToken) {
    return actualizarTokenFCM('directores', 'idDirector', $idDirector, $nuevoToken);
}

// ══════════════════════════════════════════════════════════════════════
// ELIMINACIONES
// ══════════════════════════════════════════════════════════════════════

function eliminarDirector($idDirector) {
    $con = obtenerConexion();
    $sql = "DELETE FROM directores WHERE idDirector = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idDirector);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════

function validarLoginDirector($email, $password) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM directores WHERE emailDirector = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datos = mysqli_fetch_assoc($resultado);
    if ($datos && password_verify($password, $datos['password'])) {
        if (class_exists('Security')) Security::rehashOnLogin($con, 'directores', 'idDirector', $datos['idDirector'], $password, $datos['password']);
        return _descifrarFilaDirector($datos);
    }
    return null;
}

// MFA / doble factor: generalizado a los 5 roles en include/MfaService.php.

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

// $idExcluir evita falso positivo de duplicado al editar el propio registro
function checkDirectorExistente($dni, $email, $idExcluir = 0) {
    $con = obtenerConexion();
    $dniC = Crypto::encryptDeterministic($dni);
    $sql = "SELECT idDirector FROM directores WHERE (dniDirector = ? OR emailDirector = ?) AND idDirector != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $dniC, $email, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($resultado) > 0;
}
