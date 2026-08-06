<?php
declare(strict_types=1);

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/estudiantes.php';
require_once __DIR__ . '/../../modelos/tutores.php';
require_once __DIR__ . '/../../modelos/log.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if (!in_array($type, ['director', 'secretaria', 'admin', 'profesor'])) {
    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'prestamos') {
        v1RequireFeature('feature_inventario');
        $idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
        if (!$idEstudiante) v1Error('idEstudiante requerido', 400, 'invalid_request');
        if ($type === 'estudiante' && $uid !== $idEstudiante) v1Error('Forbidden', 403, 'access_denied');

        require_once __DIR__ . '/../../modelos/conectar.php';
        $con = obtenerConexion();
        $sql = "SELECT p.idPrestamo, p.idDispositivo, d.nombreDispositivo, p.fechaPrestamo
                FROM prestamos p
                JOIN dispositivos d ON p.idDispositivo = d.idDispositivo
                WHERE p.idEstudiante = ? AND p.estadoPrestamo = 'en curso' AND p.deleted_at IS NULL
                ORDER BY p.fechaPrestamo DESC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $idEstudiante);
        mysqli_stmt_execute($stmt);
        $prestamos = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

        v1Ok([
            'activos' => count($prestamos),
            'prestamos' => array_map(fn($p) => [
                'idPrestamo' => (int)$p['idPrestamo'],
                'idDispositivo' => (int)$p['idDispositivo'],
                'nombreDispositivo' => $p['nombreDispositivo'],
                'fechaPrestamo' => $p['fechaPrestamo'],
            ], $prestamos),
        ]);
    }

    if ($action === 'familia') {
        if (!in_array($type, ['director', 'secretaria'])) v1Error('Forbidden.', 403, 'forbidden');
        $idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
        if (!$idEstudiante) v1Error('idEstudiante is required.', 400, 'validation');
        $con = obtenerConexion();
        $sql = "SELECT t.idTutor, t.nombreTutor, t.emailTutor, t.dniTutor, t.telefonoTutor, et.parentesco
                FROM tutores t
                JOIN estudiante_tutor et ON t.idTutor = et.idTutor
                WHERE et.idEstudiante = ? ORDER BY t.nombreTutor ASC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $idEstudiante);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $tutores = [];
        while ($row = mysqli_fetch_assoc($res)) {
            if (function_exists('_descifrarFilaTutor')) $row = _descifrarFilaTutor($row);
            $tutores[] = [
                'idTutor' => (int)$row['idTutor'],
                'nombreTutor' => $row['nombreTutor'],
                'emailTutor' => $row['emailTutor'],
                'dniTutor' => $row['dniTutor'],
                'telefonoTutor' => $row['telefonoTutor'],
                'parentesco' => $row['parentesco'] ?? 'Tutor',
            ];
        }
        v1Ok(['tutores' => $tutores]);
    }

    // Comportamiento por defecto del listado
    $limit  = min(max((int)($_GET['limit']  ?? 20), 1), 100);
    $offset = max((int)($_GET['offset'] ?? 0), 0);
    $ciclo  = (int)($_GET['ciclo'] ?? 0);
    $nivel  = (int)($_GET['nivel'] ?? 0);
    $grupo  = (int)($_GET['grupo'] ?? 0);
    $anio   = trim($_GET['anio'] ?? '');
    $status = strtolower(trim($_GET['status'] ?? ''));
    $q      = trim($_GET['q'] ?? '');

    $con = obtenerConexion();
    $where = ["(e.eliminado = 0 OR e.eliminado IS NULL)"];
    $params = [];
    $types = '';

    if ($type === 'profesor' && $uid > 0) {
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

    if ($ciclo > 0) { $where[] = 'e.idCiclo = ?'; $params[] = $ciclo; $types .= 'i'; }
    if ($nivel > 0) { $where[] = 'c.idNivel = ?'; $params[] = $nivel; $types .= 'i'; }
    if ($grupo > 0) { $where[] = 'e.idGrupo = ?'; $params[] = $grupo; $types .= 'i'; }
    if ($anio !== '') { $where[] = 'e.anioEstudio = ?'; $params[] = $anio; $types .= 's'; }
    if ($status === 'inactivo') $where[] = 'e.eliminado = 1';
    elseif ($status === 'activo') $where[] = '(e.eliminado = 0 OR e.eliminado IS NULL)';
    if ($q) { $where[] = "e.nombreEstudiante LIKE ?"; $params[] = "%$q%"; $types .= 's'; }

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
    if (!$st) v1Error('Database query failed.', 500, 'error');
    mysqli_stmt_bind_param($st, $types, ...$params);
    mysqli_stmt_execute($st);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);

    $countSql = "SELECT COUNT(*) as cnt FROM estudiantes e
                 JOIN ciclos c ON e.idCiclo = c.idCiclo
                 WHERE $whereClause";
    $countParams = array_slice($params, 0, -2);
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

