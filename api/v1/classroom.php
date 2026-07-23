<?php
declare(strict_types=1);

// Aula digital — read-only mobile scope (Phase 5, scoped down): browse
// modules → folders/files (download) → tareas (view own grade/feedback).
// Folder/file MANAGEMENT (create/rename/move/version/trash), grading, and
// publish toggles are intentionally NOT exposed here — that stays desktop
// -only per the mobile scoping decision (drag/drop file-manager UX doesn't
// translate to a phone). Action-param dispatched, like chat.php.
//
// GET /api/v1/classroom.php?action=modules
// GET /api/v1/classroom.php?action=folders&idModulo=
// GET /api/v1/classroom.php?action=files&idModulo=&idCarpeta=
// GET /api/v1/classroom.php?action=tasks&idModulo=
// GET /api/v1/classroom.php?action=download&id=&token=   (token via query — see note below)

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/aula.php';
require_once __DIR__ . '/../../modelos/modulos.php';
require_once __DIR__ . '/../../include/FileServer.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$action = $_GET['action'] ?? '';

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
    if (!$uploadDir || ($ruta !== false && strpos($ruta, $uploadDir . DIRECTORY_SEPARATOR) !== 0)) {
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
            return $est && $est['idCiclo'] ? listarModulosPorCiclo((int)$est['idCiclo']) : [];
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
    v1Ok(['files' => $files]);
}

if ($action === 'tasks') {
    $idModulo = (int)($_GET['idModulo'] ?? 0);
    if ($idModulo <= 0) v1Error('idModulo is required.', 400, 'validation');
    if (!classroomModuloAutorizado($type, $uid, $idModulo)) v1Error('Forbidden.', 403, 'forbidden');

    $tasks = listarTareasPorModuloAula($idModulo);
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

v1Error('Unknown action.', 400, 'validation');
