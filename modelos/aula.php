<?php
require_once __DIR__ . "/conectar.php";

// ═══════════════════════════════════════
// CARPETAS
// ═══════════════════════════════════════

function listarCarpetasPorModuloAula($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT c.*,
                   (SELECT COUNT(*) FROM aula_archivos a WHERE a.idCarpeta = c.idCarpeta AND a.eliminado = 0) AS totalArchivos,
                   (SELECT COUNT(*) FROM aula_carpetas sc WHERE sc.idPadre = c.idCarpeta AND sc.eliminado = 0) AS totalSubcarpetas
            FROM aula_carpetas c
            WHERE c.idModulo = ? AND c.eliminado = 0
            ORDER BY c.fijado DESC, c.nombre ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

function obtenerCarpetaAulaPorId($idCarpeta) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM aula_carpetas WHERE idCarpeta = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCarpeta);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($res);
    
    return $fila;
}

function insertarCarpetaAula($nombre, $idModulo, $idProfesor, $color, $icono = 'fa-folder', $idPadre = null) {
    $con = obtenerConexion();
    $sql = "INSERT INTO aula_carpetas (nombre, idModulo, idProfesor, color, icono, idPadre) VALUES (?,?,?,?,?,?)";
    $stmt = mysqli_prepare($con, $sql);
    $padre = $idPadre ?: null;
    mysqli_stmt_bind_param($stmt, "siissi", $nombre, $idModulo, $idProfesor, $color, $icono, $padre);
    $ok = mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($con);
    
    return $ok ? $id : false;
}

