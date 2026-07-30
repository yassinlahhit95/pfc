<?php
declare(strict_types=1);

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/tutores.php';
require_once __DIR__ . '/../../modelos/log.php';
require_once __DIR__ . '/../../include/Security.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if (!in_array($type, ['director', 'secretaria'])) {
    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$body = v1Body();
$idFamiliar = (int)($body['idFamiliar'] ?? 0);
$password = trim((string)($body['password'] ?? ''));

if (!$idFamiliar || $password === '') {
    v1Error('idFamiliar and password are required.', 400, 'validation');
}

$tutor = obtenerTutorPorId($idFamiliar);
if (!$tutor) {
    v1Error('Tutor not found.', 404, 'not_found');
}

if (!actualizarPasswordTutor($idFamiliar, $password)) {
    v1Error('Could not update password.', 500, 'error');
}

if ($type === 'director') {
    registrarAccion('actualizar', 'tutores', $idFamiliar, 'Cambio de contraseña (manual)');
} else {
    registrarAccionSecretaria('actualizar', 'tutores', $idFamiliar, 'Cambio de contraseña (manual)');
}

v1Ok(['success' => true]);
