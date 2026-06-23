<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
header('Content-Type: application/json; charset=utf-8');

$tipo = trim($_POST['tipo'] ?? '');
$id   = (int)($_POST['id']   ?? 0);
$pass = $_POST['nuevaPassword'] ?? '';

if (!$id || strlen($pass) < 8) {
    echo json_encode(['ok' => false, 'msg' => 'La contraseña debe tener al menos 8 caracteres.']); exit;
}

// Directors can change: profesor, estudiante, secretaria — NOT other directors
$tiposPermitidos = ['profesor', 'estudiante', 'secretaria'];
if (!in_array($tipo, $tiposPermitidos, true)) {
    echo json_encode(['ok' => false, 'msg' => 'Tipo de usuario no permitido.']); exit;
}

switch ($tipo) {
    case 'profesor':
        require_once __DIR__ . '/../../../modelos/profesores.php';
        $ok = actualizarPasswordProfesor($id, $pass);
        break;
    case 'estudiante':
        require_once __DIR__ . '/../../../modelos/estudiantes.php';
        $ok = actualizarPasswordEstudiante($id, $pass);
        break;
    case 'secretaria':
        require_once __DIR__ . '/../../../modelos/secretarias.php';
        $ok = actualizarPasswordSecretaria($id, $pass);
        break;
}

require_once __DIR__ . '/../../../modelos/log.php';
if (!empty($ok)) registrarAccion('cambiar_password', $tipo, $id, 'Por director');

echo json_encode(['ok' => (bool)($ok ?? false), 'msg' => ($ok ?? false) ? 'Contraseña actualizada.' : 'Error al actualizar la contraseña.']);
