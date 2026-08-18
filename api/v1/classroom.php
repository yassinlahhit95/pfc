<?php
declare(strict_types=1);

// Aula digital — mobile scope: browse modules → folders/files (download) →
// tareas, submit/view entregas, grade with comments, publish toggle,
// sesiones vivas (view + create), favoritos. Profesor CRUD for tareas and
// materiales (create/update/delete) mirrors the web controllers under
// controladores/profesores/aula/. Folder/file MOVE and rename/version-history
// still stay desktop-only (drag/drop file-manager UX doesn't translate to a
// phone) — only create/upload/delete are exposed here. Action-param
// dispatched, like chat.php.
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
// POST /api/v1/classroom.php?action=create-task {idModulo, titulo, descripcion?, publicado?} (multipart, archivoAdjunto?) (profesor)
// POST /api/v1/classroom.php?action=update-task {idTarea, titulo, descripcion?, publicado?} (multipart, archivoAdjunto?) (profesor)
// POST /api/v1/classroom.php?action=delete-task {idTarea} (profesor)
// POST /api/v1/classroom.php?action=create-folder {idModulo, nombre, idPadre?, color?, icono?} (profesor)
// POST /api/v1/classroom.php?action=delete-folder {idCarpeta} (profesor)
// POST /api/v1/classroom.php?action=upload-file (multipart: idModulo, idCarpeta?, titulo?, archivo) (profesor)
// POST /api/v1/classroom.php?action=delete-file {idArchivo} (profesor)
// GET  /api/v1/classroom.php?action=sessions&idModulo=
// POST /api/v1/classroom.php?action=create-session {idModulo, titulo, descripcion?, fechaSesion, horaSesion, enlaceReunion?, plataforma?}
// GET  /api/v1/classroom.php?action=favorites                    (estudiante)
// POST /api/v1/classroom.php?action=favorite {idArchivo, favorito: bool}

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/aula.php';
require_once __DIR__ . '/../../modelos/modulos.php';
require_once __DIR__ . '/../../include/FileServer.php';

$method = $_SERVER['REQUEST_METHOD'];
$postActions = ['submit', 'grade', 'publish', 'create-task', 'update-task', 'delete-task',
    'create-folder', 'delete-folder', 'upload-file', 'delete-file', 'create-session', 'favorite'];
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

// La acción de descarga no puede llevar cabecera Authorization (la abre
// un visor/navegador externo, no se obtiene mediante el cliente HTTP de la app),
// así que acepta el mismo token Bearer de 64 caracteres hex como parámetro de consulta.
// Misma tabla api_tokens/expiración/rate-limit — no es un mecanismo de autenticación más débil,
// solo un transporte distinto para esta acción.
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
            "SELECT idTarea, nota, estado, comentarioCalificacion, fechaEntrega, archivoEntrega
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
    try {
        $favorites = listarFavoritosEstudianteAula($uid);
        error_log("[API] favorites endpoint called for idEstudiante=$uid, found " . count($favorites) . " favorites");
        v1Ok(['favorites' => $favorites]);
    } catch (Exception $e) {
        error_log("[API] ERROR in listarFavoritosEstudianteAula: " . $e->getMessage());
        v1Error('Error fetching favorites: ' . $e->getMessage(), 500, 'error');
    }
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

// Shared adjunto handling for create-task/update-task — mirrors the
// validation + storage in controladores/profesores/aula/guardarTarea.php.
// $adjuntoAnterior is deleted from R2 once the new one has been confirmed
// uploaded (same "replace" semantics as the web form).
function v1SubirAdjuntoTareaAula(?string $adjuntoAnterior = null): ?string {
    if (empty($_FILES['archivoAdjunto']['name'])) return null;
    $archivo = $_FILES['archivoAdjunto'];
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        v1Error('Error uploading the attachment.', 400, 'validation');
    }
    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $mimeAllowedAdjunto = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain', 'application/zip', 'image/png', 'image/jpeg',
        'application/octet-stream',
    ];
    $mimeAdjunto = @mime_content_type($archivo['tmp_name']);
    if (!in_array($ext, ['pdf', 'docx', 'txt', 'zip', 'png', 'jpg', 'jpeg'], true)
        || ($mimeAdjunto && !in_array($mimeAdjunto, $mimeAllowedAdjunto, true))) {
        v1Error('Attachment: only PDF, DOCX, TXT, ZIP or images are allowed.', 400, 'validation');
    }
    if ($archivo['size'] > 20 * 1024 * 1024) {
        v1Error('The attachment exceeds the 20 MB limit.', 400, 'validation');
    }

    require_once __DIR__ . '/../../include/ImageOptimizer.php';
    require_once __DIR__ . '/../../include/R2Client.php';
    $nombreAdjunto = bin2hex(random_bytes(12)) . '.' . $ext;
    $tmpName = $archivo['tmp_name'];
    $imgMimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
    if (isset($imgMimes[$ext])) ImageOptimizer::optimize($tmpName, $imgMimes[$ext]);

    $mimeReal = @mime_content_type($tmpName) ?: 'application/octet-stream';
    $bytes    = file_get_contents($tmpName);
    $subioOk  = $bytes !== false && R2Client::putObject('aula/tareas/' . $nombreAdjunto, $bytes, $mimeReal);
    @unlink($tmpName);
    if (!$subioOk) v1Error('Could not save the attachment.', 500, 'error');

    if ($adjuntoAnterior) R2Client::deleteObject('aula/tareas/' . $adjuntoAnterior);
    return $nombreAdjunto;
}

