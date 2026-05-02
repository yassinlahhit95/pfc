<?php
require_once __DIR__ . "/conectar.php";

// Obtener todos los mensajes gestionados por la Dirección (Super Admin)
function listarTodosLosMensajes() {
    $con = obtenerConexion();
    $sql = "SELECT r.*, e.nombreEstudiante, p.nombreProfesor 
            FROM reclamaciones r 
            LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante 
            LEFT JOIN profesores p ON r.idProfesor = p.idProfesor 
            WHERE (r.emisor_rol = 'estudiante' AND r.idProfesor IS NULL) 
               OR (r.emisor_rol = 'profesor' AND r.idEstudiante IS NULL)
               OR (r.emisor_rol = 'admin')
            ORDER BY r.idReclamacion DESC";
            
    $resultado = mysqli_query($con, $sql);
    $listaMensajes = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $listaMensajes[] = $fila; 
    }
    mysqli_close($con);
    return $listaMensajes;
}

// Obtener los detalles de un mensaje específico por su ID
function obtenerMensajePorId($idReclamacion) {
    $con = obtenerConexion();
    $sql = "SELECT r.*, e.nombreEstudiante, p.nombreProfesor, c.nombreCiclo
            FROM reclamaciones r 
            LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante 
            LEFT JOIN profesores p ON r.idProfesor = p.idProfesor 
            LEFT JOIN ciclos c ON e.idCiclo = c.idCiclo
            WHERE r.idReclamacion = $idReclamacion";
            
    $resultado = mysqli_query($con, $sql);
    $mensaje = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $mensaje;
}

// Marcar un mensaje como leído y actualizar su estado a 'atendido'
function marcarMensajeComoLeido($idReclamacion) {
    $con = obtenerConexion();
    $sql = "UPDATE reclamaciones 
            SET leido = 1, estadoReclamacion = 'atendido' 
            WHERE idReclamacion = $idReclamacion";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Responder a un mensaje y marcarlo como atendido
function responderMensaje($idReclamacion, $contenidoRespuesta) {
    $con = obtenerConexion();
    $respuestaEscapada = mysqli_real_escape_string($con, $contenidoRespuesta);
    $sql = "UPDATE reclamaciones 
            SET respuesta = '$respuestaEscapada', estadoReclamacion = 'atendido', leido = 1 
            WHERE idReclamacion = $idReclamacion";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Insertar un nuevo mensaje o reclamación en el sistema
function insertarNuevoMensaje($idEstudiante, $idProfesor, $asunto, $descripcion, $fecha, $rolEmisor = 'estudiante') {
    $con = obtenerConexion();
    $fechaHoraActual = date('Y-m-d H:i:s');
    
    // Tratamos los IDs como NULL si no se proporcionan
    $valorEstudiante = ($idEstudiante > 0) ? $idEstudiante : 'NULL';
    $valorProfesor = ($idProfesor > 0) ? $idProfesor : 'NULL';
    
    $sql = "INSERT INTO reclamaciones (idEstudiante, idProfesor, emisor_rol, asunto, descripcion, fecha, estadoReclamacion, leido, respuesta) 
            VALUES ($valorEstudiante, $valorProfesor, '$rolEmisor', '$asunto', '$descripcion', '$fechaHoraActual', 'pendiente', 0, '')";
            
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Listar todos los mensajes enviados o recibidos por un estudiante concreto
function listarMensajesDeEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT r.*, p.nombreProfesor 
            FROM reclamaciones r 
            LEFT JOIN profesores p ON r.idProfesor = p.idProfesor 
            WHERE r.idEstudiante = $idEstudiante 
            ORDER BY r.idReclamacion DESC";
            
    $resultado = mysqli_query($con, $sql);
    $listaMensajes = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $listaMensajes[] = $fila; 
    }
    mysqli_close($con);
    return $listaMensajes;
}

// Listar los mensajes dirigidos a un profesor o enviados por él
function listarMensajesParaProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT r.*, e.nombreEstudiante, c.nombreCiclo 
            FROM reclamaciones r 
            LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante 
            LEFT JOIN ciclos c ON e.idCiclo = c.idCiclo
            WHERE r.idProfesor = $idProfesor OR (r.emisor_rol = 'profesor' AND r.idProfesor = $idProfesor)
            ORDER BY r.idReclamacion DESC";
            
    $resultado = mysqli_query($con, $sql);
    $listaMensajes = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $listaMensajes[] = $fila; 
    }
    mysqli_close($con);
    return $listaMensajes;
}

// Eliminar permanentemente un mensaje del sistema
function eliminarMensaje($idReclamacion) {
    $con = obtenerConexion();
    $sql = "DELETE FROM reclamaciones WHERE idReclamacion = $idReclamacion";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// --- SISTEMA DE CONTADORES DE MENSAJES NO LEÍDOS ---

function contarMensajesNoLeidosAdmin() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones 
            WHERE leido = 0 AND idProfesor IS NULL AND emisor_rol = 'estudiante'";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

function contarMensajesNoLeidosProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones 
            WHERE leido = 0 AND idProfesor = $idProfesor AND emisor_rol = 'estudiante'";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

function contarMensajesDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones WHERE idProfesor = $idProfesor";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}

function contarMensajesNoLeidosEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones 
            WHERE leido = 0 AND idEstudiante = $idEstudiante AND emisor_rol = 'profesor'";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}
?>