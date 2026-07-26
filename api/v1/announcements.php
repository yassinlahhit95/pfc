<?php
declare(strict_types=1);

// GET  /api/v1/announcements.php
//   Query params: limit (max 100, default 20), offset (default 0)
//   Returns non-expired announcements relevant to the authenticated user's role.
// POST /api/v1/announcements.php — director/secretaria only, mirrors
//   controladores/{admin,secretaria}/anuncios/insertar.php (web).
//   Body: {titulo, mensaje, dirigidoA?: 'todos'|'estudiantes'|'profesores'|'tutores'}

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/anuncios.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../modelos/log.php';
    require_once __DIR__ . '/../../modelos/estudiantes.php';
    require_once __DIR__ . '/../../modelos/profesores.php';
    require_once __DIR__ . '/../../modelos/tutores.php';
    require_once __DIR__ . '/../../controladores/firebase/firebase_helper.php';

    $auth = v1Auth();
    ['user_type' => $type, 'user_id' => $uid] = $auth;
    if ($type !== 'director' && $type !== 'secretaria') {
        v1Error('This endpoint is not available for this role.', 403, 'forbidden');
    }
    v1RequireFeature('feature_anuncios');

    $body      = v1Body();
    $titulo    = trim((string)($body['titulo'] ?? ''));
    $mensaje   = trim((string)($body['mensaje'] ?? ''));
    $dirigidoA = (string)($body['dirigidoA'] ?? 'todos');
    if (!in_array($dirigidoA, ['todos', 'estudiantes', 'profesores', 'tutores'], true)) {
        $dirigidoA = 'todos';
    }
    if ($titulo === '') v1Error('titulo is required.', 400, 'validation');
    if ($mensaje === '') v1Error('mensaje is required.', 400, 'validation');

    if (!insertarAnuncio($titulo, $mensaje, $dirigidoA)) {
        v1Error('Could not publish the announcement.', 500, 'error');
    }

    if ($type === 'director') {
        registrarAccion('insertar', 'anuncios', null, $titulo);
    } else {
        registrarAccionSecretaria('insertar', 'anuncios', null, $titulo);
    }

    $tokens = [];
    if ($dirigidoA === 'estudiantes' || $dirigidoA === 'todos') $tokens = [...$tokens, ...obtenerTokensEstudiantes()];
    if ($dirigidoA === 'profesores' || $dirigidoA === 'todos') $tokens = [...$tokens, ...obtenerTokensProfesores()];
    if ($dirigidoA === 'tutores' || $dirigidoA === 'todos') $tokens = [...$tokens, ...obtenerTokensTutores()];
    foreach (array_unique($tokens) as $token) {
        enviarNotificacionFirebase($token, "NUEVO ANUNCIO: $titulo", substr(strip_tags($mensaje), 0, 100) . '...', 'announcement');
    }

    v1Ok(['message' => 'Announcement published.'], 201);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type] = $auth;
v1RequireFeature('feature_anuncios');

$limit  = min(max((int)($_GET['limit']  ?? 20), 1), 100);
$offset = max((int)($_GET['offset'] ?? 0), 0);

// Map API user types to the dirigidoA enum values
$roleFilter = match($type) {
    'estudiante' => 'estudiantes',
    'profesor'   => 'profesores',
    'tutor'      => 'tutores',
    default      => null, // director / secretaria see all, unfiltered
};

$con = obtenerConexion();

if ($roleFilter !== null) {
    $st = mysqli_prepare($con,
        "SELECT idAnuncio, titulo, mensaje, fechaAnuncio, fechaExpiracion, dirigidoA
         FROM anuncios
         WHERE fechaExpiracion >= CURDATE()
           AND (dirigidoA = 'todos' OR dirigidoA = ?)
         ORDER BY fechaAnuncio DESC
         LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($st, 'sii', $roleFilter, $limit, $offset);
} else {
    $st = mysqli_prepare($con,
        "SELECT idAnuncio, titulo, mensaje, fechaAnuncio, fechaExpiracion, dirigidoA
         FROM anuncios
         WHERE fechaExpiracion >= CURDATE()
         ORDER BY fechaAnuncio DESC
         LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($st, 'ii', $limit, $offset);
}

mysqli_stmt_execute($st);
$rows = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);

v1Ok(['announcements' => $rows, 'limit' => $limit, 'offset' => $offset]);
