<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/horarios.php";

if (empty($_SESSION['idAdmin'])) {
    echo json_encode(['ok' => false, 'msg' => 'Sesión no válida.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::validateCSRFToken()) {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud no válida o expirada (CSRF).']);
    exit;
}

$idCiclo    = (int)($_POST['idCiclo'] ?? 0);
$dia        = trim($_POST['dia'] ?? '');
$horaInicio = trim($_POST['horaInicio'] ?? '');

$diasValidos = obtenerDiasHorario();
if ($idCiclo <= 0 || !in_array($dia, $diasValidos, true) || $horaInicio === '') {
    echo json_encode(['ok' => false, 'msg' => 'Datos de celda no válidos.']);
    exit;
}

if (borrarCeldaHorario($idCiclo, $dia, $horaInicio . ':00')) {
    echo json_encode(['ok' => true, 'msg' => 'Asignación eliminada.']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'No se pudo eliminar la asignación.']);
}
exit;
