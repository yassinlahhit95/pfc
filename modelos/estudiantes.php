<?php
require_once __DIR__ . "/conectar.php";

function listarEstudiantes() {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo
            FROM estudiantes
            LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            ORDER BY estudiantes.idEstudiante ASC";

    $resultado = mysqli_query($con, $sql);
    $listaEstudiantes = [];

    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaEstudiantes[] = $fila;
    }

    mysqli_close($con);
    return $listaEstudiantes;
}

function checkEstudianteExistente($dni, $email, $idExcluir = null) {
    $con = obtenerConexion();
    if ($idExcluir) {
        $sql = "SELECT idEstudiante FROM estudiantes WHERE (dniEstudiante = ? OR emailEstudiante = ?) AND idEstudiante != ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $dni, $email, $idExcluir);
    } else {
        $sql = "SELECT idEstudiante FROM estudiantes WHERE (dniEstudiante = ? OR emailEstudiante = ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $dni, $email);
    }
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    mysqli_close($con);
    return $existe;
}

function insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo) {
    if (checkEstudianteExistente($dni, $email)) {
        return false;
    }
    $con = obtenerConexion();
    $sql = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssi", $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function actualizarEstudiante($idEstudiante, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo) {
    if (checkEstudianteExistente($dni, $email, $idEstudiante)) {
        return false;
    }
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante=?, emailEstudiante=?, telefonoEstudiante=?, fechaNacimientoEstudiante=?, dniEstudiante=?, fechaAltaEstudiante=?, direccionEstudiante=?, ciudadEstudiante=?, codigoPostalEstudiante=?, observacionesEstudiante=?, idCiclo=? WHERE idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssii", $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function listarEstudiantesPorProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?) ORDER BY estudiantes.nombreEstudiante ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaEstudiantes = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaEstudiantes[] = $fila;
    }
    mysqli_close($con);
    return $listaEstudiantes;
}

function listarEstudiantesPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idCiclo = ? ORDER BY estudiantes.idEstudiante ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaEstudiantes = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaEstudiantes[] = $fila;
    }
    mysqli_close($con);
    return $listaEstudiantes;
}

function eliminarEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "DELETE FROM estudiantes WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerEstudiantePorId($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosEstudiante = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosEstudiante;
}

function actualizarPasswordEstudiante($idEstudiante, $nuevaPassword) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET password = ? WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nuevaPassword, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function actualizarPerfilEstudiante($idEstudiante, $nombre, $email, $telefono) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante=?, emailEstudiante=?, telefonoEstudiante=? WHERE idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $telefono, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerTokensEstudiantes() {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM estudiantes WHERE fcm_token IS NOT NULL AND fcm_token != ''";
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

function validarLoginEstudiante($email, $password) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM estudiantes WHERE emailEstudiante = ? AND password = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosUsuario = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosUsuario;
}

function actualizarTokenFCMEstudiante($idEstudiante, $nuevoToken) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET fcm_token = ? WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nuevoToken, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
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
    $token = $fila['fcm_token'] ?? null;
    mysqli_close($con);
    return $token;
}
?>
