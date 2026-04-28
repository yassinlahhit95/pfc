<?php
require_once("conectar.php");

// Ver mensajes admin (SOLO los dirigidos a Dirección o enviados por Dirección)
function listarTodosLosMensajes() {
    $db = obtenerConexion();
    $sql = "SELECT r.*, e.nombreEstudiante, p.nombreProfesor 
            FROM reclamaciones r 
            LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante 
            LEFT JOIN profesores p ON r.idProfesor = p.idProfesor 
            WHERE (r.emisor_rol = 'estudiante' AND r.idProfesor IS NULL) 
               OR (r.emisor_rol = 'profesor' AND r.idEstudiante IS NULL)
               OR (r.emisor_rol = 'admin')
            ORDER BY r.idReclamacion DESC";
    $res = mysqli_query($db, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Coger por ID
function obtenerMensajePorId($id) {
    $db = obtenerConexion();
    $sql = "SELECT r.*, e.nombreEstudiante, p.nombreProfesor, c.nombreCiclo
            FROM reclamaciones r 
            LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante 
            LEFT JOIN profesores p ON r.idProfesor = p.idProfesor 
            LEFT JOIN ciclos c ON e.idCiclo = c.idCiclo
            WHERE r.idReclamacion = $id";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return $fila;
}

// Marcar leido
function marcarMensajeComoLeido($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "UPDATE reclamaciones SET leido = 1, estadoReclamacion = 'atendido' WHERE idReclamacion = $id");
    mysqli_close($db);
    return $res;
}

// Nuevo mensaje
function insertarNuevoMensaje($idE, $idP, $asu, $desc, $fec, $rol = 'estudiante') {
    $db = obtenerConexion();
    // Guardamos la fecha y hora actual
    $fechaHora = date('Y-m-d H:i:s');
    $valE = ($idE > 0) ? $idE : 'NULL';
    $valP = ($idP > 0) ? $idP : 'NULL';
    $sql = "INSERT INTO reclamaciones (idEstudiante, idProfesor, emisor_rol, asunto, descripcion, fecha, estadoReclamacion, leido, respuesta) 
            VALUES ($valE, $valP, '$rol', '$asu', '$desc', '$fechaHora', 'pendiente', 0, '')";
    $res = mysqli_query($db, $sql);
    mysqli_close($db);
    return $res;
}

// Mensajes alumno (Solo los suyos)
function listarMensajesDeEstudiante($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT r.*, p.nombreProfesor FROM reclamaciones r LEFT JOIN profesores p ON r.idProfesor = p.idProfesor WHERE r.idEstudiante = $id ORDER BY r.idReclamacion DESC");
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Mensajes profe (Solo los suyos: donde es destinatario o donde él envió)
function listarMensajesParaProfesor($id) {
    $db = obtenerConexion();
    $sql = "SELECT r.*, e.nombreEstudiante, c.nombreCiclo 
            FROM reclamaciones r 
            LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante 
            LEFT JOIN ciclos c ON e.idCiclo = c.idCiclo
            WHERE r.idProfesor = $id OR (r.emisor_rol = 'profesor' AND r.idProfesor = $id)
            ORDER BY r.idReclamacion DESC";
    // Nota: Ajustamos para que r.idProfesor identifique al profesor involucrado en la conversación
    $res = mysqli_query($db, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

function eliminarMensaje($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "DELETE FROM reclamaciones WHERE idReclamacion = $id");
    mysqli_close($db);
    return $res;
}

// --- SISTEMA DE ALERTAS (MENSAJES NO LEÍDOS) ---
function contarMensajesNoLeidosAdmin() {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones WHERE leido = 0 AND idProfesor IS NULL AND emisor_rol = 'estudiante'";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return $fila['total'] ?? 0;
}

function contarMensajesNoLeidosProfesor($idProf) {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones WHERE leido = 0 AND idProfesor = $idProf AND emisor_rol = 'estudiante'";
    $res = mysqli_fetch_assoc(mysqli_query($db, $sql));
    mysqli_close($db);
    return $res['total'] ?? 0;
}

function contarMensajesDeProfesor($idProf) {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones WHERE idProfesor = $idProf";
    $res = mysqli_fetch_assoc(mysqli_query($db, $sql));
    mysqli_close($db);
    return $res['total'] ?? 0;
}

function contarMensajesNoLeidosEstudiante($idEst) {
    $db = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones WHERE leido = 0 AND idEstudiante = $idEst AND emisor_rol = 'profesor'";
    $res = mysqli_fetch_assoc(mysqli_query($db, $sql));
    mysqli_close($db);
    return $res['total'] ?? 0;
}
?>