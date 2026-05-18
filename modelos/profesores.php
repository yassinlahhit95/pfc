<?php
require_once __DIR__ . "/conectar.php";

function listarProfesores() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM profesores ORDER BY idProfesor ASC";
    $resultado = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($con);
    return $lista;
}

function checkProfesorExistente($dni, $email, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idProfesor FROM profesores WHERE (dniProfesor = ? OR emailProfesor = ?) AND idProfesor != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $dni, $email, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    mysqli_close($con);
    return $existe;
}

function insertarProfesor($nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs) {
    if (checkProfesorExistente($dni, $email)) {
        return false;
    }
    $con = obtenerConexion();
    $sql = "INSERT INTO profesores (nombreProfesor, emailProfesor, telefonoProfesor, dniProfesor, direccionProfesor, fechaNacimientoProfesor, fechaAltaProfesor, ciudadProfesor, codigoPostalProfesor, observacionesProfesor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssss", $nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs);
    mysqli_stmt_execute($stmt);
    $idNuevo = mysqli_insert_id($con);
    mysqli_close($con);
    return $idNuevo;
}

function actualizarProfesor($id, $nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs) {
    if (checkProfesorExistente($dni, $email, $id)) {
        return false;
    }
    $con = obtenerConexion();
    $sql = "UPDATE profesores SET nombreProfesor=?, emailProfesor=?, telefonoProfesor=?, dniProfesor=?, direccionProfesor=?, fechaNacimientoProfesor=?, fechaAltaProfesor=?, ciudadProfesor=?, codigoPostalProfesor=?, observacionesProfesor=? WHERE idProfesor=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssi", $nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs, $id);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function asociarCicloProfesor($idCic, $idProf) {
    $con = obtenerConexion();
    $sql = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idCic, $idProf);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function asociarModuloProfesor($idMod, $idProf) {
    $con = obtenerConexion();
    $sql = "INSERT INTO profesor_modulo (idModulo, idProfesor) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idMod, $idProf);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function eliminarProfesor($id) {
    $con = obtenerConexion();
    $sql = "DELETE FROM profesores WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerProfesorPorId($id) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM profesores WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $fila;
}

function listarProfesoresPorCiclo($idCic) {
    $con = obtenerConexion();
    $sql = "SELECT p.* FROM profesores p JOIN ciclo_profesor cp ON p.idProfesor = cp.idProfesor WHERE cp.idCiclo = ? ORDER BY p.nombreProfesor ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCic);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($con);
    return $lista;
}

function obtenerIdsModulosDeProfesor($idProf) {
    $con = obtenerConexion();
    $sql = "SELECT idModulo FROM profesor_modulo WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProf);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila['idModulo'];
    }
    mysqli_close($con);
    return $lista;
}

function limpiarModulosProfesor($idProf) {
    $con = obtenerConexion();
    $sql = "DELETE FROM profesor_modulo WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProf);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function actualizarPasswordProfesor($id, $pass) {
    $con = obtenerConexion();
    $passwordHasheada = password_hash($pass, PASSWORD_DEFAULT);
    $sql = "UPDATE profesores SET password = ? WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $passwordHasheada, $id);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function actualizarPerfilProfesor($id, $nombre, $email, $tel) {
    $con = obtenerConexion();
    $sql = "UPDATE profesores SET nombreProfesor=?, emailProfesor=?, telefonoProfesor=? WHERE idProfesor=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $tel, $id);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerProfesoresConModulosParaEstudiante($idEst) {
    $con = obtenerConexion();
    $sql = "SELECT p.idProfesor, p.nombreProfesor, m.nombreModulo
            FROM profesores p
            JOIN profesor_modulo pm ON p.idProfesor = pm.idProfesor
            JOIN modulos m ON pm.idModulo = m.idModulo
            WHERE m.idCiclo = (SELECT idCiclo FROM estudiantes WHERE idEstudiante = ?)
            ORDER BY p.nombreProfesor ASC, m.nombreModulo ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEst);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($con);
    return $lista;
}

function obtenerCiclosTutorizadosProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT c.*, n.nombreNivel 
            FROM ciclos c 
            JOIN ciclo_profesor cp ON c.idCiclo = cp.idCiclo 
            JOIN niveles n ON c.idNivel = n.idNivel
            WHERE cp.idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($con);
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
    mysqli_close($con);
    return $lista;
}

function validarLoginProfesor($email, $pass) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM profesores WHERE emailProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datos = mysqli_fetch_assoc($resultado);
    mysqli_close($con);

    if ($datos && password_verify($pass, $datos['password'])) {
        return $datos;
    }
    return null;
}

function actualizarTokenFCMProfesor($id, $token) {
    $con = obtenerConexion();
    $sql = "UPDATE profesores SET fcm_token = ? WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $token, $id);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerTokenFCMProfesor($id) {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM profesores WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    $token = null;
    if ($fila) {
        $token = $fila['fcm_token'];
    }
    mysqli_close($con);
    return $token;
}
?>
