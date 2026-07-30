<?php
declare(strict_types=1);

// Aula digital — mobile scope: browse modules → folders/files (download) →
// tareas, submit/view entregas, grade with comments, publish toggle,
// sesiones vivas (view + create), favoritos. Folder/file MANAGEMENT
// (create/rename/move/version/trash) is intentionally NOT exposed here —
// that stays desktop-only per the mobile scoping decision (drag/drop
// file-manager UX doesn't translate to a phone). Action-param dispatched,
// like chat.php.
//
// GET  /api/v1/classroom.php?action=modules
// GET  /api/v1/classroom.php?action=folders&idModulo=
// GET  /api/v1/classroom.php?action=files&idModulo=&idCarpeta=
// GET  /api/v1/classroom.php?action=tasks&idModulo=
// GET  /api/v1/classroom.php?action=download&id=&token=   (token via query — see note below)
//   &kind=entrega|correccion — id becomes idTarea, serves the estudiante's own
//   submitted file / the profesor's corrected file back, instead of a module resource
//   &kind=tarea — id becomes idTarea, serves the profesor's own attachment on the
//   task itself (aula_tareas.archivoAdjunto), visible to anyone with module access
// GET  /api/v1/classroom.php?action=submission&idTarea=          (estudiante's own entrega)
// GET  /api/v1/classroom.php?action=submissions&idTarea=         (profesor: full roster)
// POST /api/v1/classroom.php?action=submit  (multipart: idTarea, respuesta?, archivoEntrega?)
// POST /api/v1/classroom.php?action=grade   {idEntrega, nota, comentario?}
// POST /api/v1/classroom.php?action=publish {idTarea, publicado}
// GET  /api/v1/classroom.php?action=sessions&idModulo=
// POST /api/v1/classroom.php?action=create-session {idModulo, titulo, descripcion?, fechaSesion, horaSesion, enlaceReunion?, plataforma?}
// GET  /api/v1/classroom.php?action=favorites                    (estudiante)
// POST /api/v1/classroom.php?action=favorite {idArchivo, favorito: bool}

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/aula.php';
require_once __DIR__ . '/../../modelos/modulos.php';
require_once __DIR__ . '/../../include/FileServer.php';

$method = $_SERVER['REQUEST_METHOD'];
$postActions = ['submit', 'grade', 'publish', 'create-session', 'favorite'];
$action = $_GET['action'] ?? '';

if ($method === 'POST' && !in_array($action, $postActions, true)) {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}
if ($method === 'GET' && in_array($action, $postActions, true)) {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}
if ($method !== 'GET' && $method !== 'POST') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

