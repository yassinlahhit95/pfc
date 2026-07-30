<?php
declare(strict_types=1);

// PUT /api/v1/profesores-password.php
//   Change professor password. Requires director or secretaria role.

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/profesores.php';
require_once __DIR__ . '/../../modelos/log.php';
require_once __DIR__ . '/../../include/Security.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'PUT') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$usuario = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $usuario;

if (!in_array($type, ['director', 'secretaria'])) {
    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}

$body = v1Body();
$idProfesor = (int)($body['idProfesor'] ?? 0);
$nuevaPassword = trim((string)($body['nuevaPassword'] ?? ''));

if (!$idProfesor || empty($nuevaPassword)) {
    v1Error('idProfesor and nuevaPassword are required.', 400, 'validation');
}

if (strlen($nuevaPassword) < 6) {
    v1Error('Password must be at least 6 characters long.', 400, 'validation');
}

$prof = obtenerProfesorPorId($idProfesor);
if (!$prof) v1Error('Professor not found.', 404, 'not_found');

if (!actualizarPasswordProfesor($idProfesor, $nuevaPassword)) {
    v1Error('Could not update password.', 500, 'error');
}

if ($type === 'director') {
    registrarAccion('cambiar_password', 'profesor', $idProfesor, 'Por director');
} else {
    registrarAccionSecretaria('cambiar_password', 'profesor', $idProfesor, 'Por secretaria');
}

v1Ok(['success' => true]);
