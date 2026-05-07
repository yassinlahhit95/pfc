<?php
require_once __DIR__ . "/conectar.php";

// Obtener la lista de todos los profesores
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

// Comprobar si ya existe un profesor con el mismo DNI o Email
function checkProfesorExistente($dni, $email, $idExcluir = null) {
    $con = obtenerConexion();
    if ($idExcluir) {
        $sql = "SELECT idProfesor FROM profesores WHERE (dniProfesor = ? OR emailProfesor = ?) AND idProfesor != ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $dni, $email, $idExcluir);
    } else {
        $sql = "SELECT idProfesor FROM profesores WHERE (dniProfesor = ? OR emailProfesor = ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $dni, $email);
    }
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    mysqli_close($con);
    return $existe;
}

// Registrar un nuevo profesor en el sistema
function insertarProfesor(string $nombre, string $email, string $tel, string $dni, string $dir, string $f_nac, string $f_alta, string $ciudad, string $cp, string $obs): bool|int {
    if (checkProfesorExistente($dni, $email)) {
        return false;
    }
    $con = obtenerConexion();
    $sql = "INSERT INTO profesores (nombreProfesor, emailProfesor, telefonoProfesor, dniProfesor, direccionProfesor, fechaNacimientoProfesor, fechaAltaProfesor, ciudadProfesor, codigoPostalProfesor, observacionesProfesor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssss", $nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs);
    mysqli_stmt_execute($stmt);

    // Obtenemos el último ID generado (el ID del nuevo profesor)
    $sql = "SELECT MAX(idProfesor) as ultimoId FROM profesores";
    $stmt2 = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt2);
    $resultado = mysqli_stmt_get_result($stmt2);
    $filaId = mysqli_fetch_assoc($resultado);
    $idNuevo = (int)$filaId['ultimoId'];

    mysqli_close($con);
    return $idNuevo;
}

// Actualizar un profesor
function actualizarProfesor(int $id, string $nombre, string $email, string $tel, string $dni, string $dir, string $f_nac, string $f_alta, string $ciudad, string $cp, string $obs): bool {
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

// Asociar un profesor con un ciclo formativo
function asociarCicloProfesor($idCic, $idProf) {
    $con = obtenerConexion();
    $sql = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idCic, $idProf);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Asociar un profesor con un módulo profesional
function asociarModuloProfesor($idMod, $idProf) {
    $con = obtenerConexion();
    $sql = "INSERT INTO profesor_modulo (idModulo, idProfesor) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idMod, $idProf);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Eliminar un profesor del sistema
function eliminarProfesor($id) {
    $con = obtenerConexion();
    $sql = "DELETE FROM profesores WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener los datos de un profesor por su ID
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

// Obtener los profesores que imparten clases en un ciclo formativo
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

// Obtener los IDs de los módulos asignados a un profesor
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

// Eliminar todas las asignaciones de módulos de un profesor
function limpiarModulosProfesor($idProf) {
    $con = obtenerConexion();
    $sql = "DELETE FROM profesor_modulo WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProf);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Cambiar la contraseña de acceso del profesor
function actualizarPasswordProfesor($id, $pass) {
    $con = obtenerConexion();
    $sql = "UPDATE profesores SET password = ? WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $pass, $id);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Actualizar los datos básicos del perfil del profesor
function actualizarPerfilProfesor($id, $nombre, $email, $tel) {
    $con = obtenerConexion();
    $sql = "UPDATE profesores SET nombreProfesor=?, emailProfesor=?, telefonoProfesor=? WHERE idProfesor=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $tel, $id);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener profesores y sus módulos para un estudiante específico (según su ciclo)
function obtenerProfesoresConModulosParaEstudiante($idEst) {
    $con = obtenerConexion();

    // Primero obtenemos el ciclo del estudiante
    $sql = "SELECT idCiclo FROM estudiantes WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEst);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $filaCiclo = mysqli_fetch_assoc($resultado);
    $idCiclo = $filaCiclo['idCiclo'];

    // Ahora buscamos los profesores que dan clase en los módulos de ese ciclo
    $sql = "SELECT p.idProfesor, p.nombreProfesor, m.nombreModulo FROM profesores p JOIN profesor_modulo pm ON p.idProfesor = pm.idProfesor JOIN modulos m ON pm.idModulo = m.idModulo WHERE m.idCiclo = ? ORDER BY p.nombreProfesor ASC, m.nombreModulo ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($con);
    return $lista;
}

// Obtener tokens de todos los profesores para notificaciones push
function obtenerTokensProfesores() {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM profesores WHERE fcm_token IS NOT NULL AND fcm_token != ''";
    $resultado = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        if (!empty($fila['fcm_token'])) {
            $lista[] = $fila['fcm_token'];
        }
    }
    mysqli_close($con);
    return $lista;
}

// Validar login de profesor
function validarLoginProfesor($email, $pass) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM profesores WHERE emailProfesor = ? AND password = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $email, $pass);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datos = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datos;
}

// Actualizar token FCM para notificaciones
function actualizarTokenFCMProfesor($id, $token) {
    $con = obtenerConexion();
    $sql = "UPDATE profesores SET fcm_token = ? WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $token, $id);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener token FCM de un profesor específico
function obtenerTokenFCMProfesor($id) {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM profesores WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    $token = $fila['fcm_token'] ?? null;
    mysqli_close($con);
    return $token;
}
?>
