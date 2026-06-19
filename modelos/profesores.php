<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarProfesores() {
    $con = obtenerConexion();
    $sql = "SELECT p.*, c.nombreCiclo AS nombreCicloTutor
            FROM profesores p
            LEFT JOIN ciclos c ON p.idCicloTutor = c.idCiclo
            ORDER BY p.idProfesor ASC";
    $resultado = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarProfesoresTutores() {
    $con = obtenerConexion();
    $sql = "SELECT p.*, c.nombreCiclo AS nombreCicloTutor
            FROM profesores p
            LEFT JOIN ciclos c ON p.idCicloTutor = c.idCiclo
            WHERE p.esTutor = 1
            ORDER BY p.nombreProfesor ASC";
    $resultado = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerProfesorPorId($id) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM profesores WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res);
}

function listarIdsModulosDeProfesor($idProf) {
    $con = obtenerConexion();
    $sql = "SELECT idModulo FROM modulo_profesor WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProf);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila['idModulo'];
    }
    return $lista;
}

function listarProfesoresConModulosParaEstudiante($idEst) {
    $con = obtenerConexion();
    $sql = "SELECT p.idProfesor, p.nombreProfesor, m.nombreModulo
            FROM profesores p
            JOIN modulo_profesor pm ON p.idProfesor = pm.idProfesor
            JOIN modulos m ON pm.idModulo = m.idModulo
            WHERE m.idCiclo = (SELECT idCiclo FROM estudiantes WHERE idEstudiante = ?)
            ORDER BY p.nombreProfesor ASC, m.nombreModulo ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEst);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarCiclosTutorizadosProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT c.*, n.nombreNivel
            FROM ciclos c
            JOIN ciclo_profesor cp ON c.idCiclo = cp.idCiclo
            JOIN niveles n ON c.idNivel = n.idNivel
            WHERE cp.idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerTokensProfesores() {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM profesores WHERE fcm_token IS NOT NULL AND fcm_token != ''";
    $resultado = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila['fcm_token'];
    }
    return $lista;
}

function obtenerTokenFCMProfesor($id) {
    return obtenerTokenFCM('profesores', 'idProfesor', $id);
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function insertarProfesor($nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs) {
    $con = obtenerConexion();
    require_once __DIR__ . '/../include/credenciales.php';
    [$pass] = generarCredencialesTemporales($email, $nombre, 'Profesor');
    $sql = "INSERT INTO profesores (nombreProfesor, emailProfesor, password, telefonoProfesor, dniProfesor, direccionProfesor, fechaNacimientoProfesor, fechaAltaProfesor, ciudadProfesor, codigoPostalProfesor, observacionesProfesor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssssss", $nombre, $email, $pass, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs);
    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($con);
}

function asociarCicloProfesor($idCic, $idProf) {
    $con = obtenerConexion();
    $sql = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idCic, $idProf);
    return mysqli_stmt_execute($stmt);
}

function asociarModuloProfesor($idMod, $idProf) {
    $con = obtenerConexion();
    $sql = "INSERT INTO modulo_profesor (idModulo, idProfesor) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idMod, $idProf);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarProfesor($id, $nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs) {
    $con = obtenerConexion();
    $sql = "UPDATE profesores SET nombreProfesor=?, emailProfesor=?, telefonoProfesor=?, dniProfesor=?, direccionProfesor=?, fechaNacimientoProfesor=?, fechaAltaProfesor=?, ciudadProfesor=?, codigoPostalProfesor=?, observacionesProfesor=? WHERE idProfesor=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssi", $nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs, $id);
    return mysqli_stmt_execute($stmt);
}

function actualizarTutorStatus($idProfesor, $esTutor, $idCicloTutor) {
    $con = obtenerConexion();
    if ($esTutor && $idCicloTutor) {
        $sql = "UPDATE profesores SET esTutor=1, idCicloTutor=? WHERE idProfesor=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $idCicloTutor, $idProfesor);
    } else {
        $sql = "UPDATE profesores SET esTutor=0, idCicloTutor=NULL WHERE idProfesor=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    }
    return mysqli_stmt_execute($stmt);
}

function actualizarPerfilProfesor($id, $nombre, $email, $tel) {
    $con = obtenerConexion();
    $sql = "UPDATE profesores SET nombreProfesor=?, emailProfesor=?, telefonoProfesor=? WHERE idProfesor=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $tel, $id);
    return mysqli_stmt_execute($stmt);
}

function actualizarPasswordProfesor($id, $pass) {
    $con = obtenerConexion();
    $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
    $sql = "UPDATE profesores SET password = ? WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $hash, $id);
    $ok = mysqli_stmt_execute($stmt);
    if ($ok && class_exists('Security')) {
        Security::touchPasswordChanged($con, 'profesores', 'idProfesor', $id);
    }
    return $ok;
}

function actualizarTokenFCMProfesor($id, $token) {
    return actualizarTokenFCM('profesores', 'idProfesor', $id, $token);
}

// ══════════════════════════════════════════════════════════════════════
// ELIMINACIONES
// ══════════════════════════════════════════════════════════════════════

function eliminarProfesor($id) {
    $con = obtenerConexion();
    $sql = "DELETE FROM profesores WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    return mysqli_stmt_execute($stmt);
}

function limpiarModulosProfesor($idProf) {
    $con = obtenerConexion();
    $sql = "DELETE FROM modulo_profesor WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProf);
    return mysqli_stmt_execute($stmt);
}

function limpiarCiclosProfesor($idProf) {
    $con = obtenerConexion();
    $sql = "DELETE FROM ciclo_profesor WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProf);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════

function validarLoginProfesor($email, $pass) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM profesores WHERE emailProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $datos = mysqli_fetch_assoc($res);
    if ($datos && password_verify($pass, $datos['password'])) {
        if (class_exists('Security')) Security::rehashOnLogin($con, 'profesores', 'idProfesor', $datos['idProfesor'], $pass, $datos['password']);
        return $datos;
    }
    return null;
}

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

function checkProfesorExistente($dni, $email, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idProfesor FROM profesores WHERE (dniProfesor = ? OR emailProfesor = ?) AND idProfesor != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $dni, $email, $idExcluir);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($res) > 0;
}