if ($action === 'create-task') {
    if ($type !== 'profesor') v1Error('Only profesores can create tasks.', 403, 'forbidden');
    $idModulo    = (int)($_POST['idModulo'] ?? 0);
    $titulo      = trim((string)($_POST['titulo'] ?? ''));
    $descripcion = trim((string)($_POST['descripcion'] ?? ''));
    $publicado   = !empty($_POST['publicado']) ? 1 : 0;

    if ($idModulo <= 0) v1Error('idModulo is required.', 400, 'validation');
    if ($titulo === '') v1Error('titulo is required.', 400, 'validation');
    if (mb_strlen($titulo) > 150) v1Error('titulo must be 150 characters or fewer.', 400, 'validation');
    if (!classroomModuloAutorizado($type, $uid, $idModulo)) v1Error('Forbidden.', 403, 'forbidden');

    $archivoAdjunto = v1SubirAdjuntoTareaAula();

    $idTarea = insertarTareaAula($idModulo, $uid, $titulo, $descripcion, $archivoAdjunto, $publicado);
    if (!$idTarea) v1Error('Could not create the task.', 500, 'error');

    if ($publicado) {
        notificarEstudiantesPorModulo($idModulo, 'tarea_nueva', 'Nueva Tarea',
            "Se ha publicado una nueva tarea: $titulo", $idTarea, 'TAREA');
    }
    v1Ok(['message' => 'Task created.', 'idTarea' => $idTarea], 201);
}

if ($action === 'update-task') {
    if ($type !== 'profesor') v1Error('Only profesores can update tasks.', 403, 'forbidden');
    $idTarea     = (int)($_POST['idTarea'] ?? 0);
    $titulo      = trim((string)($_POST['titulo'] ?? ''));
    $descripcion = trim((string)($_POST['descripcion'] ?? ''));
    $publicado   = !empty($_POST['publicado']) ? 1 : 0;

    if ($idTarea <= 0) v1Error('idTarea is required.', 400, 'validation');
    if ($titulo === '') v1Error('titulo is required.', 400, 'validation');
    if (mb_strlen($titulo) > 150) v1Error('titulo must be 150 characters or fewer.', 400, 'validation');

    $tarea = obtenerTareaPorIdAula($idTarea);
    if (!$tarea) v1Error('Task not found.', 404, 'not_found');
    if (!classroomModuloAutorizado($type, $uid, (int)$tarea['idModulo'])) {
        v1Error('You do not have permission to manage this task.', 403, 'forbidden');
    }
    $estabaPublicada = !empty($tarea['publicado']);

    $archivoAdjunto = v1SubirAdjuntoTareaAula($tarea['archivoAdjunto'] ?? null);

    if (!actualizarTareaAula($idTarea, $titulo, $descripcion, $publicado, $archivoAdjunto)) {
        v1Error('Could not update the task.', 500, 'error');
    }
    if ($publicado && !$estabaPublicada) {
        notificarEstudiantesPorModulo((int)$tarea['idModulo'], 'tarea_nueva', 'Nueva Tarea',
            "Se ha publicado una nueva tarea: $titulo", $idTarea, 'TAREA');
    }
    v1Ok(['message' => 'Task updated.']);
}

