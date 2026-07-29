<?php
declare(strict_types=1);

// GET  /api/v1/estudiantes.php
//   Query params: limit (max 100, default 20), offset (default 0), ciclo, status (activo/inactivo), q (search by name)
//   Returns paginated list of students with basic info.
//   Director/Secretaria only.

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/estudiantes.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if (!in_array($type, ['director', 'secretaria', 'admin', 'profesor'])) {
    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$limit  = min(max((int)($_GET['limit']  ?? 20), 1), 100);
$offset = max((int)($_GET['offset'] ?? 0), 0);
$ciclo  = (int)($_GET['ciclo'] ?? 0);
$nivel  = (int)($_GET['nivel'] ?? 0);
$grupo  = (int)($_GET['grupo'] ?? 0);
$anio   = trim($_GET['anio'] ?? '');
$status = strtolower(trim($_GET['status'] ?? ''));
$q      = trim($_GET['q'] ?? '');

$con = obtenerConexion();

// Build WHERE clause
$where = ["(e.eliminado = 0 OR e.eliminado IS NULL)"];
$params = [];
$types = '';

if ($type === 'profesor' && $uid > 0) {
    // Check if professor is tutor
    $isTutor = false;
    $idCicloTutor = 0;
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!empty($_SESSION['esTutor']) && !empty($_SESSION['idCicloTutor'])) {
        $isTutor = true;
        $idCicloTutor = (int)$_SESSION['idCicloTutor'];
    }

    if ($isTutor && $idCicloTutor > 0) {
        $where[] = 'e.idCiclo = ?';
        $params[] = $idCicloTutor;
        $types .= 'i';
    } else {
        $where[] = '(e.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?) OR e.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?))';
        $params[] = $uid;
        $params[] = $uid;
        $types .= 'ii';
    }
}

if ($ciclo > 0) {
    $where[] = 'e.idCiclo = ?';
    $params[] = $ciclo;
    $types .= 'i';
}

if ($nivel > 0) {
    $where[] = 'c.idNivel = ?';
    $params[] = $nivel;
    $types .= 'i';
}

if ($grupo > 0) {
    $where[] = 'e.idGrupo = ?';
    $params[] = $grupo;
    $types .= 'i';
}

if ($anio !== '') {
    $where[] = 'e.anioEstudio = ?';
    $params[] = $anio;
    $types .= 's';
}

if ($status === 'inactivo') {
    $where[] = 'e.eliminado = 1';
} elseif ($status === 'activo') {
    $where[] = '(e.eliminado = 0 OR e.eliminado IS NULL)';
}

if ($q) {
    $where[] = "e.nombreEstudiante LIKE ?";
    $params[] = "%$q%";
    $types .= 's';
}

$whereClause = implode(' AND ', $where);

$sql = "SELECT e.idEstudiante, e.nombreEstudiante, e.emailEstudiante, e.telefonoEstudiante,
               e.idCiclo, c.nombreCiclo, c.abreviaturaCiclo, c.idNivel,
               e.curso, e.anioEstudio, e.eliminado, e.fechaAltaEstudiante, g.nombreGrupo
        FROM estudiantes e
        JOIN ciclos c ON e.idCiclo = c.idCiclo
        LEFT JOIN grupos g ON e.idGrupo = g.idGrupo
        WHERE $whereClause
        ORDER BY e.nombreEstudiante ASC
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
$countSql = "SELECT COUNT(*) as cnt FROM estudiantes e
             JOIN ciclos c ON e.idCiclo = c.idCiclo
             WHERE $whereClause";
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

$students = [];
foreach ($rows as $row) {
    $students[] = [
        'idEstudiante' => (int)$row['idEstudiante'],
        'nombreEstudiante' => $row['nombreEstudiante'],
        'emailEstudiante'  => $row['emailEstudiante'],
        'telefonoEstudiante' => $row['telefonoEstudiante'] ?? '',
        'idCiclo'          => (int)$row['idCiclo'],
        'nombreCiclo'      => $row['nombreCiclo'],
        'abreviaturaCiclo' => $row['abreviaturaCiclo'],
        'idNivel' => (int)$row['idNivel'],
        'curso' => $row['curso'],
        'anioEstudio' => $row['anioEstudio'],
        'nombreGrupo' => $row['nombreGrupo'] ?? 'Sin grupo',
        'estado' => $row['eliminado'] ? 'inactivo' : 'activo',
        'fechaAlta' => $row['fechaAltaEstudiante'],
    ];
}

v1Ok([
    'students' => $students,
    'total' => $total,
    'limit' => $limit,
    'offset' => $offset,
], 200);
