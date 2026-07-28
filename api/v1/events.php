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

$usuario = v1Auth(); // any authenticated user can see events
v1RequireFeature('feature_eventos');

require_once __DIR__ . '/../../modelos/eventos.php';

$limit  = min(max((int)($_GET['limit']  ?? 20), 1), 100);
$offset = max((int)($_GET['offset'] ?? 0), 0);
$upcoming = isset($_GET['upcoming']);

$from = $_GET['from'] ?? null;
$to   = $_GET['to']   ?? null;
// Validate date format to prevent injection via named params
if ($from && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = null;
if ($to   && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = null;
if ($upcoming) {
    $hoy  = date('Y-m-d');
    $from = ($from && $from > $hoy) ? $from : $hoy;
}

// Misma regla de visibilidad que la web: el director gestiona el calendario
// entero, el resto de roles solo ve lo que le corresponde según
// tipo_visibilidad/audiencia_json. Antes esto consultaba `eventos` a pelo, sin
// filtro de audiencia ni de baja lógica.
if ($usuario['user_type'] === 'director') {
    $rows = listarTodosEventos(['solo_activos' => true]);
    if ($from) $rows = array_filter($rows, fn($e) => $e['fechaEvento'] >= $from);
    if ($to)   $rows = array_filter($rows, fn($e) => $e['fechaEvento'] <= $to);
    usort($rows, fn($a, $b) => [$a['fechaEvento'], $a['horaEvento']] <=> [$b['fechaEvento'], $b['horaEvento']]);
} else {
    $rows = obtenerEventosParaUsuario((int)$usuario['user_id'], $usuario['user_type'], $from, $to);
}

// Solo los campos públicos del evento: audiencia_json/idCreador revelan a quién
// más va dirigido y no tienen por qué salir del servidor.
$rows = array_map(fn($e) => [
    'idEvento'          => (int)$e['idEvento'],
    'tituloEvento'      => $e['tituloEvento'],
    'descripcionEvento' => $e['descripcionEvento'],
    'fechaEvento'       => $e['fechaEvento'],
    'horaEvento'        => $e['horaEvento'],
    'ubicacionEvento'   => $e['ubicacionEvento'],
], array_slice(array_values($rows), $offset, $limit));

v1Ok(['events' => $rows, 'limit' => $limit, 'offset' => $offset]);
