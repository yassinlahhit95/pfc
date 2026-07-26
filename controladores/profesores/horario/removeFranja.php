<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
header('Content-Type: application/json');
require_once __DIR__ . '/../../../modelos/horarios.php';

if (empty($_SESSION['esTutor']) || empty($_SESSION['idCicloTutor'])) {
    http_response_code(403); echo json_encode(['ok' => false, 'msg' => 'Acceso denegado.']); exit;
}
$idCicloTutor = (int)$_SESSION['idCicloTutor'];

$idCiclo = (int)($_POST['idCiclo']   ?? 0);
$inicio  = trim($_POST['horaInicio'] ?? '');

if ($idCiclo !== $idCicloTutor) {
    echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para editar este ciclo.']); exit;
}

// rotate=false — shared token reused across this no-reload drag-and-drop page (see controladores/admin/horario/addFranja.php)
if (!Security::validateCSRFToken(null, false)) {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit;
}

if (!$idCiclo || !$inicio) {
    echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']); exit;
}

$horaSql = $inicio . ':00';

if (tieneCeldasEnFranja($idCiclo, $horaSql)) {
    echo json_encode(['ok' => false, 'msg' => 'Elimina primero todos los módulos asignados en esta franja']); exit;
}

$ok = eliminarFranjaHorario($idCiclo, $horaSql);
echo json_encode(['ok' => (bool)$ok]);
