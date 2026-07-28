<?php
declare(strict_types=1);

// GET  /api/v1/pagos.php
//   Query params: limit (max 100, default 20), offset (default 0), status (pendiente/pagado/vencido), ciclo
//   Returns paginated list of student payments with due dates.
//   Director/Secretaria only.

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/estudiantes.php';
require_once __DIR__ . '/../../modelos/pagos.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if ($type !== 'director' && $type !== 'secretaria') {
    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$limit   = min(max((int)($_GET['limit']  ?? 20), 1), 100);
$offset  = max((int)($_GET['offset'] ?? 0), 0);
$status  = strtolower(trim($_GET['status'] ?? ''));
$ciclo   = (int)($_GET['ciclo'] ?? 0);

$con = obtenerConexion();

// Build WHERE clause for filtering
$where = [];
$params = [];
$types = '';

// Status filters
if ($status === 'pagado') {
    $where[] = "p.estadoComprobante = 'aprobado'";
} elseif ($status === 'vencido') {
    $where[] = "p.fechaProximoPago < CURDATE() AND p.estadoComprobante != 'aprobado'";
} elseif ($status === 'pendiente') {
    $where[] = "p.fechaProximoPago >= CURDATE() AND p.estadoComprobante != 'aprobado'";
}

if ($ciclo > 0) {
    $where[] = 'e.idCiclo = ?';
    $params[] = $ciclo;
    $types .= 'i';
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT p.idPago, p.idEstudiante, e.nombreEstudiante,
               p.monto, p.fechaPago, p.fechaProximoPago, p.tipoPago,
               p.estadoComprobante, c.nombreCiclo, c.abreviaturaCiclo,
               CASE
                   WHEN p.estadoComprobante = 'aprobado' THEN 'pagado'
                   WHEN p.fechaProximoPago < CURDATE() AND p.estadoComprobante != 'aprobado' THEN 'vencido'
                   ELSE 'pendiente'
               END as estado
        FROM pagos p
        JOIN estudiantes e ON p.idEstudiante = e.idEstudiante
        JOIN ciclos c ON e.idCiclo = c.idCiclo
        $whereClause
        ORDER BY p.fechaProximoPago ASC, e.nombreEstudiante ASC
        LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$st = mysqli_prepare($con, $sql);
if (!$st) {
    v1Error('Database query failed.', 500, 'error');
}

mysqli_stmt_bind_param($st, $types, ...$params);
mysqli_stmt_execute($st);
$rows = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);

// Get total count for pagination
$countSql = "SELECT COUNT(*) as cnt FROM pagos p
             JOIN estudiantes e ON p.idEstudiante = e.idEstudiante
             JOIN ciclos c ON e.idCiclo = c.idCiclo
             $whereClause";
$countParams = array_slice($params, 0, -2); // Remove LIMIT and OFFSET
$countTypes = substr($types, 0, -2);

if ($countParams) {
    $st2 = mysqli_prepare($con, $countSql);
    mysqli_stmt_bind_param($st2, $countTypes, ...$countParams);
    mysqli_stmt_execute($st2);
    $countResult = mysqli_fetch_assoc(mysqli_stmt_get_result($st2));
    $total = $countResult['cnt'] ?? 0;
} else {
    $st2 = mysqli_prepare($con, $countSql);
    mysqli_stmt_execute($st2);
    $countResult = mysqli_fetch_assoc(mysqli_stmt_get_result($st2));
    $total = $countResult['cnt'] ?? 0;
}

$payments = [];
foreach ($rows as $row) {
    $payments[] = [
        'idPago' => (int)$row['idPago'],
        'idEstudiante' => (int)$row['idEstudiante'],
        'nombreEstudiante' => $row['nombreEstudiante'],
        'monto' => $row['monto'],
        'fechaPago' => $row['fechaPago'],
        'fechaProximoPago' => $row['fechaProximoPago'],
        'tipoPago' => $row['tipoPago'],
        'nombreCiclo' => $row['nombreCiclo'],
        'abreviaturaCiclo' => $row['abreviaturaCiclo'],
        'estado' => $row['estado'],
        'estadoComprobante' => $row['estadoComprobante'],
    ];
}

v1Ok([
    'payments' => $payments,
    'total' => $total,
    'limit' => $limit,
    'offset' => $offset,
], 200);
