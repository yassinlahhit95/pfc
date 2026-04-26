<?php
require_once("conectar.php");

// Ver mensajes admin
function listarTodosLosMensajes() {
    $db = obtenerConexion();
    $sql = "SELECT r.*, e.nombreEstudiante, p.nombreProfesor FROM reclamaciones r LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante LEFT JOIN profesores p ON r.idProfesor = p.idProfesor ORDER BY r.idReclamacion DESC";
    $res = mysqli_query($db, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Coger por ID
function obtenerMensajePorId($id) {
    $db = obtenerConexion();
    $sql = "SELECT r.*, e.nombreEstudiante, p.nombreProfesor FROM reclamaciones r LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante LEFT JOIN profesores p ON r.idProfesor = p.idProfesor WHERE r.idReclamacion = $id";
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

// Responder
function responderMensaje($id, $txt) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "UPDATE reclamaciones SET respuesta = '$txt', estadoReclamacion = 'atendido', leido = 1 WHERE idReclamacion = $id");
    
    if ($res) {
        $ruta = __DIR__ . "/../controladores/firebase/firebase_helper.php";
        if (file_exists($ruta)) {
            $f = mysqli_fetch_assoc(mysqli_query($db, "SELECT idEstudiante, idProfesor, emisor_rol, asunto FROM reclamaciones WHERE idReclamacion = $id"));
            if (isset($f)) {
                require_once $ruta;
                $destino = ($f['emisor_rol'] == 'estudiante') ? $f['idEstudiante'] : $f['idProfesor'];
                $rol = $f['emisor_rol'];
                $tok = obtenerTokenUsuario($destino, $rol);
                if ($tok != "") { enviarNotificacionFirebase($tok, "Respuesta a: " . $f['asunto'], $txt); }
            }
        }
    }
    mysqli_close($db);
    return $res;
}

// Nuevo mensaje
function insertarNuevoMensaje($idE, $idP, $asu, $desc, $fec, $rol = 'estudiante') {
    $db = obtenerConexion();
    $valE = ($idE > 0) ? $idE : 'NULL';
    $valP = ($idP > 0) ? $idP : 'NULL';
    $sql = "INSERT INTO reclamaciones (idEstudiante, idProfesor, emisor_rol, asunto, descripcion, fecha, estadoReclamacion, leido, respuesta) VALUES ($valE, $valP, '$rol', '$asu', '$desc', '$fec', 'pendiente', 0, '')";
    $res = mysqli_query($db, $sql);
    mysqli_close($db);
    return $res;
}

// Mensajes alumno
function listarMensajesDeEstudiante($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT r.*, p.nombreProfesor FROM reclamaciones r LEFT JOIN profesores p ON r.idProfesor = p.idProfesor WHERE r.idEstudiante = $id ORDER BY r.idReclamacion DESC");
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Mensajes profe
function listarMensajesParaProfesor($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT r.*, e.nombreEstudiante FROM reclamaciones r LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante WHERE r.idProfesor = $id ORDER BY r.idReclamacion DESC");
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
?>