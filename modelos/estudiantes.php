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

// Registrar un nuevo estudiante en el sistema
function insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo) {
    $con = obtenerConexion();
    $sql = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo) 
            VALUES ('$nombre', '$email', '$telefono', '$fechaNacimiento', '$dni', '$fechaAlta', '$direccion', '$ciudad', '$codigoPostal', '$observaciones', $idCiclo)";
    
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Actualizar todos los datos de un estudiante existente
function actualizarEstudiante($idEstudiante, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes 
            SET nombreEstudiante='$nombre', emailEstudiante='$email', telefonoEstudiante='$telefono', 
                fechaNacimientoEstudiante='$fechaNacimiento', dniEstudiante='$dni', fechaAltaEstudiante='$fechaAlta', 
                direccionEstudiante='$direccion', ciudadEstudiante='$ciudad', codigoPostalEstudiante='$codigoPostal', 
                observacionesEstudiante='$observaciones', idCiclo=$idCiclo 
            WHERE idEstudiante=$idEstudiante";
    
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Obtener los estudiantes vinculados a los ciclos que imparte un profesor
function listarEstudiantesPorProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo 
            FROM estudiantes 
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
            WHERE estudiantes.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesor) 
            ORDER BY estudiantes.nombreEstudiante ASC";
    
    $resultado = mysqli_query($con, $sql);
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
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo 
            FROM estudiantes 
            LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
            WHERE estudiantes.idCiclo = $idCiclo 
            ORDER BY estudiantes.idEstudiante ASC";
    
    $resultado = mysqli_query($con, $sql);
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
    $sql = "DELETE FROM estudiantes WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Obtener la información completa de un estudiante por su ID
function obtenerEstudiantePorId($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo 
            FROM estudiantes 
            LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
            WHERE estudiantes.idEstudiante = $idEstudiante";
    
    $resultado = mysqli_query($con, $sql);
    $datosEstudiante = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosEstudiante;
}

// Actualizar la contraseña de un estudiante
function actualizarPasswordEstudiante($idEstudiante, $nuevaPassword) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET password = '$nuevaPassword' WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Actualizar los datos básicos del perfil de un estudiante
function actualizarPerfilEstudiante($idEstudiante, $nombre, $email, $telefono) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes 
            SET nombreEstudiante='$nombre', emailEstudiante='$email', telefonoEstudiante='$telefono' 
            WHERE idEstudiante=$idEstudiante";
    $resultado = mysqli_query($con, $sql);
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
    $emailEscapado = mysqli_real_escape_string($con, $email);
    $passEscapada = mysqli_real_escape_string($con, $password);
    $sql = "SELECT * FROM estudiantes 
            WHERE emailEstudiante = '$emailEscapado' AND password = '$passEscapada'";
    $resultado = mysqli_query($con, $sql);
    $datosUsuario = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosUsuario;
}

// Guardar o actualizar el token FCM de un estudiante
function actualizarTokenFCMEstudiante($idEstudiante, $nuevoToken) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET fcm_token = '$nuevoToken' WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Obtener el token FCM actual de un estudiante específico
function obtenerTokenFCMEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM estudiantes WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    $token = $fila['fcm_token'] ?? null;
    mysqli_close($con);
    return $token;
}
