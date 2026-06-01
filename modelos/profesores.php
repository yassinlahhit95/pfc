<?php
require_once __DIR__ . "/conectar.php";

function listarProfesores() {
    $con = obtenerConexion();
    $sql1 = "SELECT * FROM profesores ORDER BY idProfesor ASC";
    $resultado = mysqli_query($con, $sql1);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function checkProfesorExistente($dni, $email, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql1 = "SELECT idProfesor FROM profesores WHERE (dniProfesor = ? OR emailProfesor = ?) AND idProfesor != ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "ssi", $dni, $email, $idExcluir);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $existe = mysqli_num_rows($res) > 0;
    return $existe;
}

function insertarProfesor($nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs) {
    $con = obtenerConexion();
    $pass = password_hash('123456', PASSWORD_DEFAULT);
    $sql1 = "INSERT INTO profesores (nombreProfesor, emailProfesor, password, telefonoProfesor, dniProfesor, direccionProfesor, fechaNacimientoProfesor, fechaAltaProfesor, ciudadProfesor, codigoPostalProfesor, observacionesProfesor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "sssssssssss", $nombre, $email, $pass, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs);
    mysqli_stmt_execute($resultado);
    $idNuevo = mysqli_insert_id($con);
    return $idNuevo;
}

function actualizarProfesor($id, $nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs) {
    $con = obtenerConexion();
    $sql1 = "UPDATE profesores SET nombreProfesor=?, emailProfesor=?, telefonoProfesor=?, dniProfesor=?, direccionProfesor=?, fechaNacimientoProfesor=?, fechaAltaProfesor=?, ciudadProfesor=?, codigoPostalProfesor=?, observacionesProfesor=? WHERE idProfesor=?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "ssssssssssi", $nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs, $id);
    $ok = mysqli_stmt_execute($resultado);
    return $ok;
}

function asociarCicloProfesor($idCic, $idProf) {
    $con = obtenerConexion();
    $sql1 = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES (?, ?)";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "ii", $idCic, $idProf);
    $ok = mysqli_stmt_execute($resultado);
    return $ok;
}

function asociarModuloProfesor($idMod, $idProf) {
    $con = obtenerConexion();
    $sql1 = "INSERT INTO modulo_profesor (idModulo, idProfesor) VALUES (?, ?)";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "ii", $idMod, $idProf);
    $ok = mysqli_stmt_execute($resultado);
    return $ok;
}

function eliminarProfesor($id) {
    $con = obtenerConexion();
    $sql1 = "DELETE FROM profesores WHERE idProfesor = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $id);
    $ok = mysqli_stmt_execute($resultado);
    return $ok;
}

function obtenerProfesorPorId($id) {
    $con = obtenerConexion();
    $sql1 = "SELECT * FROM profesores WHERE idProfesor = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $id);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $fila = mysqli_fetch_assoc($res);
    return $fila;
}


function listarIdsModulosDeProfesor($idProf) {
    $con = obtenerConexion();
    $sql1 = "SELECT idModulo FROM modulo_profesor WHERE idProfesor = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idProf);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila['idModulo'];
    }
    return $lista;
}

function limpiarModulosProfesor($idProf) {
    $con = obtenerConexion();
    $sql1 = "DELETE FROM modulo_profesor WHERE idProfesor = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idProf);
    $ok = mysqli_stmt_execute($resultado);
    return $ok;
}

function limpiarCiclosProfesor($idProf) {
    $con = obtenerConexion();
    $sql1 = "DELETE FROM ciclo_profesor WHERE idProfesor = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idProf);
    $ok = mysqli_stmt_execute($resultado);
    return $ok;
}

function actualizarPasswordProfesor($id, $pass) {
    $con = obtenerConexion();
    $passwordHasheada = password_hash($pass, PASSWORD_DEFAULT);
    $sql1 = "UPDATE profesores SET password = ? WHERE idProfesor = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "si", $passwordHasheada, $id);
    $ok = mysqli_stmt_execute($resultado);
    return $ok;
}

function actualizarPerfilProfesor($id, $nombre, $email, $tel) {
    $con = obtenerConexion();
    $sql1 = "UPDATE profesores SET nombreProfesor=?, emailProfesor=?, telefonoProfesor=? WHERE idProfesor=?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "sssi", $nombre, $email, $tel, $id);
    $ok = mysqli_stmt_execute($resultado);
    return $ok;
}

function listarProfesoresConModulosParaEstudiante($idEst) {
    $con = obtenerConexion();
    $sql1 = "SELECT p.idProfesor, p.nombreProfesor, m.nombreModulo
            FROM profesores p
            JOIN modulo_profesor pm ON p.idProfesor = pm.idProfesor
            JOIN modulos m ON pm.idModulo = m.idModulo
            WHERE m.idCiclo = (SELECT idCiclo FROM estudiantes WHERE idEstudiante = ?)
            ORDER BY p.nombreProfesor ASC, m.nombreModulo ASC";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idEst);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarCiclosTutorizadosProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql1 = "SELECT c.*, n.nombreNivel
            FROM ciclos c
            JOIN ciclo_profesor cp ON c.idCiclo = cp.idCiclo
            JOIN niveles n ON c.idNivel = n.idNivel
            WHERE cp.idProfesor = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idProfesor);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerTokensProfesores() {
    $con = obtenerConexion();
    $sql1 = "SELECT fcm_token FROM profesores WHERE fcm_token IS NOT NULL AND fcm_token != ''";
    $resultado = mysqli_query($con, $sql1);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila['fcm_token'];
    }
    return $lista;
}

function validarLoginProfesor($email, $pass) {
    $con = obtenerConexion();
    $sql1 = "SELECT * FROM profesores WHERE emailProfesor = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "s", $email);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $datos = mysqli_fetch_assoc($res);

    if ($datos && password_verify($pass, $datos['password'])) {
        return $datos;
    }
    return null;
}

function actualizarTokenFCMProfesor($id, $token) {
    $con = obtenerConexion();
    $sql1 = "UPDATE profesores SET fcm_token = ? WHERE idProfesor = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "si", $token, $id);
    $ok = mysqli_stmt_execute($resultado);
    return $ok;
}

function obtenerTokenFCMProfesor($id) {
    $con = obtenerConexion();
    $sql1 = "SELECT fcm_token FROM profesores WHERE idProfesor = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $id);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $fila = mysqli_fetch_assoc($res);
    $token = null;
    if ($fila) {
        $token = $fila['fcm_token'];
    }
    return $token;
}
