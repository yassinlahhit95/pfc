<?php
declare(strict_types=1);

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/secretarias.php';
require_once __DIR__ . '/../../modelos/log.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if ($type !== 'director') {
    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}

$method = $_SERVER['REQUEST_METHOD'];
$con = obtenerConexion();

if ($method === 'GET') {
    $limit  = min(max((int)($_GET['limit']  ?? 20), 1), 100);
    $offset = max((int)($_GET['offset'] ?? 0), 0);
    $q      = trim($_GET['q'] ?? '');

    $where = ["1=1"];
    $params = [];
    $types = '';

    if ($q) {
        $where[] = "nombreSecretaria LIKE ? OR emailSecretaria LIKE ?";
        $params[] = "%$q%";
        $params[] = "%$q%";
        $types .= 'ss';
    }

    $whereClause = 'WHERE ' . implode(' AND ', $where);

    $sql = "SELECT idSecretaria, nombreSecretaria, emailSecretaria, activoSecretaria
            FROM secretarias
            $whereClause
            ORDER BY nombreSecretaria ASC
            LIMIT ? OFFSET ?";

    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    $st = mysqli_prepare($con, $sql);
    if (!$st) v1Error('Database query failed.', 500, 'error');

    if ($types) mysqli_stmt_bind_param($st, $types, ...$params);
    mysqli_stmt_execute($st);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);

    $countSql = "SELECT COUNT(*) as cnt FROM secretarias $whereClause";
    $countParams = array_slice($params, 0, -2);
    $countTypes = substr($types, 0, -2);
    
    $cst = mysqli_prepare($con, $countSql);
    if ($countTypes) mysqli_stmt_bind_param($cst, $countTypes, ...$countParams);
    mysqli_stmt_execute($cst);
    $total = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($cst))['cnt'];

    v1Ok([
        'secretarias' => $rows,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);
}

if ($method === 'POST') {
    $body = v1Body();
    $nombre = trim((string)($body['nombreSecretaria'] ?? ''));
    $email = trim((string)($body['emailSecretaria'] ?? ''));

    if (!$nombre || !$email) {
        v1Error('Nombre and email are required.', 400, 'validation');
    }

    $id = insertarSecretaria($nombre, $email);
    if (!$id) v1Error('Could not create secretaria.', 500, 'error');

    registrarAccion('crear', 'secretarias', $id, 'Creado desde app');
    v1Ok(['success' => true, 'idSecretaria' => $id]);
}

if ($method === 'PUT') {
    $body = v1Body();
    $id = (int)($body['idSecretaria'] ?? 0);
    $nombre = trim((string)($body['nombreSecretaria'] ?? ''));
    $email = trim((string)($body['emailSecretaria'] ?? ''));
    $newPassword = (string)($body['newPassword'] ?? '');

    if (!$id || !$nombre || !$email) {
        v1Error('id, nombre and email are required.', 400, 'validation');
    }

    if ($newPassword !== '' && strlen($newPassword) < 8) {
        v1Error('Password must be at least 8 characters.', 400, 'validation');
    }

    if (!actualizarSecretaria($id, $nombre, $email)) {
        v1Error('Could not update secretaria.', 500, 'error');
    }

    if ($newPassword !== '' && !actualizarPasswordSecretaria($id, $newPassword)) {
        v1Error('Could not update password.', 500, 'error');
    }

    registrarAccion('actualizar', 'secretarias', $id, 'Actualizado desde app');
    if ($newPassword !== '') {
        registrarAccion('cambiar_password', 'secretarias', $id, 'Contraseña restablecida por director desde app');
    }
    v1Ok(['success' => true]);
}

if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) v1Error('id is required.', 400, 'validation');

    $body = v1Body();
    $password = (string)($body['password'] ?? '');
    if ($password === '') v1Error('Password is required.', 400, 'validation');

    $stmt = mysqli_prepare($con, "SELECT password FROM directores WHERE idDirector = ?");
    mysqli_stmt_bind_param($stmt, "i", $uid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    if (!$user || !password_verify($password, $user['password'])) {
        v1Error('Invalid password.', 401, 'unauthorized');
    }

    if (!eliminarSecretaria($id)) {
        v1Error('Could not delete secretaria.', 500, 'error');
    }

    registrarAccion('eliminar', 'secretarias', $id, 'Borrado desde app');
    v1Ok(['success' => true]);
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
