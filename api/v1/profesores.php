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

$method = $_SERVER['REQUEST_METHOD'];
require_once __DIR__ . '/../../modelos/log.php';

if ($method === 'GET') {
$limit  = min(max((int)($_GET['limit']  ?? 20), 1), 100);
$offset = max((int)($_GET['offset'] ?? 0), 0);
$status = strtolower(trim($_GET['status'] ?? ''));
$q      = trim($_GET['q'] ?? '');

$con = obtenerConexion();

// Build WHERE clause
$where = [];
$params = [];
$types = '';

// No soft-delete column for profesores — all listed are active by design
$where[] = "1=1";

// Status filter unused: profesores are always active (no eliminado column)
// Keeping the parameter acceptance for API compatibility, but silently ignoring it
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
}

if ($method === 'POST') {
    $body = v1Body();
    $nombre = trim((string)($body['nombreProfesor'] ?? ''));
    $email = trim((string)($body['emailProfesor'] ?? ''));
    $telefono = trim((string)($body['telefonoProfesor'] ?? ''));
    $dni = trim((string)($body['dniProfesor'] ?? ''));
    $direccion = trim((string)($body['direccionProfesor'] ?? ''));
    $fechaNacimiento = trim((string)($body['fechaNacimientoProfesor'] ?? ''));
    $ciudad = trim((string)($body['ciudadProfesor'] ?? ''));
    $codigoPostal = trim((string)($body['codigoPostalProfesor'] ?? ''));
    $observaciones = trim((string)($body['observacionesProfesor'] ?? ''));
    $fechaAlta = date('Y-m-d');
    
    if ($nombre === '' || $email === '') {
        v1Error('Nombre y email son requeridos.', 400, 'validation');
    }

    $id = insertarProfesor($nombre, $email, $telefono, $dni, $direccion, $fechaNacimiento, $fechaAlta, $ciudad, $codigoPostal, $observaciones);
    if (!$id) {
        v1Error('Could not create professor.', 500, 'error');
    }

    if ($type === 'director') {
        registrarAccion('insertar', 'profesores', null, $nombre);
    } else {
        registrarAccionSecretaria('insertar', 'profesores', null, $nombre);
    }
    v1Ok(['success' => true]);
}

if ($method === 'PUT') {
    $body = v1Body();
    $idProfesor = (int)($body['idProfesor'] ?? 0);
    if (!$idProfesor) v1Error('idProfesor is required.', 400, 'validation');

    $prof = obtenerProfesorPorId($idProfesor);
    if (!$prof) v1Error('Professor not found.', 404, 'not_found');

    $nombre = trim((string)($body['nombreProfesor'] ?? $prof['nombreProfesor']));
    $email = trim((string)($body['emailProfesor'] ?? $prof['emailProfesor']));
    $telefono = trim((string)($body['telefonoProfesor'] ?? $prof['telefonoProfesor']));
    $dni = trim((string)($body['dniProfesor'] ?? $prof['dniProfesor']));
    $direccion = trim((string)($body['direccionProfesor'] ?? $prof['direccionProfesor']));
    $fechaNacimiento = trim((string)($body['fechaNacimientoProfesor'] ?? $prof['fechaNacimientoProfesor']));
    $ciudad = trim((string)($body['ciudadProfesor'] ?? $prof['ciudadProfesor']));
    $codigoPostal = trim((string)($body['codigoPostalProfesor'] ?? $prof['codigoPostalProfesor']));
    $observaciones = trim((string)($body['observacionesProfesor'] ?? $prof['observacionesProfesor']));
    $fechaAlta = $prof['fechaAltaProfesor'];

    if (!actualizarProfesor($idProfesor, $nombre, $email, $telefono, $dni, $direccion, $fechaNacimiento, $fechaAlta, $ciudad, $codigoPostal, $observaciones)) {
        v1Error('Could not update professor.', 500, 'error');
    }

    if ($type === 'director') {
        registrarAccion('actualizar', 'profesores', $idProfesor, $nombre);
    } else {
        registrarAccionSecretaria('actualizar', 'profesores', $idProfesor, $nombre);
    }
    v1Ok(['success' => true]);
}

if ($method === 'DELETE') {
    $idProfesor = (int)($_GET['id'] ?? 0);
    if (!$idProfesor) v1Error('id parameter is required.', 400, 'validation');
    $body = v1Body();
    $password = (string)($body['password'] ?? '');
    if ($password === '') v1Error('Password is required to delete.', 400, 'validation');

    $con = obtenerConexion();
    if ($type === 'director') {
        $stmt = mysqli_prepare($con, "SELECT password FROM directores WHERE idDirector = ?");
    } else {
        $stmt = mysqli_prepare($con, "SELECT password FROM secretarias WHERE idSecretaria = ?");
    }
    mysqli_stmt_bind_param($stmt, "i", $uid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    if (!$user || !password_verify($password, $user['password'])) {
        v1Error('Invalid password.', 401, 'unauthorized');
    }

    if (!eliminarProfesor($idProfesor)) {
        v1Error('Could not delete professor.', 500, 'error');
    }

    if ($type === 'director') {
        registrarAccion('eliminar', 'profesores', $idProfesor, 'Borrado desde app');
    } else {
        registrarAccionSecretaria('eliminar', 'profesores', $idProfesor, 'Borrado desde app');
    }
    v1Ok(['success' => true]);
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
