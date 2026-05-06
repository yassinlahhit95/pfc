<?php
require_once __DIR__ . "/conectar.php";

// Obtener la lista de todos los estudiantes registrados
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

// Comprobar si ya existe un estudiante con el mismo DNI o Email
function checkEstudianteExistente($dni, $email, $idExcluir = null) {
    $con = obtenerConexion();
    if ($idExcluir) {
        $stmt = mysqli_prepare($con, "SELECT idEstudiante FROM estudiantes WHERE (dniEstudiante = ? OR emailEstudiante = ?) AND idEstudiante != ?");
        mysqli_stmt_bind_param($stmt, "ssi", $dni, $email, $idExcluir);
    } else {
        $stmt = mysqli_prepare($con, "SELECT idEstudiante FROM estudiantes WHERE (dniEstudiante = ? OR emailEstudiante = ?)");
        mysqli_stmt_bind_param($stmt, "ss", $dni, $email);
    }
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    mysqli_close($con);
    return $existe;
}

// Registrar un nuevo estudiante en el sistema
function insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo) {
    if (checkEstudianteExistente($dni, $email)) {
        return false;
    }
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssssssssi", $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Actualizar todos los datos de un estudiante existente
function actualizarEstudiante($idEstudiante, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo) {
    if (checkEstudianteExistente($dni, $email, $idEstudiante)) {
        return false;
    }
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE estudiantes SET nombreEstudiante=?, emailEstudiante=?, telefonoEstudiante=?, fechaNacimientoEstudiante=?, dniEstudiante=?, fechaAltaEstudiante=?, direccionEstudiante=?, ciudadEstudiante=?, codigoPostalEstudiante=?, observacionesEstudiante=?, idCiclo=? WHERE idEstudiante=?");
    mysqli_stmt_bind_param($stmt, "ssssssssssii", $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener los estudiantes vinculados a los ciclos que imparte un profesor
function listarEstudiantesPorProfesor($idProfesor) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?) ORDER BY estudiantes.nombreEstudiante ASC");
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

// Listar todos los estudiantes matriculados en un ciclo específico
function listarEstudiantesPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idCiclo = ? ORDER BY estudiantes.idEstudiante ASC");
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

// Eliminar un estudiante por su ID
function eliminarEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM estudiantes WHERE idEstudiante = ?");
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener la información completa de un estudiante por su ID
function obtenerEstudiantePorId($idEstudiante) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idEstudiante = ?");
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosEstudiante = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosEstudiante;
}

// Actualizar la contraseña de un estudiante
function actualizarPasswordEstudiante($idEstudiante, $nuevaPassword) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE estudiantes SET password = ? WHERE idEstudiante = ?");
    mysqli_stmt_bind_param($stmt, "si", $nuevaPassword, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Actualizar los datos básicos del perfil de un estudiante
function actualizarPerfilEstudiante($idEstudiante, $nombre, $email, $telefono) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE estudiantes SET nombreEstudiante=?, emailEstudiante=?, telefonoEstudiante=? WHERE idEstudiante=?");
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $telefono, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener los tokens FCM de todos los estudiantes para notificaciones masivas
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

// Validar las credenciales de acceso de un estudiante
function validarLoginEstudiante($email, $password) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM estudiantes WHERE emailEstudiante = ? AND password = ?");
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosUsuario = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosUsuario;
}

// Guardar o actualizar el token FCM de un estudiante
function actualizarTokenFCMEstudiante($idEstudiante, $nuevoToken) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE estudiantes SET fcm_token = ? WHERE idEstudiante = ?");
    mysqli_stmt_bind_param($stmt, "si", $nuevoToken, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener el token FCM actual de un estudiante específico
function obtenerTokenFCMEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT fcm_token FROM estudiantes WHERE idEstudiante = ?");
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    $token = $fila['fcm_token'] ?? null;
    mysqli_close($con);
    return $token;
}
