<?php
declare(strict_types=1);

// GET /api/v1/announcements.php
// Query params: limit (max 100, default 20), offset (default 0)
// Returns non-expired announcements relevant to the authenticated user's role.

require_once __DIR__ . '/_api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type] = $auth;

$limit  = min(max((int)($_GET['limit']  ?? 20), 1), 100);
$offset = max((int)($_GET['offset'] ?? 0), 0);

// Map API user types to the dirigidoA enum values
$roleFilter = match($type) {
    'estudiante' => 'estudiantes',
    'profesor'   => 'profesores',
    'tutor'      => 'tutores',
    default      => null, // director sees all
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
