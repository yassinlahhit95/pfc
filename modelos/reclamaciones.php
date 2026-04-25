<?php
require_once("conectar.php");

/**
 * Lista todos los mensajes registrados en el sistema.
 */
function listarTodosLosMensajes() {
    $conexion = obtenerConexion();
    $sql = "SELECT r.*, e.nombreEstudiante, p.nombreProfesor 
            FROM reclamaciones r
            LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante
            LEFT JOIN profesores p ON r.idProfesor = p.idProfesor
            ORDER BY r.idReclamacion DESC";
    $resultado = mysqli_query($conexion, $sql);
    $listaDeMensajes = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaDeMensajes[] = $fila;
    }
    mysqli_close($conexion);
    return $listaDeMensajes;
}

/**
 * Obtiene un mensaje específico por su ID.
 */
function obtenerMensajePorId($idReclamacion) {
    $conexion = obtenerConexion();
    $stmt = mysqli_prepare($conexion, "SELECT r.*, e.nombreEstudiante, p.nombreProfesor 
            FROM reclamaciones r
            LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante
            LEFT JOIN profesores p ON r.idProfesor = p.idProfesor
            WHERE r.idReclamacion = ?");
    mysqli_stmt_bind_param($stmt, "i", $idReclamacion);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $mensaje = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    return $mensaje;
}

/**
 * Marca un mensaje como leído.
 */
function marcarMensajeComoLeido($idReclamacion) {
    $conexion = obtenerConexion();
    $stmt = mysqli_prepare($conexion, "UPDATE reclamaciones SET leido = 1, estadoReclamacion = 'atendido' WHERE idReclamacion = ?");
    mysqli_stmt_bind_param($stmt, "i", $idReclamacion);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    return $resultado;
}

/**
 * Guarda una respuesta o comentario para un mensaje.
 */
function responderMensaje($idReclamacion, $textoRespuesta) {
    $conexion = obtenerConexion();
    
    // Obtener datos del mensaje original para saber a quién notificar
    $stmtData = mysqli_prepare($conexion, "SELECT idEstudiante, idProfesor, emisor_rol, asunto FROM reclamaciones WHERE idReclamacion = ?");
    mysqli_stmt_bind_param($stmtData, "i", $idReclamacion);
    mysqli_stmt_execute($stmtData);
    $resData = mysqli_stmt_get_result($stmtData);
    $original = mysqli_fetch_assoc($resData);
    mysqli_stmt_close($stmtData);

    $stmtUpdate = mysqli_prepare($conexion, "UPDATE reclamaciones SET respuesta = ?, estadoReclamacion = 'atendido', leido = 1 WHERE idReclamacion = ?");
    mysqli_stmt_bind_param($stmtUpdate, "si", $textoRespuesta, $idReclamacion);
    $resultado = mysqli_stmt_execute($stmtUpdate);
    mysqli_stmt_close($stmtUpdate);
    
    if ($resultado && $original) {
        // Notificación opcional si el sistema Firebase está disponible
        $helperPath = __DIR__ . "/../controladores/firebase/firebase_helper.php";
        if (file_exists($helperPath)) {
            require_once $helperPath;
            $destId = null;
            $destRol = "";
            
            if ($original['emisor_rol'] == 'estudiante') {
                $destId = $original['idEstudiante'];
                $destRol = 'estudiante';
            } else if ($original['emisor_rol'] == 'profesor') {
                $destId = $original['idProfesor'];
                $destRol = 'profesor';
            }
            
            if ($destId) {
                $token = obtenerTokenUsuario($destId, $destRol);
                if ($token) {
                    enviarNotificacionFirebase($token, "Respuesta a: " . $original['asunto'], $textoRespuesta);
                }
            }
        }
    }
    
    mysqli_close($conexion);
    return $resultado;
}

/**
 * Inserta un nuevo mensaje con identificación del emisor.
 */
function insertarNuevoMensaje($idEstudiante, $idProfesor, $asunto, $descripcion, $fecha, $emisor_rol = 'estudiante') {
    $conexion = obtenerConexion();
    
    $stmt = mysqli_prepare($conexion, "INSERT INTO reclamaciones (idEstudiante, idProfesor, emisor_rol, asunto, descripcion, fecha, estadoReclamacion, leido, respuesta) 
            VALUES (?, ?, ?, ?, ?, ?, 'pendiente', 0, '')");
    
    $valEst = empty($idEstudiante) ? null : $idEstudiante;
    $valProf = empty($idProfesor) ? null : $idProfesor;
    
    mysqli_stmt_bind_param($stmt, "iissss", $valEst, $valProf, $emisor_rol, $asunto, $descripcion, $fecha);
    $resultado = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    return $resultado;
}

/**
 * Lista mensajes para la bandeja de entrada de un estudiante.
 */
function listarMensajesDeEstudiante($idEstudiante) {
    $conexion = obtenerConexion();
    $stmt = mysqli_prepare($conexion, "SELECT r.*, p.nombreProfesor 
            FROM reclamaciones r
            LEFT JOIN profesores p ON r.idProfesor = p.idProfesor
            WHERE r.idEstudiante = ?
            ORDER BY r.idReclamacion DESC");
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { $lista[] = $fila; }
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    return $lista;
}

/**
 * Lista mensajes para la bandeja de entrada de un profesor.
 */
function listarMensajesParaProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $stmt = mysqli_prepare($conexion, "SELECT r.*, e.nombreEstudiante 
            FROM reclamaciones r
            LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante
            WHERE r.idProfesor = ?
            ORDER BY r.idReclamacion DESC");
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { $lista[] = $fila; }
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    return $lista;
}

/**
 * Lista de profesores asignados a un estudiante con nombre de módulo.
 */
function obtenerProfesoresConModulosParaEstudiante($idEstudiante) {
    $conexion = obtenerConexion();
    $stmt = mysqli_prepare($conexion, "SELECT p.idProfesor, p.nombreProfesor, m.nombreModulo 
            FROM profesores p 
            JOIN profesor_modulo pm ON p.idProfesor = pm.idProfesor 
            JOIN modulos m ON pm.idModulo = m.idModulo 
            JOIN estudiantes e ON m.idCiclo = e.idCiclo 
            WHERE e.idEstudiante = ? 
            ORDER BY p.nombreProfesor ASC");
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { $lista[] = $fila; }
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    return $lista;
}

function eliminarMensaje($idReclamacion) {
    $conexion = obtenerConexion();
    $stmt = mysqli_prepare($conexion, "DELETE FROM reclamaciones WHERE idReclamacion = ?");
    mysqli_stmt_bind_param($stmt, "i", $idReclamacion);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    return $resultado;
}
?>