function actualizarCarpetaAula($idCarpeta, $nombre, $color, $icono = 'fa-folder') {
    $con = obtenerConexion();
    $sql = "UPDATE aula_carpetas SET nombre=?, color=?, icono=? WHERE idCarpeta=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $color, $icono, $idCarpeta);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

// Devuelve el idCarpeta indicado + TODAS sus subcarpetas (recursivo, cualquier nivel)
// Optimizado para evitar el problema N+1 mediante una única consulta al módulo.
function obtenerArbolCarpetaAula($idCarpeta) {
    $con = obtenerConexion();
    
    // Primero obtenemos el idModulo de la carpeta inicial
    $sqlModulo = "SELECT idModulo FROM aula_carpetas WHERE idCarpeta = ?";
    $stmtM = mysqli_prepare($con, $sqlModulo);
    mysqli_stmt_bind_param($stmtM, "i", $idCarpeta);
    mysqli_stmt_execute($stmtM);
    $resM = mysqli_stmt_get_result($stmtM);
    $filaM = mysqli_fetch_assoc($resM);
    
    if (!$filaM) return [intval($idCarpeta)];
    
    $idModulo = $filaM['idModulo'];

    // Obtenemos todas las carpetas del módulo de una sola vez
    $sqlTodas = "SELECT idCarpeta, idPadre FROM aula_carpetas WHERE idModulo = ? AND eliminado = 0";
    $stmtT = mysqli_prepare($con, $sqlTodas);
    mysqli_stmt_bind_param($stmtT, "i", $idModulo);
    mysqli_stmt_execute($stmtT);
    $resT = mysqli_stmt_get_result($stmtT);
    
    $adj = []; // Lista de adyacencia: idPadre => [idHijo1, idHijo2, ...]
    while ($f = mysqli_fetch_assoc($resT)) {
        $p = $f['idPadre'] ? intval($f['idPadre']) : 0;
        $adj[$p][] = intval($f['idCarpeta']);
    }

    // Recorrido BFS/DFS en memoria sobre la lista de adyacencia
    $ids = [intval($idCarpeta)];
    $cola = [intval($idCarpeta)];
    
    while ($cola) {
        $actual = array_shift($cola);
        if (isset($adj[$actual])) {
            foreach ($adj[$actual] as $hijo) {
                if (!in_array($hijo, $ids, true)) {
                    $ids[] = $hijo;
                    $cola[] = $hijo;
                }
            }
        }
    }
    
    return $ids;
}

// Borrado lógico (papelera) RECURSIVO: la carpeta, todas sus subcarpetas (a
// cualquier nivel) y todos sus archivos se marcan como eliminados. Evita
// registros huérfanos al borrar un árbol de carpetas.
function borrarCarpetaAula($idCarpeta) {
    $ids = obtenerArbolCarpetaAula($idCarpeta);
    if (empty($ids)) return false;
    $con = obtenerConexion();
    $ahora = mysqli_real_escape_string($con, date('Y-m-d H:i:s'));
    $in = implode(',', array_map('intval', $ids));
    $ok = mysqli_query($con, "UPDATE aula_carpetas SET eliminado=1, fechaEliminacion='$ahora' WHERE idCarpeta IN ($in)");
    mysqli_query($con, "UPDATE aula_archivos SET eliminado=1, fechaEliminacion='$ahora' WHERE idCarpeta IN ($in)");
    
    return $ok;
}

// Fijar / desfijar (pin) una carpeta
function togglePinCarpetaAula($idCarpeta) {
    $con = obtenerConexion();
    $sql = "UPDATE aula_carpetas SET fijado = 1 - fijado WHERE idCarpeta = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCarpeta);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

// Fijar / desfijar (pin) un archivo
function togglePinArchivoAula($idArchivo) {
    $con = obtenerConexion();
    $sql = "UPDATE aula_archivos SET fijado = 1 - fijado WHERE idArchivo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArchivo);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

// Mueve una carpeta a un nuevo padre ($idPadre = null => raíz del módulo).
// Evita ciclos: no dentro de sí misma ni de sus descendientes; mismo módulo.
function moverCarpetaAula($idCarpeta, $idPadre) {
    $carpeta = obtenerCarpetaAulaPorId($idCarpeta);
    if (!$carpeta) return false;
    $idPadre = $idPadre ? intval($idPadre) : null;
    if ($idPadre !== null) {
        if ($idPadre === intval($idCarpeta)) return false;
        if (in_array($idPadre, obtenerArbolCarpetaAula($idCarpeta), true)) return false;
        $destino = obtenerCarpetaAulaPorId($idPadre);
        if (!$destino || $destino['idModulo'] != $carpeta['idModulo'] || $destino['eliminado']) return false;
    }
    $con = obtenerConexion();
    $sql = "UPDATE aula_carpetas SET idPadre=? WHERE idCarpeta=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idPadre, $idCarpeta);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

// ═══════════════════════════════════════
// ARCHIVOS
// ═══════════════════════════════════════

function listarArchivosPorModuloAula($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT a.*, p.nombreProfesor,
                   c.nombre AS nombreCarpeta, c.color AS colorCarpeta
            FROM aula_archivos a
            JOIN profesores p ON a.idProfesor = p.idProfesor
            LEFT JOIN aula_carpetas c ON a.idCarpeta = c.idCarpeta
            WHERE a.idModulo = ? AND a.eliminado = 0
            ORDER BY a.fijado DESC, a.nombreOriginal ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

function listarArchivosPorCarpetaAula($idCarpeta) {
    $con = obtenerConexion();
    $sql = "SELECT a.*, p.nombreProfesor
            FROM aula_archivos a
            JOIN profesores p ON a.idProfesor = p.idProfesor
            WHERE a.idCarpeta = ? AND a.eliminado = 0
            ORDER BY a.fijado DESC, a.nombreOriginal ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCarpeta);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

function obtenerArchivoPorId($idArchivo) {
    $con = obtenerConexion();
    $sql = "SELECT a.*, p.nombreProfesor, m.nombreModulo
            FROM aula_archivos a
            JOIN profesores p ON a.idProfesor = p.idProfesor
            JOIN modulos m ON a.idModulo = m.idModulo
            WHERE a.idArchivo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArchivo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($res);
    
    return $fila;
}

// ¿Existe ya un archivo (no eliminado) con ese nombre visible en la misma ubicación?
// El nombre incluye la extensión, así que "doc.pdf" y "doc.docx" NO colisionan.
function existeArchivoNombreAula($idModulo, $idCarpeta, $nombreOriginal) {
    $con = obtenerConexion();
    if ($idCarpeta) {
        $sql  = "SELECT COUNT(*) AS c FROM aula_archivos WHERE idCarpeta=? AND eliminado=0 AND nombreOriginal=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "is", $idCarpeta, $nombreOriginal);
    } else {
        $sql  = "SELECT COUNT(*) AS c FROM aula_archivos WHERE idModulo=? AND idCarpeta IS NULL AND eliminado=0 AND nombreOriginal=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "is", $idModulo, $nombreOriginal);
    }
    mysqli_stmt_execute($stmt);
    $f = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return intval($f['c']) > 0;
}

// Devuelve un nombre visible único en la ubicación, añadiendo " (2)", " (3)"... si hace falta
function nombreUnicoArchivoAula($idModulo, $idCarpeta, $nombreOriginal) {
    if (!existeArchivoNombreAula($idModulo, $idCarpeta, $nombreOriginal)) return $nombreOriginal;
    $punto = strrpos($nombreOriginal, '.');
    if ($punto === false || $punto === 0) { $base = $nombreOriginal; $ext = ''; }
    else { $base = substr($nombreOriginal, 0, $punto); $ext = substr($nombreOriginal, $punto); }
    $i = 2;
    do {
        $candidato = $base . ' (' . $i . ')' . $ext;
        $i++;
    } while ($i < 1000 && existeArchivoNombreAula($idModulo, $idCarpeta, $candidato));
    return $candidato;
}

function insertarArchivoAula($nombreArchivo, $nombreOriginal, $extension, $tamanio, $descripcion, $idCarpeta, $idModulo, $idProfesor) {
    $con = obtenerConexion();
    $sql = "INSERT INTO aula_archivos (nombreArchivo, nombreOriginal, extension, tamanio, descripcion, idCarpeta, idModulo, idProfesor)
            VALUES (?,?,?,?,?,?,?,?)";
    $stmt = mysqli_prepare($con, $sql);
    $idCarp = $idCarpeta ?: null;
    mysqli_stmt_bind_param($stmt, "sssissii", $nombreArchivo, $nombreOriginal, $extension, $tamanio, $descripcion, $idCarp, $idModulo, $idProfesor);
    $ok = mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($con);
    
    return $ok ? $id : false;
}

// Borrado lógico: el archivo se envía a la papelera (no se borra del disco)
function borrarArchivoAula($idArchivo) {
    $con = obtenerConexion();
    $ahora = date('Y-m-d H:i:s');
    $sql = "UPDATE aula_archivos SET eliminado=1, fechaEliminacion=? WHERE idArchivo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $ahora, $idArchivo);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

function contarArchivosPorModuloAula($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) AS total FROM aula_archivos WHERE idModulo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $f = mysqli_fetch_assoc($res);
    
    return intval($f['total']);
}

// ═══════════════════════════════════════
// TAREAS
// ═══════════════════════════════════════

function listarTareasPorModuloAula($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT t.*, p.nombreProfesor,
                   (SELECT COUNT(*) FROM aula_entregas e WHERE e.idTarea = t.idTarea) AS totalEntregas,
                   (SELECT COUNT(*) FROM aula_entregas e WHERE e.idTarea = t.idTarea AND e.estado = 'corregida') AS totalCorregidas
            FROM aula_tareas t
            JOIN profesores p ON t.idProfesor = p.idProfesor
            WHERE t.idModulo = ? AND t.publicado = 1
            ORDER BY t.fechaCreacion DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

function obtenerTareaPorIdAula($idTarea) {
    $con = obtenerConexion();
    $sql = "SELECT t.*, p.nombreProfesor, m.nombreModulo, m.idCiclo
            FROM aula_tareas t
            JOIN profesores p ON t.idProfesor = p.idProfesor
            JOIN modulos m ON t.idModulo = m.idModulo
            WHERE t.idTarea = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idTarea);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($res);
    
    return $fila;
}

function moverArchivoAula($idArchivo, $idCarpeta) {
    $con = obtenerConexion();
    $sql = "UPDATE aula_archivos SET idCarpeta=? WHERE idArchivo=?";
    $stmt = mysqli_prepare($con, $sql);
    $idCarp = $idCarpeta ?: null;
    mysqli_stmt_bind_param($stmt, "ii", $idCarp, $idArchivo);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

// ═══════════════════════════════════════
// ENTREGAS
// ═══════════════════════════════════════

function obtenerEntregaAula($idTarea, $idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM aula_entregas WHERE idTarea=? AND idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idTarea, $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($res);
    
    return $fila;
}

function enviarEntregaAula($idTarea, $idEstudiante, $archivoEntrega, $respuesta) {
    $con = obtenerConexion();
    $existing = "SELECT idEntrega, version FROM aula_entregas WHERE idTarea=? AND idEstudiante=?";
    $s = mysqli_prepare($con, $existing);
    mysqli_stmt_bind_param($s, "ii", $idTarea, $idEstudiante);
    mysqli_stmt_execute($s);
    mysqli_stmt_store_result($s);
    mysqli_stmt_bind_result($s, $idEntrega, $version);
    mysqli_stmt_fetch($s);
    $existe = mysqli_stmt_num_rows($s) > 0;

    if ($existe) {
        // Guardar versión anterior
        $vSql = "INSERT INTO aula_versiones_entrega (idTarea, idEstudiante, archivoEntrega, respuesta, version)
                 SELECT idTarea, idEstudiante, archivoEntrega, respuesta, version
                 FROM aula_entregas WHERE idTarea=? AND idEstudiante=?";
        $vs = mysqli_prepare($con, $vSql);
        mysqli_stmt_bind_param($vs, "ii", $idTarea, $idEstudiante);
        mysqli_stmt_execute($vs);

        $nuevaVersion = $version + 1;
        if ($archivoEntrega) {
            $uSql = "UPDATE aula_entregas SET archivoEntrega=?, respuesta=?, version=?, fechaEntrega=NOW(), estado='enviada' WHERE idTarea=? AND idEstudiante=?";
            $us = mysqli_prepare($con, $uSql);
            mysqli_stmt_bind_param($us, "ssiii", $archivoEntrega, $respuesta, $nuevaVersion, $idTarea, $idEstudiante);
        } else {
            $uSql = "UPDATE aula_entregas SET respuesta=?, version=?, fechaEntrega=NOW(), estado='enviada' WHERE idTarea=? AND idEstudiante=?";
            $us = mysqli_prepare($con, $uSql);
            mysqli_stmt_bind_param($us, "siii", $respuesta, $nuevaVersion, $idTarea, $idEstudiante);
        }
        $ok = mysqli_stmt_execute($us);
    } else {
        $iSql = "INSERT INTO aula_entregas (idTarea, idEstudiante, archivoEntrega, respuesta) VALUES (?,?,?,?)";
        $is = mysqli_prepare($con, $iSql);
        mysqli_stmt_bind_param($is, "iiss", $idTarea, $idEstudiante, $archivoEntrega, $respuesta);
        $ok = mysqli_stmt_execute($is);
    }
    
    return $ok;
}

function listarVersionesPorEntregaAula($idTarea, $idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM aula_versiones_entrega WHERE idTarea=? AND idEstudiante=? ORDER BY version DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idTarea, $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

// ═══════════════════════════════════════
// COMENTARIOS / FEEDBACK
// ═══════════════════════════════════════

function listarComentariosPorEntregaAula($idEntrega) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM aula_comentarios WHERE idEntrega=? ORDER BY fechaComentario ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEntrega);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

// ═══════════════════════════════════════
// NOTIFICACIONES
// ═══════════════════════════════════════

function insertarNotificacionAula($idUsuario, $tipoUsuario, $tipo, $titulo, $mensaje, $idReferencia = null, $tipoReferencia = null) {
    $con = obtenerConexion();
    $sql = "INSERT INTO aula_notificaciones (idUsuario, tipoUsuario, tipo, titulo, mensaje, idReferencia, tipoReferencia)
            VALUES (?,?,?,?,?,?,?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "issssis", $idUsuario, $tipoUsuario, $tipo, $titulo, $mensaje, $idReferencia, $tipoReferencia);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

function marcarTodasLeidasAula($idUsuario, $tipoUsuario) {
    $con = obtenerConexion();
    $sql = "UPDATE aula_notificaciones SET leida=1 WHERE idUsuario=? AND tipoUsuario=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "is", $idUsuario, $tipoUsuario);
    mysqli_stmt_execute($stmt);
    
}

// Tokens FCM de los estudiantes de un ciclo (para push en tiempo real)
function obtenerTokensFCMPorCicloAula($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM estudiantes
            WHERE idCiclo = ? AND fcm_token IS NOT NULL AND fcm_token != ''";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $tokens = [];
    while ($f = mysqli_fetch_assoc($res)) $tokens[] = $f['fcm_token'];
    
    return $tokens;
}

function notificarEstudiantesCicloAula($idCiclo, $tipo, $titulo, $mensaje, $idRef = null, $tipoRef = null) {
    $con = obtenerConexion();
    $sql = "SELECT idEstudiante FROM estudiantes WHERE idCiclo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($f = mysqli_fetch_assoc($res)) {
        insertarNotificacionAula($f['idEstudiante'], 'estudiante', $tipo, $titulo, $mensaje, $idRef, $tipoRef);
    }
    
}

// ═══════════════════════════════════════
// ANALYTICS - TRACKING DE USO
// ═══════════════════════════════════════

function registrarAnalytics($idUsuario, $tipoUsuario, $accion, $idModulo = null, $metadatos = null) {
    $con = obtenerConexion();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
    $metadatos_json = $metadatos ? json_encode($metadatos) : null;

    $sql = "INSERT INTO aula_analytics (idUsuario, tipoUsuario, accion, idModulo, ip, userAgent, metadatos)
            VALUES (?,?,?,?,?,?,?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "issiiss", $idUsuario, $tipoUsuario, $accion, $idModulo, $ip, $userAgent, $metadatos_json);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

function obtenerAnalyticsPorModulo($idModulo, $fechaInicio = null, $fechaFin = null) {
    $con = obtenerConexion();
    $fechaInicio = $fechaInicio ?? date('Y-m-d', strtotime('-30 days'));
    $fechaFin = $fechaFin ?? date('Y-m-d');

    $sql = "SELECT
                accion,
                COUNT(*) as total,
                COUNT(DISTINCT idUsuario) as usuarios,
                DATE(fechaCreacion) as fecha
            FROM aula_analytics
            WHERE idModulo=? AND DATE(fechaCreacion) BETWEEN ? AND ?
            GROUP BY accion, DATE(fechaCreacion)
            ORDER BY fecha DESC, total DESC";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $idModulo, $fechaInicio, $fechaFin);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $analytics = [];
    while ($f = mysqli_fetch_assoc($res)) {
        $analytics[] = $f;
    }
    
    return $analytics;
}

function obtenerResumenAnalytics($idModulo, $dias = 30) {
    $con = obtenerConexion();
    $fechaInicio = date('Y-m-d', strtotime("-$dias days"));

    $sql = "SELECT
                COUNT(*) as totalAcciones,
                COUNT(DISTINCT idUsuario) as usuariosUnicos,
                SUM(CASE WHEN accion='descargar' THEN 1 ELSE 0 END) as totalDescargas,
                SUM(CASE WHEN accion='subir' THEN 1 ELSE 0 END) as totalSubidas,
                SUM(CASE WHEN accion='ver' THEN 1 ELSE 0 END) as totalVistas,
                SUM(CASE WHEN accion='entrega' THEN 1 ELSE 0 END) as totalEntregas
            FROM aula_analytics
            WHERE idModulo=? AND DATE(fechaCreacion) >= ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "is", $idModulo, $fechaInicio);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $resumen = mysqli_fetch_assoc($res);
    
    return $resumen;
}

function obtenerTopArchivosPorDescargas($idModulo, $limite = 10) {
    $con = obtenerConexion();
    $sql = "SELECT
                a.idArchivo,
                a.nombreOriginal,
                COUNT(*) as descargas
            FROM aula_analytics aa
            JOIN aula_archivos a ON JSON_EXTRACT(aa.metadatos, '$.idArchivo') = a.idArchivo
            WHERE aa.idModulo=? AND aa.accion='descargar'
            GROUP BY a.idArchivo
            ORDER BY descargas DESC
            LIMIT ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idModulo, $limite);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $top = [];
    while ($f = mysqli_fetch_assoc($res)) {
        $top[] = $f;
    }
    
    return $top;
}

function obtenerTopTareasPorEntregas($idModulo, $limite = 10) {
    $con = obtenerConexion();
    $sql = "SELECT
                t.idTarea,
                t.titulo,
                COUNT(*) as entregas
            FROM aula_analytics aa
            JOIN aula_tareas t ON JSON_EXTRACT(aa.metadatos, '$.idTarea') = t.idTarea
            WHERE aa.idModulo=? AND aa.accion='entrega'
            GROUP BY t.idTarea
            ORDER BY entregas DESC
            LIMIT ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idModulo, $limite);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $top = [];
    while ($f = mysqli_fetch_assoc($res)) {
        $top[] = $f;
    }
    
    return $top;
}

// ═══════════════════════════════════════
// SESIONES VIVAS
// ═══════════════════════════════════════

function crearSesionViva($idModulo, $idProfesor, $titulo, $descripcion, $fechaSesion, $horaSesion, $enlaceReunion, $plataforma) {
    $con = obtenerConexion();
    $sql = "INSERT INTO aula_sesiones_vivas (idModulo, idProfesor, titulo, descripcion, fechaSesion, horaSesion, enlaceReunion, plataforma)
            VALUES (?,?,?,?,?,?,?,?)";
    $stmt = mysqli_prepare($con, $sql);
    $desc = $descripcion ?: null;
    $enlace = $enlaceReunion ?: null;
    $plat = $plataforma ?: null;
    mysqli_stmt_bind_param($stmt, "iissssss", $idModulo, $idProfesor, $titulo, $desc, $fechaSesion, $horaSesion, $enlace, $plat);
    $ok = mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($con);
    
    return $ok ? $id : false;
}

function listarSesionesPorModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT s.*, p.nombreProfesor,
                   (SELECT COUNT(*) FROM aula_asistencia_sesion a WHERE a.idSesion = s.idSesion) AS totalAsistentes
            FROM aula_sesiones_vivas s
            JOIN profesores p ON s.idProfesor = p.idProfesor
            WHERE s.idModulo = ?
            ORDER BY s.fechaSesion DESC, s.horaSesion DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

function listarSesionesPorProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT s.*, m.nombreModulo,
                   (SELECT COUNT(*) FROM aula_asistencia_sesion a WHERE a.idSesion = s.idSesion) AS totalAsistentes
            FROM aula_sesiones_vivas s
            JOIN modulos m ON s.idModulo = m.idModulo
            WHERE s.idProfesor = ?
            ORDER BY s.fechaSesion DESC, s.horaSesion DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

function obtenerSesionPorId($idSesion) {
    $con = obtenerConexion();
    $sql = "SELECT s.*, p.nombreProfesor, m.nombreModulo,
                   (SELECT COUNT(*) FROM aula_asistencia_sesion a WHERE a.idSesion = s.idSesion) AS totalAsistentes
            FROM aula_sesiones_vivas s
            JOIN profesores p ON s.idProfesor = p.idProfesor
            JOIN modulos m ON s.idModulo = m.idModulo
            WHERE s.idSesion = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idSesion);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($res);
    
    return $fila;
}

function actualizarSesionViva($idSesion, $titulo, $descripcion, $fechaSesion, $horaSesion, $enlaceReunion, $plataforma) {
    $con = obtenerConexion();
    $sql = "UPDATE aula_sesiones_vivas SET titulo=?, descripcion=?, fechaSesion=?, horaSesion=?, enlaceReunion=?, plataforma=? WHERE idSesion=?";
    $stmt = mysqli_prepare($con, $sql);
    $desc = $descripcion ?: null;
    $enlace = $enlaceReunion ?: null;
    $plat = $plataforma ?: null;
    mysqli_stmt_bind_param($stmt, "ssssssi", $titulo, $desc, $fechaSesion, $horaSesion, $enlace, $plat, $idSesion);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

function borrarSesionViva($idSesion) {
    $con = obtenerConexion();
    $sql = "DELETE FROM aula_sesiones_vivas WHERE idSesion=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idSesion);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

function listarAsistenciasPorSesion($idSesion) {
    $con = obtenerConexion();
    $sql = "SELECT a.*, e.nombreEstudiante, e.emailEstudiante
            FROM aula_asistencia_sesion a
            JOIN estudiantes e ON a.idEstudiante = e.idEstudiante
            WHERE a.idSesion = ?
            ORDER BY a.horaUnion ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idSesion);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

function contarAsistenciaPorSesion($idSesion) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) AS total FROM aula_asistencia_sesion WHERE idSesion=? AND presente=1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idSesion);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $f = mysqli_fetch_assoc($res);
    
    return intval($f['total']);
}

// ═══════════════════════════════════════
// FUNCIONES AUXILIARES
// ═══════════════════════════════════════

function obtenerEstudiantesPorModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT DISTINCT e.*
            FROM estudiantes e
            JOIN ciclo_profesor cp ON e.idCiclo = cp.idCiclo
            WHERE cp.idCiclo = (SELECT idCiclo FROM modulos WHERE idModulo = ?)
            AND e.idCiclo IS NOT NULL";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

function notificarEstudiantesPorModulo($idModulo, $tipo, $titulo, $mensaje, $idReferencia = null, $tipoReferencia = null) {
    $estudiantes = obtenerEstudiantesPorModulo($idModulo);
    foreach ($estudiantes as $est) {
        insertarNotificacionAula($est['idEstudiante'], 'estudiante', $tipo, $titulo, $mensaje, $idReferencia, $tipoReferencia);
    }
}

function validarFechaHoraSesion($fechaSesion, $horaSesion) {
    try {
        $ahora = new DateTime();
        $fechaHoraSesion = new DateTime($fechaSesion . ' ' . $horaSesion);

        if ($fechaHoraSesion <= $ahora) {
            return "La fecha y hora de la sesión debe ser en el futuro";
        }
        return null;
    } catch (Exception $e) {
        return "Formato de fecha u hora inválido";
    }
}

function validarEnlaceReunion($enlace) {
    if (empty($enlace)) {
        return null;
    }

    if (!filter_var($enlace, FILTER_VALIDATE_URL)) {
        return "El enlace debe ser una URL válida (ej: https://meet.google.com/...)";
    }
    return null;
}

// ═══════════════════════════════════════
// GESTIÓN DE RECURSOS · HELPERS DE PRESENTACIÓN
// ═══════════════════════════════════════

// Devuelve [clase CSS, icono FontAwesome] según la extensión del archivo
function iconoArchivoAula($ext) {
    $ext = strtolower($ext);
    $mapa = [
        'pdf'  => ['ico-pdf', 'fa-file-pdf'],
        'doc'  => ['ico-doc', 'fa-file-word'],   'docx' => ['ico-doc', 'fa-file-word'],
        'odt'  => ['ico-doc', 'fa-file-word'],    'rtf' => ['ico-doc', 'fa-file-word'],
        'xls'  => ['ico-xls', 'fa-file-excel'],   'xlsx' => ['ico-xls', 'fa-file-excel'],
        'ods'  => ['ico-xls', 'fa-file-excel'],   'csv' => ['ico-xls', 'fa-file-csv'],
        'ppt'  => ['ico-ppt', 'fa-file-powerpoint'], 'pptx' => ['ico-ppt', 'fa-file-powerpoint'],
        'odp'  => ['ico-ppt', 'fa-file-powerpoint'],
        'jpg'  => ['ico-img', 'fa-file-image'],   'jpeg' => ['ico-img', 'fa-file-image'],
        'png'  => ['ico-img', 'fa-file-image'],   'gif' => ['ico-img', 'fa-file-image'],
        'webp' => ['ico-img', 'fa-file-image'],   'svg' => ['ico-img', 'fa-file-image'],
        'txt'  => ['ico-txt', 'fa-file-lines'],
        'zip'  => ['ico-zip', 'fa-file-zipper'],  'rar' => ['ico-zip', 'fa-file-zipper'],
    ];
    return $mapa[$ext] ?? ['ico-otro', 'fa-file'];
}

// Indica si la extensión se puede previsualizar embebida en el navegador
function archivoPrevisualizableAula($ext) {
    return in_array(strtolower($ext), ['pdf','txt','csv','jpg','jpeg','png','gif','webp','svg']);
}

// Formatea un tamaño en bytes a una cadena legible
function formatearTamanioAula($bytes) {
    $bytes = (float)$bytes;
    if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576)    return round($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024)       return round($bytes / 1024, 0) . ' KB';
    return $bytes . ' B';
}

// ═══════════════════════════════════════
// GESTIÓN DE RECURSOS · SUBCARPETAS
// ═══════════════════════════════════════

// Lista las carpetas de un módulo filtrando por carpeta padre.
// $idPadre = null devuelve las carpetas de nivel raíz.
function listarCarpetasPorPadreAula($idModulo, $idPadre = null) {
    $con = obtenerConexion();
    if ($idPadre === null) {
        $sql = "SELECT c.*,
                       (SELECT COUNT(*) FROM aula_archivos a WHERE a.idCarpeta = c.idCarpeta AND a.eliminado = 0) AS totalArchivos,
                       (SELECT COUNT(*) FROM aula_carpetas sc WHERE sc.idPadre = c.idCarpeta AND sc.eliminado = 0) AS totalSubcarpetas
                FROM aula_carpetas c
                WHERE c.idModulo = ? AND c.idPadre IS NULL AND c.eliminado = 0
                ORDER BY c.fijado DESC, c.nombre ASC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idModulo);
    } else {
        $sql = "SELECT c.*,
                       (SELECT COUNT(*) FROM aula_archivos a WHERE a.idCarpeta = c.idCarpeta AND a.eliminado = 0) AS totalArchivos,
                       (SELECT COUNT(*) FROM aula_carpetas sc WHERE sc.idPadre = c.idCarpeta AND sc.eliminado = 0) AS totalSubcarpetas
                FROM aula_carpetas c
                WHERE c.idModulo = ? AND c.idPadre = ? AND c.eliminado = 0
                ORDER BY c.fijado DESC, c.nombre ASC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $idModulo, $idPadre);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

// Devuelve la ruta de migas (breadcrumb) de una carpeta hasta la raíz
function obtenerRutaCarpetaAula($idCarpeta) {
    $ruta = [];
    $actual = $idCarpeta;
    $guard = 0; // evita bucles infinitos por datos corruptos
    while ($actual && $guard < 50) {
        $c = obtenerCarpetaAulaPorId($actual);
        if (!$c) break;
        array_unshift($ruta, $c);
        $actual = $c['idPadre'];
        $guard++;
    }
    return $ruta;
}

// ═══════════════════════════════════════
// GESTIÓN DE RECURSOS · RENOMBRAR
// ═══════════════════════════════════════

function renombrarArchivoAula($idArchivo, $nuevoNombre) {
    $con = obtenerConexion();
    $sql = "UPDATE aula_archivos SET nombreOriginal=? WHERE idArchivo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nuevoNombre, $idArchivo);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

// ═══════════════════════════════════════
// GESTIÓN DE RECURSOS · VERSIONES DE ARCHIVO
// ═══════════════════════════════════════

// Sustituye el fichero físico de un recurso guardando la versión anterior
// en aula_archivo_versiones e incrementando el número de versión.
function actualizarArchivoConVersionAula($idArchivo, $nuevoNombreArchivo, $nuevoNombreOriginal, $nuevaExtension, $nuevoTamanio, $idProfesor) {
    $archivo = obtenerArchivoPorId($idArchivo);
    if (!$archivo) return false;
    $con = obtenerConexion();
    // 1. Archivar la versión actual
    $vSql = "INSERT INTO aula_archivo_versiones (idArchivo, nombreArchivo, nombreOriginal, extension, tamanio, version, idProfesor)
             VALUES (?,?,?,?,?,?,?)";
    $vs = mysqli_prepare($con, $vSql);
    mysqli_stmt_bind_param($vs, "isssiii",
        $idArchivo, $archivo['nombreArchivo'], $archivo['nombreOriginal'],
        $archivo['extension'], $archivo['tamanio'], $archivo['version'], $idProfesor);
    mysqli_stmt_execute($vs);
    // 2. Actualizar el registro principal con el nuevo fichero
    $nuevaVersion = intval($archivo['version']) + 1;
    $uSql = "UPDATE aula_archivos
             SET nombreArchivo=?, nombreOriginal=?, extension=?, tamanio=?, version=?, fechaSubida=NOW()
             WHERE idArchivo=?";
    $us = mysqli_prepare($con, $uSql);
    mysqli_stmt_bind_param($us, "sssiii",
        $nuevoNombreArchivo, $nuevoNombreOriginal, $nuevaExtension, $nuevoTamanio, $nuevaVersion, $idArchivo);
    $ok = mysqli_stmt_execute($us);
    
    return $ok ? $nuevaVersion : false;
}

function listarVersionesArchivoAula($idArchivo) {
    $con = obtenerConexion();
    $sql = "SELECT v.*, p.nombreProfesor
            FROM aula_archivo_versiones v
            JOIN profesores p ON v.idProfesor = p.idProfesor
            WHERE v.idArchivo = ?
            ORDER BY v.version DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArchivo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

// ═══════════════════════════════════════
// GESTIÓN DE RECURSOS · FAVORITOS (estudiantes)
// ═══════════════════════════════════════

function marcarFavoritoAula($idEstudiante, $idArchivo) {
    $con = obtenerConexion();
    $sql = "INSERT IGNORE INTO aula_favoritos (idEstudiante, idArchivo) VALUES (?,?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idArchivo);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

function quitarFavoritoAula($idEstudiante, $idArchivo) {
    $con = obtenerConexion();
    $sql = "DELETE FROM aula_favoritos WHERE idEstudiante=? AND idArchivo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idArchivo);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

function esFavoritoAula($idEstudiante, $idArchivo) {
    $con = obtenerConexion();
    $sql = "SELECT 1 FROM aula_favoritos WHERE idEstudiante=? AND idArchivo=? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idArchivo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($res) > 0;
    
    return $existe;
}

function listarFavoritosEstudianteAula($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT a.*, p.nombreProfesor, m.nombreModulo, f.fechaMarcado
            FROM aula_favoritos f
            JOIN aula_archivos a ON f.idArchivo = a.idArchivo
            JOIN profesores p ON a.idProfesor = p.idProfesor
            JOIN modulos m ON a.idModulo = m.idModulo
            WHERE f.idEstudiante = ? AND a.eliminado = 0
            ORDER BY f.fechaMarcado DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

// ═══════════════════════════════════════
// GESTIÓN DE RECURSOS · ACCESOS (lectura y estadísticas)
// ═══════════════════════════════════════

function registrarAccesoArchivoAula($idArchivo, $idEstudiante, $tipo = 'vista') {
    $con = obtenerConexion();
    // Guardar la fecha/hora exacta en horario de España (no depende del huso del servidor)
    $fecha = date('Y-m-d H:i:s');
    $sql = "INSERT INTO aula_archivo_accesos (idArchivo, idEstudiante, tipo, fechaAcceso) VALUES (?,?,?,?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iiss", $idArchivo, $idEstudiante, $tipo, $fecha);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

// Recursos más consultados de un módulo (#10)
function obtenerRecursosMasConsultadosAula($idModulo, $limite = 10) {
    $con = obtenerConexion();
    // Se cuentan ESTUDIANTES ÚNICOS (no cada acceso): si un alumno ve/descarga
    // el mismo archivo varias veces, cuenta como 1.
    $sql = "SELECT a.idArchivo, a.nombreOriginal, a.extension,
                   COUNT(DISTINCT ac.idEstudiante) AS totalAccesos,
                   COUNT(DISTINCT CASE WHEN ac.tipo='vista'    THEN ac.idEstudiante END) AS vistas,
                   COUNT(DISTINCT CASE WHEN ac.tipo='descarga' THEN ac.idEstudiante END) AS descargas,
                   MAX(ac.fechaAcceso) AS ultimoAcceso
            FROM aula_archivos a
            LEFT JOIN aula_archivo_accesos ac ON ac.idArchivo = a.idArchivo
            WHERE a.idModulo = ? AND a.eliminado = 0
            GROUP BY a.idArchivo
            ORDER BY vistas DESC, descargas DESC
            LIMIT ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idModulo, $limite);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

// Control de lectura/descarga: qué estudiantes han visto y/o descargado un archivo (#11).
// Devuelve todos los estudiantes del ciclo con la última fecha de vista y de descarga.
function obtenerControlLecturaArchivoAula($idArchivo, $idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, e.emailEstudiante,
                   MAX(CASE WHEN ac.tipo='vista'    THEN ac.fechaAcceso END) AS fechaVista,
                   MAX(CASE WHEN ac.tipo='descarga' THEN ac.fechaAcceso END) AS fechaDescarga
            FROM estudiantes e
            LEFT JOIN aula_archivo_accesos ac
                   ON ac.idEstudiante = e.idEstudiante AND ac.idArchivo = ?
            WHERE e.idCiclo = ?
            GROUP BY e.idEstudiante
            ORDER BY (MAX(CASE WHEN ac.tipo='vista' THEN ac.fechaAcceso END) IS NOT NULL) DESC, e.nombreEstudiante ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idArchivo, $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    
    return $lista;
}

// ═══════════════════════════════════════
// GESTIÓN DE RECURSOS · PAPELERA DE RECICLAJE
// ═══════════════════════════════════════

function listarPapeleraModuloAula($idModulo) {
    $con = obtenerConexion();
    $archivos = [];
    $sqlA = "SELECT a.*, p.nombreProfesor, c.nombre AS nombreCarpeta
             FROM aula_archivos a
             JOIN profesores p ON a.idProfesor = p.idProfesor
             LEFT JOIN aula_carpetas c ON a.idCarpeta = c.idCarpeta
             WHERE a.idModulo = ? AND a.eliminado = 1
             ORDER BY a.fechaEliminacion DESC";
    $sa = mysqli_prepare($con, $sqlA);
    mysqli_stmt_bind_param($sa, "i", $idModulo);
    mysqli_stmt_execute($sa);
    $ra = mysqli_stmt_get_result($sa);
    while ($f = mysqli_fetch_assoc($ra)) $archivos[] = $f;

    $carpetas = [];
    $sqlC = "SELECT * FROM aula_carpetas WHERE idModulo = ? AND eliminado = 1 ORDER BY fechaEliminacion DESC";
    $sc = mysqli_prepare($con, $sqlC);
    mysqli_stmt_bind_param($sc, "i", $idModulo);
    mysqli_stmt_execute($sc);
    $rc = mysqli_stmt_get_result($sc);
    while ($f = mysqli_fetch_assoc($rc)) $carpetas[] = $f;

    
    return ['archivos' => $archivos, 'carpetas' => $carpetas];
}

function restaurarArchivoAula($idArchivo) {
    $con = obtenerConexion();
    $sql = "UPDATE aula_archivos SET eliminado=0, fechaEliminacion=NULL WHERE idArchivo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArchivo);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

// Restaura la carpeta y todo su árbol (subcarpetas + archivos)
function restaurarCarpetaAula($idCarpeta) {
    $ids = obtenerArbolCarpetaAula($idCarpeta);
    if (empty($ids)) return false;
    $con = obtenerConexion();
    $in = implode(',', array_map('intval', $ids));
    $ok = mysqli_query($con, "UPDATE aula_carpetas SET eliminado=0, fechaEliminacion=NULL WHERE idCarpeta IN ($in)");
    mysqli_query($con, "UPDATE aula_archivos SET eliminado=0, fechaEliminacion=NULL WHERE idCarpeta IN ($in)");
    
    return $ok;
}

// Borrado definitivo de un archivo: elimina disco + BD (versiones por CASCADE)
function eliminarDefinitivoArchivoAula($idArchivo) {
    $archivo = obtenerArchivoPorId($idArchivo);
    if (!$archivo) return false;
    $dir = __DIR__ . "/../public/uploads/aula/archivos/";
    $ruta = $dir . $archivo['nombreArchivo'];
    if (file_exists($ruta)) unlink($ruta);
    // Borrar también los ficheros físicos de las versiones antiguas
    foreach (listarVersionesArchivoAula($idArchivo) as $v) {
        $rv = $dir . $v['nombreArchivo'];
        if (file_exists($rv)) unlink($rv);
    }
    $con = obtenerConexion();
    $sql = "DELETE FROM aula_archivos WHERE idArchivo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArchivo);
    $ok = mysqli_stmt_execute($stmt);
    
    return $ok;
}

function eliminarDefinitivoCarpetaAula($idCarpeta) {
    $con = obtenerConexion();
    $sql = "DELETE FROM aula_carpetas WHERE idCarpeta=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCarpeta);
    $ok = mysqli_stmt_execute($stmt);

    return $ok;
}

// Eliminación DEFINITIVA y recursiva de una carpeta: borra los ficheros físicos
// (y versiones) de todos los archivos del árbol y luego las carpetas (las
// subcarpetas caen por ON DELETE CASCADE sobre idPadre).
function eliminarDefinitivoCarpetaRecursivoAula($idCarpeta) {
    $ids = obtenerArbolCarpetaAula($idCarpeta);
    if (empty($ids)) $ids = [intval($idCarpeta)];
    $con = obtenerConexion();
    $in  = implode(',', array_map('intval', $ids));
    $res = mysqli_query($con, "SELECT idArchivo FROM aula_archivos WHERE idCarpeta IN ($in)");
    if ($res) {
        while ($f = mysqli_fetch_assoc($res)) eliminarDefinitivoArchivoAula($f['idArchivo']);
    }
    return eliminarDefinitivoCarpetaAula($idCarpeta);
}

// Vacía de la papelera los elementos eliminados hace más de $dias días
function purgarPapeleraAntiguaAula($dias = 30) {
    $limite = date('Y-m-d H:i:s', strtotime("-$dias days"));
    $con = obtenerConexion();
    $sql = "SELECT idArchivo FROM aula_archivos WHERE eliminado=1 AND fechaEliminacion < ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $limite);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $ids = [];
    while ($f = mysqli_fetch_assoc($res)) $ids[] = $f['idArchivo'];
    
    foreach ($ids as $id) eliminarDefinitivoArchivoAula($id);
    return count($ids);
}

// ═══════════════════════════════════════
// GESTIÓN DE RECURSOS · ALMACENAMIENTO POR CICLO
// ═══════════════════════════════════════

// Bytes usados por todos los recursos (no eliminados) de un ciclo
function obtenerUsoAlmacenamientoCicloAula($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT COALESCE(SUM(a.tamanio),0) AS usado
            FROM aula_archivos a
            JOIN modulos m ON a.idModulo = m.idModulo
            WHERE m.idCiclo = ? AND a.eliminado = 0";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $f = mysqli_fetch_assoc($res);
    
    return floatval($f['usado']);
}

function obtenerLimiteAlmacenamientoCicloAula($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT limiteBytes FROM aula_almacenamiento_ciclo WHERE idCiclo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $f = mysqli_fetch_assoc($res);
    
    return $f ? floatval($f['limiteBytes']) : 5368709120; // 5 GB por defecto
}

