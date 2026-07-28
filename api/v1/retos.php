<?php
declare(strict_types=1);

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/retos.php';
require_once __DIR__ . '/../../modelos/modulos.php';
require_once __DIR__ . '/../../include/FileServer.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === 'GET' && $action === 'download') {
    $token = (string)($_GET['token'] ?? '');
    if (strlen($token) !== 64 || !ctype_xdigit($token)) {
        v1Error('Invalid or missing token.', 401, 'unauthenticated');
    }
    $con = obtenerConexion();
    $st = mysqli_prepare($con, 'SELECT user_type, user_id FROM api_tokens WHERE token = ? AND expires_at > NOW() LIMIT 1');
    mysqli_stmt_bind_param($st, 's', $token);
    mysqli_stmt_execute($st);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
    if (!$row) v1Error('Token expired or not found.', 401, 'token_expired');
    
    $type = $row['user_type'];
    $uid  = (int)$row['user_id'];

    $kind = (string)($_GET['kind'] ?? 'reto');
    $idReto = (int)($_GET['id'] ?? 0);
    
    $reto = obtenerRetoPorId($idReto);
    if (!$reto) v1Error('Reto no encontrado.', 404, 'not_found');

    if ($kind === 'reto') {
        $nombreArchivo = $reto['archivoAdjunto'];
        if (!$nombreArchivo) v1Error('Este reto no tiene un archivo adjunto.', 404, 'not_found');
        $uploadDir = realpath(__DIR__ . "/../../public/uploads/aula/tareas");
        $filePath = "$uploadDir/$nombreArchivo";
        FileServer::serveFile($filePath, $nombreArchivo);
    }
    
    v1Error('Acción de descarga no válida.', 400, 'validation');
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;
$postActions = ['submit'];

if ($method === 'GET') {
    if ($action === 'list') {
        if ($type === 'estudiante') {
            $con = obtenerConexion();
            $sql = "SELECT r.*, m.nombreModulo, p.nombreProfesor 
                    FROM aula_retos r
                    JOIN modulos m ON r.idModulo = m.idModulo
                    JOIN modulos_estudiantes me ON m.idModulo = me.idModulo
                    JOIN profesores p ON r.idProfesor = p.idProfesor
                    WHERE me.idEstudiante = ? AND r.publicado = 1
                    ORDER BY r.fechaCreacion DESC";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "i", $uid);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            
            $retos = [];
            while ($fila = mysqli_fetch_assoc($res)) {
                $idReto = (int)$fila['idReto'];
                $entrega = obtenerEntregaReto($idReto, $uid);
                if ($entrega) {
                    $fila['entregado'] = true;
                    $fila['nota'] = $entrega['nota'];
                    $fila['fechaEntrega'] = $entrega['fechaEntrega'];
                } else {
                    $fila['entregado'] = false;
                }
                $retos[] = $fila;
            }
            v1Ok(['retos' => $retos]);
        } elseif ($type === 'profesor') {
            $con = obtenerConexion();
            $sql = "SELECT r.*, m.nombreModulo, p.nombreProfesor 
                    FROM aula_retos r
                    JOIN modulos m ON r.idModulo = m.idModulo
                    JOIN profesores p ON r.idProfesor = p.idProfesor
                    WHERE r.idProfesor = ?
                    ORDER BY r.fechaCreacion DESC";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "i", $uid);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            
            $retos = [];
            while ($fila = mysqli_fetch_assoc($res)) {
                $retos[] = $fila;
            }
            v1Ok(['retos' => $retos]);
        }
        
        v1Error('No disponible para este rol.', 403, 'forbidden');
    }
    
    v1Error('Acción no válida.', 400, 'validation');
}

v1Error('Método no permitido.', 405, 'method_not_allowed');
