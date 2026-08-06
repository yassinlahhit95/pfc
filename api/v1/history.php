<?php
declare(strict_types=1);

// Evita que los warnings/notices de PHP contaminen la salida JSON
ini_set('display_errors', '0');
error_reporting(0);

// GET /api/v1/history.php — unified audit log activities for Director role only
//
// Query params:
//   date: (format: YYYY-MM-DD, defaults to current date)
//
// Returns combined logs from log_acciones (admin changes) and historial_secretarias (secretary actions)

require_once __DIR__ . '/_api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

// Authenticate user
$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

// Access restricted to director only
if ($type !== 'director') {
    v1Error('This endpoint is only available for the Director role.', 403, 'forbidden');
}

$date = $_GET['date'] ?? date('Y-m-d');

// Valida el formato de fecha YYYY-MM-DD
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    v1Error('Invalid date format. Expected YYYY-MM-DD.', 400, 'validation');
}

$con = obtenerConexion();

// Query 1: Secretary logs
$sqlSecretarias = "
    SELECT 
        s.nombreSecretaria AS user_name,
        h.accion,
        h.entidad AS tabla,
        h.detalles AS descripcion,
        h.fecha
    FROM historial_secretarias h
    LEFT JOIN secretarias s ON h.idSecretaria = s.idSecretaria
    WHERE DATE(h.fecha) = ?
";

// Query 2: Admin logs
$sqlAdmin = "
    SELECT 
        d.nombreDirector AS user_name,
        l.accion,
        l.tabla,
        l.descripcion,
        l.fecha
    FROM log_acciones l
    LEFT JOIN directores d ON l.idAdmin = d.idDirector
    WHERE DATE(l.fecha) = ?
";

try {
    $logs = [];

    // Obtiene los logs de secretaría
    $st1 = mysqli_prepare($con, $sqlSecretarias);
    if (!$st1) {
        throw new Exception(mysqli_error($con));
    }
    mysqli_stmt_bind_param($st1, 's', $date);
    mysqli_stmt_execute($st1);
    $res1 = mysqli_stmt_get_result($st1);
    while ($row = mysqli_fetch_assoc($res1)) {
        $logs[] = [
            'role' => 'secretaria',
            'user_name' => $row['user_name'] ?? 'Sistema',
            'accion' => $row['accion'],
            'tabla' => $row['tabla'],
            'descripcion' => $row['descripcion'] ?? '',
            'fecha' => $row['fecha']
        ];
    }

    // Obtiene los logs de admin
    $st2 = mysqli_prepare($con, $sqlAdmin);
    if (!$st2) {
        throw new Exception(mysqli_error($con));
    }
    mysqli_stmt_bind_param($st2, 's', $date);
    mysqli_stmt_execute($st2);
    $res2 = mysqli_stmt_get_result($st2);
    while ($row = mysqli_fetch_assoc($res2)) {
        $logs[] = [
            'role' => 'admin',
            'user_name' => $row['user_name'] ?? 'Sistema',
            'accion' => $row['accion'],
            'tabla' => $row['tabla'],
            'descripcion' => $row['descripcion'] ?? '',
            'fecha' => $row['fecha']
        ];
    }

    // Sort by fecha DESC (newest first)
    usort($logs, function ($a, $b) {
        return strcmp($b['fecha'], $a['fecha']);
    });

    v1Ok(['history' => $logs]);

} catch (\Throwable $e) {
    v1Error('Database error: ' . $e->getMessage(), 500, 'database_error');
}
