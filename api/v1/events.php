<?php
declare(strict_types=1);

// GET /api/v1/events.php
// Query params: limit (max 100, default 20), offset (default 0), upcoming (flag)
//   ?upcoming  — only events from today onwards
//   ?from=YYYY-MM-DD&to=YYYY-MM-DD — date range filter

require_once __DIR__ . '/_api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

v1Auth(); // any authenticated user can see events

$limit  = min(max((int)($_GET['limit']  ?? 20), 1), 100);
$offset = max((int)($_GET['offset'] ?? 0), 0);
$upcoming = isset($_GET['upcoming']);

$from = $_GET['from'] ?? null;
$to   = $_GET['to']   ?? null;
// Validate date format to prevent injection via named params
if ($from && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = null;
if ($to   && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = null;

$con = obtenerConexion();

$where  = [];
$params = [];
$types  = '';

if ($upcoming) {
    $where[] = 'fechaEvento >= CURDATE()';
}
if ($from) {
    $where[] = 'fechaEvento >= ?';
    $params[] = $from;
    $types   .= 's';
}
if ($to) {
    $where[] = 'fechaEvento <= ?';
    $params[] = $to;
    $types   .= 's';
}

$sql = 'SELECT idEvento, tituloEvento, descripcionEvento, fechaEvento, horaEvento, ubicacionEvento
        FROM eventos';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY fechaEvento ASC, horaEvento ASC LIMIT ? OFFSET ?';
$params[] = $limit;
$params[] = $offset;
$types   .= 'ii';

$st = mysqli_prepare($con, $sql);
if ($params) {
    mysqli_stmt_bind_param($st, $types, ...$params);
}
mysqli_stmt_execute($st);
$rows = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);

v1Ok(['events' => $rows, 'limit' => $limit, 'offset' => $offset]);