if (!in_array($type, ['director', 'secretaria'])) {
    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}

if ($method === 'POST') {
    $body = v1Body();
    $action = $body['action'] ?? ($_GET['action'] ?? '');

    if ($action === 'familia') {
        $idEstudiante = (int)($body['idEstudiante'] ?? 0);
        $nombre = trim((string)($body['nombreTutor'] ?? ''));
        $email = trim((string)($body['emailTutor'] ?? ''));
        $dni = trim((string)($body['dniTutor'] ?? ''));
        $telefono = trim((string)($body['telefonoTutor'] ?? ''));
        $parentesco = trim((string)($body['parentesco'] ?? 'Tutor'));

        if (!$idEstudiante || empty($nombre) || empty($email) || empty($dni)) v1Error('Missing required fields.', 400, 'validation');
        $tutorExistente = obtenerTutorPorDni($dni);
        if ($tutorExistente) {
            $idTutor = (int)$tutorExistente['idTutor'];
        } else {
            $idTutor = insertarTutor($nombre, $email, $dni, $telefono);
            if (!$idTutor) v1Error('Could not create tutor.', 500, 'error');
        }
        if (!vincularEstudianteTutor($idEstudiante, $idTutor, $parentesco)) v1Error('Link failed.', 500, 'error');
        
        if ($type === 'director') registrarAccion('vincular_tutor', 'estudiante_tutor', $idEstudiante, "Tutor ID: $idTutor ($nombre)");
        else registrarAccionSecretaria('vincular_tutor', 'estudiante_tutor', $idEstudiante, "Tutor ID: $idTutor ($nombre)");
        v1Ok(['success' => true, 'idTutor' => $idTutor]);
    }

    // POST por defecto
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

    if ($nombre === '' || $email === '' || $idCiclo === 0) v1Error('Missing fields.', 400, 'validation');
    if (!insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $curso, $anioEstudio, $idGrupo)) {
        v1Error('Could not create student.', 500, 'error');
    }

    if ($type === 'director') registrarAccion('insertar', 'estudiantes', null, $nombre);
    else registrarAccionSecretaria('insertar', 'estudiantes', null, $nombre);
    v1Ok(['success' => true]);
}

