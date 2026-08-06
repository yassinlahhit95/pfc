<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
header('Content-Type: application/json; charset=utf-8');

// Solo los tutores (profesor con esTutor=1) pueden usar este endpoint
if (empty($_SESSION['esTutor']) || empty($_SESSION['idCicloTutor'])) {
    echo json_encode(['ok' => false, 'msg' => 'Sin permiso.']); exit;
}

$idCicloTutor = (int)$_SESSION['idCicloTutor'];

// CSRF token is already validated in ProfesorGuard.php for all POST requests (with rotate=false to survive AJAX)

$id   = (int)($_POST['id']   ?? 0);
$pass = $_POST['nuevaPassword'] ?? '';

if (!$id || strlen($pass) < 8) {
    echo json_encode(['ok' => false, 'msg' => 'La contraseña debe tener al menos 8 caracteres.']); exit;
}

require_once __DIR__ . '/../../../modelos/estudiantes.php';

// Authorization: student must belong to tutor's ciclo
if (!estudiantePerteneceACiclo($id, $idCicloTutor)) {
    echo json_encode(['ok' => false, 'msg' => 'El estudiante no pertenece a tu ciclo.']); exit;
}

$ok = actualizarPasswordEstudiante($id, $pass);

require_once __DIR__ . '/../../../modelos/log.php';
if ($ok) registrarAccion('cambiar_password', 'estudiante', $id, 'Por tutor-profesor ciclo '.$idCicloTutor);

echo json_encode(['ok' => (bool)$ok, 'msg' => $ok ? 'Contraseña actualizada.' : 'Error al actualizar la contraseña.']);
