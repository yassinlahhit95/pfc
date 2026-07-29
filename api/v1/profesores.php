<?php
declare(strict_types=1);

// GET  /api/v1/profesores.php
//   Query params: limit (max 100, default 20), offset (default 0), status (activo/inactivo), q (search by name)
//   Returns paginated list of professors with basic info.
//   Director/Secretaria only.

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/profesores.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if ($type !== 'director' && $type !== 'secretaria') {
    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$limit  = min(max((int)($_GET['limit']  ?? 20), 1), 100);
$offset = max((int)($_GET['offset'] ?? 0), 0);
$status = strtolower(trim($_GET['status'] ?? ''));
$q      = trim($_GET['q'] ?? '');

$con = obtenerConexion();

// Build WHERE clause
$where = [];
$params = [];
$types = '';

// No soft-delete column for profesores
$where[] = "1=1";

if ($q) {
    $where[] = "p.nombreProfesor LIKE ?";
    $params[] = "%$q%";
    $types .= 's';
}

$whereClause = 'WHERE ' . implode(' AND ', $where);

$sql = "SELECT p.idProfesor, p.nombreProfesor, p.emailProfesor,
               p.telefonoProfesor, p.esTutor, p.idCicloTutor,
               COALESCE(c.abreviaturaCiclo, '') as cicloTutoria
        FROM profesores p
        LEFT JOIN ciclos c ON p.idCicloTutor = c.idCiclo
        $whereClause
        ORDER BY p.nombreProfesor ASC
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
$countSql = "SELECT COUNT(*) as cnt FROM profesores p $whereClause";
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

$professors = [];
foreach ($rows as $row) {
    $professors[] = [
        'idProfesor' => (int)$row['idProfesor'],
        'nombreProfesor' => $row['nombreProfesor'],
        'emailProfesor' => $row['emailProfesor'],
        'telefonoProfesor' => $row['telefonoProfesor'],
        'esTutor' => (bool)$row['esTutor'],
        'cicloTutoria' => $row['cicloTutoria'],
    ];
}

v1Ok([
    'professors' => $professors,
    'total' => $total,
    'limit' => $limit,
    'offset' => $offset,
], 200);
