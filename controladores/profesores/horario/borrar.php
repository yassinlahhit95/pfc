<?php
require_once __DIR__ . '/../../../include/Security.php';
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../../modelos/horarios.php';

if (empty($_SESSION['idProfesor']) || empty($_SESSION['esTutor']) || empty($_SESSION['idCicloTutor'])) {
    http_response_code(403); echo json_encode(['ok' => false, 'msg' => 'Acceso denegado.']); exit;
}
$idCicloTutor = (int)$_SESSION['idCicloTutor'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::validateCSRFToken()) {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud no válida o expirada (CSRF).']); exit;
}

$idCiclo    = (int)($_POST['idCiclo'] ?? 0);
$dia        = trim($_POST['dia'] ?? '');
$horaInicio = trim($_POST['horaInicio'] ?? '');

if ($idCiclo !== $idCicloTutor) {
    echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para editar este ciclo.']); exit;
}

$diasValidos = obtenerDiasHorario();
if ($idCiclo <= 0 || !in_array($dia, $diasValidos, true) || $horaInicio === '') {
    echo json_encode(['ok' => false, 'msg' => 'Datos de celda no válidos.']); exit;
}

if (borrarCeldaHorario($idCiclo, $dia, $horaInicio . ':00')) {
    echo json_encode(['ok' => true, 'msg' => 'Asignación eliminada.']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'No se pudo eliminar la asignación.']);
}
exit;
