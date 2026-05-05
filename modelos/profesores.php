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
    $dniEscapado = mysqli_real_escape_string($con, $dni);
    $emailEscapado = mysqli_real_escape_string($con, $email);
    
    $sql = "SELECT idProfesor FROM profesores WHERE (dniProfesor = '$dniEscapado' OR emailProfesor = '$emailEscapado')";
    if ($idExcluir) {
        $sql .= " AND idProfesor != $idExcluir";
    }
    
    $resultado = mysqli_query($con, $sql);
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
    $sql = "INSERT INTO profesores (nombreProfesor, emailProfesor, telefonoProfesor, dniProfesor, direccionProfesor, fechaNacimientoProfesor, fechaAltaProfesor, ciudadProfesor, codigoPostalProfesor, observacionesProfesor) VALUES ('$nombre', '$email', '$tel', '$dni', '$dir', '$f_nac', '$f_alta', '$ciudad', '$cp', '$obs')";
    $resultado = mysqli_query($con, $sql);
    
    // Sacamos el ID mas alto (el nuevo)
    $sql = "SELECT MAX(idProfesor) as ultimoId FROM profesores";
    $resultado = mysqli_query($con, $sql);
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
    $sql = "UPDATE profesores SET nombreProfesor='$nombre', emailProfesor='$email', telefonoProfesor='$tel', dniProfesor='$dni', direccionProfesor='$dir', fechaNacimientoProfesor='$f_nac', fechaAltaProfesor='$f_alta', ciudadProfesor='$ciudad', codigoPostalProfesor='$cp', observacionesProfesor='$obs' WHERE idProfesor=$id";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Unir profe con ciclo
function asociarCicloProfesor($idCic, $idProf) {
    $con = obtenerConexion();
    $sql = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($idCic, $idProf)";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Unir profe con modulo
function asociarModuloProfesor($idMod, $idProf) {
    $con = obtenerConexion();
    $sql = "INSERT INTO profesor_modulo (idModulo, idProfesor) VALUES ($idMod, $idProf)";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Quitar profe
function eliminarProfesor($id) {
    $con = obtenerConexion();
    $sql = "DELETE FROM profesores WHERE idProfesor = $id";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Datos de un profe por ID
function obtenerProfesorPorId($id) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM profesores WHERE idProfesor = $id";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $fila;
}

// Ver profes que dan clase en un ciclo
function listarProfesoresPorCiclo($idCic) {
    $con = obtenerConexion();
    $sql = "SELECT p.* FROM profesores p JOIN ciclo_profesor cp ON p.idProfesor = cp.idProfesor WHERE cp.idCiclo = $idCic ORDER BY p.nombreProfesor ASC";
    $resultado = mysqli_query($con, $sql);
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
    $sql = "SELECT idModulo FROM profesor_modulo WHERE idProfesor = $idProf";
    $resultado = mysqli_query($con, $sql);
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
    $sql = "DELETE FROM profesor_modulo WHERE idProfesor = $idProf";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Cambiar la clave
function actualizarPasswordProfesor($id, $pass) {
    $con = obtenerConexion();
    $sql = "UPDATE profesores SET password = '$pass' WHERE idProfesor = $id";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Datos basicos de perfil propio
function actualizarPerfilProfesor($id, $nombre, $email, $tel) {
    $con = obtenerConexion();
    $sql = "UPDATE profesores SET nombreProfesor='$nombre', emailProfesor='$email', telefonoProfesor='$tel' WHERE idProfesor=$id";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}
// Obtener profesores y sus módulos para un estudiante específico (según su ciclo)
function obtenerProfesoresConModulosParaEstudiante($idEst) {
    $con = obtenerConexion();
    
    // Primero obtenemos el ciclo del estudiante
    $sql = "SELECT idCiclo FROM estudiantes WHERE idEstudiante = $idEst";
    $resultado = mysqli_query($con, $sql);
    $filaCiclo = mysqli_fetch_assoc($resultado);
    $idCiclo = $filaCiclo['idCiclo'];

    // Ahora buscamos los profesores que dan clase en los módulos de ese ciclo
    $sql = "SELECT p.idProfesor, p.nombreProfesor, m.nombreModulo 
            FROM profesores p 
            JOIN profesor_modulo pm ON p.idProfesor = pm.idProfesor 
            JOIN modulos m ON pm.idModulo = m.idModulo 
            WHERE m.idCiclo = $idCiclo 
            ORDER BY p.nombreProfesor ASC, m.nombreModulo ASC";
            
    $resultado = mysqli_query($con, $sql);
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
    $emailEscapado = mysqli_real_escape_string($con, $email);
    $passEscapada = mysqli_real_escape_string($con, $pass);
    $sql = "SELECT * FROM profesores WHERE emailProfesor = '$emailEscapado' AND password = '$passEscapada'";
    $resultado = mysqli_query($con, $sql);
    $datos = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datos;
}

// Actualizar token FCM para notificaciones
function actualizarTokenFCMProfesor($id, $token) {
    $con = obtenerConexion();
    $sql = "UPDATE profesores SET fcm_token = '$token' WHERE idProfesor = $id";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Obtener token FCM de un profesor específico
function obtenerTokenFCMProfesor($id) {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM profesores WHERE idProfesor = $id";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    $token = $fila['fcm_token'] ?? null;
    mysqli_close($con);
    return $token;
}