// The download action can't carry an Authorization header (it's opened by
// an external viewer/browser, not fetched via the app's own HTTP client),
// so it accepts the same 64-hex Bearer token as a query param instead.
// Same api_tokens table/expiry/rate-limit — not a weaker auth mechanism,
// just a different transport for this one action.
if ($action === 'download') {
    $token = (string)($_GET['token'] ?? '');
    if (strlen($token) !== 64 || !ctype_xdigit($token)) {
        v1Error('Invalid or missing token.', 401, 'unauthenticated');
    }
    $con = obtenerConexion();
    $st = mysqli_prepare($con,
        'SELECT user_type, user_id FROM api_tokens WHERE token = ? AND expires_at > NOW() LIMIT 1');
    mysqli_stmt_bind_param($st, 's', $token);
    mysqli_stmt_execute($st);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
    if (!$row) v1Error('Token expired or not found.', 401, 'token_expired');
    if (!RateLimiter::allow($con, 'apiv1_' . substr($token, 0, 8), 120, 60, 300)) {
        v1Error('Rate limit exceeded.', 429, 'rate_limited');
    }
    $type = $row['user_type'];
    $uid  = (int)$row['user_id'];

    // kind=entrega|correccion: the student's own submitted file, or the file
    // the profesor sent back with the correction — a separate storage path
    // (public/uploads/aula/entregas|correcciones) from the module's own
    // resources (aula/archivos, handled by the default branch below). Only
    // the owning estudiante can fetch either one via mobile for now — a
    // profesor/director downloading a specific student's submission isn't
    // wired up on the mobile side yet (their own review screen has no
    // download link either), so it's out of scope here.
    $kind = (string)($_GET['kind'] ?? 'recurso');
    if ($kind === 'entrega' || $kind === 'correccion') {
        if (!in_array($type, ['estudiante', 'profesor', 'director', 'secretaria'], true)) {
            v1Error('Role not permitted.', 403, 'forbidden');
        }
        
        $idTarea = (int)($_GET['id'] ?? 0);
        if ($idTarea <= 0) v1Error('id is required.', 400, 'validation');
        
        $targetUid = $uid;
        if ($type !== 'estudiante') {
            $targetUid = (int)($_GET['idEstudiante'] ?? 0);
            if ($targetUid <= 0) v1Error('idEstudiante is required for staff.', 400, 'validation');
        }
        
        $entrega = obtenerEntregaAula($idTarea, $targetUid);
        if (!$entrega) v1Error('Submission not found.', 404, 'not_found');
        
        if ($type === 'profesor') {
            $tarea = obtenerTareaPorIdAula($idTarea);
            if (!$tarea || !classroomModuloAutorizado('profesor', $uid, (int)$tarea['idModulo'])) {
                v1Error('You do not teach this module.', 403, 'forbidden');
            }
        }

        $campo = $kind === 'entrega' ? 'archivoEntrega' : 'archivoCorreccion';
        $nombreArchivo = $entrega[$campo] ?? null;
        if (!$nombreArchivo) v1Error('No file for this submission.', 404, 'not_found');

        $carpeta = $kind === 'entrega' ? 'entregas' : 'correcciones';
        $ext  = strtolower((string)pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        $mimesEntrega = ['pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain'];
        $mime = $mimesEntrega[$ext] ?? 'application/octet-stream';

        $uploadDir = realpath(__DIR__ . "/../../public/uploads/aula/$carpeta");
        $candidato = $uploadDir !== false ? $uploadDir . DIRECTORY_SEPARATOR . $nombreArchivo : false;
        $ruta      = $candidato !== false ? realpath($candidato) : false;

        $normDir  = $uploadDir ? str_replace('\\', '/', $uploadDir) . '/' : '';
        $normRuta = $ruta ? str_replace('\\', '/', $ruta) : '';

        if (!$uploadDir || ($ruta !== false && stripos($normRuta, $normDir) !== 0)) {
            http_response_code(404);
            exit('El fichero ya no existe.');
        }
        servirArchivo($ruta !== false ? $ruta : $candidato, "aula/$carpeta/$nombreArchivo", $nombreArchivo, $mime, false);
    }

    // kind=tarea: the file the profesor attached to the task itself (e.g.
    // instructions/material), as opposed to a student's own entrega/correction
    // above — visible to anyone with access to the task's module (student,
    // profesor, director, secretaria), not just the owning estudiante.
    if ($kind === 'tarea') {
        $idTarea = (int)($_GET['id'] ?? 0);
        if ($idTarea <= 0) v1Error('id is required.', 400, 'validation');
        $tarea = obtenerTareaPorIdAula($idTarea);
        if (!$tarea || !classroomModuloAutorizado($type, $uid, (int)$tarea['idModulo'])) {
            v1Error('Task not found.', 404, 'not_found');
        }
        $nombreArchivo = $tarea['archivoAdjunto'] ?? null;
        if (!$nombreArchivo) v1Error('No attachment for this task.', 404, 'not_found');

        $ext  = strtolower((string)pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        $mimesTarea = ['pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain'];
        $mime = $mimesTarea[$ext] ?? 'application/octet-stream';

        $uploadDir = realpath(__DIR__ . "/../../public/uploads/aula/tareas");
        $candidato = $uploadDir !== false ? $uploadDir . DIRECTORY_SEPARATOR . $nombreArchivo : false;
        $ruta      = $candidato !== false ? realpath($candidato) : false;

        $normDir  = $uploadDir ? str_replace('\\', '/', $uploadDir) . '/' : '';
        $normRuta = $ruta ? str_replace('\\', '/', $ruta) : '';

        if (!$uploadDir || ($ruta !== false && stripos($normRuta, $normDir) !== 0)) {
            http_response_code(404);
            exit('El fichero ya no existe.');
        }
        servirArchivo($ruta !== false ? $ruta : $candidato, "aula/tareas/$nombreArchivo", $nombreArchivo, $mime, false);
    }

    $idArchivo = (int)($_GET['id'] ?? 0);
    if ($idArchivo <= 0) v1Error('id is required.', 400, 'validation');
    $archivo = obtenerArchivoPorId($idArchivo);
    if (!$archivo || $archivo['eliminado']) v1Error('File not found.', 404, 'not_found');

    $modulo  = obtenerModuloPorId((int)$archivo['idModulo']);
    $idCiclo = $modulo['idCiclo'] ?? 0;

    $autorizado = false;
    $esEstudiante = false;
    if ($type === 'director' || $type === 'secretaria') {
        $autorizado = true;
    } elseif ($type === 'profesor') {
        $misModulos = listarModulosDeProfesor($uid);
        $autorizado = in_array((int)$archivo['idModulo'], array_column($misModulos, 'idModulo'), true);
    } elseif ($type === 'estudiante') {
        $st2 = mysqli_prepare($con, 'SELECT idCiclo FROM estudiantes WHERE idEstudiante = ?');
        mysqli_stmt_bind_param($st2, 'i', $uid);
        mysqli_stmt_execute($st2);
        $est = mysqli_fetch_assoc(mysqli_stmt_get_result($st2));
        $autorizado = $est && (int)$est['idCiclo'] === (int)$idCiclo;
        $esEstudiante = $autorizado;
    }
    if (!$autorizado) v1Error('You do not have access to this file.', 403, 'forbidden');

    if ($esEstudiante) {
        registrarAccesoArchivoAula($idArchivo, $uid, 'descarga');
    }

    $mimes = [
        'pdf' => 'application/pdf', 'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'txt' => 'text/plain', 'csv' => 'text/csv', 'rtf' => 'application/rtf',
        'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
        'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
        'zip' => 'application/zip', 'rar' => 'application/vnd.rar',
    ];
    $ext  = strtolower($archivo['extension']);
    $mime = $mimes[$ext] ?? 'application/octet-stream';

    $uploadDir = realpath(__DIR__ . '/../../public/uploads/aula/archivos');
    $candidato = $uploadDir !== false ? $uploadDir . DIRECTORY_SEPARATOR . $archivo['nombreArchivo'] : false;
    $ruta      = $candidato !== false ? realpath($candidato) : false;

    $normDir  = $uploadDir ? str_replace('\\', '/', $uploadDir) . '/' : '';
    $normRuta = $ruta ? str_replace('\\', '/', $ruta) : '';

    if (!$uploadDir || ($ruta !== false && stripos($normRuta, $normDir) !== 0)) {
        http_response_code(404);
        exit('El fichero ya no existe.');
    }

    // Attachment (not inline) — a mobile "download" action should always
    // save/hand off the file, not try to render it in-browser.
    servirArchivo($ruta !== false ? $ruta : $candidato, 'aula/archivos/' . $archivo['nombreArchivo'], $archivo['nombreOriginal'], $mime, false);
}

// ── Every other action uses the normal Bearer-header auth ─────────────────────
$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

function classroomModuloAutorizado(string $type, int $uid, int $idModulo): bool {
    if ($type === 'director' || $type === 'secretaria') return true;
    if ($type === 'profesor') {
        $misModulos = listarModulosDeProfesor($uid);
        return in_array($idModulo, array_column($misModulos, 'idModulo'), true);
    }
    if ($type === 'estudiante') {
        $modulo = obtenerModuloPorId($idModulo);
        if (!$modulo) return false;
        $con = obtenerConexion();
        $st = mysqli_prepare($con, 'SELECT idCiclo FROM estudiantes WHERE idEstudiante = ?');
        mysqli_stmt_bind_param($st, 'i', $uid);
        mysqli_stmt_execute($st);
        $est = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
        return $est && (int)$est['idCiclo'] === (int)$modulo['idCiclo'];
    }
    return false;
}

if ($action === 'modules') {
    $modules = match ($type) {
        'estudiante' => (function () use ($uid) {
            $con = obtenerConexion();
            $st = mysqli_prepare($con, 'SELECT idCiclo FROM estudiantes WHERE idEstudiante = ?');
            mysqli_stmt_bind_param($st, 'i', $uid);
            mysqli_stmt_execute($st);
            $est = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
            return $est && $est['idCiclo'] ? listarModulosDeCicloConNombre((int)$est['idCiclo']) : [];
        })(),
        'profesor' => listarModulosDeProfesor($uid),
        // Dirección/secretaría pueden supervisar cualquier recurso del aula
        // (mismo permiso que ya tiene el panel web, ver verArchivo.php).
        'director', 'secretaria' => listarModulos(),
        default => [],
    };
    v1Ok(['modules' => $modules]);
}

if ($action === 'folders') {
    $idModulo = (int)($_GET['idModulo'] ?? 0);
    if ($idModulo <= 0) v1Error('idModulo is required.', 400, 'validation');
    if (!classroomModuloAutorizado($type, $uid, $idModulo)) v1Error('Forbidden.', 403, 'forbidden');
    v1Ok(['folders' => listarCarpetasPorModuloAula($idModulo)]);
}

if ($action === 'files') {
    $idModulo  = (int)($_GET['idModulo'] ?? 0);
    $idCarpeta = isset($_GET['idCarpeta']) ? (int)$_GET['idCarpeta'] : null;
    if ($idModulo <= 0) v1Error('idModulo is required.', 400, 'validation');
    if (!classroomModuloAutorizado($type, $uid, $idModulo)) v1Error('Forbidden.', 403, 'forbidden');
    $files = $idCarpeta ? listarArchivosPorCarpetaAula($idCarpeta) : listarArchivosPorModuloAula($idModulo);
    if ($type === 'estudiante' && $files) {
        $con = obtenerConexion();
        $ids = array_column($files, 'idArchivo');
        $ph  = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $st = mysqli_prepare($con, "SELECT idArchivo FROM aula_favoritos WHERE idEstudiante = ? AND idArchivo IN ($ph)");
        mysqli_stmt_bind_param($st, 'i' . $types, ...[$uid, ...$ids]);
        mysqli_stmt_execute($st);
        $favoritos = array_column(mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC), 'idArchivo');
        $favoritos = array_map('intval', $favoritos);
        foreach ($files as &$f) {
            $f['esFavorito'] = in_array((int)$f['idArchivo'], $favoritos, true);
        }
        unset($f);
    }
    v1Ok(['files' => $files]);
}

if ($action === 'tasks') {
    $idModulo = (int)($_GET['idModulo'] ?? 0);
    if ($idModulo <= 0) v1Error('idModulo is required.', 400, 'validation');
    if (!classroomModuloAutorizado($type, $uid, $idModulo)) v1Error('Forbidden.', 403, 'forbidden');

    // Profesor sees drafts too (needed for the publish toggle); every other
    // role only ever sees published tasks, same as the read-only web views.
    $tasks = $type === 'profesor' ? listarTareasPorModuloProfesorAula($idModulo) : listarTareasPorModuloAula($idModulo);
    if ($type === 'estudiante' && $tasks) {
        $con = obtenerConexion();
        $ids = array_column($tasks, 'idTarea');
        $ph  = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $st = mysqli_prepare($con,
            "SELECT idTarea, nota, estado, comentarioCalificacion, fechaEntrega
             FROM aula_entregas WHERE idTarea IN ($ph) AND idEstudiante = ?");
        mysqli_stmt_bind_param($st, $types . 'i', ...[...$ids, $uid]);
        mysqli_stmt_execute($st);
        $entregas = [];
        foreach (mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC) as $e) {
            $entregas[(int)$e['idTarea']] = $e;
        }
        foreach ($tasks as &$t) {
            $t['miEntrega'] = $entregas[(int)$t['idTarea']] ?? null;
        }
        unset($t);
    }
    v1Ok(['tasks' => $tasks]);
}

if ($action === 'submission') {
    if ($type !== 'estudiante') v1Error('Only estudiantes can view their own submission.', 403, 'forbidden');
    $idTarea = (int)($_GET['idTarea'] ?? 0);
    if ($idTarea <= 0) v1Error('idTarea is required.', 400, 'validation');
    $tarea = obtenerTareaPorIdAula($idTarea);
    if (!$tarea || !classroomModuloAutorizado($type, $uid, (int)$tarea['idModulo'])) {
        v1Error('Task not found.', 404, 'not_found');
    }
    v1Ok(['submission' => obtenerEntregaAula($idTarea, $uid)]);
}

if ($action === 'submissions') {
    $idTarea = (int)($_GET['idTarea'] ?? 0);
    if ($idTarea <= 0) v1Error('idTarea is required.', 400, 'validation');
    $tarea = obtenerTareaPorIdAula($idTarea);
    if (!$tarea) v1Error('Task not found.', 404, 'not_found');
    if (!classroomModuloAutorizado($type, $uid, (int)$tarea['idModulo']) || $type === 'estudiante') {
        v1Error('Forbidden.', 403, 'forbidden');
    }
    v1Ok(['submissions' => listarEntregasPorTareaAula($idTarea)]);
}

if ($action === 'sessions') {
    $idModulo = (int)($_GET['idModulo'] ?? 0);
    if ($idModulo <= 0) v1Error('idModulo is required.', 400, 'validation');
    if (!classroomModuloAutorizado($type, $uid, $idModulo)) v1Error('Forbidden.', 403, 'forbidden');
    v1Ok(['sessions' => listarSesionesPorModulo($idModulo)]);
}

if ($action === 'favorites') {
    if ($type !== 'estudiante') v1Error('Only estudiantes have favorites.', 403, 'forbidden');
    v1Ok(['favorites' => listarFavoritosEstudianteAula($uid)]);
}

// ── POST actions ────────────────────────────────────────────────────────

if ($action === 'submit') {
    if ($type !== 'estudiante') v1Error('Only estudiantes can submit entregas.', 403, 'forbidden');
    $idTarea   = (int)($_POST['idTarea'] ?? 0);
    $respuesta = trim((string)($_POST['respuesta'] ?? ''));
    if ($idTarea <= 0) v1Error('idTarea is required.', 400, 'validation');

    $tarea = obtenerTareaPorIdAula($idTarea);
    if (!$tarea || !$tarea['publicado']) v1Error('The task is not available.', 404, 'not_found');
    if (!classroomModuloAutorizado($type, $uid, (int)$tarea['idModulo'])) {
        v1Error('You do not have access to this task.', 403, 'forbidden');
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
        $bytes    = file_get_contents($file['tmp_name']);
        $subioOk  = $bytes !== false && R2Client::putObject('aula/entregas/' . $nombreArchivo, $bytes, $mimeReal);
        @unlink($file['tmp_name']);
        if (!$subioOk) v1Error('Could not save the file on the server.', 500, 'error');
        $archivoEntrega = $nombreArchivo;
    }

    if (!enviarEntregaAula($idTarea, $uid, $archivoEntrega, $respuesta)) {
        v1Error('Could not save the submission.', 500, 'error');
    }
    insertarNotificacionAula((int)$tarea['idProfesor'], 'profesor', 'entrega_enviada',
        'Nueva entrega: ' . $tarea['titulo'], 'Un estudiante ha enviado su entrega.', $idTarea, 'tarea');
    $fh = __DIR__ . '/../../controladores/firebase/firebase_helper.php';
    if (file_exists($fh)) {
        require_once $fh;
        $token = obtenerTokenUsuario((int)$tarea['idProfesor'], 'profesor');
        if ($token) {
            enviarNotificacionFirebase($token, 'Nueva entrega: ' . $tarea['titulo'],
                'Un estudiante ha enviado su entrega.', 'entrega_enviada', ['idTarea' => $idTarea]);
        }
    }
    v1Ok(['message' => 'Submission sent.'], 201);
}

if ($action === 'grade') {
    if ($type !== 'profesor') v1Error('Only profesores can grade entregas.', 403, 'forbidden');
    // multipart/form-data (not v1Body()'s JSON) — grading optionally carries a
    // correction file, same reasoning as the 'submit' action above.
    $idEntrega  = (int)($_POST['idEntrega'] ?? 0);
    $nota       = $_POST['nota'] ?? null;
    $comentario = trim((string)($_POST['comentario'] ?? ''));
    if ($idEntrega <= 0 || !is_numeric($nota) || (float)$nota < 0 || (float)$nota > 10) {
        v1Error('idEntrega and a nota between 0 and 10 are required.', 400, 'validation');
    }

    $entrega = obtenerEntregaPorIdAula($idEntrega);
    if (!$entrega) v1Error('Submission not found.', 404, 'not_found');
    if (!classroomModuloAutorizado($type, $uid, (int)$entrega['idModulo'])) {
        v1Error('You do not have permission to grade this submission.', 403, 'forbidden');
    }

    $archivoCorreccion = null;
    if (!empty($_FILES['archivoCorreccion']['name'])) {
        $file = $_FILES['archivoCorreccion'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mimeAllowed = [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'application/octet-stream',
        ];
        $mimeReal = @mime_content_type($file['tmp_name']);
        if (!in_array($ext, ['pdf', 'docx', 'txt'], true) || ($mimeReal && !in_array($mimeReal, $mimeAllowed, true))) {
            v1Error('The correction file must be PDF, DOCX or TXT.', 400, 'validation');
        }
        if ($file['size'] > 20 * 1024 * 1024) {
            v1Error('File too large (max 20 MB).', 400, 'validation');
        }
        require_once __DIR__ . '/../../include/R2Client.php';
        $nombreCorreccion = bin2hex(random_bytes(12)) . '.' . $ext;
        $bytes   = file_get_contents($file['tmp_name']);
        $subioOk = $bytes !== false && R2Client::putObject('aula/correcciones/' . $nombreCorreccion, $bytes, $mimeReal ?: 'application/octet-stream');
        @unlink($file['tmp_name']);
        if (!$subioOk) v1Error('Could not save the correction file on the server.', 500, 'error');
        $archivoCorreccion = $nombreCorreccion;
    }

    if (!calificarEntregaAula($idEntrega, (float)$nota, $comentario, $archivoCorreccion)) {
        v1Error('Could not save the grade.', 500, 'error');
    }
    if ($archivoCorreccion !== null && !empty($entrega['archivoCorreccion'])) {
        require_once __DIR__ . '/../../include/R2Client.php';
        R2Client::deleteObject('aula/correcciones/' . $entrega['archivoCorreccion']);
        $rutaVieja = __DIR__ . '/../../public/uploads/aula/correcciones/' . $entrega['archivoCorreccion'];
        if (is_file($rutaVieja)) @unlink($rutaVieja);
    }
    insertarNotificacionAula((int)$entrega['idEstudiante'], 'estudiante', 'entrega_corregida',
        'Entrega Corregida',
        "Tu entrega de «{$entrega['tituloTarea']}» ha sido corregida: " . number_format((float)$nota, 2),
        (int)$entrega['idTarea'], 'TAREA');
    $fh = __DIR__ . '/../../controladores/firebase/firebase_helper.php';
    if (file_exists($fh)) {
        require_once $fh;
        $token = obtenerTokenUsuario((int)$entrega['idEstudiante'], 'estudiante');
        if ($token) {
            enviarNotificacionFirebase($token, 'Entrega calificada: ' . $entrega['tituloTarea'],
                'Tu entrega ha sido corregida: ' . number_format((float)$nota, 2) . ' sobre 10.',
                'entrega_calificada', ['idTarea' => (int)$entrega['idTarea']]);
        }
    }
    v1Ok(['message' => 'Grade saved.']);
}

if ($action === 'publish') {
    if ($type !== 'profesor') v1Error('Only profesores can publish tasks.', 403, 'forbidden');
    $body = v1Body();
    $idTarea = (int)($body['idTarea'] ?? 0);
    if ($idTarea <= 0) v1Error('idTarea is required.', 400, 'validation');

    $tarea = obtenerTareaPorIdAula($idTarea);
    if (!$tarea) v1Error('Task not found.', 404, 'not_found');
    if (!classroomModuloAutorizado($type, $uid, (int)$tarea['idModulo'])) {
        v1Error('You do not have permission to manage this task.', 403, 'forbidden');
    }

    $nuevoEstado = empty($tarea['publicado']) ? 1 : 0;
    if (!actualizarPublicadoTareaAula($idTarea, $nuevoEstado)) {
        v1Error('Could not change the task state.', 500, 'error');
    }
    if ($nuevoEstado) {
        notificarEstudiantesPorModulo((int)$tarea['idModulo'], 'tarea_nueva', 'Nueva Tarea',
            "Se ha publicado una nueva tarea: {$tarea['titulo']}", $idTarea, 'TAREA');
    }
    v1Ok(['message' => $nuevoEstado ? 'Task published.' : 'Task hidden.', 'publicado' => $nuevoEstado]);
}

if ($action === 'create-session') {
    if ($type !== 'profesor') v1Error('Only profesores can create sesiones vivas.', 403, 'forbidden');
    $body = v1Body();
    $idModulo      = (int)($body['idModulo'] ?? 0);
    $titulo        = trim((string)($body['titulo'] ?? ''));
    $descripcion   = trim((string)($body['descripcion'] ?? ''));
    $fechaSesion   = (string)($body['fechaSesion'] ?? '');
    $horaSesion    = (string)($body['horaSesion'] ?? '');
    $enlaceReunion = trim((string)($body['enlaceReunion'] ?? ''));
    $plataforma    = trim((string)($body['plataforma'] ?? ''));

    if ($idModulo <= 0 || $titulo === '' || $fechaSesion === '' || $horaSesion === '') {
        v1Error('idModulo, titulo, fechaSesion and horaSesion are required.', 400, 'validation');
    }
    $errFecha = validarFechaHoraSesion($fechaSesion, $horaSesion);
    if ($errFecha) v1Error($errFecha, 400, 'validation');
    if ($enlaceReunion !== '') {
        $errEnlace = validarEnlaceReunion($enlaceReunion);
        if ($errEnlace) v1Error($errEnlace, 400, 'validation');
    }
    if (!classroomModuloAutorizado($type, $uid, $idModulo)) v1Error('Forbidden.', 403, 'forbidden');

    $idSesion = crearSesionViva($idModulo, $uid, $titulo, $descripcion, $fechaSesion, $horaSesion, $enlaceReunion, $plataforma);
    if (!$idSesion) v1Error('Could not create the session.', 500, 'error');

    $modulo = obtenerModuloPorId($idModulo);
    notificarEstudiantesPorModulo(
        $idModulo, 'sesion_nueva', 'Nueva sesión viva: ' . $titulo,
        'Se ha creado una nueva sesión viva en ' . ($modulo['nombreModulo'] ?? '') .
            ' para el ' . date('d/m/Y H:i', strtotime($fechaSesion . ' ' . $horaSesion)),
        $idSesion, 'sesion'
    );
    v1Ok(['message' => 'Session created.', 'idSesion' => $idSesion], 201);
}

if ($action === 'favorite') {
    if ($type !== 'estudiante') v1Error('Only estudiantes have favorites.', 403, 'forbidden');
    $body = v1Body();
    $idArchivo = (int)($body['idArchivo'] ?? 0);
    if ($idArchivo <= 0) v1Error('idArchivo is required.', 400, 'validation');

    $archivo = obtenerArchivoPorId($idArchivo);
    if (!$archivo || !classroomModuloAutorizado($type, $uid, (int)$archivo['idModulo'])) {
        v1Error('You do not have access to this file.', 403, 'forbidden');
    }

    if (esFavoritoAula($uid, $idArchivo)) {
        quitarFavoritoAula($uid, $idArchivo);
        v1Ok(['favorito' => 0]);
    }
    marcarFavoritoAula($uid, $idArchivo);
    v1Ok(['favorito' => 1]);
}

v1Error('Unknown action.', 400, 'validation');
