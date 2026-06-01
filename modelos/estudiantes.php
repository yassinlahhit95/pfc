<?php
require_once __DIR__ . "/conectar.php";

// saca todos los estudiantes con el nombre del ciclo
function listarEstudiantes() {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo, ciclos.idNivel
            FROM estudiantes
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            ORDER BY estudiantes.idEstudiante ASC";

    $res = mysqli_query($con, $sql);
    $rows = [];

    while($fila = mysqli_fetch_assoc($res)) {
        $rows[] = $fila;
    }

    return $rows;
}



function insertarEstudiante($nombre, $email, $tel, $fecha_nac, $dni, $fecha_alta, $dir, $ciudad, $cp, $obs, $idCiclo, $curso = 'Grado Medio') {
    $con = obtenerConexion();
    $pass = password_hash('123456', PASSWORD_DEFAULT);
    $sql = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, password, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo, curso) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssssssis", $nombre, $email, $pass, $tel, $fecha_nac, $dni, $fecha_alta, $dir, $ciudad, $cp, $obs, $idCiclo, $curso);
    $res = mysqli_stmt_execute($stmt);
    return $res;
}

function actualizarEstudiante($id, $nombre, $email, $tel, $fecha_nac, $dni, $fecha_alta, $dir, $ciudad, $cp, $obs, $idCiclo, $curso = 'Grado Medio') {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante=?, emailEstudiante=?, telefonoEstudiante=?, fechaNacimientoEstudiante=?, dniEstudiante=?, fechaAltaEstudiante=?, direccionEstudiante=?, ciudadEstudiante=?, codigoPostalEstudiante=?, observacionesEstudiante=?, idCiclo=?, curso=? WHERE idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssisi", $nombre, $email, $tel, $fecha_nac, $dni, $fecha_alta, $dir, $ciudad, $cp, $obs, $idCiclo, $curso, $id);
    $res = mysqli_stmt_execute($stmt);
    return $res;
}

function listarEstudiantesDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT DISTINCT e.*, c.nombreCiclo, c.abreviaturaCiclo, c.idNivel
            FROM estudiantes e
            JOIN ciclos c ON e.idCiclo = c.idCiclo
            WHERE e.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
               OR e.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?)
            ORDER BY e.nombreEstudiante ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarEstudiantesPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo FROM estudiantes JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idCiclo = ? ORDER BY estudiantes.idEstudiante ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaEstudiantes = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaEstudiantes[] = $fila;
    }
    return $listaEstudiantes;
}

// borrar el estudiante de base de datos
function eliminarEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "DELETE FROM estudiantes WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    return $resultado;
}

function obtenerEstudiantePorId($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo FROM estudiantes JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosEstudiante = mysqli_fetch_assoc($resultado);
    return $datosEstudiante;
}

function actualizarPasswordEstudiante($idEstudiante, $nuevaPassword) {
    $con = obtenerConexion();
    $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
    $sql = "UPDATE estudiantes SET password = ? WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $hash, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    return $resultado;
}

function actualizarPerfilEstudiante($idEstudiante, $nombre, $email, $telefono) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante=?, emailEstudiante=?, telefonoEstudiante=? WHERE idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $telefono, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    return $resultado;
}

function obtenerTokensEstudiantes() {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM estudiantes WHERE fcm_token IS NOT NULL AND fcm_token != ''";
    $resultado = mysqli_query($con, $sql);
    $listaTokens = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaTokens[] = $fila['fcm_token'];
    }
    return $listaTokens;
}

function checkEstudianteExistente($dni, $email, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idEstudiante FROM estudiantes WHERE (dniEstudiante = ? OR emailEstudiante = ?) AND idEstudiante != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $dni, $email, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    return $existe;
}

function validarLoginEstudiante($email, $password) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM estudiantes WHERE emailEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosUsuario = mysqli_fetch_assoc($resultado);

    if ($datosUsuario && password_verify($password, $datosUsuario['password'])) {
        return $datosUsuario;
    }
    return null;
}

function actualizarTokenFCMEstudiante($idEstudiante, $nuevoToken) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET fcm_token = ? WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nuevoToken, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    return $resultado;
}

function obtenerTokenFCMEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM estudiantes WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    $token = null;
    if ($fila) {
        $token = $fila['fcm_token'];
    }
    return $token;
}
