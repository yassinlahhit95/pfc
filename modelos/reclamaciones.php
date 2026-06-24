<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
//  CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarTodosLosMensajes(int $limite = 200, int $offset = 0) {
    $con = obtenerConexion();
    $sql = "SELECT r.*, e.nombreEstudiante, p.nombreProfesor
            FROM reclamaciones r
            LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante
            LEFT JOIN profesores p ON r.idProfesor = p.idProfesor
            WHERE r.id_parent IS NULL
              AND ((r.emisor_rol = 'estudiante' AND r.idProfesor IS NULL)
               OR (r.emisor_rol = 'profesor' AND r.idEstudiante IS NULL)
               OR (r.emisor_rol = 'admin'))
            ORDER BY r.idReclamacion DESC
            LIMIT ? OFFSET ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $limite, $offset);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaMensajes = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaMensajes[] = $fila;
    }

    return $listaMensajes;
}

function obtenerMensajePorId($idReclamacion) {
    $con = obtenerConexion();
    $sql = "SELECT r.*, e.nombreEstudiante, p.nombreProfesor, c.nombreCiclo, c.abreviaturaCiclo FROM reclamaciones r LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante LEFT JOIN profesores p ON r.idProfesor = p.idProfesor LEFT JOIN ciclos c ON e.idCiclo = c.idCiclo WHERE r.idReclamacion = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idReclamacion);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $mensaje = mysqli_fetch_assoc($resultado);
    
    return $mensaje;
}

// ══════════════════════════════════════════════════════════════════════
//  ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function marcarMensajeComoLeido($idReclamacion) {
    $con = obtenerConexion();
    $sql = "UPDATE reclamaciones SET leido = 1, estadoReclamacion = 'atendido' WHERE idReclamacion = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idReclamacion);
    $resultado = mysqli_stmt_execute($stmt);
    
    return $resultado;
}


// ══════════════════════════════════════════════════════════════════════
//  INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function insertarNuevoMensaje($idEstudiante, $idProfesor, $asunto, $descripcion, $rolEmisor = 'estudiante') {
    $con = obtenerConexion();
    $fechaHoraActual = date('Y-m-d H:i:s');

    $valorEstudiante = ($idEstudiante > 0) ? $idEstudiante : null;
    $valorProfesor = ($idProfesor > 0) ? $idProfesor : null;

    $sql = "INSERT INTO reclamaciones (idEstudiante, idProfesor, emisor_rol, asunto, descripcion, fecha, estadoReclamacion, leido, respuesta) VALUES (?, ?, ?, ?, ?, ?, 'pendiente', 0, '')";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iissss", $valorEstudiante, $valorProfesor, $rolEmisor, $asunto, $descripcion, $fechaHoraActual);

    $resultado = mysqli_stmt_execute($stmt);
    
    return $resultado;
}

function listarMensajesDeEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT r.*, p.nombreProfesor FROM reclamaciones r LEFT JOIN profesores p ON r.idProfesor = p.idProfesor WHERE r.idEstudiante = ? AND r.id_parent IS NULL ORDER BY r.idReclamacion DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaMensajes = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaMensajes[] = $fila;
    }
    
    return $listaMensajes;
}

function listarMensajesParaProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT r.*, e.nombreEstudiante, c.nombreCiclo, c.abreviaturaCiclo FROM reclamaciones r LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante LEFT JOIN ciclos c ON e.idCiclo = c.idCiclo WHERE r.id_parent IS NULL AND (r.idProfesor = ? OR (r.emisor_rol = 'profesor' AND r.idProfesor = ?)) ORDER BY r.idReclamacion DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaMensajes = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaMensajes[] = $fila;
    }
    
    return $listaMensajes;
}

// Comprobaciones de propiedad (evitan IDOR: actuar sobre mensajes ajenos)
function mensajePerteneceAEstudiante($idReclamacion, $idEstudiante) {
    $m = obtenerMensajePorId($idReclamacion);
    return $m && (int)$m['idEstudiante'] === (int)$idEstudiante;
}
function mensajePerteneceAProfesor($idReclamacion, $idProfesor) {
    $m = obtenerMensajePorId($idReclamacion);
    return $m && (int)$m['idProfesor'] === (int)$idProfesor;
}

function editarMensaje(int $idReclamacion, string $contenido): bool {
    $con  = obtenerConexion();
    $ahora = date('Y-m-d H:i:s');
    $st = mysqli_prepare($con,
        'UPDATE reclamaciones SET descripcion = ?, editado = 1, fecha_edicion = ?
         WHERE idReclamacion = ? AND emisor_rol = "admin"');
    mysqli_stmt_bind_param($st, 'ssi', $contenido, $ahora, $idReclamacion);
    mysqli_stmt_execute($st);
    return mysqli_affected_rows($con) > 0;
}

function eliminarMensaje($idReclamacion) {
    $con = obtenerConexion();
    $sql = "DELETE FROM reclamaciones WHERE idReclamacion = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idReclamacion);
    $resultado = mysqli_stmt_execute($stmt);
    
    return $resultado;
}

function contarMensajesNoLeidosAdmin() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones
            WHERE leido = 0
            AND (
                (emisor_rol = 'estudiante' AND idProfesor IS NULL)
                OR (emisor_rol = 'profesor' AND idEstudiante IS NULL)
            )";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    
    return intval($fila['total']);
}


function contarMensajesNoLeidosProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones WHERE leido = 0 AND idProfesor = ? AND emisor_rol != 'profesor'";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    
    return intval($fila['total']);
}

function contarMensajesDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones WHERE idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    
    return intval($fila['total']);
}

function contarMensajesNoLeidosEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM reclamaciones WHERE leido = 0 AND idEstudiante = ? AND emisor_rol != 'estudiante'";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);

    return intval($fila['total']);
}

function obtenerHiloCompleto(int $idRaiz): array {
    $con = obtenerConexion();
    $st = mysqli_prepare($con,
        'SELECT r.*, e.nombreEstudiante, p.nombreProfesor
         FROM reclamaciones r
         LEFT JOIN estudiantes e ON r.idEstudiante = e.idEstudiante
         LEFT JOIN profesores  p ON r.idProfesor   = p.idProfesor
         WHERE r.idReclamacion = ? OR r.id_parent = ?
         ORDER BY r.fecha ASC, r.idReclamacion ASC');
    mysqli_stmt_bind_param($st, 'ii', $idRaiz, $idRaiz);
    mysqli_stmt_execute($st);
    return mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);
}

function insertarRespuestaMensaje(int $idParent, ?int $idEstudiante, ?int $idProfesor, string $contenido, string $emisorRol): bool {
    $con   = obtenerConexion();
    $ahora = date('Y-m-d H:i:s');
    // Inherit asunto from parent
    $parent = obtenerMensajePorId($idParent);
    $asunto = $parent['asunto'] ?? 'Respuesta';
    $estId  = $idEstudiante ?? ($parent['idEstudiante'] ? (int)$parent['idEstudiante'] : null);
    $profId = $idProfesor   ?? ($parent['idProfesor']   ? (int)$parent['idProfesor']   : null);
    $st = mysqli_prepare($con,
        'INSERT INTO reclamaciones
         (idEstudiante, idProfesor, id_parent, emisor_rol, asunto, descripcion, fecha, estadoReclamacion, leido, respuesta)
         VALUES (?, ?, ?, ?, ?, ?, ?, "atendido", 0, "")');
    mysqli_stmt_bind_param($st, 'iiissss', $estId, $profId, $idParent, $emisorRol, $asunto, $contenido, $ahora);
    return mysqli_stmt_execute($st);
}