if ($action === 'delete-task') {
    if ($type !== 'profesor') v1Error('Only profesores can delete tasks.', 403, 'forbidden');
    $body = v1Body();
    $idTarea = (int)($body['idTarea'] ?? 0);
    if ($idTarea <= 0) v1Error('idTarea is required.', 400, 'validation');

    $tarea = obtenerTareaPorIdAula($idTarea);
    if (!$tarea) v1Error('Task not found.', 404, 'not_found');
    if (!classroomModuloAutorizado($type, $uid, (int)$tarea['idModulo'])) {
        v1Error('You do not have permission to manage this task.', 403, 'forbidden');
    }
    if (!eliminarTareaAula($idTarea)) v1Error('Could not delete the task.', 500, 'error');
    v1Ok(['message' => 'Task deleted.']);
}

if ($action === 'create-folder') {
    if ($type !== 'profesor') v1Error('Only profesores can create folders.', 403, 'forbidden');
    $body     = v1Body();
    $idModulo = (int)($body['idModulo'] ?? 0);
    $nombre   = trim((string)($body['nombre'] ?? ''));
    $idPadre  = (int)($body['idPadre'] ?? 0) ?: null;
    $color    = (string)($body['color'] ?? '#0ea5e9');
    $icono    = (string)($body['icono'] ?? 'fa-folder');

    if ($idModulo <= 0) v1Error('idModulo is required.', 400, 'validation');
    if ($nombre === '') v1Error('nombre is required.', 400, 'validation');
    if (!classroomModuloAutorizado($type, $uid, $idModulo)) v1Error('Forbidden.', 403, 'forbidden');

    // Same sanitization as crearCarpeta.php — reject arbitrary color/icon values.
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) $color = '#0ea5e9';
    if (!preg_match('/^fa-[a-z0-9-]+$/', $icono))    $icono = 'fa-folder';

    if ($idPadre) {
        $padre = obtenerCarpetaAulaPorId($idPadre);
        if (!$padre || (int)$padre['idModulo'] !== $idModulo) $idPadre = null;
    }

    $idCarpeta = insertarCarpetaAula($nombre, $idModulo, $uid, $color, $icono, $idPadre);
    if (!$idCarpeta) v1Error('Could not create the folder.', 500, 'error');
    v1Ok(['message' => 'Folder created.', 'idCarpeta' => $idCarpeta], 201);
}

if ($action === 'delete-folder') {
    if ($type !== 'profesor') v1Error('Only profesores can delete folders.', 403, 'forbidden');
    $body      = v1Body();
    $idCarpeta = (int)($body['idCarpeta'] ?? 0);
    if ($idCarpeta <= 0) v1Error('idCarpeta is required.', 400, 'validation');

    $carpeta = obtenerCarpetaAulaPorId($idCarpeta);
    if (!$carpeta || (int)$carpeta['idProfesor'] !== $uid) v1Error('Folder not found.', 404, 'not_found');

    borrarCarpetaRecursivoAula($idCarpeta);
    v1Ok(['message' => 'Folder deleted.']);
}

