<?php
require_once __DIR__ . '/../../../include/SecretariaGuard.php';
header('Content-Type: application/json; charset=utf-8');

$id   = (int)($_POST['id']   ?? 0);
$pass = $_POST['nuevaPassword'] ?? '';

if (!$id || strlen($pass) < 8) {
    echo json_encode(['ok' => false, 'msg' => 'La contraseña debe tener al menos 8 caracteres.']); exit;
}

require_once __DIR__ . '/../../../modelos/estudiantes.php';
$ok = actualizarPasswordEstudiante($id, $pass);

require_once __DIR__ . '/../../../modelos/log.php';
if ($ok) registrarAccion('cambiar_password', 'estudiante', $id, 'Por secretaria');

echo json_encode(['ok' => (bool)$ok, 'msg' => $ok ? 'Contraseña actualizada.' : 'Error al actualizar la contraseña.']);
