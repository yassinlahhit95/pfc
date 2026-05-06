<?php
require_once __DIR__ . "/conectar.php";

// Ver lista de todos los profes
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
        $stmt = mysqli_prepare($con, "SELECT idProfesor FROM profesores WHERE (dniProfesor = ? OR emailProfesor = ?) AND idProfesor != ?");
        mysqli_stmt_bind_param($stmt, "ssi", $dni, $email, $idExcluir);
    } else {
        $stmt = mysqli_prepare($con, "SELECT idProfesor FROM profesores WHERE (dniProfesor = ? OR emailProfesor = ?)");
        mysqli_stmt_bind_param($stmt, "ss", $dni, $email);
    }
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    mysqli_close($con);
    return $existe;
}

// Meter profe nuevo
function insertarProfesor($nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs) {
    if (checkProfesorExistente($dni, $email)) {
        return false;
    }
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "INSERT INTO profesores (nombreProfesor, emailProfesor, telefonoProfesor, dniProfesor, direccionProfesor, fechaNacimientoProfesor, fechaAltaProfesor, ciudadProfesor, codigoPostalProfesor, observacionesProfesor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssssssss", $nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs);
    mysqli_stmt_execute($stmt);

    // Sacamos el ID mas alto (el nuevo)
    $stmt2 = mysqli_prepare($con, "SELECT MAX(idProfesor) as ultimoId FROM profesores");
    mysqli_stmt_execute($stmt2);
    $resultado = mysqli_stmt_get_result($stmt2);
    $filaId = mysqli_fetch_assoc($resultado);
    $idNuevo = $filaId['ultimoId'];

    mysqli_close($con);
    return $idNuevo;
}

// Actualizar un profesor
function actualizarProfesor($id, $nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs) {
    if (checkProfesorExistente($dni, $email, $id)) {
        return false;
    }
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE profesores SET nombreProfesor=?, emailProfesor=?, telefonoProfesor=?, dniProfesor=?, direccionProfesor=?, fechaNacimientoProfesor=?, fechaAltaProfesor=?, ciudadProfesor=?, codigoPostalProfesor=?, observacionesProfesor=? WHERE idProfesor=?");
    mysqli_stmt_bind_param($stmt, "ssssssssssi", $nombre, $email, $tel, $dni, $dir, $f_nac, $f_alta, $ciudad, $cp, $obs, $id);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Unir profe con ciclo
function asociarCicloProfesor($idCic, $idProf) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ii", $idCic, $idProf);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Unir profe con modulo
function asociarModuloProfesor($idMod, $idProf) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "INSERT INTO profesor_modulo (idModulo, idProfesor) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ii", $idMod, $idProf);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Quitar profe
function eliminarProfesor($id) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM profesores WHERE idProfesor = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Datos de un profe por ID
function obtenerProfesorPorId($id) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM profesores WHERE idProfesor = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $fila;
}

// Ver profes que dan clase en un ciclo
function listarProfesoresPorCiclo($idCic) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT p.* FROM profesores p JOIN ciclo_profesor cp ON p.idProfesor = cp.idProfesor WHERE cp.idCiclo = ? ORDER BY p.nombreProfesor ASC");
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

// Ver que modulos tiene este profe
function obtenerIdsModulosDeProfesor($idProf) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT idModulo FROM profesor_modulo WHERE idProfesor = ?");
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

// Borrar todas las clases de un profe
function limpiarModulosProfesor($idProf) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM profesor_modulo WHERE idProfesor = ?");
    mysqli_stmt_bind_param($stmt, "i", $idProf);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Cambiar la clave
function actualizarPasswordProfesor($id, $pass) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE profesores SET password = ? WHERE idProfesor = ?");
    mysqli_stmt_bind_param($stmt, "si", $pass, $id);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Datos basicos de perfil propio
function actualizarPerfilProfesor($id, $nombre, $email, $tel) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE profesores SET nombreProfesor=?, emailProfesor=?, telefonoProfesor=? WHERE idProfesor=?");
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $tel, $id);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener profesores y sus módulos para un estudiante específico (según su ciclo)
function obtenerProfesoresConModulosParaEstudiante($idEst) {
    $con = obtenerConexion();

    // Primero obtenemos el ciclo del estudiante
    $stmt = mysqli_prepare($con, "SELECT idCiclo FROM estudiantes WHERE idEstudiante = ?");
    mysqli_stmt_bind_param($stmt, "i", $idEst);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $filaCiclo = mysqli_fetch_assoc($resultado);
    $idCiclo = $filaCiclo['idCiclo'];

    // Ahora buscamos los profesores que dan clase en los módulos de ese ciclo
    $stmt = mysqli_prepare($con, "SELECT p.idProfesor, p.nombreProfesor, m.nombreModulo FROM profesores p JOIN profesor_modulo pm ON p.idProfesor = pm.idProfesor JOIN modulos m ON pm.idModulo = m.idModulo WHERE m.idCiclo = ? ORDER BY p.nombreProfesor ASC, m.nombreModulo ASC");
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
    $stmt = mysqli_prepare($con, "SELECT * FROM profesores WHERE emailProfesor = ? AND password = ?");
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
    $stmt = mysqli_prepare($con, "UPDATE profesores SET fcm_token = ? WHERE idProfesor = ?");
    mysqli_stmt_bind_param($stmt, "si", $token, $id);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener token FCM de un profesor específico
function obtenerTokenFCMProfesor($id) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT fcm_token FROM profesores WHERE idProfesor = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    $token = $fila['fcm_token'] ?? null;
    mysqli_close($con);
    return $token;
}
