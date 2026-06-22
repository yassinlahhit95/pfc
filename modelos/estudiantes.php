<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarEstudiantes() {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo, ciclos.idNivel
            FROM estudiantes
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            ORDER BY estudiantes.idEstudiante ASC";
    $res = mysqli_query($con, $sql);
    $rows = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $rows[] = $fila;
    }
    return $rows;
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
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo
            FROM estudiantes
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            WHERE estudiantes.idCiclo = ?
            ORDER BY estudiantes.idEstudiante ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerEstudiantePorId($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo
            FROM estudiantes
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            WHERE estudiantes.idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($resultado);
}

function obtenerTokensEstudiantes() {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM estudiantes WHERE fcm_token IS NOT NULL AND fcm_token != ''";
    $resultado = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila['fcm_token'];
    }
    return $lista;
}

function obtenerTokenFCMEstudiante($idEstudiante) {
    return obtenerTokenFCM('estudiantes', 'idEstudiante', $idEstudiante);
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function insertarEstudiante($nombre, $email, $tel, $fecha_nac, $dni, $fecha_alta, $dir, $ciudad, $cp, $obs, $idCiclo, $curso = 'Grado Medio', $anioEstudio = null) {
    $con = obtenerConexion();
    require_once __DIR__ . '/../include/credenciales.php';
    [$pass] = generarCredencialesTemporales($email, $nombre, 'Estudiante');
    $sql = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, password, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo, curso, anioEstudio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssssssiss", $nombre, $email, $pass, $tel, $fecha_nac, $dni, $fecha_alta, $dir, $ciudad, $cp, $obs, $idCiclo, $curso, $anioEstudio);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarEstudiante($id, $nombre, $email, $tel, $fecha_nac, $dni, $fecha_alta, $dir, $ciudad, $cp, $obs, $idCiclo, $curso = 'Grado Medio', $anioEstudio = null) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante=?, emailEstudiante=?, telefonoEstudiante=?, fechaNacimientoEstudiante=?, dniEstudiante=?, fechaAltaEstudiante=?, direccionEstudiante=?, ciudadEstudiante=?, codigoPostalEstudiante=?, observacionesEstudiante=?, idCiclo=?, curso=?, anioEstudio=? WHERE idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssissi", $nombre, $email, $tel, $fecha_nac, $dni, $fecha_alta, $dir, $ciudad, $cp, $obs, $idCiclo, $curso, $anioEstudio, $id);
    return mysqli_stmt_execute($stmt);
}

function actualizarPerfilEstudiante($idEstudiante, $nombre, $email, $telefono) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante=?, emailEstudiante=?, telefonoEstudiante=? WHERE idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $telefono, $idEstudiante);
    return mysqli_stmt_execute($stmt);
}

function actualizarPasswordEstudiante($idEstudiante, $nuevaPassword) {
    $con = obtenerConexion();
    $hash = password_hash($nuevaPassword, PASSWORD_BCRYPT, ['cost' => 12]);
    $sql = "UPDATE estudiantes SET password = ? WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $hash, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    if ($resultado && class_exists('Security')) {
        Security::touchPasswordChanged($con, 'estudiantes', 'idEstudiante', $idEstudiante);
    }
    return $resultado;
}

function actualizarTokenFCMEstudiante($idEstudiante, $nuevoToken) {
    return actualizarTokenFCM('estudiantes', 'idEstudiante', $idEstudiante, $nuevoToken);
}

// ══════════════════════════════════════════════════════════════════════
// ELIMINACIONES
// ══════════════════════════════════════════════════════════════════════

function eliminarEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "DELETE FROM estudiantes WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════

function validarLoginEstudiante($email, $password) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM estudiantes WHERE emailEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datos = mysqli_fetch_assoc($resultado);
    if ($datos && password_verify($password, $datos['password'])) {
        if (class_exists('Security')) Security::rehashOnLogin($con, 'estudiantes', 'idEstudiante', $datos['idEstudiante'], $password, $datos['password']);
        return $datos;
    }
    return null;
}

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

function estudiantePerteneceACiclo($idEstudiante, $idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT 1 FROM estudiantes WHERE idEstudiante = ? AND idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($res) > 0;
}

function estudiantePerteneceAProfesor($idEstudiante, $idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT 1 FROM estudiantes e
            WHERE e.idEstudiante = ?
            AND (e.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
              OR e.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?))";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $idEstudiante, $idProfesor, $idProfesor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($res) > 0;
}

function checkEstudianteExistente($dni, $email, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idEstudiante FROM estudiantes WHERE (dniEstudiante = ? OR emailEstudiante = ?) AND idEstudiante != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $dni, $email, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($resultado) > 0;
}