if ($method === 'PUT') {
    $body = v1Body();
    $action = $body['action'] ?? ($_GET['action'] ?? '');

    if ($action === 'password') {
        $idEstudiante = (int)($body['idEstudiante'] ?? 0);
        $nuevaPassword = trim((string)($body['nuevaPassword'] ?? ''));
        if (!$idEstudiante || empty($nuevaPassword)) v1Error('Required fields missing.', 400, 'validation');
        if (strlen($nuevaPassword) < 6) v1Error('Password must be at least 6 chars.', 400, 'validation');
        
        $est = obtenerEstudiantePorId($idEstudiante);
        if (!$est) v1Error('Student not found.', 404, 'not_found');
        if (!actualizarPasswordEstudiante($idEstudiante, $nuevaPassword)) v1Error('Could not update password.', 500, 'error');
        
        if ($type === 'director') registrarAccion('cambiar_password', 'estudiante', $idEstudiante, 'Por director');
        else registrarAccionSecretaria('cambiar_password', 'estudiante', $idEstudiante, 'Por secretaria');
        v1Ok(['success' => true]);
    }

    if ($action === 'familia') {
        $idTutor = (int)($body['idTutor'] ?? 0);
        $idEstudiante = (int)($body['idEstudiante'] ?? 0);
        $nombre = trim((string)($body['nombreTutor'] ?? ''));
        $email = trim((string)($body['emailTutor'] ?? ''));
        $dni = trim((string)($body['dniTutor'] ?? ''));
        $telefono = trim((string)($body['telefonoTutor'] ?? ''));
        $parentesco = trim((string)($body['parentesco'] ?? ''));

        if (!$idTutor || !$idEstudiante || empty($nombre) || empty($email) || empty($dni)) v1Error('Missing fields.', 400, 'validation');
        if (!actualizarTutor($idTutor, $nombre, $email, $dni, $telefono)) v1Error('Update failed.', 500, 'error');
        
        if (!empty($parentesco)) {
            $con = obtenerConexion();
            $upd = mysqli_prepare($con, "UPDATE estudiante_tutor SET parentesco = ? WHERE idEstudiante = ? AND idTutor = ?");
            mysqli_stmt_bind_param($upd, "sii", $parentesco, $idEstudiante, $idTutor);
            mysqli_stmt_execute($upd);
        }
        if ($type === 'director') registrarAccion('actualizar', 'tutores', $idTutor, $nombre);
        else registrarAccionSecretaria('actualizar', 'tutores', $idTutor, $nombre);
        v1Ok(['success' => true]);
    }

    // PUT por defecto
    $idEstudiante = (int)($body['idEstudiante'] ?? 0);
    if (!$idEstudiante) v1Error('idEstudiante is required.', 400, 'validation');

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

    if ($type === 'director') registrarAccion('actualizar', 'estudiantes', $idEstudiante, $nombre);
    else registrarAccionSecretaria('actualizar', 'estudiantes', $idEstudiante, $nombre);
    v1Ok(['success' => true]);
}

if ($method === 'DELETE') {
    $action = $_GET['action'] ?? '';
    if ($action === 'familia') {
        $idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
        $idTutor = (int)($_GET['idTutor'] ?? 0);
        if (!$idEstudiante || !$idTutor) v1Error('idEstudiante and idTutor are required.', 400, 'validation');
        $con = obtenerConexion();
        $del = mysqli_prepare($con, "DELETE FROM estudiante_tutor WHERE idEstudiante = ? AND idTutor = ?");
        mysqli_stmt_bind_param($del, "ii", $idEstudiante, $idTutor);
        if (!mysqli_stmt_execute($del)) v1Error('Could not unlink tutor.', 500, 'error');
        if ($type === 'director') registrarAccion('desvincular_tutor', 'estudiante_tutor', $idEstudiante, "Tutor ID: $idTutor desvinculado");
        else registrarAccionSecretaria('desvincular_tutor', 'estudiante_tutor', $idEstudiante, "Tutor ID: $idTutor desvinculado");
        v1Ok(['success' => true]);
    }

    $idEstudiante = (int)($_GET['id'] ?? 0);
    if (!$idEstudiante) v1Error('id parameter is required.', 400, 'validation');
    $body = v1Body();
    $password = (string)($body['password'] ?? '');
    if ($password === '') v1Error('Password is required to delete.', 400, 'validation');

    $con = obtenerConexion();
    if ($type === 'director') $stmt = mysqli_prepare($con, "SELECT password FROM directores WHERE idDirector = ?");
    else $stmt = mysqli_prepare($con, "SELECT password FROM secretarias WHERE idSecretaria = ?");
    
    mysqli_stmt_bind_param($stmt, "i", $uid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    if (!$user || !password_verify($password, $user['password'])) v1Error('Invalid password.', 401, 'unauthorized');
    if (!eliminarEstudianteSuave($idEstudiante)) v1Error('Could not delete student.', 500, 'error');

    if ($type === 'director') registrarAccion('eliminar', 'estudiantes', $idEstudiante, 'Borrado desde app');
    else registrarAccionSecretaria('eliminar', 'estudiantes', $idEstudiante, 'Borrado desde app');
    v1Ok(['success' => true]);
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