if ($action === 'upload-file') {
    if ($type !== 'profesor') v1Error('Only profesores can upload files.', 403, 'forbidden');
    $idModulo  = (int)($_POST['idModulo'] ?? 0);
    $idCarpeta = (int)($_POST['idCarpeta'] ?? 0) ?: null;
    $titulo    = trim((string)($_POST['titulo'] ?? ''));

    if ($idModulo <= 0) v1Error('idModulo is required.', 400, 'validation');
    if (!classroomModuloAutorizado($type, $uid, $idModulo)) v1Error('Forbidden.', 403, 'forbidden');
    if (empty($_FILES['archivo']['name'])) v1Error('archivo is required.', 400, 'validation');

    $modulo = obtenerModuloPorId($idModulo);
    if (!$modulo) v1Error('Module not found.', 404, 'not_found');

    $archivo = $_FILES['archivo'];
    if ($archivo['error'] !== UPLOAD_ERR_OK) v1Error('Error uploading the file.', 400, 'validation');

    $permitidos = ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt', 'xls', 'xlsx', 'ods', 'csv',
        'ppt', 'pptx', 'odp', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'zip', 'rar'];
    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $permitidos, true)) v1Error("File type not allowed ($ext).", 400, 'validation');
    if ($archivo['size'] > 20 * 1024 * 1024) v1Error('The file exceeds the 20 MB limit.', 400, 'validation');

    $mimesCanonicos = [
        'pdf' => 'application/pdf', 'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'txt' => 'text/plain', 'rtf' => 'application/rtf', 'odt' => 'application/vnd.oasis.opendocument.text',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet', 'csv' => 'text/csv',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif',
        'webp' => 'image/webp', 'zip' => 'application/zip', 'rar' => 'application/x-rar-compressed',
    ];
    $mimesAceptados = [
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword', 'application/x-ole-storage', 'application/vnd.ms-office'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        'txt' => ['text/plain'], 'rtf' => ['text/rtf', 'application/rtf'],
        'odt' => ['application/vnd.oasis.opendocument.text', 'application/zip'],
        'xls' => ['application/vnd.ms-excel', 'application/x-ole-storage', 'application/vnd.ms-office'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
        'ods' => ['application/vnd.oasis.opendocument.spreadsheet', 'application/zip'],
        'csv' => ['text/csv', 'text/plain'],
        'ppt' => ['application/vnd.ms-powerpoint', 'application/x-ole-storage', 'application/vnd.ms-office'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'],
        'odp' => ['application/vnd.oasis.opendocument.presentation', 'application/zip'],
        'jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg'], 'png' => ['image/png'],
        'gif' => ['image/gif'], 'webp' => ['image/webp'], 'zip' => ['application/zip'],
        'rar' => ['application/x-rar', 'application/x-rar-compressed', 'application/vnd.rar'],
    ];
    $mimeReal = @mime_content_type($archivo['tmp_name']) ?: '';
    if ($mimeReal !== '' && isset($mimesAceptados[$ext]) && !in_array($mimeReal, $mimesAceptados[$ext], true)) {
        v1Error("The file content does not match its extension ($ext).", 400, 'validation');
    }

    $idCiclo     = (int)$modulo['idCiclo'];
    $limiteCiclo = obtenerLimiteAlmacenamientoCicloAula($idCiclo);
    $usadoCiclo  = obtenerUsoAlmacenamientoCicloAula($idCiclo);
    if (($usadoCiclo + $archivo['size']) > $limiteCiclo) {
        v1Error('This upload would exceed the ciclo storage limit.', 400, 'validation');
    }

    require_once __DIR__ . '/../../include/ImageOptimizer.php';
    require_once __DIR__ . '/../../include/R2Client.php';
    $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $ext;
    $tmpName = $archivo['tmp_name'];
    $imgMimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
    if (isset($imgMimes[$ext])) ImageOptimizer::optimize($tmpName, $imgMimes[$ext]);

    $contentType = $mimesCanonicos[$ext] ?? 'application/octet-stream';
    $bytes       = file_get_contents($tmpName);
    $subioOk     = $bytes !== false && R2Client::putObject('aula/archivos/' . $nombreArchivo, $bytes, $contentType);
    @unlink($tmpName);
    if (!$subioOk) v1Error('Could not save the file on the server.', 500, 'error');

    $nombreVisible = $archivo['name'];
    if ($titulo !== '') {
        $base = $titulo;
        $sufijo = '.' . $ext;
        if ($ext !== '' && strtolower(substr($base, -strlen($sufijo))) === strtolower($sufijo)) {
            $base = substr($base, 0, -strlen($sufijo));
        }
        $base = trim($base);
        if ($base !== '') $nombreVisible = $ext !== '' ? $base . '.' . $ext : $base;
    }
    $nombreVisible = nombreUnicoArchivoAula($idModulo, $idCarpeta, $nombreVisible);

    $idArchivo = insertarArchivoAula($nombreArchivo, $nombreVisible, $ext, (int)$archivo['size'], '', $idCarpeta, $idModulo, $uid);
    if (!$idArchivo) v1Error('Could not save the file.', 500, 'error');

    notificarEstudiantesCicloAula($idCiclo, 'archivo_subido', 'Nuevo archivo en ' . $modulo['nombreModulo'],
        $nombreVisible, $idArchivo, 'archivo');
    v1Ok(['message' => 'File uploaded.', 'idArchivo' => $idArchivo], 201);
}

if ($action === 'delete-file') {
    if ($type !== 'profesor') v1Error('Only profesores can delete files.', 403, 'forbidden');
    $body      = v1Body();
    $idArchivo = (int)($body['idArchivo'] ?? 0);
    if ($idArchivo <= 0) v1Error('idArchivo is required.', 400, 'validation');

    $archivo = obtenerArchivoPorId($idArchivo);
    if (!$archivo || (int)$archivo['idProfesor'] !== $uid) v1Error('File not found.', 404, 'not_found');

    borrarArchivoAula($idArchivo);
    v1Ok(['message' => 'File deleted.']);
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
