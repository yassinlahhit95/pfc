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
    
    // Security check: verify user has access to the module of this reto
    $idModulo = (int)$reto['idModulo'];
    $autorizado = false;
    if ($type === 'director' || $type === 'secretaria') {
        $autorizado = true;
    } elseif ($type === 'profesor') {
        $misModulos = listarModulosDeProfesor($uid);
        $autorizado = in_array($idModulo, array_column($misModulos, 'idModulo'), true);
    } elseif ($type === 'estudiante') {
        $modulo = obtenerModuloPorId($idModulo);
        if ($modulo) {
            $st2 = mysqli_prepare($con, 'SELECT idCiclo FROM estudiantes WHERE idEstudiante = ?');
            mysqli_stmt_bind_param($st2, 'i', $uid);
            mysqli_stmt_execute($st2);
            $est = mysqli_fetch_assoc(mysqli_stmt_get_result($st2));
            if ($est && (int)$est['idCiclo'] === (int)$modulo['idCiclo']) {
                $autorizado = true;
            }
        }
    }
    
    if (!$autorizado) v1Error('You do not have permission to access this file.', 403, 'forbidden');

    if ($kind === 'reto') {
        $nombreArchivo = $reto['archivoAdjunto'];
        if (!$nombreArchivo) v1Error('Este reto no tiene un archivo adjunto.', 404, 'not_found');
        
        $ext = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        $mimes = ['pdf' => 'application/pdf', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'txt' => 'text/plain'];
        $mime = $mimes[$ext] ?? 'application/octet-stream';
        
        $uploadDir = realpath(__DIR__ . "/../../public/uploads/aula/tareas");
        $candidato = $uploadDir !== false ? $uploadDir . DIRECTORY_SEPARATOR . $nombreArchivo : false;
        $ruta = $candidato !== false ? realpath($candidato) : false;
        
        $normDir = $uploadDir ? str_replace('\\', '/', $uploadDir) . '/' : '';
        $normRuta = $ruta ? str_replace('\\', '/', $ruta) : '';
        
        if (!$uploadDir || ($ruta !== false && stripos($normRuta, $normDir) !== 0)) {
            http_response_code(404);
            exit('El fichero ya no existe.');
        }
        
        servirArchivo($ruta !== false ? $ruta : $candidato, "aula/tareas/$nombreArchivo", $nombreArchivo, $mime, false);
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
                    JOIN estudiantes e ON m.idCiclo = e.idCiclo
                    JOIN profesores p ON r.idProfesor = p.idProfesor
                    WHERE e.idEstudiante = ? AND r.publicado = 1
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

if ($method === 'POST') {
    if ($action === 'submit') {
        if ($type !== 'estudiante') {
            v1Error('Only estudiantes can submit a reto.', 403, 'forbidden');
        }
        
        $idReto = (int)($_POST['idReto'] ?? 0);
        $respuesta = trim((string)($_POST['respuesta'] ?? ''));
        if ($idReto <= 0) v1Error('idReto is required.', 400, 'validation');
        
        $reto = obtenerRetoPorId($idReto);
        if (!$reto || !$reto['publicado']) v1Error('The reto is not available.', 404, 'not_found');
        
        // Comprueba que el estudiante pertenece al ciclo
        $idModulo = (int)$reto['idModulo'];
        $modulo = obtenerModuloPorId($idModulo);
        if (!$modulo) v1Error('Module not found.', 404, 'not_found');
        
        $st2 = mysqli_prepare($con, 'SELECT idCiclo FROM estudiantes WHERE idEstudiante = ?');
        mysqli_stmt_bind_param($st2, 'i', $uid);
        mysqli_stmt_execute($st2);
        $est = mysqli_fetch_assoc(mysqli_stmt_get_result($st2));
        if (!$est || (int)$est['idCiclo'] !== (int)$modulo['idCiclo']) {
            v1Error('You do not have access to this reto.', 403, 'forbidden');
        }
        
        if ($respuesta === '' && empty($_FILES['archivoEntrega']['name'])) {
            v1Error('Write a response or attach a file.', 400, 'validation');
        }
        
        $archivoEntrega = null;
        if (!empty($_FILES['archivoEntrega']['name'])) {
            $file = $_FILES['archivoEntrega'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $mimeAllowed = [
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/plain',
                'application/octet-stream',
            ];
            $mimeReal = @mime_content_type($file['tmp_name']);
            if (!in_array($ext, ['pdf', 'docx', 'txt'], true) || ($mimeReal && !in_array($mimeReal, $mimeAllowed, true))) {
                v1Error('Only PDF, DOCX or TXT files are allowed.', 400, 'validation');
            }
            if ($file['size'] > 20 * 1024 * 1024) {
                v1Error('File too large (max 20 MB).', 400, 'validation');
            }
            require_once __DIR__ . '/../../include/R2Client.php';
            $nombreArchivo = bin2hex(random_bytes(12)) . '.' . $ext;
            $mimeReal = $mimeReal ?: 'application/octet-stream';
            $bytes = file_get_contents($file['tmp_name']);
            $subioOk = $bytes !== false && R2Client::putObject('aula/entregas/' . $nombreArchivo, $bytes, $mimeReal);
            @unlink($file['tmp_name']);
            if (!$subioOk) v1Error('Could not save the file on the server.', 500, 'error');
            $archivoEntrega = $nombreArchivo;
        }
        
        // Reuse enviarEntregaAula but for reto. Wait, the DB model for reto submissions?
        // Retos share the same schema for entregas as Tareas.
        if (!enviarEntregaAula($idReto, $uid, $archivoEntrega, $respuesta)) {
            v1Error('Could not save the submission.', 500, 'error');
        }
        
        // Notify professor
        insertarNotificacionAula((int)$reto['idProfesor'], 'profesor', 'entrega_enviada',
            'Nueva entrega de reto: ' . $reto['titulo'], 'Un estudiante ha enviado su reto.', $idReto, 'reto');
            
        $fh = __DIR__ . '/../../controladores/firebase/firebase_helper.php';
        if (file_exists($fh)) {
            require_once $fh;
            $fcmToken = obtenerTokenUsuario((int)$reto['idProfesor'], 'profesor');
            if ($fcmToken) {
                enviarNotificacionFirebase($fcmToken, 'Nueva entrega de reto: ' . $reto['titulo'],
                    'Un estudiante ha enviado su reto.', 'entrega_enviada', ['idReto' => $idReto]);
            }
        }
        
        v1Ok(['message' => 'Submission sent.'], 201);
    }
    v1Error('Acción no válida.', 400, 'validation');
}

v1Error('Método no permitido.', 405, 'method_not_allowed');
