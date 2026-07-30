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

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
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
               e.curso, e.anioEstudio, e.eliminado, e.fechaAltaEstudiante, g.nombreGrupo,
               e.fechaNacimientoEstudiante, e.dniEstudiante, e.direccionEstudiante,
               e.ciudadEstudiante, e.codigoPostalEstudiante, e.observacionesEstudiante, e.idGrupo
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
    $r = _descifrarFilaEstudiante($row);
    $students[] = [
        'idEstudiante' => (int)$r['idEstudiante'],
        'nombreEstudiante' => $r['nombreEstudiante'],
        'emailEstudiante'  => $r['emailEstudiante'],
        'telefonoEstudiante' => $r['telefonoEstudiante'] ?? '',
        'idCiclo'          => (int)$r['idCiclo'],
        'nombreCiclo'      => $r['nombreCiclo'],
        'abreviaturaCiclo' => $r['abreviaturaCiclo'],
        'idNivel' => (int)$r['idNivel'],
        'curso' => $r['curso'],
        'anioEstudio' => $r['anioEstudio'],
        'nombreGrupo' => $r['nombreGrupo'] ?? 'Sin grupo',
        'idGrupo' => $r['idGrupo'] ? (int)$r['idGrupo'] : null,
        'estado' => $r['eliminado'] ? 'inactivo' : 'activo',
        'fechaAlta' => $r['fechaAltaEstudiante'],
        'fechaNacimientoEstudiante' => $r['fechaNacimientoEstudiante'] ?? '',
        'dniEstudiante' => $r['dniEstudiante'] ?? '',
        'direccionEstudiante' => $r['direccionEstudiante'] ?? '',
        'ciudadEstudiante' => $r['ciudadEstudiante'] ?? '',
        'codigoPostalEstudiante' => $r['codigoPostalEstudiante'] ?? '',
        'observacionesEstudiante' => $r['observacionesEstudiante'] ?? '',
    ];
}

    v1Ok([
        'students' => $students,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset,
    ], 200);
}

// ---------------------------------------------------------
// CRUD Operations (Only Director and Secretaria)
// ---------------------------------------------------------
if (!in_array($type, ['director', 'secretaria'])) {
    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}

require_once __DIR__ . '/../../modelos/log.php';

if ($method === 'POST') {
    $body = v1Body();
    $nombre = trim((string)($body['nombreEstudiante'] ?? ''));
    $email = trim((string)($body['emailEstudiante'] ?? ''));
    $telefono = trim((string)($body['telefonoEstudiante'] ?? ''));
    $fechaNacimiento = trim((string)($body['fechaNacimientoEstudiante'] ?? ''));
    $dni = trim((string)($body['dniEstudiante'] ?? ''));
    $direccion = trim((string)($body['direccionEstudiante'] ?? ''));
    $ciudad = trim((string)($body['ciudadEstudiante'] ?? ''));
    $codigoPostal = trim((string)($body['codigoPostalEstudiante'] ?? ''));
    $observaciones = trim((string)($body['observacionesEstudiante'] ?? ''));
    $idCiclo = (int)($body['idCiclo'] ?? 0);
    $curso = trim((string)($body['curso'] ?? 'Grado Medio'));
    $anioEstudio = trim((string)($body['anioEstudio'] ?? ''));
    $idGrupo = !empty($body['idGrupo']) ? (int)$body['idGrupo'] : null;
    $fechaAlta = date('Y-m-d');

    if ($nombre === '' || $email === '' || $idCiclo === 0) {
        v1Error('Nombre, email and idCiclo are required.', 400, 'validation');
    }

    if (!insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $curso, $anioEstudio, $idGrupo)) {
        v1Error('Could not create student.', 500, 'error');
    }

    if ($type === 'director') {
        registrarAccion('insertar', 'estudiantes', null, $nombre);
    } else {
        registrarAccionSecretaria('insertar', 'estudiantes', null, $nombre);
    }
    v1Ok(['success' => true]);
}

if ($method === 'PUT') {
    $body = v1Body();
    $idEstudiante = (int)($body['idEstudiante'] ?? 0);
    if (!$idEstudiante) v1Error('idEstudiante is required.', 400, 'validation');

    // Obtain current data
    $est = obtenerEstudiantePorId($idEstudiante);
    if (!$est) v1Error('Student not found.', 404, 'not_found');

    $nombre = trim((string)($body['nombreEstudiante'] ?? $est['nombreEstudiante']));
    $email = trim((string)($body['emailEstudiante'] ?? $est['emailEstudiante']));
    $telefono = trim((string)($body['telefonoEstudiante'] ?? $est['telefonoEstudiante']));
    $fechaNacimiento = trim((string)($body['fechaNacimientoEstudiante'] ?? $est['fechaNacimientoEstudiante']));
    $dni = trim((string)($body['dniEstudiante'] ?? $est['dniEstudiante']));
    $direccion = trim((string)($body['direccionEstudiante'] ?? $est['direccionEstudiante']));
    $ciudad = trim((string)($body['ciudadEstudiante'] ?? $est['ciudadEstudiante']));
    $codigoPostal = trim((string)($body['codigoPostalEstudiante'] ?? $est['codigoPostalEstudiante']));
    $observaciones = trim((string)($body['observacionesEstudiante'] ?? $est['observacionesEstudiante']));
    $idCiclo = (int)($body['idCiclo'] ?? $est['idCiclo']);
    $curso = trim((string)($body['curso'] ?? $est['curso']));
    $anioEstudio = trim((string)($body['anioEstudio'] ?? $est['anioEstudio']));
    $idGrupo = isset($body['idGrupo']) ? ((int)$body['idGrupo'] > 0 ? (int)$body['idGrupo'] : null) : $est['idGrupo'];
    $fechaAlta = $est['fechaAltaEstudiante'];

    if (!actualizarEstudiante($idEstudiante, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $curso, $anioEstudio, $idGrupo)) {
        v1Error('Could not update student.', 500, 'error');
    }

    if ($type === 'director') {
        registrarAccion('actualizar', 'estudiantes', $idEstudiante, $nombre);
    } else {
        registrarAccionSecretaria('actualizar', 'estudiantes', $idEstudiante, $nombre);
    }
    v1Ok(['success' => true]);
}

if ($method === 'DELETE') {
    $idEstudiante = (int)($_GET['id'] ?? 0);
    if (!$idEstudiante) v1Error('id parameter is required.', 400, 'validation');
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

    if (!eliminarEstudianteSuave($idEstudiante)) {
        v1Error('Could not delete student.', 500, 'error');
    }

    if ($type === 'director') {
        registrarAccion('eliminar', 'estudiantes', $idEstudiante, 'Borrado desde app');
    } else {
        registrarAccionSecretaria('eliminar', 'estudiantes', $idEstudiante, 'Borrado desde app');
    }
    v1Ok(['success' => true]);
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
